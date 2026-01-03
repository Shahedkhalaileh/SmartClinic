<?php
// Read M1 symptoms from dataset
function getM1Symptoms() {
    $file = '../chat/m1/effective_m1_keys.txt';
    if (!file_exists($file)) {
        return [];
    }
    
    $symptoms = [];
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $symptom = trim($line);
        if (!empty($symptom)) {
            $symptoms[] = $symptom;
        }
    }
    return $symptoms;
}

// Map symptom names to user-friendly questions
function getSymptomQuestion($symptom) {
    $mapping = [
        'fatigue_general' => 'Do you feel fatigue?',
        'fever_general' => 'Do you have fever?',
        'Cough' => 'Do you have cough?',
        'difficulty_breathing' => 'Do you have difficulty breathing?',
        'scaly patches on the skin' => 'Do you have scaly patches on the skin?',
        'nausea_or_vomiting' => 'Do you have nausea or vomiting?',
        'swelling' => 'Do you have swelling?',
        'back_pain' => 'Do you have back pain?',
        'fractures' => 'Do you have fractures?',
        'jaundice' => 'Do you have jaundice?',
        'distinct facial features (small jaw)' => 'Do you have distinct facial features (small jaw)?',
        'wheezing' => 'Do you have wheezing?',
        'numbness_or_weakness_general' => 'Do you have numbness or weakness?',
        'swelling of the legs or ankles' => 'Do you have swelling of the legs or ankles?',
        'intellectual disability' => 'Do you have intellectual disability?',
        'chest_pain' => 'Do you have chest pain?',
        'increased urination or urine changes' => 'Do you have increased urination or urine changes?',
        'headaches' => 'Do you have headaches?',
        'developmental delays' => 'Do you have developmental delays?',
        'rash_general' => 'Do you have rash?',
        'high blood pressure' => 'Do you have high blood pressure?',
        'decreased appetite' => 'Do you have decreased appetite?',
        'difficulty concentrating' => 'Do you have difficulty concentrating?',
        'blood_in_urine_or_stool' => 'Do you have blood in urine or stool?',
        'slow healing of wounds' => 'Do you have slow healing of wounds?',
        'facial_numbness_or_weakness' => 'Do you have facial numbness or weakness?',
        'abnormal_urine' => 'Do you have abnormal urine?',
        'heart_rate_issues' => 'Do you have heart rate issues?',
        'painful urination' => 'Do you have painful urination?',
        'redness_general' => 'Do you have redness?',
        'abdominal_pain_general' => 'Do you have abdominal pain?',
        'pelvic pain' => 'Do you have pelvic pain?',
        'frequent urination' => 'Do you have frequent urination?',
        'weight_loss_severe_or_unintentional' => 'Do you have weight loss (severe or unintentional)?',
        'weight_changes_general' => 'Do you have weight changes?',
        'loss of appetite' => 'Do you have loss of appetite?',
        'jaw or arm pain' => 'Do you have jaw or arm pain?',
        'severe headache' => 'Do you have severe headache?',
        'heart_rate_irregularities' => 'Do you have heart rate irregularities?',
        'sweating' => 'Do you have sweating?',
        'dizziness' => 'Do you have dizziness?',
        'blind_spots' => 'Do you have blind spots?',
        'joint_muscle_pain' => 'Do you have joint or muscle pain?',
        'sleep_or_appetite_changes' => 'Do you have sleep or appetite changes?',
        'post-nasal drip' => 'Do you have post-nasal drip?',
        'Frequent Nosebleeds' => 'Do you have frequent nosebleeds?',
        'thick nasal discharge' => 'Do you have thick nasal discharge?',
        'bleeding tendencies' => 'Do you have bleeding tendencies?',
        'bleeding_and_bruising' => 'Do you have bleeding and bruising?',
        'stiffness' => 'Do you have stiffness?',
    ];
    
    // Return mapped question or generate from symptom name
    if (isset($mapping[$symptom])) {
        return $mapping[$symptom];
    }
    
    // Generate question from symptom name
    $question = str_replace('_', ' ', $symptom);
    $question = ucfirst(strtolower($question));
    return "Do you have {$question}?";
}

