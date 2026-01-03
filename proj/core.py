# core.py — نسخة محسّنة مع تحسينات موثوقية و readiness
# -*- coding: utf-8 -*-
import pandas as pd
import random
import os, sys, json, pickle, subprocess, re, logging
os.environ["OPENROUTER_KEY"] = "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"

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger("core")

# ===== إعداد مسارات متوافقة مع أي نظام =====
BASE_DIR = os.path.dirname(__file__)
PKL_PATH = os.getenv("PKL_PATH", os.path.join(BASE_DIR, "m1", "dept_models.pkl"))
logger.info(f"[DEBUG] Looking for dept_models.pkl at: {PKL_PATH} | Exists: {os.path.exists(PKL_PATH)}")

# ===== Config validation on startup =====
REQUIRED_ENV = ["OPENROUTER_KEY"]  # أضف أي متغيرات env مطلوبة
missing_env = [k for k in REQUIRED_ENV if not os.getenv(k)]
if missing_env:
    logger.warning(f"Missing required environment variables: {missing_env}")

if not os.path.exists(PKL_PATH):
    logger.warning(f"PKL file not found: {PKL_PATH}")

# ===== تحميل المتنبّئ M1 إن توفر =====
try:
    from m1.m1_predect_dept import M1DeptPredictor
    m1_dept = M1DeptPredictor(pkl_path=PKL_PATH, min_required=4)
    logger.info("[DEBUG] M1DeptPredictor loaded.")
except Exception as e:
    logger.error(f"M1DeptPredictor init failed: {e}", exc_info=True)
    m1_dept = None


PER_CLASS_IMPORTANCE_PATH = os.path.join(BASE_DIR, "per_class_feature_importance.xlsx")
TOP_MODEL_SHEET = "WeightedEnsemble"  # يمكنك تغييره حسب الحاجة

try:
    df_importance = pd.read_excel(PER_CLASS_IMPORTANCE_PATH, sheet_name=TOP_MODEL_SHEET)
    # إنشاء قاموس: class_name -> list of features مرتبة حسب importance تنازليًا
    importance_dict = {
        class_name: (
            df_class[df_class['importance'] > 0]
            .sort_values('importance', ascending=False)['feature']
            .tolist()
        )
        for class_name, df_class in df_importance.groupby('class_name')
    }
    print(f"[DEBUG] Loaded per-class feature importance for {len(importance_dict)} classes")
except Exception as e:
    print(f"[ERROR] Could not load per-class importance: {e}")
    importance_dict = {}



# ===== دوال مساعدة =====
def parse_llm_json(out: str):
    """
    استخراج JSON من إخراج LLM بطريقة آمنة
    """
    try:
        return json.loads(out)
    except json.JSONDecodeError:
        matches = re.findall(r'\{.*?\}', out, re.S)
        for m in matches:
            try:
                return json.loads(m)
            except json.JSONDecodeError:
                continue
        raise ValueError(f"LLM output cannot be parsed as JSON: {out}")

def safe_run(func, *args, **kwargs):
    """
    تشغيل دالة مع تسجيل أي استثناءات
    """
    try:
        return func(*args, **kwargs)
    except Exception as e:
        logger.error(f"Exception in {func.__name__}: {e}", exc_info=True)
        raise

# ===== Health-check / readiness =====
def health_check():
    report = {
        "status": "ok",
        "errors": [],
        "checks": {}
    }
        # تحقق أن M1 يتأثر بالمدخلات
    dist_empty = run_M1_on_answers({})
    dist_real  = run_M1_on_answers({"fever": 1, "cough": 1})

    if dist_empty == dist_real:
        raise RuntimeError("M1 output not affected by inputs")


    # 1️⃣ ملفات وبيئة
    if not os.path.exists(PKL_PATH):
        report["errors"].append("PKL file missing")
        report["checks"]["pkl"] = False
    else:
        report["checks"]["pkl"] = True

    if m1_dept is None:
        report["errors"].append("M1 predictor not initialized")
        report["checks"]["m1_loaded"] = False
    else:
        report["checks"]["m1_loaded"] = True

    # 2️⃣ الدوال الحرجة
    for fn in [
        "normalize_value_for_key",
        "canonicalize_to_m1_keys",
        "run_M1_on_answers",
        "m1_followups"
    ]:
        if fn not in globals():
            report["errors"].append(f"Missing function: {fn}")
            report["checks"][fn] = False
        else:
            report["checks"][fn] = True

    # 3️⃣ feature pool
    try:
        pool = m1_feature_pool()
        if not pool or not isinstance(pool, list):
            raise ValueError("Empty or invalid feature pool")
        report["checks"]["feature_pool"] = len(pool)
    except Exception as e:
        report["errors"].append(f"Feature pool error: {e}")
        report["checks"]["feature_pool"] = False

    # 4️⃣ تدفّق واقعي (simulate user)
    try:
        fake_raw = {
            "fever": "yes",
            "dry_cough": "no",
            "age": "45",
            "bpsys": "130"
        }

        normalized = {
            k: normalize_value_for_key(k, v)
            for k, v in fake_raw.items()
        }

        canonical = canonicalize_to_m1_keys(normalized, pool)

        if not canonical:
            raise RuntimeError("Canonicalization dropped all features")

        dist = run_M1_on_answers(canonical)

        if not isinstance(dist, dict):
            raise RuntimeError("M1 returned invalid output")

        report["checks"]["m1_flow"] = True
    except Exception as e:
        report["errors"].append(f"M1 flow failed: {e}")
        report["checks"]["m1_flow"] = False

    # 5️⃣ تحقق من سكربتات M2
    m2_status = {}
    for disease, meta in DISEASE_REGISTRY.items():
        path = meta.get("script_path")
        m2_status[disease] = os.path.exists(path)
        if not m2_status[disease]:
            report["errors"].append(f"M2 script missing for {disease}")

    report["checks"]["m2_scripts"] = m2_status

    # النتيجة النهائية
    if report["errors"]:
        report["status"] = "error"

    return report
