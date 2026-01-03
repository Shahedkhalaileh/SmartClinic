<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Session-Id');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

// Check if user is logged in
if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? '';

// Python Flask API URL - النظام في proj/controller.py
$python_api_base = 'http://localhost:5000';

// دالة للتواصل مع API
function callPythonAPI($endpoint, $data = [], $method = 'POST', $session_id = null) {
    global $python_api_base;
    
    $url = $python_api_base . $endpoint;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, $method === 'POST');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Always send JSON body for POST requests, even if empty
    if ($method === 'POST') {
        // Ensure we send an object {} not an array []
        // Convert empty array to empty object
        if (empty($data)) {
            $json_data = '{}';
        } else {
            $json_data = json_encode($data);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    }
    
    $headers = ['Content-Type: application/json'];
    if ($session_id) {
        $headers[] = 'X-Session-Id: ' . $session_id;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // فحص الاتصال
    if ($http_code === 0 || $error) {
        return [
            'error' => 'Cannot connect to Flask server. Please make sure it is running on ' . $python_api_base,
            'http_code' => 0,
            'curl_error' => $error
        ];
    }
    
    if ($http_code !== 200) {
        // محاولة فك تشفير JSON للخطأ
        $error_data = json_decode($response, true);
        $error_message = 'API returned status code: ' . $http_code;
        if ($error_data && isset($error_data['error'])) {
            $error_message .= ' - ' . $error_data['error'];
        } elseif ($response) {
            $error_message .= ' - Response: ' . substr($response, 0, 200);
        }
        return ['error' => $error_message, 'http_code' => $http_code, 'response' => $response];
    }
    
    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Invalid JSON response: ' . substr($response, 0, 200), 'response' => $response];
    }
    
    return $decoded;
}

// إدارة Session ID
if (!isset($_SESSION['chatbot_sid'])) {
    $_SESSION['chatbot_sid'] = null;
}

// Reset session
if ($action === 'reset') {
    $session_id = $_SESSION['chatbot_sid'];
    $result = callPythonAPI('/api/reset_sid', [], 'POST', $session_id);
    $_SESSION['chatbot_sid'] = null;
    echo json_encode($result ?: ['ok' => true]);
    exit();
}

// Save diagnosis to database
if ($action === 'save_diagnosis') {
    include("../connection.php");
    
    if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    
    $useremail = $_SESSION["user"];
    $userrow = $database->query("SELECT pid FROM patient WHERE pemail='$useremail'");
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["pid"];
    
    $input_data = json_decode(file_get_contents('php://input'), true);
    if ($input_data === null) {
        $input_data = [];
    }
    
    $disease = mysqli_real_escape_string($database, $input_data['disease'] ?? '');
    $percentage = floatval($input_data['percentage'] ?? 0);
    $department_id = intval($input_data['department_id'] ?? 0);
    $department_name = mysqli_real_escape_string($database, $input_data['department_name'] ?? '');
    $nlg_title = mysqli_real_escape_string($database, $input_data['nlg_title'] ?? '');
    $nlg_summary = mysqli_real_escape_string($database, $input_data['nlg_summary'] ?? '');
    
    // إنشاء جدول chatbot_diagnosis إذا لم يكن موجوداً
    $create_table = "CREATE TABLE IF NOT EXISTS chatbot_diagnosis (
        id INT(11) NOT NULL AUTO_INCREMENT,
        pid INT(11) NOT NULL,
        disease VARCHAR(255) DEFAULT NULL,
        percentage DECIMAL(5,2) DEFAULT 0,
        department_id INT(11) DEFAULT NULL,
        department_name VARCHAR(255) DEFAULT NULL,
        nlg_title TEXT DEFAULT NULL,
        nlg_summary TEXT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY pid (pid)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $database->query($create_table);
    
    // حفظ النتيجة
    $stmt = $database->prepare("INSERT INTO chatbot_diagnosis (pid, disease, percentage, department_id, department_name, nlg_title, nlg_summary) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdiss", $userid, $disease, $percentage, $department_id, $department_name, $nlg_title, $nlg_summary);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Diagnosis saved successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => $database->error]);
    }
    $stmt->close();
    exit();
}

// Get request data
$input_data = json_decode(file_get_contents('php://input'), true);
if ($input_data === null) {
    $input_data = [];
}
$user_answers = $input_data['answers'] ?? [];
$message = $input_data['message'] ?? '';
$intent = $input_data['intent'] ?? '';
$session_id = $_SESSION['chatbot_sid'] ?? null;

// تحديد أي API نستخدم: chat_m1 للنص الحر أو بدء الجلسة، controller للـ checkbox
// إذا كان هناك answers فقط (بدون message أو intent) -> controller
// وإلا -> chat_m1
$has_answers_only = !empty($user_answers) && empty($message) && empty($intent);
$endpoint = $has_answers_only ? '/api/controller' : '/api/chat_m1';

