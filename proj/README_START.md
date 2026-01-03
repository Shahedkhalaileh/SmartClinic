# كيفية تشغيل الشات بوت

## الطريقة السريعة (Windows):
1. افتح ملف `START_SERVER.bat` بالنقر المزدوج
2. انتظر حتى يظهر "Running on http://localhost:5000"
3. افتح الموقع في المتصفح

## الطريقة اليدوية:

### 1. افتح Terminal/Command Prompt
### 2. اذهب إلى مجلد proj:
```bash
cd C:\xampp\htdocs\SmartClinic\proj
```

### 3. تأكد من تثبيت المكتبات المطلوبة:
```bash
pip install -r requirements.txt
```

### 4. شغّل Flask Server:
```bash
python controller.py
```

### 5. يجب أن ترى:
```
 * Running on http://127.0.0.1:5000
```

## ملاحظات مهمة:
- يجب أن يكون Flask Server شغال قبل فتح صفحة chatbot.php
- Server يعمل على `http://localhost:5000`
- لا تغلق Terminal أثناء استخدام الشات بوت
- لإيقاف Server: اضغط `Ctrl+C` في Terminal

## استكشاف الأخطاء:
- إذا ظهر خطأ "ModuleNotFoundError": شغّل `pip install -r requirements.txt`
- إذا ظهر خطأ "Port 5000 already in use": أغلق البرنامج الذي يستخدم المنفذ 5000
- تأكد من أن Python مثبت (Python 3.8 أو أحدث)