# ===== خرايط الترميز =====
BOOL_MAP = {
    # عربي
    "نعم": 1, "اي نعم": 1, "آه": 1, "صح": 1,
    "لا": 0, "كلا": 0, "مو": 0, "مش": 0,
    # إنجليزي
    "yes": 1, "y": 1, "true": 1, "1": 1,
    "no": 0, "n": 0, "false": 0, "0": 0,
}

GENDER_MAP = {
    # عربي
    "ذكر": 1, "رجل": 1,
    "أنثى": 0, "امرأة": 0,
    # إنجليزي
    "male": 1, "m": 1,
    "female": 0, "f": 0,
}

SMOKE_MAP = BOOL_MAP

ACTIVE_MAP = {
    # عربي
    "لا تمارين منتظمة": 0,
    "تمارين خفيفة (مثل المشي أحيانًا)": 1,
    # إنجليزي
    "no regular exercise": 0,
    "light exercise (like walking occasionally)": 1,
}

LIFESTYLE_MAP = {
    "مدينة": 1, "بلدة": 2, "قرية": 3,
    "city": 1, "town": 2, "village": 3,
}

CHP_MAP = {
    "يحدث مع المجهود ويزول مع الراحة": 1,
    "ألم صدر غير نمطي: غير معتاد أو مختلف عن الألم الطبيعي": 2,
    "ألم صدر غير مرتبط بالقلب": 3,
    "لا يوجد ألم صدر": 4,
    "happens with exertion and goes away with rest": 1,
    "atypical chest pain: unusual or different from normal chest pain": 2,
    "chest pain not related to the heart": 3,
    "no chest pain": 4,
}

ECGPATT_MAP = {

    "ارتفاع st (احتمال نوبة قلبية)": 1,
    "انخفاض st (احتمال تدفق دم منخفض)": 2,
    "انعكاس t (احتمال إجهاد القلب)": 3,
    "طبيعي": 4,
    "st-elevation (possible heart attack)": 1,
    "st-depression (possible reduced blood flow)": 2,
    "t-inversion (possible heart strain)": 3,
    "normal": 4,
}


# ===== مفاتيح وخرايط الأسئلة =====
AUTO_MAPPING = {
    "fever": "Do you have fever?",
    "cough": "Do you have cough?",
    "fatigue": "Do you feel fatigue?",
    "headache_general": "Do you have frequent headache?",
    "blood_pressure": "Enter Blood Pressure reading:",
    "blood_glucose_level": "Enter Blood Glucose Level (mg/dL):",
    "bmi": "Enter Body Mass Index (BMI):",
    "bpsys": "Enter Systolic Blood Pressure (Sys):",
    "bpdias": "Enter Diastolic Blood Pressure (Dias):",
    "years": "Enter number of years:",
    "chp": "Enter value for chp:",
    "sex": "Select sex:",
    "gender": "Select gender:",
    "lifestyle": "Enter lifestyle code (numeric):",
    "ecgpatt": "Enter ECG pattern code (numeric):",
}