// Get M2 questions for specific disease
function getM2Questions($disease) {
    $questions = [];
    
    switch ($disease) {
        case 'Diabetes':
            $questions = [
                ['name' => 'gender', 'type' => 'radio', 'options' => ['Male', 'Female'], 'q' => 'Select gender:'],
                ['name' => 'age', 'type' => 'number', 'q' => 'Enter your age:'],
                ['name' => 'hypertension', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have hypertension?'],
                ['name' => 'heart_disease', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have heart disease?'],
                ['name' => 'bmi', 'type' => 'number', 'q' => 'Enter your Body Mass Index (BMI):'],
                ['name' => 'blood_glucose_level', 'type' => 'number', 'q' => 'Enter Blood Glucose Level (mg/dL):'],
            ];
            break;
            
        case 'HeartDisease':
            $questions = [
                ['name' => 'age', 'type' => 'number', 'q' => 'Enter your age:'],
                ['name' => 'sex', 'type' => 'radio', 'options' => ['Male', 'Female'], 'q' => 'Select sex:'],
                ['name' => 'smoke', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you smoke?'],
                ['name' => 'years', 'type' => 'number', 'q' => 'Enter number of years:'],
                ['name' => 'chp', 'type' => 'number', 'q' => 'Enter value for chp:'],
                ['name' => 'height', 'type' => 'number', 'q' => 'Enter your height (cm):'],
                ['name' => 'weight', 'type' => 'number', 'q' => 'Enter your weight (kg):'],
                ['name' => 'fh', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have family history?'],
                ['name' => 'active', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Are you physically active?'],
                ['name' => 'lifestyle', 'type' => 'number', 'q' => 'Enter lifestyle code (numeric):'],
                ['name' => 'ihd', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have IHD?'],
                ['name' => 'hr', 'type' => 'number', 'q' => 'Enter heart rate:'],
                ['name' => 'dm', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have diabetes mellitus?'],
                ['name' => 'bpsys', 'type' => 'number', 'q' => 'Enter Systolic Blood Pressure (Sys):'],
                ['name' => 'bpdias', 'type' => 'number', 'q' => 'Enter Diastolic Blood Pressure (Dias):'],
                ['name' => 'htn', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have hypertension?'],
                ['name' => 'ecgpatt', 'type' => 'number', 'q' => 'Enter ECG pattern code (numeric):'],
            ];
            break;
            
        case 'COVID-19':
            $questions = [
                ['name' => 'breathing_problem', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have breathing problem?'],
                ['name' => 'fever', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have fever?'],
                ['name' => 'dry_cough', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have dry cough?'],
                ['name' => 'sore_throat', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have sore throat?'],
                ['name' => 'running_nose', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have running nose?'],
                ['name' => 'asthma', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have asthma?'],
                ['name' => 'chronic_lung_disease', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have chronic lung disease?'],
                ['name' => 'headache', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have headache?'],
                ['name' => 'heart_disease', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have heart disease?'],
                ['name' => 'diabetes', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have diabetes?'],
                ['name' => 'hyper_tension', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have hyper tension?'],
                ['name' => 'fatigue', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you feel fatigue?'],
                ['name' => 'gastrointestinal', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you have gastrointestinal issues?'],
                ['name' => 'abroad_travel', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Did you travel abroad?'],
                ['name' => 'contact_with_covid_patient', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Did you have contact with COVID patient?'],
                ['name' => 'attended_large_gathering', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Did you attend large gathering?'],
                ['name' => 'visited_public_exposed_places', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Did you visit public exposed places?'],
                ['name' => 'family_working_public_places', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Does your family work in public places?'],
                ['name' => 'wearing_masks', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you wear masks?'],
                ['name' => 'sanitization_from_market', 'type' => 'radio', 'options' => ['Yes', 'No'], 'q' => 'Do you sanitize from market?'],
            ];
            break;
    }
    
    return $questions;
}

// Return M1 symptoms as JSON for API
if (isset($_GET['action']) && $_GET['action'] === 'get_m1_symptoms') {
    header('Content-Type: application/json');
    $symptoms = getM1Symptoms();
    $questions = [];
    foreach ($symptoms as $symptom) {
        $questions[] = [
            'name' => $symptom,
            'type' => 'checkbox',
            'q' => getSymptomQuestion($symptom)
        ];
    }
    echo json_encode(['questions' => $questions]);
    exit();
}

// Return M2 questions for disease
if (isset($_GET['action']) && $_GET['action'] === 'get_m2_questions' && isset($_GET['disease'])) {
    header('Content-Type: application/json');
    $disease = $_GET['disease'];
    $questions = getM2Questions($disease);
    echo json_encode(['questions' => $questions]);
    exit();
}
?>


