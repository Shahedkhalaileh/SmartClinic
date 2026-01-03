<?php
session_start();

if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" or $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit();
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

include("../connection.php");
include("../translations.php");
$userrow = $database->query("select * from patient where pemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];
?>
<!DOCTYPE html>
<html lang="<?php echo getLang(); ?>" dir="<?php echo isArabic() ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/language.css">
    <link rel="stylesheet" href="../css/patient/common.css">
    <title><?php echo t('chat_bot'); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(277deg, #e4e4e9ff 0%, #171677ff 50%, #0f0966ff 100%);
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            display: flex;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        .menu {
            width: 280px;
            min-width: 280px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.08);
            border-right: 1px solid rgba(10, 118, 216, 0.1);
            padding: 20px 0;
            height: 100vh;
            overflow-y: auto;
            flex-shrink: 0;
        }

        .menu-container {
            width: 100%;
            border-collapse: collapse;
        }

        .menu-row {
            margin: 5px 0;
        }

        .menu-btn {
            padding: 12px 20px;
            transition: all 0.3s ease;
            border-radius: 12px;
            margin: 5px 10px;
            background-position: 20px 50%;
            background-repeat: no-repeat;
            cursor: pointer;
        }

        .menu-btn:hover {
            background-color: rgba(10, 118, 216, 0.15);
            transform: translateX(5px);
        }

        .menu-text {
            padding-left: 50px;
            font-weight: 600;
            font-size: 16px;
            color: #444;
            text-align: left;
        }

        .menu-btn:hover .menu-text {
            color: #0A76D8;
        }

        .non-style-link-menu {
            text-decoration: none;
            color: inherit;
        }

        .menu-active {
            background: linear-gradient(135deg, #0A76D8 0%, #171677ff 100%);
            color: white;
        }

        .menu-active .menu-text {
            color: white;
        }

        .profile-container {
            background: rgba(10, 118, 216, 0.1);
            border-radius: 15px;
            padding: 15px;
            margin: 10px;
        }

        .profile-title {
            font-weight: 700;
            font-size: 16px;
            color: #333;
        }

        .profile-subtitle {
            font-size: 12px;
            color: #666;
        }

        .logout-btn {
            width: 100%;
            padding: 10px;
            background: #0A76D8;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .dash-body {
            flex: 1;
            margin: 0;
            padding: 0;
            overflow: hidden;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .chatbot-wrapper {
            max-width: 100%;
            margin: 0;
            background: white;
            border-radius: 0;
            box-shadow: none;
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
        }

        .chatbot-header {
            background: linear-gradient(135deg, #0A76D8 0%, #171677ff 100%);
            backdrop-filter: blur(15px);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
            margin: 0;
            height: auto;
            border-bottom: 1px solid rgba(10, 118, 216, 0.1);
        }

        .chatbot-header h1 {
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chatbot-header .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn-header {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-header:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .chat-messages-container {
            padding: 0;
            background: transparent;
            flex-shrink: 0;
            min-height: 0;
            display: none;
            order: 3;
        }

        .chat-messages-container:not(:empty) {
            display: block;
            padding: 20px;
            background: #f8f9fa;
            min-height: 200px;
        }


        .message {
            display: flex;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.user {
            justify-content: flex-end;
        }

        .message.bot {
            justify-content: flex-start;
        }

        .message-bubble {
            max-width: 70%;
            padding: 12px 18px;
            border-radius: 18px;
            word-wrap: break-word;
            line-height: 1.5;
        }

        .message.user .message-bubble {
            background: linear-gradient(135deg, #0A76D8 0%, #171677ff 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message.bot .message-bubble {
            background: white;
            color: #333;
            border: 1px solid #e0e0e0;
            border-bottom-left-radius: 4px;
        }

        .message.diagnosis .message-bubble {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
        }

        .questions-container {
            background: linear-gradient(to bottom, #ffffff 0%, #f8f9ff 100%);
            border-top: none;
            padding: 15px;
            padding-top: 0;
            padding-bottom: 30px;
            margin-top: 0;
            margin-bottom: 0;
            box-shadow: none;
            position: relative;
            z-index: 10;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            min-height: calc(100vh - 80px);
            max-height: calc(100vh - 80px);
            height: calc(100vh - 80px);
            order: 1;
        }

        .questions-container::-webkit-scrollbar {
            width: 10px;
        }

        .questions-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 5px;
        }

        .questions-container::-webkit-scrollbar-thumb {
            background: #0A76D8;
            border-radius: 5px;
        }

        .questions-container::-webkit-scrollbar-thumb:hover {
            background: #5568d3;
        }

        /* تصميم محسّن للأسئلة */
        form {
            margin: 0;
            padding: 0;
            margin-top: 0;
            padding-top: 0;
        }
        
        form .field {
            margin: 1rem 0;
            margin-top: 0;
            padding: 20px;
            background: white;
            border-radius: 16px;
            border: 2px solid #e8ecf7;
            box-shadow: 0 2px 8px rgba(10, 118, 216, 0.06);
            transition: all 0.3s ease;
        }
        
        form .field:first-child {
            margin-top: 0;
            padding-top: 15px;
        }

        form .field:hover {
            box-shadow: 0 4px 16px rgba(10, 118, 216, 0.12);
            border-color: #d0d9f0;
        }

        .field label {
            display: block;
            margin-bottom: 1rem;
            color: #2d3748;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: 0.3px;
        }

        .field input[type="text"],
        .field input[type="number"] {
            width: 100%;
            background: #f8f9ff;
            color: #2d3748;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .field input[type="text"]:focus,
        .field input[type="number"]:focus {
            border-color: #0A76D8;
            outline: none;
            box-shadow: 0 0 0 4px rgba(10, 118, 216, 0.12);
            background: white;
            transform: translateY(-1px);
        }

        .opt {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 0.6rem 0;
            padding: 16px 20px;
            background: #f8f9ff;
            border: 2px solid #e8ecf7;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            width: 100%;
            user-select: none;
            position: relative;
        }

        .opt::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: #0A76D8;
            border-radius: 12px 0 0 12px;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .opt:hover {
            background: linear-gradient(90deg, #f0f4ff 0%, #ffffff 100%);
            border-color: #0A76D8;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(10, 118, 216, 0.15);
        }

        .opt:hover::before {
            opacity: 1;
        }

        .opt * {
            pointer-events: none; /* منع تفاعل العناصر الداخلية */
        }

        .opt input {
            pointer-events: auto; /* السماح بالتفاعل مع input */
        }

        .opt span {
            pointer-events: none; /* منع تفاعل span - الـ row يتعامل مع النقر */
        }


        .opt:has(input[type="radio"]:checked),
        .opt:has(input[type="checkbox"]:checked) {
            background: linear-gradient(135deg, #0A76D8 0%, #171677ff 100%);
            border-color: #0A76D8;
            color: white;
            box-shadow: 0 4px 16px rgba(10, 118, 216, 0.3);
            transform: translateX(4px);
        }

        .opt:has(input[type="radio"]:checked)::before,
        .opt:has(input[type="checkbox"]:checked)::before {
            opacity: 1;
            background: rgba(255, 255, 255, 0.3);
        }

        .opt:has(input[type="radio"]:checked) span,
        .opt:has(input[type="checkbox"]:checked) span {
            color: white;
            font-weight: 600;
        }

        .opt input {
            cursor: pointer;
        }

        .opt input[type="radio"] {
            transform: scale(1.4);
            accent-color: #0A76D8;
        }

        .opt input[type="checkbox"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 22px;
            height: 22px;
            min-width: 22px;
            min-height: 22px;
            border: 2px solid #0A76D8;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
            accent-color: #0A76D8;
        }

        .opt input[type="checkbox"]:checked {
            background-color: #0A76D8;
            border-color: #0A76D8;
        }

        .opt input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 14px;
            font-weight: bold;
            line-height: 1;
        }

        .opt input[type="checkbox"]:hover {
            border-color: #006dd3;
            box-shadow: 0 0 0 3px rgba(10, 118, 216, 0.1);
        }

        .opt input[type="checkbox"]:checked:hover {
            background-color: #006dd3;
            border-color: #006dd3;
        }

        .opt:has(input[type="radio"]:checked) input,
        .opt:has(input[type="checkbox"]:checked) input {
            accent-color: #0A76D8;
        }

        .opt span {
            flex: 1;
            color: #2d3748;
            font-size: 15px;
            font-weight: 500;
            transition: color 0.25s ease;
        }

        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e8ecf7;
            margin-bottom: 20px;
            padding-bottom: 20px;
        }

        /* تصميم جميل لعرض النتيجة */
        .diagnosis-result-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border-radius: 20px;
            padding: 20px;
            padding-top: 0;
            margin: 0;
            box-shadow: 0 8px 30px rgba(10, 118, 216, 0.15);
            border: 2px solid #e8ecf7;
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .diagnosis-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            margin-top: 0;
            padding-bottom: 15px;
            padding-top: 0;
            border-bottom: 2px solid #e8ecf7;
        }

        .diagnosis-icon {
            font-size: 40px;
            background: linear-gradient(135deg, #0A76D8 0%, #171677ff 100%);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(10, 118, 216, 0.3);
        }

        .diagnosis-title {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }

        .diagnosis-content {
            margin-bottom: 15px;
            margin-top: 0;
        }

        .diagnosis-item {
            margin-bottom: 15px;
            margin-top: 0;
            padding: 15px;
            background: white;
            border-radius: 12px;
            border: 1px solid #e8ecf7;
        }
        
        .diagnosis-item:first-child {
            margin-top: 0;
        }

        .diagnosis-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #718096;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .diagnosis-value {
            display: block;
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
        }

        .probability-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .probability-bar {
            flex: 1;
            height: 12px;
            background: #e8ecf7;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .probability-fill {
            height: 100%;
            background: linear-gradient(90deg, #0A76D8 0%, #171677ff 100%);
            border-radius: 10px;
            transition: width 1s ease;
            box-shadow: 0 2px 8px rgba(10, 118, 216, 0.4);
        }

        .probability-text {
            font-size: 18px;
            font-weight: 700;
            color: #0A76D8;
            min-width: 50px;
            text-align: right;
        }

        .diagnosis-summary {
            padding: 15px;
            background: #f0f4ff;
            border-radius: 12px;
            border-left: 4px solid #0A76D8;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .diagnosis-summary h4 {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
            margin: 0 0 10px 0;
        }

        .diagnosis-summary p {
            font-size: 15px;
            color: #4a5568;
            line-height: 1.6;
            margin: 0;
        }

        .diagnosis-actions {
            margin-top: 15px;
            margin-bottom: 0;
            padding-top: 15px;
            padding-bottom: 0;
            border-top: 2px solid #e8ecf7;
        }

        /* تصميم جديد للرسالة النصية فقط */
        .recommendation-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border-radius: 20px;
            padding: 40px 30px;
            margin: 0;
            box-shadow: 0 8px 30px rgba(10, 118, 216, 0.15);
            border: 2px solid #e8ecf7;
            animation: fadeInUp 0.5s ease;
            text-align: center;
        }

        .recommendation-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .recommendation-message {
            margin-bottom: 30px;
        }

        .recommendation-text {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            line-height: 1.6;
            margin: 0;
        }

        .recommendation-action {
            margin-top: 30px;
        }

        .department-link-modern {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 30px;
            background: linear-gradient(135deg, #0A76D8 0%, #171677ff 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(10, 118, 216, 0.3);
        }

        .department-link-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(10, 118, 216, 0.4);
        }

        .department-link-modern .link-icon {
            font-size: 20px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #0A76D8 0%, #171677ff 100%);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 16px;
            margin-top: 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(10, 118, 216, 0.3);
            letter-spacing: 0.5px;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(10, 118, 216, 0.4);
            background: linear-gradient(135deg, #006dd3 0%, #0f0966ff 100%);
        }

        .btn-submit:active {
            transform: translateY(-1px);
        }

        .btn-reset {
            background: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            margin-top: 15px;
            transition: all 0.3s;
        }

        .btn-reset:hover {
            background: #e0e0e0;
        }

        .department-link {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #0A76D8 0%, #171677ff 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .department-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(10, 118, 216, 0.4);
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }


        .more-symptoms-card {
            background: white;
            border: 2px solid #e8ecf7;
            border-radius: 16px;
            padding: 25px;
            margin: 0;
            box-shadow: 0 2px 8px rgba(10, 118, 216, 0.06);
            transition: all 0.3s ease;
        }

        .more-symptoms-card:hover {
            box-shadow: 0 4px 16px rgba(10, 118, 216, 0.12);
            border-color: #d0d9f0;
        }

        .more-symptoms-card strong {
            display: block;
            margin-bottom: 25px;
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
            letter-spacing: 0.3px;
        }

        .more-symptoms-buttons {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .btn-yes,
        .btn-no {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }

        .btn-yes {
            background: linear-gradient(135deg, #0A76D8 0%, #171677ff 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(10, 118, 216, 0.3);
        }

        .btn-yes:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(10, 118, 216, 0.4);
            background: linear-gradient(135deg, #006dd3 0%, #0f0966ff 100%);
        }

        .btn-no {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        .btn-no:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
            background: linear-gradient(135deg, #5a6268 0%, #343a40 100%);
        }

        .start-chat-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
            padding: 40px 20px;
            background: transparent;
            margin: 20px;
            order: 2;
        }

        .btn-start-chat {
            padding: 18px 40px;
            font-size: 18px;
            font-weight: 700;
            color: white;
            background: linear-gradient(135deg, #0A76D8 0%, #171677ff 100%);
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(10, 118, 216, 0.4);
        }

        .btn-start-chat:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(10, 118, 216, 0.5);
            background: linear-gradient(135deg, #006dd3 0%, #0f0966ff 100%);
        }

        .btn-start-chat:active {
            transform: translateY(-1px);
        }

        /* RTL Support */
        [dir="rtl"] .menu-btn {
            background-position: calc(100% - 20px) 50%;
            text-align: right;
        }

        [dir="rtl"] .menu-text {
            padding-left: 0;
            padding-right: 50px;
            text-align: right;
        }

        [dir="rtl"] .menu-btn:hover {
            transform: translateX(-5px);
        }

        [dir="rtl"] .message.user {
            justify-content: flex-start;
        }

        [dir="rtl"] .message.bot {
            justify-content: flex-end;
        }

        [dir="rtl"] .checkbox-option input,
        [dir="rtl"] .radio-option input {
            margin-left: 10px;
            margin-right: 0;
        }

        .language-switcher-header {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1001;
        }

        [dir="rtl"] .language-switcher-header {
            right: auto;
            left: 15px;
        }
    </style>
</head>

<body>
    <div class="language-switcher-header">
        <?php include("../language-switcher.php"); ?>
    </div>
    <div class="container">
        <div class="menu" id="sidebarMenu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username, 0, 13) ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail, 0, 22) ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php"><input type="button" value="<?php echo t('logout'); ?>"
                                            class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-home">
                        <a href="index.php" class="non-style-link-menu">
                            <p class="menu-text"><?php echo t('home'); ?></p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu">
                            <p class="menu-text"><?php echo t('all_doctors'); ?></p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu">
                            <p class="menu-text"><?php echo t('schedule'); ?></p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu">
                            <p class="menu-text"><?php echo t('my_appointments'); ?></p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="specialties.php" class="non-style-link-menu">
                            <p class="menu-text"><?php echo t('specialties'); ?></p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-ai menu-active menu-icon-ai-active">
                        <a href="chatbot.php" class="non-style-link-menu non-style-link-menu-active">
                            <p class="menu-text"><?php echo isArabic() ? 'مساعد' : 'Chat Bot'; ?></p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu">
                            <p class="menu-text"><?php echo t('settings'); ?></p>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <div class="chatbot-wrapper">
                <div class="chatbot-header">
                    <h1>
                        <span>🤖</span>
                        <?php echo t('chat_bot'); ?>
                    </h1>
                    <div class="header-actions">
                        <button class="btn-header" onclick="resetChat()">
                            <?php echo isArabic() ? '🔄 جلسة جديدة' : '🔄 New Session'; ?>
                        </button>
                            </div>
        </div>

                <div class="questions-container" id="questionsContainer" style="display: none;"></div>

                <div class="start-chat-container" id="startChatContainer">
                    <button class="btn-start-chat" onclick="if(window.startChatFull) window.startChatFull(); else if(window._startChatFull) window._startChatFull();">
                        <?php echo isArabic() ? '🚀 ابدأ المحادثة' : '🚀 Start Chat'; ?>
                    </button>
                </div>

                <div class="chat-messages-container" id="chatMessages" style="display: none;">
    </div>
        </div>
    </div>
    </div>

    <script>
        let sessionId = null;
        let isProcessing = false;
        let currentAnswers = {};
        let lastAsk = [];
        // أخذ اللغة تلقائياً من إعدادات الموقع
        let selectedLanguage = (document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl') ? 'ar' : 'en';

        // Define startChat early - will be replaced with full implementation later
        function startChat() {
            if (window._startChatFull) {
                window._startChatFull();
            } else {
                console.log('startChat: Waiting for full implementation...');
                setTimeout(() => {
                    if (window._startChatFull) {
                        window._startChatFull();
                    } else {
                        alert('جاري التحميل...');
                    }
                }, 100);
            }
        }
        window.startChat = startChat;

        // API functions
        async function apiCall(endpoint, data = {}) {
            try {
                const headers = {
                    'Content-Type': 'application/json'
                };
                if (sessionId) {
                    headers['X-Session-Id'] = sessionId;
                }

                const response = await fetch('chatbot_api.php' + endpoint, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.sid) {
                    sessionId = result.sid;
                }
                return result;
            } catch (error) {
                console.error('API Call Error:', error);
                const isArabic = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
                return {
                    error: isArabic 
                        ? 'خطأ في الاتصال بالخادم. تأكد من أن Flask Server شغال على http://localhost:5000'
                        : 'Connection error. Please make sure Flask Server is running on http://localhost:5000',
                    http_code: 0
                };
            }
        }

        function addMessage(text, type = 'bot', isHTML = false) {
            const messagesContainer = document.getElementById('chatMessages');
            
            // إظهار messagesContainer عندما نضيف رسالة
            if (messagesContainer) {
                messagesContainer.style.display = 'block';
                messagesContainer.style.padding = '20px';
                messagesContainer.style.background = '#f8f9fa';
            }

            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            
            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';
            if (isHTML) {
                bubble.innerHTML = text;
                    } else {
                bubble.textContent = text;
            }
            
            messageDiv.appendChild(bubble);
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function displayQuestions(questions) {
            console.log('displayQuestions called with:', questions);
            lastAsk = Array.isArray(questions) ? questions : [questions];
            console.log('lastAsk after processing:', lastAsk);
            
            if (!lastAsk.length) {
                console.log('No questions to display');
                if (document.getElementById('questionsContainer')) {
                    document.getElementById('questionsContainer').style.display = 'none';
                }
                return;
            }

            // استخدام questionsContainer لعرض الأسئلة في الصفحة
            const container = document.getElementById('questionsContainer');
            if (!container) {
                console.error('questionsContainer not found!');
                return;
            }
            
            // إزالة أي form سابق
            const oldForm = document.getElementById('askForm');
            if (oldForm) {
                oldForm.remove();
            }
            
            const form = document.createElement('form');
            form.id = 'askForm';
            form.style.margin = '0';
            form.style.padding = '0';
            form.style.marginTop = '0';
            form.style.paddingTop = '0';
            form.style.marginBottom = '0';
            form.style.paddingBottom = '0';
            form.innerHTML = '';
            container.innerHTML = '';
            container.style.display = 'block';
            container.style.marginTop = '0';
            container.style.paddingTop = '0';
            container.style.marginBottom = '0';
            console.log('Starting to render questions in page...');

            for (const q of lastAsk) {
                console.log('Rendering question:', q);
                const field = document.createElement('div');
                field.className = 'field';
                
                const label = document.createElement('label');
                // استخدام اللغة المختارة في الشات بوت
                const isArabic = selectedLanguage === 'ar';
                // أسئلة M2 تأتي مباشرة بـ q (النص العربي أو الإنجليزي حسب اللغة)
                // نستخدم q_ar للعربية و q_en للإنجليزية
                if (isArabic && q.q_ar) {
                    label.textContent = q.q_ar;
                } else if (!isArabic && q.q_en) {
                    label.textContent = q.q_en;
                } else if (isArabic && q.q) {
                    // إذا كان q موجوداً فقط، نستخدمه (يفترض أنه بالعربية)
                    label.textContent = q.q;
                } else if (!isArabic && q.q) {
                    // إذا كان q موجوداً فقط، نستخدمه (يفترض أنه بالإنجليزية)
                    label.textContent = q.q;
                } else {
                    label.textContent = q.name || '';
                }
                console.log('Question label:', label.textContent, 'isArabic:', isArabic, 'selectedLanguage:', selectedLanguage);
                field.appendChild(label);

                if (q.type === 'radio' && Array.isArray(q.options)) {
                    for (const opt of q.options) {
                        const v = (typeof opt === 'object') ? (opt.value ?? opt.label) : opt;
                        // للخيارات، نستخدم label_ar للعربية و label_en للإنجليزية
                        let l;
                        const isArabicOpt = selectedLanguage === 'ar';
                        if (typeof opt === 'object') {
                            if (isArabicOpt && opt.label_ar) {
                                l = opt.label_ar;
                            } else if (!isArabicOpt && opt.label_en) {
                                l = opt.label_en;
                            } else if (isArabicOpt && opt.label) {
                                l = opt.label;
                            } else if (!isArabicOpt && opt.label) {
                                l = opt.label;
                            } else {
                                l = opt.value || '';
                            }
                        } else {
                            // إذا كانت نصية مباشرة، نستخدمها كما هي (Flask يرسلها باللغة الصحيحة)
                            l = opt;
                        }
                        const row = document.createElement('div');
                        row.className = 'opt';
                        row.style.cursor = 'pointer';
                        const inp = document.createElement('input');
                        inp.type = 'radio';
                        inp.name = q.name;
                        inp.value = v;
                        row.onclick = function() {
                            inp.checked = true;
                        };
                        inp.onclick = function(e) {
                            e.stopPropagation(); // منع التكرار
                        };
                        const span = document.createElement('span');
                        span.textContent = l;
                        row.appendChild(inp);
                        row.appendChild(span);
                        field.appendChild(row);
                    }
                } else if (q.type === 'checkbox' && Array.isArray(q.options)) {
                    for (const opt of q.options) {
                        const v = (typeof opt === 'object') ? (opt.value ?? opt.label) : opt;
                        // للخيارات، نستخدم label_ar للعربية و label_en للإنجليزية
                        let l;
                        const isArabicOpt = selectedLanguage === 'ar';
                        if (typeof opt === 'object') {
                            if (isArabicOpt && opt.label_ar) {
                                l = opt.label_ar;
                            } else if (!isArabicOpt && opt.label_en) {
                                l = opt.label_en;
                            } else if (isArabicOpt && opt.label) {
                                l = opt.label;
                            } else if (!isArabicOpt && opt.label) {
                                l = opt.label;
                            } else {
                                l = opt.value || '';
                            }
                        } else {
                            // إذا كانت نصية مباشرة، نستخدمها كما هي (Flask يرسلها باللغة الصحيحة)
                            l = opt;
                        }
                        const row = document.createElement('div');
                        row.className = 'opt';
                        row.style.cursor = 'pointer';
                        const inp = document.createElement('input');
                        inp.type = 'checkbox';
                        inp.name = q.name;
                        inp.value = v;
                        row.onclick = function() {
                            inp.checked = !inp.checked;
                        };
                        inp.onclick = function(e) {
                            e.stopPropagation(); // منع التكرار
                        };
                        const span = document.createElement('span');
                        span.textContent = l;
                        row.appendChild(inp);
                        row.appendChild(span);
                        field.appendChild(row);
                    }
                } else if (q.type === 'number') {
                    const inp = document.createElement('input');
                    inp.type = 'number';
                    inp.name = q.name;
                    inp.step = 'any';
                    field.appendChild(inp);
                } else { // text/other
                    const inp = document.createElement('input');
                    inp.type = 'text';
                    inp.name = q.name;
                    field.appendChild(inp);
                }
                form.appendChild(field);
            }

            const actions = document.createElement('div');
            actions.className = 'actions';
            const submitBtn = document.createElement('button');
            submitBtn.className = 'btn-submit';
            submitBtn.type = 'button';
            const isArabic = selectedLanguage === 'ar';
            submitBtn.textContent = isArabic ? 'إرسال الإجابات' : 'Submit Answers';
            submitBtn.onclick = submitAnswers;
            actions.appendChild(submitBtn);
            
            container.appendChild(form);
            container.appendChild(actions);
            
            // التأكد من أن الـ container مرئي
            container.style.display = 'block';
            container.style.visibility = 'visible';
            container.style.opacity = '1';
            
            console.log('Questions rendered successfully in page. Form HTML length:', form.innerHTML.length);
            
            // الانتقال إلى الأسئلة بعد فترة قصيرة لضمان ظهورها
            setTimeout(() => {
                // التأكد من أن container مرئي
                container.style.display = 'block';
                container.style.visibility = 'visible';
                container.style.opacity = '1';
                
                // Scroll إلى أعلى container (لا حاجة الآن لأن scroll داخل container نفسه)
                container.scrollTop = 0;
            }, 300);
        }

        // تجميع الإجابات بنفس طريقة proj/index.html
        function collectAnswers() {
            const data = {};
            const form = document.getElementById('askForm');
            if (!form) return data;
            
            const fd = new FormData(form);
            const names = new Set();
            for (const el of form.querySelectorAll('[name]')) names.add(el.name);
            
            for (const name of names) {
                const inputs = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
                if (inputs[0]?.type === 'checkbox') {
                    data[name] = [];
                    inputs.forEach(inp => {
                        if (inp.checked) data[name].push(inp.value);
                    });
                } else if (inputs[0]?.type === 'radio') {
                    const checked = form.querySelector(`[name="${CSS.escape(name)}"]:checked`);
                    if (checked) data[name] = checked.value;
                } else {
                    data[name] = fd.get(name);
                }
            }
            return data;
        }

        // هل نستخدم chat_m1 أم controller؟
        function shouldUseChat(askList) {
            const names = askList.map(q => q.name || '');
            // إزالة user_language من التحقق لأننا نرسله تلقائياً
            return names.every(n => n === 'free_text_symptoms');
        }

        // دالة لعرض سؤال "هل لديك أعراض أخرى؟" بعد checkbox كسؤال radio عادي
        function showMoreSymptomsQuestion() {
            const isArabic = selectedLanguage === 'ar';
            const container = document.getElementById('questionsContainer');
            if (!container) return;
            
            // إزالة أي form سابق
            const oldForm = document.getElementById('askForm');
            if (oldForm) {
                oldForm.remove();
            }
            
            const form = document.createElement('form');
            form.id = 'askForm';
            form.style.margin = '0';
            form.style.padding = '0';
            form.style.marginTop = '0';
            form.style.paddingTop = '0';
            form.style.marginBottom = '0';
            form.style.paddingBottom = '0';
            form.innerHTML = '';
            container.innerHTML = '';
            container.style.display = 'block';
            
            // إنشاء سؤال radio عادي
            const field = document.createElement('div');
            field.className = 'field';
            
            const label = document.createElement('label');
            label.textContent = isArabic ? 'هل لديك أعراض أخرى؟' : 'Do you have more symptoms?';
            field.appendChild(label);
            
            // خيارات radio
            const options = [
                { value: 'more_yes', label_ar: 'نعم', label_en: 'Yes' },
                { value: 'more_no', label_ar: 'لا', label_en: 'No' }
            ];
            
            for (const opt of options) {
                const row = document.createElement('div');
                row.className = 'opt';
                row.style.cursor = 'pointer';
                
                const inp = document.createElement('input');
                inp.type = 'radio';
                inp.name = 'more_symptoms';
                inp.value = opt.value;
                inp.onclick = function(e) {
                    e.stopPropagation();
                };
                
                row.onclick = function() {
                    inp.checked = true;
                    // عند الاختيار، نرسل الإجابة بعد ثانية
                    setTimeout(() => {
                        handleMoreSymptomsAfterCheckbox(opt.value);
                    }, 300);
                };
                
                const span = document.createElement('span');
                span.textContent = isArabic ? opt.label_ar : opt.label_en;
                
                row.appendChild(inp);
                row.appendChild(span);
                field.appendChild(row);
            }
            
            form.appendChild(field);
            container.appendChild(form);
            
            // حفظ السؤال في lastAsk للتوافق مع submitAnswers
            lastAsk = [{
                name: 'more_symptoms',
                type: 'radio',
                q: isArabic ? 'هل لديك أعراض أخرى؟' : 'Do you have more symptoms?',
                options: options
            }];
        }

        async function submitAnswers() {
            if (isProcessing || !lastAsk.length) return;
            isProcessing = true;

            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'message bot';
            loadingDiv.innerHTML = '<div class="message-bubble">⏳ ' + (document.documentElement.lang === 'ar' ? 'جاري المعالجة...' : 'Processing...') + '</div>';
            document.getElementById('chatMessages').appendChild(loadingDiv);
            
            try {
                const answers = collectAnswers();
                const isArabic = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
                
                // إخفاء جميع الإجابات ما عدا free_text_symptoms
                // نعرض فقط الكتابة الحرة للمستخدم
                // باقي الإجابات (user_language, symptoms_m1, M2 fields) تُخزن فقط ولا تُعرض
                
                // تحديث اللغة إذا تم تغييرها (لكن عادة نستخدم اللغة من الموقع)
                if (answers.user_language) {
                    selectedLanguage = answers.user_language;
                    console.log('Language updated:', selectedLanguage);
                }
                
                // فقط إذا كان هناك free_text_symptoms، نعرضه
                // جميع الإجابات الأخرى (user_language, symptoms_m1, M2 fields) تُخزن فقط ولا تُعرض
                if (answers.free_text_symptoms) {
                    addMessage(answers.free_text_symptoms, 'user');
                }
                // لا نعرض أي إجابات أخرى - تُخزن فقط في الخادم
                
                // نفس منطق proj/index.html - استخدام chat_m1 أو controller
                // shouldUseChat يحدد إذا كان user_language أو free_text_symptoms فقط
                // في هذه الحالة نستخدم chat_m1، وإلا نستخدم controller
                // لكن chatbot_api.php يحدد هذا تلقائياً بناءً على البيانات
                // لذلك نرسل answers فقط
                const result = await apiCall('', { answers: answers });
                
                loadingDiv.remove();
                
                // التحقق إذا كانت الأسئلة من نوع checkbox (M1)
                const isCheckboxQuestions = lastAsk.some(q => q.type === 'checkbox' && q.name === 'symptoms_m1');
                
                console.log('After checkbox submit - isCheckboxQuestions:', isCheckboxQuestions);
                console.log('Result:', result);
                console.log('result.ask:', result.ask);
                console.log('result.ui_hint:', result.ui_hint);
                
                // إذا كانت أسئلة checkbox
                if (isCheckboxQuestions) {
                    // التحقق من وجود ask في الرد
                    let hasMoreCheckboxQuestions = false;
                    let hasM2Questions = false;
                    
                    if (result.ask) {
                        const askArray = Array.isArray(result.ask) ? result.ask : [result.ask];
                        
                        // التحقق من وجود المزيد من أسئلة checkbox
                        hasMoreCheckboxQuestions = askArray.some(q => 
                            q.type === 'checkbox' && q.name === 'symptoms_m1'
                        );
                        
                        // التحقق من وجود أسئلة M2 (أي أسئلة ليست checkbox)
                        hasM2Questions = askArray.some(q => 
                            q.name && 
                            q.name !== 'symptoms_m1' && 
                            q.name !== 'free_text_symptoms' &&
                            q.type !== 'checkbox'
                        );
                    }
                    
                    // إذا كان هناك المزيد من أسئلة checkbox، نستمر في عرضها
                    if (hasMoreCheckboxQuestions) {
                        console.log('More checkbox questions, continuing...');
                        handleResponse(result);
                        return;
                    }
                    
                    // إذا كان هناك أسئلة M2، ننتقل إليها مباشرة
                    if (hasM2Questions) {
                        console.log('M2 questions found, proceeding...');
                        handleResponse(result);
                        return;
                    }
                    
                    // إذا كان Flask يرسل سؤال more_symptoms أو ui_hint، نعرضه
                    if (result.ui_hint?.ask_more_symptoms) {
                        console.log('Flask sent ui_hint ask_more_symptoms, showing it...');
                        handleResponse(result);
                        return;
                    }
                    
                    // إذا كان هناك ask مع more_symptoms، نعرضه
                    if (result.ask) {
                        const askArray = Array.isArray(result.ask) ? result.ask : [result.ask];
                        const hasMoreSymptomsQuestion = askArray.some(q => 
                            q.name === 'more_symptoms' || 
                            (q.q && (q.q.includes('أعراض أخرى') || q.q.includes('other symptoms')))
                        );
                        if (hasMoreSymptomsQuestion) {
                            console.log('Flask sent more_symptoms question, showing it...');
                            handleResponse(result);
                            return;
                        }
                    }
                    
                    // إذا لم يكن هناك شيء (لا checkbox، لا M2، لا final_diagnosis)، نعرض سؤال "هل لديك أعراض أخرى؟"
                    // أو إذا كان Flask يرسل noop (لا يوجد عمل)
                    // أو إذا كان ask فارغ أو array فارغ
                    const hasEmptyAsk = !result.ask || (Array.isArray(result.ask) && result.ask.length === 0);
                    
                    if ((!result.final_diagnosis && !result.top_department && hasEmptyAsk) || 
                        (result.ui_hint && result.ui_hint.noop && !hasM2Questions)) {
                        console.log('No more questions, showing more symptoms question after checkbox');
                        showMoreSymptomsQuestion();
                        return;
                    }
                }
                
                // معالجة الرد العادي
                handleResponse(result);
            } catch (error) {
                loadingDiv.remove();
                addMessage('❌ ' + (document.documentElement.lang === 'ar' ? 'حدث خطأ' : 'An error occurred'), 'bot');
            } finally {
                isProcessing = false;
            }
        }

        async function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            if (!message || isProcessing) return;

            addMessage(message, 'user');
            input.value = '';
            isProcessing = true;

            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'message bot';
            loadingDiv.innerHTML = '<div class="message-bubble">⏳ ' + (document.documentElement.lang === 'ar' ? 'جاري المعالجة...' : 'Processing...') + '</div>';
            document.getElementById('chatMessages').appendChild(loadingDiv);

            try {
                const result = await apiCall('', { message: message });
                loadingDiv.remove();
                handleResponse(result);
            } catch (error) {
                loadingDiv.remove();
                addMessage('❌ ' + (document.documentElement.lang === 'ar' ? 'حدث خطأ' : 'An error occurred'), 'bot');
            } finally {
                isProcessing = false;
            }
        }

        function handleResponse(data) {
            console.log('handleResponse called with data:', data);
            
            if (data.error) {
                const isArabic = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
                let errorMsg = '❌ ' + data.error;
                
                // إذا كان الخطأ 500، أضف رسالة مساعدة
                if (data.http_code === 500) {
                    errorMsg += '\n\n' + (isArabic 
                        ? '⚠️ يرجى التأكد من أن Flask Server شغال على http://localhost:5000'
                        : '⚠️ Please make sure Flask Server is running on http://localhost:5000');
                }
                
                // إذا كان الخطأ متعلق بـ M2، أضف رسالة مساعدة
                if (data.error.includes('تعذّر حساب النتيجة') || data.error.includes('Unable to calculate')) {
                    errorMsg += '\n\n' + (isArabic 
                        ? '💡 قد تكون هناك مشكلة في بيانات M2. تحقق من Terminal الذي يشغّل Flask Server لمعرفة التفاصيل.'
                        : '💡 There might be an issue with M2 data. Check the Terminal running Flask Server for details.');
                }
                
                addMessage(errorMsg, 'bot');
                console.error('API Error:', data);
                return;
            }

            // تحديث اللغة إذا تم تغييرها (لكن عادة نستخدم اللغة من الموقع)
            if (data.user_language) {
                selectedLanguage = data.user_language;
                console.log('Language updated from response:', selectedLanguage);
            }

            // Handle UI hint for more symptoms - يظهر قبل أسئلة M2
            if (data.ui_hint && data.ui_hint.ask_more_symptoms) {
                // عرض سؤال "هل لديك أعراض أخرى؟" كسؤال radio عادي
                showMoreSymptomsQuestion();
                return;
            }

            // Handle UI hint noop - يعني لا يوجد عمل
            // لكن إذا كانت الأسئلة السابقة checkbox، نعرض سؤال "هل لديك أعراض أخرى؟"
            if (data.ui_hint && data.ui_hint.noop) {
                console.log('UI hint noop');
                const wasCheckboxQuestions = lastAsk.some(q => q.type === 'checkbox' && q.name === 'symptoms_m1');
                if (wasCheckboxQuestions) {
                    // بعد انتهاء checkbox، نعرض سؤال "هل لديك أعراض أخرى؟"
                    showMoreSymptomsQuestion();
                    return;
                }
                document.getElementById('questionsContainer').style.display = 'none';
                return;
            }

            // Handle questions - أولوية عالية
            console.log('Checking for ask:', data.ask);
            if (data.ask) {
                console.log('ask found:', data.ask, 'length:', Array.isArray(data.ask) ? data.ask.length : 'not array');
                
                // تحويل ask إلى array إذا لم يكن array
                const askArray = Array.isArray(data.ask) ? data.ask : [data.ask];
                
                // التحقق من سؤال "هل لديك أعراض أخرى؟" (more_symptoms)
                const moreSymptomsQuestion = askArray.find(q => q.name === 'more_symptoms' || (q.q && (q.q.includes('أعراض أخرى') || q.q.includes('other symptoms'))));
                
                if (moreSymptomsQuestion) {
                    // عرض سؤال "هل لديك أعراض أخرى؟" كسؤال radio عادي
                    showMoreSymptomsQuestion();
                    return;
                }
                
                // التحقق إذا كانت هذه أسئلة M2 (ليست checkbox) وكانت الأسئلة السابقة checkbox
                const isM2Questions = askArray.some(q => 
                    q.name && 
                    q.name !== 'symptoms_m1' && 
                    q.name !== 'free_text_symptoms' &&
                    q.type !== 'checkbox'
                );
                
                // إذا كانت أسئلة M2 وكانت الأسئلة السابقة checkbox، نعرض سؤال "هل لديك أعراض أخرى؟" أولاً
                if (isM2Questions && lastAsk.some(q => q.type === 'checkbox' && q.name === 'symptoms_m1')) {
                    console.log('M2 questions detected after checkbox, showing more symptoms question first');
                    // حفظ أسئلة M2 للاستخدام لاحقاً
                    window.pendingM2Questions = askArray;
                    // عرض سؤال "هل لديك أعراض أخرى؟" كسؤال radio عادي
                    showMoreSymptomsQuestion();
                    return;
                }
                
                if (askArray.length > 0) {
                    console.log('Displaying questions:', askArray);
                    
                    displayQuestions(askArray);
                    
                    // الانتقال إلى الأسئلة بعد عرضها
                    setTimeout(() => {
                        const container = document.getElementById('questionsContainer');
                        if (container && container.style.display !== 'none') {
                            // التأكد من أن container مرئي
                            container.style.display = 'block';
                            container.style.visibility = 'visible';
                            container.style.opacity = '1';
                            
                            // Scroll إلى أعلى container (scroll الآن داخل container نفسه)
                            container.scrollTop = 0;
                        }
                    }, 500);
                    
                    
                    // لا نعرض note إذا كان هناك ask
                    return;
                }
            }

            // Handle notes - فقط إذا لم يكن هناك ask
            if (data.note) {
                addMessage(data.note, 'bot');
            }

            // Handle final diagnosis - حفظ النتيجة في قاعدة البيانات وعرض رسالة نصية فقط
            if (data.final_diagnosis) {
                const diagnosis = data.final_diagnosis;
                const isArabic = selectedLanguage === 'ar';
                
                // حفظ النتيجة في قاعدة البيانات
                saveDiagnosisToDatabase(data);
                
                // عرض رسالة نصية فقط بدون النتيجة
                const container = document.getElementById('questionsContainer');
                if (container) {
                    let html = `
                        <div class="recommendation-card">
                            <div class="recommendation-icon">💡</div>
                            <div class="recommendation-message">
                                <p class="recommendation-text">${isArabic 
                                    ? 'حسب إجاباتك، ننصحك بالذهاب إلى هذا القسم' 
                                    : 'Based on your answers, we recommend visiting this department'}</p>
                            </div>
                    `;
                    
                    if (data.department_id && data.department_name_db) {
                        html += `
                            <div class="recommendation-action">
                                <a href="doctors_by_specialty.php?id=${data.department_id}" class="department-link-modern">
                                    <span class="link-icon">👨‍⚕️</span>
                                    <span>${isArabic ? 'عرض الأطباء في ' : 'View Doctors in '}${data.department_name_db}</span>
                                </a>
                            </div>
                        `;
                    }
                    
                    html += `</div>`;
                    
                    container.innerHTML = html;
                    container.style.display = 'block';
                }
                return;
            }

            // Handle department recommendation - نفس الرسالة النصية
            if (data.top_department && !data.final_diagnosis) {
                const isArabic = selectedLanguage === 'ar';
                const container = document.getElementById('questionsContainer');
                if (container) {
                    let html = `
                        <div class="recommendation-card">
                            <div class="recommendation-icon">💡</div>
                            <div class="recommendation-message">
                                <p class="recommendation-text">${isArabic 
                                    ? 'حسب إجاباتك، ننصحك بالذهاب إلى هذا القسم' 
                                    : 'Based on your answers, we recommend visiting this department'}</p>
                            </div>
                    `;
                    
                    if (data.department_id && data.department_name_db) {
                        html += `
                            <div class="recommendation-action">
                                <a href="doctors_by_specialty.php?id=${data.department_id}" class="department-link-modern">
                                    <span class="link-icon">👨‍⚕️</span>
                                    <span>${isArabic ? 'عرض الأطباء في ' : 'View Doctors in '}${data.department_name_db}</span>
                                </a>
                            </div>
                        `;
                    }
                    
                    html += `</div>`;
                    
                    container.innerHTML = html;
                    container.style.display = 'block';
                }
            }
        }

        async function handleMoreSymptoms(intent) {
            if (isProcessing) return;
            
            const isArabic = selectedLanguage === 'ar';
            
            // إخفاء بطاقة السؤال
            const card = document.getElementById('moreSymptomsCard');
            if (card) {
                card.remove();
            }
            
            // إضافة رسالة المستخدم
            addMessage(intent === 'more_yes' 
                ? (isArabic ? 'نعم، لدي أعراض أخرى' : 'Yes, I have more symptoms')
                : (isArabic ? 'لا، ليس لدي المزيد' : 'No, I don\'t have more'), 'user');

            // إذا كانت الإجابة "نعم"، نعرض input لكتابة الأعراض في questionsContainer
            if (intent === 'more_yes') {
                const container = document.getElementById('questionsContainer');
                if (container) {
                    container.innerHTML = '';
                    container.style.display = 'block';
                    
                    const symptomsInputCard = document.createElement('div');
                    symptomsInputCard.className = 'more-symptoms-card';
                    symptomsInputCard.id = 'symptomsInputCard';
                    symptomsInputCard.style.margin = '0';
                    symptomsInputCard.style.padding = '20px';
                    symptomsInputCard.innerHTML = `
                        <strong>${isArabic ? 'اكتب الأعراض الإضافية:' : 'Enter additional symptoms:'}</strong>
                        <div style="margin-top: 20px;">
                            <input type="text" id="additionalSymptomsInput" 
                                   placeholder="${isArabic ? 'اكتب الأعراض هنا...' : 'Type symptoms here...'}" 
                                   style="width: 100%; padding: 14px 18px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px; background: #f8f9ff; color: #2d3748; font-weight: 500; transition: all 0.3s ease;"
                                   onfocus="this.style.borderColor='#0A76D8'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(10, 118, 216, 0.12)';"
                                   onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8f9ff'; this.style.boxShadow='none';"
                                   onkeypress="if(event.key === 'Enter') submitAdditionalSymptoms()">
                        </div>
                        <div class="more-symptoms-buttons" style="margin-top: 25px;">
                            <button class="btn-yes" onclick="submitAdditionalSymptoms()" style="flex: 1;">
                                ${isArabic ? 'إرسال' : 'Submit'}
                            </button>
                        </div>
                    `;
                    container.appendChild(symptomsInputCard);
                    
                    // Focus على input
                    setTimeout(() => {
                        const input = document.getElementById('additionalSymptomsInput');
                        if (input) input.focus();
                    }, 100);
                }
                
                return; // لا نرسل للخادم بعد، ننتظر إدخال الأعراض
            }
            
            // إذا كانت الإجابة "لا"، نرسل مباشرة
            isProcessing = true;
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'message bot';
            loadingDiv.innerHTML = '<div class="message-bubble">⏳ ' + (isArabic ? 'جاري المعالجة...' : 'Processing...') + '</div>';
            document.getElementById('chatMessages').appendChild(loadingDiv);

            try {
                const result = await apiCall('', { intent: intent });
                loadingDiv.remove();
                handleResponse(result);
            } catch (error) {
                loadingDiv.remove();
                addMessage('❌ ' + (isArabic ? 'حدث خطأ' : 'An error occurred'), 'bot');
            } finally {
                isProcessing = false;
            }
        }

        // دالة للتعامل مع إجابة "هل لديك أعراض أخرى؟" بعد checkbox
        async function handleMoreSymptomsAfterCheckbox(intent) {
            if (isProcessing) return;
            
            const isArabic = selectedLanguage === 'ar';
            
            // إخفاء بطاقة السؤال
            const card = document.getElementById('moreSymptomsCard');
            if (card) {
                card.remove();
            }
            
            // إذا كانت الإجابة "نعم"، نعرض input فقط (لا نرسل)
            if (intent === 'more_yes') {
                const container = document.getElementById('questionsContainer');
                if (container) {
                    container.innerHTML = '';
                    container.style.display = 'block';
                    
                    const symptomsInputCard = document.createElement('div');
                    symptomsInputCard.className = 'more-symptoms-card';
                    symptomsInputCard.id = 'symptomsInputCard';
                    symptomsInputCard.style.margin = '0';
                    symptomsInputCard.style.padding = '20px';
                    symptomsInputCard.innerHTML = `
                        <strong>${isArabic ? 'اكتب الأعراض الإضافية (اختياري):' : 'Enter additional symptoms (optional):'}</strong>
                        <div style="margin-top: 20px;">
                            <input type="text" id="additionalSymptomsInput" 
                                   placeholder="${isArabic ? 'اكتب الأعراض هنا...' : 'Type symptoms here...'}" 
                                   style="width: 100%; padding: 14px 18px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px; background: #f8f9ff; color: #2d3748; font-weight: 500; transition: all 0.3s ease;"
                                   onfocus="this.style.borderColor='#0A76D8'; this.style.background='white'; this.style.boxShadow='0 0 0 4px rgba(10, 118, 216, 0.12)';"
                                   onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8f9ff'; this.style.boxShadow='none';">
                        </div>
                        <div class="more-symptoms-buttons" style="margin-top: 25px;">
                            <button class="btn-yes" onclick="proceedToM2()" style="flex: 1;">
                                ${isArabic ? 'متابعة' : 'Continue'}
                            </button>
                        </div>
                    `;
                    container.appendChild(symptomsInputCard);
                    
                    // Focus على input
                    setTimeout(() => {
                        const input = document.getElementById('additionalSymptomsInput');
                        if (input) input.focus();
                    }, 100);
                }
                return; // لا نرسل للخادم
            }
            
            // إذا كانت الإجابة "لا"، نعرض أسئلة M2 المحفوظة أو نرسل more_no
            if (intent === 'more_no') {
                // إذا كان هناك أسئلة M2 محفوظة، نعرضها مباشرة
                if (window.pendingM2Questions && window.pendingM2Questions.length > 0) {
                    console.log('Showing pending M2 questions');
                    const container = document.getElementById('questionsContainer');
                    if (container) {
                        container.innerHTML = '';
                        container.style.display = 'block';
                    }
                    displayQuestions(window.pendingM2Questions);
                    window.pendingM2Questions = null;
                    return;
                }
                
                // إذا لم يكن هناك أسئلة محفوظة، نرسل more_no
                isProcessing = true;
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'message bot';
                loadingDiv.innerHTML = '<div class="message-bubble">⏳ ' + (isArabic ? 'جاري المعالجة...' : 'Processing...') + '</div>';
                document.getElementById('chatMessages').appendChild(loadingDiv);

                try {
                    const result = await apiCall('', { intent: intent });
                    loadingDiv.remove();
                    handleResponse(result);
                } catch (error) {
                    loadingDiv.remove();
                    addMessage('❌ ' + (isArabic ? 'حدث خطأ' : 'An error occurred'), 'bot');
                } finally {
                    isProcessing = false;
                }
            }
        }

        // دالة للمتابعة إلى M2 بعد كتابة الأعراض (اختياري)
        async function proceedToM2() {
            if (isProcessing) return;
            
            const isArabic = selectedLanguage === 'ar';
            
            // إخفاء بطاقة input
            const container = document.getElementById('questionsContainer');
            if (container) {
                container.innerHTML = '';
                container.style.display = 'block';
            }
            
            // إذا كان هناك أسئلة M2 محفوظة، نعرضها مباشرة
            if (window.pendingM2Questions && window.pendingM2Questions.length > 0) {
                console.log('Showing pending M2 questions after input');
                displayQuestions(window.pendingM2Questions);
                window.pendingM2Questions = null;
                return;
            }
            
            // إذا لم يكن هناك أسئلة محفوظة، نرسل more_no
            isProcessing = true;
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'message bot';
            loadingDiv.innerHTML = '<div class="message-bubble">⏳ ' + (isArabic ? 'جاري المعالجة...' : 'Processing...') + '</div>';
            document.getElementById('chatMessages').appendChild(loadingDiv);

            try {
                const result = await apiCall('', { intent: 'more_no' });
                loadingDiv.remove();
                handleResponse(result);
            } catch (error) {
                loadingDiv.remove();
                addMessage('❌ ' + (isArabic ? 'حدث خطأ' : 'An error occurred'), 'bot');
            } finally {
                isProcessing = false;
            }
        }

        async function submitAdditionalSymptoms() {
            if (isProcessing) return;
            
            const input = document.getElementById('additionalSymptomsInput');
            const symptoms = input ? input.value.trim() : '';
            
            if (!symptoms) {
                const isArabic = selectedLanguage === 'ar';
                alert(isArabic ? 'الرجاء إدخال الأعراض' : 'Please enter symptoms');
                return;
            }
            
            isProcessing = true;
            const isArabic = selectedLanguage === 'ar';
            
            // إخفاء بطاقة input من questionsContainer
            const container = document.getElementById('questionsContainer');
            if (container) {
                container.innerHTML = '';
                container.style.display = 'none';
            }
            
            // إضافة رسالة المستخدم في chatMessages
            const chatMessages = document.getElementById('chatMessages');
            if (chatMessages) {
                chatMessages.style.display = 'block';
                addMessage(symptoms, 'user');
            }
            
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'message bot';
            loadingDiv.innerHTML = '<div class="message-bubble">⏳ ' + (isArabic ? 'جاري المعالجة...' : 'Processing...') + '</div>';
            if (chatMessages) {
                chatMessages.appendChild(loadingDiv);
            }

            try {
                // إرسال الأعراض الإضافية كـ message - بعدها سيظهر أسئلة M2
                const result = await apiCall('', { message: symptoms });
                if (loadingDiv.parentNode) {
                    loadingDiv.remove();
                }
                handleResponse(result);
            } catch (error) {
                if (loadingDiv.parentNode) {
                    loadingDiv.remove();
                }
                if (chatMessages) {
                    addMessage('❌ ' + (isArabic ? 'حدث خطأ' : 'An error occurred'), 'bot');
                }
            } finally {
                isProcessing = false;
            }
        }

        // Full implementation of startChat
        async function startChatFull() {
            // إخفاء زر البدء
            const startContainer = document.getElementById('startChatContainer');
            if (startContainer) {
                startContainer.style.display = 'none';
            }

            // إظهار chat messages container
            const chatMessages = document.getElementById('chatMessages');
            if (chatMessages) {
                chatMessages.style.display = 'block';
                chatMessages.style.padding = '20px';
                chatMessages.style.background = '#f8f9fa';
            }

            // إظهار questions container من بداية الصفحة مباشرة بعد header
            const questionsContainer = document.getElementById('questionsContainer');
            if (questionsContainer) {
                questionsContainer.style.display = 'block';
                questionsContainer.style.marginTop = '0';
                questionsContainer.style.paddingTop = '0';
                questionsContainer.style.marginBottom = '0';
                questionsContainer.style.order = '1';
            }

            try {
                // إعادة تعيين الجلسة أولاً لضمان بدء جديد
                await apiCall('?action=reset', {});
                
                // إرسال اللغة تلقائياً من إعدادات الموقع بدلاً من انتظار سؤال اختيار اللغة
                const currentLang = (document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl') ? 'ar' : 'en';
                selectedLanguage = currentLang;
                
                // إرسال اللغة مباشرة لبدء المحادثة
                const result = await apiCall('', { answers: { user_language: currentLang } });
                console.log('Start chat result:', result);
                handleResponse(result);
            } catch (error) {
                console.error('Start chat error:', error);
                const isArabic = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
                addMessage('❌ ' + (isArabic ? 'حدث خطأ في الاتصال' : 'Connection error'), 'bot');
            }
        }
        window._startChatFull = startChatFull;
        window.startChat = startChatFull;
        window.startChatFull = startChatFull; // جعله متاحاً في onclick

        async function resetChat() {
            sessionId = null;
            currentAnswers = {};
            
            // فحص وجود العناصر قبل الوصول إليها
            const chatMessages = document.getElementById('chatMessages');
            const questionsContainer = document.getElementById('questionsContainer');
            const startContainer = document.getElementById('startChatContainer');
            
            if (chatMessages) {
                chatMessages.innerHTML = '';
                chatMessages.style.display = 'none';
            }
            if (questionsContainer) {
                questionsContainer.innerHTML = '';
                questionsContainer.style.display = 'none';
            }
            if (startContainer) {
                startContainer.style.display = 'flex';
            }

            try {
                await apiCall('?action=reset', {});
            } catch (error) {
                console.error('Reset error:', error);
            }
        }

        // Make functions globally available
        window.resetChat = resetChat;
        window.sendMessage = sendMessage;
        window.handleMoreSymptoms = handleMoreSymptoms;
        window.handleMoreSymptomsAfterCheckbox = handleMoreSymptomsAfterCheckbox;
        window.submitAdditionalSymptoms = submitAdditionalSymptoms;
        window.proceedToM2 = proceedToM2;
        window.submitAnswers = submitAnswers;

        // دالة لحفظ النتيجة في قاعدة البيانات
        async function saveDiagnosisToDatabase(data) {
            try {
                const diagnosis = data.final_diagnosis || {};
                const response = await fetch('chatbot_api.php?action=save_diagnosis', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        disease: diagnosis.disease || '',
                        percentage: diagnosis.percentage || 0,
                        department_id: data.department_id || null,
                        department_name: data.department_name_db || '',
                        nlg_title: data.final_diagnosis_nlg?.title || '',
                        nlg_summary: data.final_diagnosis_nlg?.summary || ''
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    console.log('Diagnosis saved successfully');
                } else {
                    console.error('Failed to save diagnosis:', result.error);
                }
            } catch (error) {
                console.error('Error saving diagnosis:', error);
            }
        }

        // لا نبدأ المحادثة تلقائياً - المستخدم يضغط على زر "ابدأ المحادثة"
    </script>
</body>

</html>