AUTO_MAPPING_AR = {
    "sore throat": "ألم/التهاب حلق",
    "running nose": "رشح/سيلان أنف",
    "diabetes": "سكري",
    "hyper tension": "ارتفاع ضغط",
    "htn": "ارتفاع ضغط",
    "breathing problem": "ضيق نفس",
    "headache general": "صداع ",
    "headache": "صداع",
    "fatigue": "تعب",
    "dry cough": "سعال جاف",
    "fever": "حمّى",
    "fatigue_general": "تعب عام",
    "fever_general": "حمّى",
    "Cough": "سعال",
    "difficulty_breathing": "ضيق التنفس",
    "scaly patches on the skin": "بقع قشرية على الجلد",
    "nausea_or_vomiting": "غثيان أو قيء",
    "swelling": "تورّم",
    "back_pain": "ألم الظهر",
    "fractures": "كسور",
    "jaundice": "يرقان",
    "distinct facial features (small jaw)": "ملامح وجه مميزة (فك صغير)",
    "wheezing": "صفير التنفس",
    "numbness_or_weakness_general": "خدر أو ضعف عام",
    "swelling of the legs or ankles": "تورّم الساقين أو الكاحلين",
    "intellectual disability": "إعاقة ذهنية",
    "chest_pain": "ألم الصدر",
    "increased urination or urine changes": "زيادة التبول أو تغيّرات في البول",
    "headaches": "صداع",
    "developmental delays": "تأخر في النمو",
    "rash_general": "طفح جلدي عام",
    "high blood pressure": "ارتفاع ضغط الدم",
    "decreased appetite": "انخفاض الشهية",
    "difficulty concentrating": "صعوبة التركيز",
    "blood_in_urine_or_stool": "دم في البول أو البراز",
    "slow healing of wounds": "بطء التئام الجروح",
    "facial_numbness_or_weakness": "خدر أو ضعف في الوجه",
    "abnormal_urine": "بول غير طبيعي",
    "heart_rate_issues": "مشكلات في معدل ضربات القلب",
    "painful urination": "تبول مؤلم",
    "redness_general": "احمرار عام",
    "abdominal_pain_general": "ألم بطني عام",
    "pelvic pain": "ألم الحوض",
    "frequent urination": "تكرار التبول",
    "weight_loss_severe_or_unintentional": "فقدان وزن شديد أو غير مقصود",
    "weight_changes_general": "تغيّرات في الوزن",
    "loss of appetite": "فقدان الشهية",
    "jaw or arm pain": "ألم الفك أو الذراع",
    "severe headache": "صداع شديد",
    "heart_rate_irregularities": "عدم انتظام ضربات القلب",
    "sweating": "تعرّق",
    "dizziness": "دوخة",
    "blind_spots": "بقع عمياء في الرؤية",
    "joint_muscle_pain": "ألم المفاصل والعضلات",
    "sleep_or_appetite_changes": "تغيّرات في النوم أو الشهية",
    "post-nasal drip": "سيلان خلفي للأنف",
    "Frequent Nosebleeds": "نزيف أنف متكرر",
    "thick nasal discharge": "إفرازات أنفية كثيفة",
    "bleeding tendencies": "ميل للنزيف",
    "bleeding_and_bruising": "نزيف وكدمات",
    "stiffness": "تيبّس"
}

BINARY_KEYS = [
]

DERIVED_TAGS = [
    "count_positive", "pct_positive",
    "respiratory_idx", "cardiac_idx", "neurology_idx", "ent_idx",
    "derm_idx", "gi_idx", "uro_idx", "heme_idx", "endocrine_idx", "immune_idx"
]

# ===== دوال أساسية للتعامل مع الأسئلة والإجابات =====
def core_start_conversation(payload):
    logger.debug(f"[core_start_conversation] Called with: {payload}")

    result = {
        "sid": "TEST-SID-001",
        "session": {
            "asked_symptoms": []   # 👈 مهم جدًا
        },
        "ask": [
            {
                "name": "initial",
                "type": "text",
                "q": "اذكر لي كل الأعراض التي تشعر بها."
            }
        ]
    }

    logger.debug(f"[core_start_conversation] Returning: {result}")
    return result

def core_handle_answers(payload):
    """
    تدفّق المعالجة محسّن:
    - استقبال إجابات المستخدم
    - تحديث asked_symptoms
    - طرح أسئلة M1 checkbox على دفعات
    - التوقف عند نفاد الأعراض أو الاكتفاء
    - أولوية الأسئلة حسب أهميتها لكل قسم بشكل ديناميكي
    """

    logger.debug(f"[core_handle_answers] Payload: {payload}")

    sid = payload.get("sid", "NO-SID")
    session = payload.get("session", {})

    # جمع كل الإجابات السابقة
    answers_accumulated = session.get("answers_accumulated", {})

    # دمج الإجابات الجديدة من checkbox
    if "answers" in payload:
        new_answers = payload["answers"]
        for k, v in new_answers.items():
            if k not in answers_accumulated:
                answers_accumulated[k] = []
            answers_accumulated[k].extend(v)

    # =========================
    # إدارة asked_symptoms
    # =========================
    asked = set(session.get("asked_symptoms", []))
    for vals in answers_accumulated.values():
        asked.update(vals)

    # =========================
    # تحديث top_department ديناميكيًا حسب الإجابات الحالية
    # =========================
    feature_pool = m1_feature_pool()
    flat_answers = flatten_checkbox_answers(answers_accumulated)
    flat_answers = canonicalize_to_m1_keys(flat_answers, feature_pool)  # ✅
    
    # ✅ حقن 0 لأي عرض عُرض ضمن asked ولم يظهر كموجب في flat_answers
    asked_zero_raw = {a: 0 for a in asked or []}
    asked_zero_canon = canonicalize_to_m1_keys(asked_zero_raw, feature_pool)
    for k in (asked_zero_canon or {}):
        if k not in flat_answers:
            flat_answers[k] = 0

    m1_scores = run_M1_on_answers(flat_answers)
    session["M1_scores"] = m1_scores  # حفظ النتائج

    top_department = None
    if m1_scores:
        top_department = max(m1_scores, key=m1_scores.get)

    # =========================
    # توليد أسئلة M1 حسب الأهمية
    # =========================
    feature_pool = m1_feature_pool()


    question, asked = generate_questions_from_columns(
        columns=feature_pool,
        asked_set=asked,
        batch_size=5,
        m1_scores=session.get("M1_scores"),
        answered_map=session.get("answers", {}),
        add_entire_batch_to_asked=False,
        lang=session.get("lang", "ar")  # ✅ تمرير اللغة من الجلسة
    )

    # =========================
    # تحديث الجلسة
    # =========================
    session["asked_symptoms"] = list(asked)
    session["answers_accumulated"] = answers_accumulated

    # =========================
    # إن وُجد سؤال جديد → اسأله
    # =========================
    if question:
        logger.debug(f"[core_handle_answers] Asking next M1 checkbox batch | top_department={top_department}")
        return {
            "ask": [question],
            "sid": sid,
            "session": session
        }

    # =========================
    # لا توجد أعراض جديدة أو الاكتفاء → تشغيل M2
    # =========================
    logger.debug("[core_handle_answers] No more symptoms or sufficiency reached, final M1 scores")

    m2_result = None
    if top_department:
        disease = DEPT_TO_DISEASE.get(top_department)
        if disease:
            m2_input = flatten_checkbox_answers(answers_accumulated)
            m2_result = run_M2(disease, m2_input)
            logger.debug(f"[core_handle_answers] M2 result for {disease}: {m2_result}")

    return {
        "sid": sid,
        "result": {
            "M1": m1_scores,
            "M2": m2_result  # ✅ الآن M2 تُرجع مباشرة مع النتيجة
        },
        "session": session
    }