// بناء البيانات للطلب - دائماً أرسل object حتى لو كان فارغاً
$request_data = [];
if (!empty($user_answers)) {
    $request_data['answers'] = $user_answers;
}
if (!empty($message)) {
    $request_data['message'] = $message;
}
if (!empty($intent)) {
    $request_data['intent'] = $intent;
}

// Call Python API
$result = callPythonAPI($endpoint, $request_data, 'POST', $session_id);

// حفظ Session ID من الاستجابة
if (isset($result['sid'])) {
    $_SESSION['chatbot_sid'] = $result['sid'];
}

// دالة للحصول على ID القسم من قاعدة البيانات
function getDepartmentId($dept_name) {
    include("../connection.php");
    
    // خريطة أسماء الأقسام من النظام إلى قاعدة البيانات
    $dept_mapping = [
        'Internal Medicine' => 'Internal medicine',
        'Cardiology' => 'Cardiology',
        'Gastroenterology' => 'Gastroenterology',
        'Dermatology' => 'Dermatology',
        'ENT' => 'Otorhinolaryngology',
        'Neurology' => 'Neurology',
        'Psychiatry' => 'Psychiatry',
        'Oncology' => 'Oncology',
        'Respiratory' => 'Respiratory',
        'Infectious Diseases' => 'Infectious Diseases',
        'Genetic Disorders' => 'Genetic Disorders',
        'Gentics Disorders' => 'Genetic Disorders',
        'Urology/Nephrology' => 'Nephrology',
        'Urology' => 'Urology',
        'Orthopedics' => 'Orthopaedics',
        'Pediatrics' => 'Pediatrics',
        'pediatrics' => 'Pediatrics',
        'General Medicine' => 'General Medicine',
        'Surgery' => 'Surgery',
        'Hematology' => 'Hematology',
        'Immunology' => 'Immunology',
    ];
    
    $db_dept_name = $dept_mapping[$dept_name] ?? $dept_name;
    
    // البحث في قاعدة البيانات
    $stmt = $database->prepare("SELECT id, sname FROM specialties WHERE sname = ? OR sname LIKE ? LIMIT 1");
    $search_term = "%" . $db_dept_name . "%";
    $stmt->bind_param("ss", $db_dept_name, $search_term);
    $stmt->execute();
    $spec_result = $stmt->get_result();
    
    if ($spec_result && $spec_result->num_rows > 0) {
        $spec_row = $spec_result->fetch_assoc();
        return ['id' => $spec_row['id'], 'name' => $spec_row['sname']];
    }
    
    // بحث غير حساس لحالة الأحرف
    $query = "SELECT id, sname FROM specialties WHERE LOWER(sname) LIKE LOWER(?) LIMIT 1";
    $stmt2 = $database->prepare($query);
    $search_term2 = "%" . $db_dept_name . "%";
    $stmt2->bind_param("s", $search_term2);
    $stmt2->execute();
    $spec_result2 = $stmt2->get_result();
    
    if ($spec_result2 && $spec_result2->num_rows > 0) {
        $spec_row2 = $spec_result2->fetch_assoc();
        return ['id' => $spec_row2['id'], 'name' => $spec_row2['sname']];
    }
    
    return null;
}

// خريطة الأمراض إلى الأقسام المقترحة
function getDepartmentFromDisease($disease) {
    $disease_to_dept = [
        'HeartDisease' => 'Cardiology',
        'Diabetes' => 'Internal Medicine',
        'COVID-19' => 'Infectious Diseases',
        'Osteoporosis' => 'Orthopedics',
    ];
    return $disease_to_dept[$disease] ?? 'General Medicine';
}

// إذا كان هناك قسم موصى به (من M1)
if (isset($result['top_department']) && !empty($result['top_department'])) {
    $dept_info = getDepartmentId($result['top_department']);
    if ($dept_info) {
        $result['department_id'] = $dept_info['id'];
        $result['department_name_db'] = $dept_info['name'];
    }
}

// إذا كان هناك تشخيص نهائي (من M2)، اقترح القسم المناسب
if (isset($result['final_diagnosis']) && isset($result['final_diagnosis']['disease'])) {
    $disease = $result['final_diagnosis']['disease'];
    $recommended_dept = getDepartmentFromDisease($disease);
    
    if (!isset($result['top_department'])) {
        $result['top_department'] = $recommended_dept;
    }
    
    $dept_info = getDepartmentId($recommended_dept);
    if ($dept_info) {
        $result['department_id'] = $dept_info['id'];
        $result['department_name_db'] = $dept_info['name'];
        $result['recommended_department'] = $recommended_dept;
    }
}

echo json_encode($result);
?>