# ===== تحميل مفاتيح التدريب من PKL =====
def _load_m1_cols_from_pkl():
    try:
        with open(PKL_PATH, "rb") as f:
            PKL = pickle.load(f)
        effective = PKL.get("effective_feature_cols") or PKL.get("selected_features")
        feature_cols = PKL.get("feature_cols", [])
        base_cols = [c for c in feature_cols if c not in DERIVED_TAGS]
        final_cols = [c for c in (effective or base_cols) if c in base_cols] if effective else base_cols
        print(f"[DEBUG] M1 training features loaded: count={len(final_cols)}")
        return final_cols
    except Exception as e:
        print(f"[ERROR] Unable to read PKL features: {e}")
        return None

def m1_feature_pool():
    if m1_dept and hasattr(m1_dept, "original_feature_cols"):
        cols = list(m1_dept.original_feature_cols)
        if cols:
            print(f"[DEBUG] Predictor training-only features: {len(cols)}")
            return cols
        return _load_m1_cols_from_pkl() or []


# ===== توليد الأسئلة =====
def checkbox_question(col: str):
    label = AUTO_MAPPING.get(col, f"Do you have {col.replace('_',' ')}?")
    return {"name": col, "type": "checkbox", "q": label}

def radio_question(col: str, options):
    label = AUTO_MAPPING.get(col, f"Choose {col.replace('_',' ')}:")
    return {"name": col, "type": "radio", "options": options, "q": label}





def generate_questions_from_columns(
    columns,
    asked_set=None,
    batch_size=5,
    m1_scores=None,
    answered_map=None,
    add_entire_batch_to_asked=False,
    lang: str = "ar"  # ✅ جديد: لغة الإخراج ("ar" أو "en")
):
    """
    يولّد دفعة أعراض (checkbox) حصراً من أعمدة التدريب،
    مع منع التكرار تمامًا:
      - أي عرض سُئل سابقًا لا يُعاد.
      - أي عرض موجود في إجابات المستخدم (0 أو 1) لا يُعاد.

    الأولوية:
      1) الأعراض المهمّة وفق ترتيب الأقسام (m1_scores إن وُجدت، وإلا importance_dict).
      2) بقية الأعمدة غير المُضافة.
    """

    # --- حراسة المدخلات ---
    # asked_set → set
    if asked_set is None:
        asked_set = set()
    elif not isinstance(asked_set, set):
        try:
            asked_set = set(asked_set)
        except Exception:
            asked_set = set()

    columns      = columns or []
    answered_map = answered_map or {}

    # --- تطبيع أسماء الأعمدة وبناء خريطة lower -> original ---
    def norm(s): return (s or "").strip().lower()

    pool_lc      = {norm(c) for c in columns}
    pool_lc_map  = {norm(c): c for c in columns}  # key: lower, val: original

    # asked (مطبّع)
    asked_lc = {norm(a) for a in asked_set}

    # answered_map: احصره في مفاتيح الأعمدة فقط، ثم طبّع
    answered_lc = set()
    if answered_map:
        for k in answered_map.keys():
            kn = norm(k)
            if kn in pool_lc:
                answered_lc.add(kn)

    # أي عنصر في avoid_set لن يُسأل مجددًا
    avoid_set = asked_lc | answered_lc

    remaining_lc = set()
    remaining    = []

    # --- ترتيب الأقسام ---
    # تحصين m1_scores: تجاهل الأقسام غير الموجودة في importance_dict
    if m1_scores:
        sorted_departments = sorted(
            (k for k in m1_scores.keys() if k in importance_dict),
            key=lambda k: m1_scores[k],
            reverse=True
        )
    else:
        sorted_departments = list(importance_dict.keys()) if isinstance(importance_dict, dict) else []

    # (1) ضم الأعراض المهمة حسب الأقسام (إن توفّر importance_dict)
    if sorted_departments and isinstance(importance_dict, dict):
        for dept in sorted_departments:
            feats = importance_dict.get(dept, []) or []
            for f in feats:
                fl = norm(f)
                # ضمن الأعمدة + غير مكررة + غير مسؤولة سابقًا + غير ضمن avoid_set
                if fl in pool_lc and fl not in remaining_lc and fl not in avoid_set:
                    remaining.append(pool_lc_map[fl])  # استخدم المفتاح الأصلي
                    remaining_lc.add(fl)

    # (2) بقية الأعمدة (fallback/استكمال)
    for c in columns:
        cl = norm(c)
        if cl in pool_lc and cl not in remaining_lc and cl not in avoid_set:
            remaining.append(pool_lc_map[cl])
            remaining_lc.add(cl)

    # لا شيء للسؤال
    if not remaining:
        return None, asked_set

    # دفعة (حجم ثابت)
    batch = remaining[:batch_size]

    # إزالة أي ازدواجية داخل الدفعة (وفق lowercase) مع الحفاظ على المفتاح الأصلي
    uniq = {}
    for c in batch:
        key = norm(c)
        if key not in uniq:
            uniq[key] = c  # احتفظ بالأصل


    def _en_text(s: str) -> str:
        # توليد نص إنجليزي بسيط من اسم العمود (fallback)
        return (s or "").replace("_", " ").strip()

    options = []
    for c in uniq.values():
        label_ar = AUTO_MAPPING_AR.get(c, c)
        label_en = _en_text(c)
        options.append({
            "value": c,
            # نضبط label الأساسية حسب اللغة المطلوبة
            "label": label_en if lang == "en" else label_ar,
            # ونحتفظ بالحقول الثنائية للواجهة
            "label_ar": label_ar,
            "label_en": label_en
        })


    q_ar = "اختر كل الأعراض التي تشعر بها من القائمة أدناه:"
    q_en = "Select all the symptoms you are experiencing from the list below:"

    question = {
        "name": "symptoms_m1",
        "type": "checkbox",
        "options": options,
        # نضبط q الأساسية حسب اللغة المطلوبة
        "q": q_en if lang == "en" else q_ar,
        # ونحتفظ بالنصين معًا لاستخدام الواجهة بحرّية
        "q_ar": q_ar,
        "q_en": q_en
    }

    # سياسة الإضافة إلى asked:
    # - إذا كنت تعتبر أن كل خيار ظهر للمستخدم قد "سُئل" (حتى لو لم يحدده) فأبقِها True.
    # - إن أردت احتساب "سؤال" فقط لما اختاره المريض، اجعلها False وأضف المختار عند المعالجة.
    if add_entire_batch_to_asked:
        asked_set.update(uniq.values())

    return question, asked_set




# ===== أسئلة M2 مكتوبة يدويًا باللغة العربية =====
def m2_manual_questions_ar(disease: str):
    """
    أسئلة مكتوبة داخل الكود لكل مرض M2 باللغة العربية
    كل سؤال يحتوي على الاسم، النوع (radio/checkbox/number)، النص، والخيارات إذا موجودة
    """
    if disease == "HeartDisease":

        return [
            {"name": "age", "type": "number", "q": "أدخل عمرك:"},
            {"name": "sex", "type": "radio", "q": "اختر الجنس:", "options": ["ذكر", "أنثى"]},
            {"name": "smoke", "type": "radio", "q": "هل تدخن؟", "options": ["لا", "نعم"]},
            {"name": "years", "type": "number", "q": "كم عدد سنوات التدخين؟"},
            {"name": "chp", "type": "radio", "q": "اختر نوع ألم الصدر:", "options": [
                "يحدث مع المجهود ويزول مع الراحة",
                "ألم صدر غير نمطي: غير معتاد أو مختلف عن الألم الطبيعي",
                "ألم صدر غير مرتبط بالقلب",
                "لا يوجد ألم صدر"
            ]},
            {"name": "height", "type": "number", "q": "أدخل طولك (سم):"},
            {"name": "weight", "type": "number", "q": "أدخل وزنك (كجم):"},
            {"name": "fh", "type": "radio", "q": "هل لديك تاريخ عائلي لأمراض القلب؟", "options": ["لا", "نعم"]},
            {"name": "active", "type": "radio", "q": "هل تمارس نشاطًا بدنيًا؟", "options": [
                "لا تمارين منتظمة",
                "تمارين خفيفة (مثل المشي أحيانًا)"
            ]},
            {"name": "lifestyle", "type": "radio", "q": "اختر نمط حياتك:", "options": ["مدينة", "بلدة", "قرية"]},
            {"name": "ihd", "type": "radio", "q": "هل أجريت قسطرة قلبية أو أي تدخل في القلب؟", "options": ["لا", "نعم"]},
            {"name": "hr", "type": "number", "q": "أدخل معدل ضربات القلب (HR):"},
            {"name": "bpsys", "type": "number", "q": "أدخل ضغط الدم الانقباضي (Sys):"},
            {"name": "bpdias", "type": "number", "q": "أدخل ضغط الدم الانبساطي (Dias):"},
            {"name": "dm", "type": "radio", "q": "هل لديك مرض السكري؟", "options": ["لا", "نعم"]},
            {"name": "htn", "type": "radio", "q": "هل لديك ارتفاع ضغط الدم؟", "options": ["لا", "نعم"]},
            {"name": "ecgpatt", "type": "radio", "q": "اختر نمط تخطيط القلب:", "options": [
                "ارتفاع ST (احتمال نوبة قلبية)",
                "انخفاض ST (احتمال تدفق دم منخفض)",
                "انعكاس T (احتمال إجهاد القلب)",
                "طبيعي"
            ]},     
        ]
        
    
    elif disease == "Diabetes":

        return [
            {"name": "gender", "type": "radio", "q": "اختر الجنس:", "options": ["ذكر", "أنثى"]},
            {"name": "age", "type": "number", "q": "أدخل عمرك:"},
            {"name": "hypertension", "type": "radio", "q": "هل لديك ارتفاع ضغط الدم؟", "options": ["لا", "نعم"]},
            {"name": "heart_disease", "type": "radio", "q": "هل لديك مرض في القلب؟", "options": ["لا", "نعم"]},
            {"name": "bmi", "type": "number", "q": "أدخل مؤشر كتلة الجسم (BMI):"},
            {"name": "blood_glucose_level", "type": "number", "q": "أدخل مستوى سكر الدم (ملغ/ديسيلتر):"},
        ]
        
    return []


# ===== أسئلة M2 مكتوبة يدويًا باللغة الإنجليزية =====
def m2_manual_questions(disease: str):
    """
    أسئلة مكتوبة داخل الكود لكل مرض M2 باللغة الإنجليزية
    كل سؤال يحتوي على الاسم، النوع (radio/checkbox/number)، النص، والخيارات إذا موجودة
    """
    if disease == "HeartDisease":
        return [
            {"name": "age", "type": "number", "q": "Enter your age:"},
            {"name": "sex", "type": "radio", "q": "Select your sex:", "options": ["Male", "Female"]},
            {"name": "smoke", "type": "radio", "q": "Do you smoke?", "options": ["No", "Yes"]},
            {"name": "years", "type": "number", "q": "How many years have you been smoking?"},
            {"name": "chp", "type": "radio", "q": "select your chest pain (chp):", "options": ["Happens with exertion and goes away with rest", "Atypical chest pain: unusual or different from normal chest pain", "Chest pain not related to the heart", "No chest pain"]},
            {"name": "height", "type": "number", "q": "Enter your height (cm):"},
            {"name": "weight", "type": "number", "q": "Enter your weight (kg):"},
            {"name": "fh", "type": "radio", "q": "Do you have a family history of heart disease?", "options": ["No", "Yes"]},
            {"name": "active", "type": "radio", "q": "Are you physically active?", "options": ["No regular exercise", "Light exercise (like walking occasionally)"]},
            {"name": "lifestyle", "type": "radio", "q": "select your lifestyle:", "options": ["City", "Town", "Village"]},
            {"name": "ihd", "type": "radio", "q": "Do you have any cardiac catheterization or any intervention into the heart?", "options": ["No", "Yes"]},
            {"name": "hr", "type": "number", "q": "Enter your Heart Rate (HR):"},
            {"name": "bpsys", "type": "number", "q": "Enter your Systolic Blood Pressure (Sys):"},
            {"name": "bpdias", "type": "number", "q": "Enter your Diastolic Blood Pressure (Dias):"},
            {"name": "dm", "type": "radio", "q": "Do you have Diabetes?", "options": ["No", "Yes"]},
            {"name": "htn", "type": "radio", "q": "Do you have Hypertension?", "options": ["No", "Yes"]},
            {"name": "ecgpatt", "type": "radio", "q": "select your lifestyle:", "options": ["ST-Elevation (Possible heart attack)", "ST-Depression (Possible reduced blood flow)", "T-Inversion (Possible heart strain)", "Normal"]},

        ]
    elif disease == "Diabetes":
        return [
            {"name": "gender", "type": "radio", "q": "Select your gender:", "options": ["Male", "Female"]},
            {"name": "age", "type": "number", "q": "Enter your age:"},
            {"name": "hypertension", "type": "radio", "q": "Do you have Hypertension?", "options": ["No", "Yes"]},
            {"name": "heart_disease", "type": "radio", "q": "Do you have Heart Disease?", "options": ["No", "Yes"]},
            {"name": "bmi", "type": "number", "q": "Enter Body Mass Index (BMI):"},
            {"name": "blood_glucose_level", "type": "number", "q": "Enter Blood Glucose Level (mg/dL):"},
        ]
    return []



def generate_questions(model_name, feature_pool=None, disease_required=None, lang="ar", limit=5):
    """
    واجهة توليد أسئلة عامة:
    - M1: يعيد قائمة بأسئلة checkbox (سؤال واحد ضمن قائمة) حسب اللغة.
    - M2: يعيد أسئلة المرض المطلوبة حسب اللغة.
    """
    # ✅ M1 → نرجّع قائمة أسئلة جاهزة
    if model_name == "M1" and feature_pool:
        q, _asked = generate_questions_from_columns(
            columns=feature_pool,
            asked_set=set(),
            batch_size=limit,
            lang=lang  # ✅ تمرير اللغة
        )
        # لف السؤال في قائمة كما تتوقع الواجهة
        return [q] if q else []

    # ✅ M2 → جمع الأسئلة اليدوية حسب الأمراض المطلوبة
    if model_name == "M2" and disease_required:
        questions = []
        for disease in disease_required:
            if lang == "ar":
                questions.extend(m2_manual_questions_ar(disease))
            else:
                questions.extend(m2_manual_questions(disease))
        return questions

    return []

    if model_name == "M2" and disease_required:
        questions = []
        for disease in disease_required:
            if lang == "ar":
                questions.extend(m2_manual_questions_ar(disease))
            else:
                questions.extend(m2_manual_questions(disease))
        return questions

    return []
# ===== تطبيع القيم =====
def normalize_value_for_key(key: str, v):
    """
    Normalize user input values for M1 / M2 models
    Supports Arabic & English categorical mappings
    """

    if v is None:
        return 0

    # =========================
    # أرقام تمر مباشرة
    # =========================
    if isinstance(v, (int, float)):
        return v

    if not isinstance(v, str):
        return v

    s = v.strip().lower()

    # =========================
    # نعم / لا (عام)
    # =========================
    if s in BOOL_MAP:
        return BOOL_MAP[s]

    # =========================
    # ترميز حسب المفتاح (M2)
    # =========================
    if key in {"sex", "gender"}:
        return GENDER_MAP.get(s, 0)

    if key in {"smoke", "dm", "htn", "ihd", "fh", "hypertension", "heart_disease"}:
        return BOOL_MAP.get(s, 0)

    if key == "active":
        return ACTIVE_MAP.get(s, 0)

    if key == "lifestyle":
        return LIFESTYLE_MAP.get(s, 0)

    if key == "chp":
        return CHP_MAP.get(s, 0)

    if key == "ecgpatt":
        return ECGPATT_MAP.get(s, 0)

    # =========================
    # أرقام كنص
    # =========================
    try:
        return float(s)
    except ValueError:
        return v
# ===== خرائط الأمراض ومفاتيحها =====
DISEASE_REGISTRY = {
    "Diabetes": {
        "script_path": os.getenv("DIAB_SCRIPT", os.path.join(BASE_DIR, "m2", "diabetes_prediction", "predict.py")),
        "required_keys": ["gender", "age", "hypertension", "heart_disease", "bmi", "blood_glucose_level"]
    },
    "HeartDisease": {
        "script_path": os.getenv("HEART_SCRIPT", os.path.join(BASE_DIR, "m2", "heart_disease2", "predict.py")),
        "required_keys": [
            "age", "sex", "smoke", "years", "chp", "height", "weight", "fh", "active",
            "lifestyle", "ihd", "hr", "dm", "bpsys", "bpdias", "htn", "ecgpatt"
        ]
    }
}
DEPT_TO_DISEASE = {
    "Cardiology": "HeartDisease",
    "Oncology": "Diabetes",
    "ENT": "Diabetes",
    "Dermatology": "Diabetes",
    "Gastroenterology": "Diabetes",
    "Neurology": "Diabetes",
    "Urology/Nephrology": "Diabetes",
    "Hematology": "Diabetes",
    "Immunology": "Diabetes",
    "pediatrics": "Diabetes",
    "Gentics Disorders": "Diabetes",
    "Infectious Diseases": "Diabetes",
    "General Medicine": "Diabetes",
    "Internal Medicine": "Diabetes",
    "Orthopedics": "Diabetes",
    "Pediatrics": "Diabetes",
    "Psychiatry": "Diabetes",
    "Surgery": "Diabetes",
    "Therapy": "Diabetes",
    "Respiratory": "Diabetes",
    "Uncategorized": "Diabetes"

}
def get_required_keys_for_disease(disease: str):
    return DISEASE_REGISTRY.get(disease, {}).get("required_keys", [])

def flatten_checkbox_answers(answers_accumulated: dict):
    """
    يحوّل:
    {"symptoms_m1": ["fever", "cough"]}
    إلى:
    {"fever": 1, "cough": 1}
    """
    flat = {}

    for _, values in (answers_accumulated or {}).items():
        if isinstance(values, list):
            for v in values:
                flat[v] = 1

    return flat

# ===== تشغيل M1 =====
def run_M1_on_answers(answers: dict):

    if m1_dept is None:
        logger.warning("run_M1_on_answers: M1 predictor not initialized.")
        return {}
    try:
        # استخدام safe_run لضمان تسجيل أي استثناء
        result = safe_run(m1_dept.predict_dict, answers or {}, top=5)
        return {k: float(v) for k, v in result.items() if isinstance(v, (int, float))}

    except Exception as e:
        logger.error(f"run_M1_on_answers failed: {e}")
        return {}

# ===== تشغيل M2 =====
def run_M2(disease: str, data: dict):

    selected = DISEASE_REGISTRY.get(disease, {})
    data = dict(data or {})

    # صفّر القيم الفارغة وحوّل كل المطلوب إلى أعداد
    for k in selected.get("required_keys", []):
        v = data.get(k, 0)
        if v in (None, "", " ", "null"):
            v = 0
        v = normalize_value_for_key(k, v)
        if isinstance(v, str):
            try:
                v = float(v.strip())
            except Exception:
                v = 0
        data[k] = v

    # ملء القيم الافتراضية للمفاتيح المطلوبة
    for k in selected.get("required_keys", []):
        data.setdefault(k, 0)

    if os.getenv("DEBUG_M2_IO", "0") == "1":
        logger.info(f"[M2 INPUT::{disease}] {json.dumps(data, ensure_ascii=False)}")

    try:
        script_path = selected.get("script_path")
        if not script_path or not os.path.exists(script_path):
            logger.error(f"M2 script not found for {disease}")
            return None

        proc = subprocess.run(
            [sys.executable, script_path],
            input=json.dumps(data).encode("utf-8"),
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            check=False
        )
        stdout = (proc.stdout or b"").decode("utf-8", errors="replace").strip()
        stderr = (proc.stderr or b"").decode("utf-8", errors="replace").strip()

        if proc.returncode != 0:
            logger.error(f"M2 script returned non-zero exit code {proc.returncode}")
            logger.error(f"[STDERR]\n{stderr}")
            logger.error(f"[STDOUT]\n{stdout}")
            return None

        if not stdout:
            logger.error(f"M2 script returned empty stdout for '{disease}'")
            logger.error(f"[STDERR]\n{stderr}")
            return None

        # محاولة تحويل stdout إلى رقم
        try:
            score = float(stdout.strip())
        except ValueError:
            # fallback: البحث عن أي رقم في stdout
            matches = re.findall(r"[\-\+]?\d*\.?\d+", stdout)
            if matches:
                score = float(matches[0])
                logger.warning(f"Parsed numeric value from non-strict output: {matches[0]}")
            else:
                logger.error(f"Unable to parse numeric score from M2 stdout: '{stdout}'")
                logger.error(f"[STDERR]\n{stderr}")
                return None

        return max(0.0, min(1.0, score))
    except Exception as e:
        logger.error(f"Exception while running M2 for '{disease}': {e}", exc_info=True)
        return None



def m1_is_sufficient(
    answers: dict,
    max_questions: int = 25,   # حدّك الأقصى لأسئلة الأعراض
    min_positive: int = 3,      # الحد الأدنى للإيجابيات
    min_questions: int = 10,    # الحد الأدنى لعدد الأسئلة المُجاب عنها فعليًا
    margin_delta: float = 0.05    # هامش الفارق بين أعلى وثاني قسم
):
    """
    منطق إيقاف M1:
    - يحسب الاكتفاء بناءً على الأعراض التي تمت الإجابة عنها فعليًا (0 أو >0).
    - 0 تُحسب كسؤال مُجاب لكنها ليست إشارة إيجابية.
    - يعتمد القرار على عدد الإيجابيات، وعدد الأسئلة، وهامش التوزيع.
    """

    if not isinstance(answers, dict):
        return False, "invalid answers"

    # نعتبر «مُشاهَداً» كل مفتاح له قيمة ليست None (سواء 0 أو >0)
    observed_items = {k: v for k, v in answers.items() if v is not None}

    # احسب عدد الأسئلة المُجاب عنها فعليًا
    total_questions = len(observed_items)

    # احسب عدد الإيجابيات (قيم > 0 فقط)
    def _to_num(v):
        s = str(v).strip().lower()
        if s in ("نعم", "yes", "true", "1"):
            return 1.0
        if s in ("لا", "no", "false", "0"):
            return 0.0
        try:
            return float(s)
        except Exception:
            # قيم نصية غير رقمية تُعتبر غير إيجابية
            return 0.0

    positive_count = sum(1 for v in observed_items.values() if _to_num(v) > 0.0)

    # شروط الحد الأدنى
    if positive_count < min_positive:
        return False, "not enough positive signals"
    if total_questions < min_questions:
        return False, "not enough questions"

    # توزيع الأقسام على المُشاهَد فقط (اختياري إن أردت الهامش)
    dist = run_M1_on_answers(observed_items) or {}
    scores = sorted(
        [v for v in dist.values() if isinstance(v, (int, float))],
        reverse=True
    )
    if len(scores) < 2:
        return False, "insufficient score distribution"

    top1, top2 = scores[0], scores[1]

    # قرار الهامش
    if (top1 - top2) >= margin_delta:
        return True, "clear top-vs-second margin"

    # حد أقصى للأسئلة (يُحسب على الأسئلة الفعلية، وليس طول الـ pool)
    if total_questions >= max_questions:
        return True, "max questions reached"

    return False, "need more information"

##############
def canonicalize_to_m1_keys(extracted: dict, m1_keys: list):
    key_map = {k.lower(): k for k in (m1_keys or [])}

    out = {}
    for k, v in (extracted or {}).items():
        kl = k.lower()
        if kl in key_map:
            out[key_map[kl]] = v
    return out

# ===== أسئلة متابعات M1 بدون تحيّز مع نص عربي كامل =====
import random




def m1_followups(answers, top_dept=None, limit=5, lang="ar", m1_scores=None):
    """
    Wrapper فقط:
    يعيد أسئلة الشيك بوكس من المصدر الوحيد generate_questions_from_columns
    مع تمرير اللغة و(اختياريًا) درجات M1 لتحسين ترتيب الأعراض.
    """
    asked = set((answers or {}).keys())
    pool = m1_feature_pool() or []

    q_list, _ = generate_questions_from_columns(
        columns=pool,
        asked_set=asked,
        batch_size=limit,
        m1_scores=m1_scores,      # ✅ لو موجودة أفضل تمريرها
        lang=lang                  # ✅ تمرير اللغة
    )
    # نعيد دائمًا قائمة (list) متوافقة مع الواجهات العليا
    return q_list if isinstance(q_list, list) else ([q_list] if q_list else [])


# ===== Guards =====
REQUIRED_FUNCS = ["m1_followups", "run_M1_on_answers", "run_M2"]
for fn in REQUIRED_FUNCS:
    if fn not in globals():
        raise RuntimeError(f"Missing required function: {fn}")

