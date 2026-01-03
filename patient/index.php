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
$stmt = $database->prepare("SELECT * FROM patient WHERE pemail=?");
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();

if ($userrow->num_rows > 0) {
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];
} else {
    header("location: ../login.php");
    exit();
}

date_default_timezone_set('Asia/Amman');
$today = date('Y-m-d');
$patientrow = $database->query("select  * from  patient;");
$doctorrow = $database->query("select  * from  doctor;");
$appointmentrow = $database->query("select  * from  appointment where appodate>='$today';");
$schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");

// Get selected doctor ID from GET parameter or default
$selected_doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;

// Check if patient has any appointments (to show chat button)
$has_appointments = $database->query("SELECT COUNT(*) as count 
                                          FROM appointment a 
                                          INNER JOIN schedule s ON a.scheduleid = s.scheduleid 
                                          WHERE a.pid = $userid AND s.scheduledate >= '$today'")->fetch_assoc()['count'] > 0;

// Get list of doctors that patient has appointments with
$doctors_list = $database->query("SELECT DISTINCT d.docid, d.docname 
                                   FROM doctor d
                                   INNER JOIN schedule s ON CAST(s.docid AS UNSIGNED) = d.docid
                                   INNER JOIN appointment a ON a.scheduleid = s.scheduleid
                                   WHERE a.pid = $userid
                                   ORDER BY d.docname ASC");

// Count total doctors
$doctors_count = $doctors_list->num_rows;
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

    <title><?php echo t('dashboard'); ?></title>
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
            overflow-x: hidden !important;
        }

        .container {
            display: flex !important;
            flex-direction: row !important;
            width: 100% !important;
            height: 100vh !important;
            overflow: hidden !important;
            position: relative !important;
        }

        .menu {
            width: 280px !important;
            min-width: 280px !important;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(15px) !important;
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.08) !important;
            border-right: 1px solid rgba(102, 126, 234, 0.1) !important;
            padding: 20px 0 !important;
            position: relative !important;
            height: 100vh !important;
            overflow-y: auto !important;
            flex-shrink: 0 !important;
            z-index: 100 !important;
            margin: 0 !important;
            border-radius: 0 !important;
        }

        .dash-body {
            flex: 1 !important;
            margin: 20px 20px 0 15px !important;
            padding: 15px !important;
            overflow: visible !important;
            width: auto !important;
            border-radius: 25px 25px 0 0 !important;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .menu-container {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        .menu-row {
            margin: 5px 0 !important;
        }

        .menu-btn {
            padding: 12px 20px !important;
            transition: all 0.3s ease !important;
            border-radius: 12px !important;
            margin: 5px 10px !important;
            background-position: 20px 50% !important;
            background-repeat: no-repeat !important;
            cursor: pointer !important;
        }

        .menu-btn:hover {
            background-color: rgba(74, 49, 185, 0.15) !important;
            transform: translateX(5px) !important;
        }

        .menu-text {
            padding-left: 50px !important;
            font-weight: 600 !important;
            font-size: 16px !important;
            color: #444 !important;
            text-align: left !important;
        }

        .menu-btn:hover .menu-text {
            color: #4a31b9 !important;
        }

        .non-style-link-menu {
            text-decoration: none !important;
            color: inherit !important;
        }

        .menu-active {
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%) !important;
            color: white !important;
        }

        .menu-active .menu-text {
            color: white !important;
        }

        .menu-active .non-style-link-menu {
            color: white !important;
        }

        .profile-container {
            background: rgba(24, 25, 129, 0.1) !important;
            border-radius: 15px !important;
            padding: 15px !important;
            margin: 10px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .profile-container td[width="30%"] {
            width: 30% !important;
            min-width: 30% !important;
            max-width: 30% !important;
        }

        .profile-container img[src*="user.png"] {
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
        }

        .profile-container table {
            width: 100% !important;
        }

        .dash-body {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(15px) !important;
            border-radius: 25px 25px 0 0 !important;
            margin: 20px 20px 0 15px !important;
            padding: 30px !important;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1) !important;
        }

        .filter-container,
        .doctor-header,
        .patient-header {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px) !important;
            border-radius: 20px !important;
            padding: 20px !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(24, 25, 129, 0.15) !important;
        }

        .dashboard-items {
            background: linear-gradient(277deg, #181981ff 0%, #100242ff 100%) !important;
            border-radius: 15px !important;
            color: white !important;
            box-shadow: 0 8px 25px rgba(24, 25, 129, 0.3) !important;
            transition: all 0.4s ease !important;
            border: none !important;
            padding: 15px !important;
            min-height: 100px !important;
            align-items: center !important;
        }

        .dashboard-items:hover {
            transform: translateY(-8px) scale(1.02) !important;
            box-shadow: 0 15px 40px rgba(24, 25, 129, 0.4) !important;
        }

        .h1-dashboard {
            color: white !important;
            font-weight: 900 !important;
            font-size: 32px !important;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2) !important;
            line-height: 1.2 !important;
        }

        .h3-dashboard {
            color: rgba(255, 255, 255, 0.95) !important;
            font-weight: 600 !important;
            font-size: 13px !important;
        }

        .btn-primary,
        .login-btn,
        .btn-primary-soft {
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%) !important;
            border: none !important;
            border-radius: 25px !important;
            padding: 12px 30px !important;
            color: white !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 15px rgba(74, 49, 185, 0.3) !important;
            transition: all 0.3s ease !important;
        }

        .btn-primary:hover,
        .login-btn:hover,
        .btn-primary-soft:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 12px 30px rgba(74, 49, 185, 0.4) !important;
        }

        .sub-table {
            background: white !important;
            border-radius: 15px !important;
            overflow: hidden !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08) !important;
            border-collapse: collapse !important;
            display: table !important;
            width: 100% !important;
        }

        .table-headin {
            background: linear-gradient(277deg, #181981ff 0%, #100242ff 100%) !important;
            color: white !important;
            font-weight: 700 !important;
            padding: 15px 12px !important;
            text-align: left !important;
            font-size: 14px !important;
        }

        .sub-table tbody tr {
            transition: all 0.3s ease !important;
            border-bottom: 1px solid rgba(24, 25, 129, 0.1) !important;
        }

        .sub-table tbody tr:last-child {
            border-bottom: none !important;
        }

        .sub-table tbody tr:hover {
            background: rgba(24, 25, 129, 0.08) !important;
        }

        .sub-table tbody td {
            padding: 15px 12px !important;
            font-size: 14px !important;
            color: #333 !important;
        }

        .abc.scroll {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px) !important;
            border-radius: 20px !important;
            padding: 15px !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(102, 126, 234, 0.1) !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .input-text {
            border-radius: 12px !important;
            border: 2px solid rgba(24, 25, 129, 0.2) !important;
            padding: 12px 18px 12px 45px !important;
            transition: all 0.3s ease !important;
        }

        .input-text:focus {
            outline: none !important;
            border-color: #4a31b9 !important;
            box-shadow: 0 0 0 3px rgba(74, 49, 185, 0.1) !important;
        }

        input[type="search"] {
            padding-left: 45px !important;
        }

        .nav-bar {
            background: transparent !important;
        }

        .nav-bar p {
            font-size: 28px !important;
            font-weight: 900 !important;
            background: linear-gradient(135deg, #4a31b9 0%, #0c0242 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.2 !important;
        }

        .dash-body>table>tbody>tr:first-child td {
            padding-top: 0 !important;
            padding-bottom: 10px !important;
        }

        .dash-body>table>tbody>tr:first-child {
            margin-bottom: 10px !important;
        }

        #chatButton {
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%) !important;
            box-shadow: 0 8px 25px rgba(74, 49, 185, 0.4) !important;
            transition: all 0.3s ease !important;
        }


        #chatButton:hover {
            transform: scale(1.1) !important;
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.6) !important;
        }


        #chatPopup {
            border-radius: 20px !important;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2) !important;
            border: 1px solid rgba(102, 126, 234, 0.2) !important;
        }


        #chatHeader {
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%) !important;
            border-radius: 20px 20px 0 0 !important;
        }

        h1,
        h3 {
            color: #333 !important;
        }

        h1 {
            font-size: 28px !important;
            font-weight: 800 !important;
            background: linear-gradient(135deg, #4a31b9 0%, #0c0242 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            margin: 5px 0 !important;
            line-height: 1.2 !important;
        }

        h3 {
            font-size: 20px !important;
            font-weight: 700 !important;
            color: #4a31b9 !important;
            margin: 5px 0 !important;
        }

        p {
            color: #666 !important;
            line-height: 1.7 !important;
        }

        .non-style-link {
            color: #4a31b9 !important;
            transition: color 0.3s !important;
        }

        .non-style-link:hover {
            color: #0c0242 !important;
        }

        table[width="100%"] {
            display: table !important;
            width: 100% !important;
        }

        table[width="100%"] td[width="50%"] {
            display: table-cell !important;
            width: 50% !important;
            vertical-align: top !important;
        }

        .dash-body table tr td {
            display: table-cell !important;
        }

        .dash-body table tr {
            display: table-row !important;
        }

        .dash-body table[width="100%"] tr td[width="50%"] {
            display: table-cell !important;
            width: 50% !important;
            vertical-align: top !important;
        }

        .filter-container {
            width: 100% !important;
        }

        .filter-container table {
            width: 100% !important;
            table-layout: fixed !important;
            border-spacing: 10px !important;
            border-collapse: separate !important;
        }

        .filter-container table td[style*="width: 25%"] {
            width: 25% !important;
            display: table-cell !important;
            padding: 5px !important;
            vertical-align: top !important;
        }

        .dashboard-items {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        .dashboard-items>div:first-child {
            flex: 1 !important;
            min-width: 0 !important;
        }

        .dashboard-icons {
            flex-shrink: 0 !important;
            opacity: 0.9 !important;
            background-color: rgba(255, 255, 255, 0.2) !important;
            border-radius: 50% !important;
            padding: 8px !important;
            width: 32px !important;
            height: 32px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-size: 16px 16px !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            margin-left: 10px !important;
        }

        .dash-body>table>tbody>tr>td[width="50%"] {
            display: table-cell !important;
            width: 50% !important;
            vertical-align: top !important;
            padding: 10px !important;
        }


        .dash-body table[width="100%"] {
            table-layout: auto !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        .dash-body table[width="100%"] tr td[width="50%"] {
            display: table-cell !important;
            width: 50% !important;
            max-width: 50% !important;
            vertical-align: top !important;
        }

        .dash-body table[style*="border-collapse"] {
            display: table !important;
            width: 100% !important;
        }

        .dash-body table[style*="border-collapse"] tr {
            display: table-row !important;
        }

        .dash-body table[style*="border-collapse"] tr td {
            display: table-cell !important;
        }

        .dashbord-tables {
            animation: transitionIn-Y-over 0.5s;
        }

        .filter-container {
            animation: transitionIn-Y-bottom 0.5s;
        }

        .sub-table,
        .anime {
            animation: transitionIn-Y-bottom 0.5s;
        }

        #chatButton {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 24px;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            text-align: center;
            line-height: 60px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            z-index: 999;
        }

        /* Move chat button to left side for Arabic (RTL) */
        [dir="rtl"] #chatButton {
            right: auto;
            left: 25px;
        }

        #chatButton .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }

        /* Move notification badge to left side for Arabic (RTL) */
        [dir="rtl"] #chatButton .notification-badge {
            right: auto;
            left: -5px;
        }

        #chatPopup {
            display: none;
            position: fixed;
            bottom: 120px;
            right: 40px;
            width: 380px;
            height: 550px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        /* Move chat popup to left side for Arabic (RTL) */
        [dir="rtl"] #chatPopup {
            right: auto;
            left: 40px;
        }

        #chatHeader {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: nowrap;
        }

        #chatHeader>div {
            flex: 1;
            min-width: 0;
        }

        #chatHeader h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
            font-weight: 700;
            white-space: nowrap;
            color: white !important;
        }

        #chatHeader button {
            background: transparent;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            padding: 0;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            flex-shrink: 0;
            margin-left: 15px;
            line-height: 1;
        }

        #chatHeader button:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        [dir="rtl"] #chatHeader button {
            margin-left: 0;
            margin-right: 15px;
        }

        /* Book Appointment Button in Chat */
        #chatPopup .btn-primary,
        #chatPopup button.btn-primary {
            display: inline-block !important;
            width: auto !important;
            min-width: fit-content !important;
            padding: 12px 30px !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            border-radius: 25px !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            border: none !important;
            cursor: pointer !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
            transition: all 0.3s ease !important;
            text-decoration: none !important;
            white-space: nowrap !important;
        }

        #chatPopup .btn-primary:hover,
        #chatPopup button.btn-primary:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5) !important;
        }

        .doctor-selector-chat {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .doctor-select-chat {
            padding: 8px 15px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            outline: none;
            flex: 1;
        }

        .doctor-select-chat option {
            background: #4a31b9 !important;
            color: white !important;
            padding: 10px !important;
            font-weight: 600 !important;
        }

        .doctor-select-chat option:first-child {
            background: #667eea !important;
            color: white !important;
        }

        .doctor-select-chat option.has-unread {
            font-weight: 700 !important;
            background: #ff4444 !important;
            color: #ffffff !important;
        }

        .doctor-select-chat option:hover {
            background: #5a41c9 !important;
        }

        /* For selected option with unread messages */
        .doctor-select-chat option.has-unread:checked {
            background: rgba(255, 68, 68, 1) !important;
        }

        .doctor-select-chat option:first-child {
            background: rgba(255, 255, 255, 0.3);
        }

        #chatBox {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background: linear-gradient(277deg, #181981ff 0%, #100242ff 100%);
            display: flex;
            flex-direction: column;
            gap: 10px;
            justify-content: flex-start;
            align-items: stretch;
            min-height: 0;
        }

        #chatBox:empty {
            justify-content: center;
            align-items: center;
            background: linear-gradient(277deg, #181981ff 0%, #100242ff 100%);
        }

        #chatBox .message-container {
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
            min-height: 100%;
        }

        #chatBox .message {
            margin: 8px 0;
        }

        #chatBox .message:first-child {
            margin-top: 0;
        }

        #chatBox .message:last-child {
            margin-bottom: 0;
        }

        /* Empty chat design - show beautiful design when no messages */

        .message {
            padding: 12px 18px;
            border-radius: 18px;
            max-width: 75%;
            word-wrap: break-word;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .message.sent {
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%) !important;
            color: white !important;
            align-self: flex-end !important;
            margin-left: auto !important;
            margin-right: 0 !important;
            border-bottom-right-radius: 4px;
        }

        .message.received {
            background: white !important;
            color: #333 !important;
            align-self: flex-start !important;
            margin-left: 0 !important;
            margin-right: auto !important;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .message-time {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 5px;
        }

        .chat-input-container {
            padding: 20px 25px;
            background: white;
            border-top: 1px solid rgba(102, 126, 234, 0.1);
            display: flex;
            gap: 10px;
            align-items: center;
        }

        #messageInput {
            flex: 1;
            padding: 12px 18px;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 25px;
            font-size: 14px;
            outline: none;
        }

        #messageInput:focus {
            border-color: #4a31b9;
            box-shadow: 0 0 0 3px rgba(74, 49, 185, 0.1);
        }

        .send-btn-chat {
            padding: 12px 25px;
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%);
            color: white;
            border: none;
            border-radius: 25px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(74, 49, 185, 0.3);
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: rgba(255, 255, 255, 0.98);
            border: none;
            padding: 12px 15px;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            font-size: 24px;
        }

        .menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .menu-overlay.active {
            display: block;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .dash-body {
                margin: 15px !important;
                padding: 15px !important;
            }

            .filter-container {
                overflow-x: auto;
            }

            .dashboard-items {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .menu {
                position: fixed !important;
                left: -280px !important;
                transition: left 0.3s ease !important;
                z-index: 1000 !important;
                height: 100vh !important;
            }

            .menu.active {
                left: 0 !important;
            }

            .menu-overlay {
                display: block;
            }

            .menu-overlay.active {
                display: block;
            }

            .dash-body {
                margin: 10px !important;
                padding: 10px !important;
                width: 100% !important;
            }

            .container {
                flex-direction: column !important;
            }

            .dashboard-items {
                grid-template-columns: 1fr !important;
                gap: 15px !important;
            }

            .dashboard-items td {
                padding: 15px !important;
            }

            .filter-container {
                padding: 15px !important;
            }

            .header-search {
                flex-direction: column !important;
                gap: 10px !important;
            }

            .header-searchbar {
                width: 100% !important;
            }

            .filter-container-items {
                width: 100% !important;
                margin: 5px 0 !important;
            }

            #chatPopup {
                width: 90% !important;
                max-width: 90% !important;
                height: 80vh !important;
                right: 5% !important;
                left: 5% !important;
            }
        }


        /* إشعارات القائمة الجانبية */
        .menu-notif-badge {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            background: #e74c3c;
            color: white;
            font-size: 11px;
            font-weight: bold;
            min-width: 20px;
            height: 20px;
            border-radius: 10px;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 2px 6px;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.5);
            animation: pulseBadge 1.5s infinite;
            z-index: 10;
        }

        [dir="rtl"] .menu-notif-badge {
            right: auto;
            left: 12px;
        }

        .menu-row {
            position: relative;
        }

        @keyframes pulseBadge {

            0%,
            100% {
                transform: translateY(-50%) scale(1);
            }

            50% {
                transform: translateY(-50%) scale(1.1);
            }
        }

        @media (max-width: 480px) {
            .menu {
                width: 100% !important;
            }

            .dash-body {
                margin: 5px !important;
                padding: 10px !important;
            }

            .dashboard-items {
                gap: 10px !important;
            }

            .dashboard-items td {
                padding: 12px !important;
            }

            .h1-dashboard {
                font-size: 28px !important;
            }

            .h3-dashboard {
                font-size: 14px !important;
            }

            .profile-container {
                padding: 10px !important;
            }

            .menu-btn {
                padding: 10px 15px !important;
            }

            .menu-text {
                padding-left: 40px !important;
                font-size: 14px !important;
            }

            #chatPopup {
                width: 95% !important;
                height: 85vh !important;
                right: 2.5% !important;
                left: 2.5% !important;
            }
        }

        .language-switcher-header {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1001;
        }

        [dir="rtl"] .language-switcher-header {
            right: auto !important;
            left: 15px !important;
        }

        /* Ensure language switcher has same style as main page */
        .language-switcher-header .language-switcher {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* RTL Menu adjustments - Icons on right, text beside them */
        [dir="rtl"] .menu-btn {
            background-position: calc(100% - 20px) 50% !important;
            text-align: right !important;
        }

        [dir="rtl"] .menu-text {
            padding-left: 0 !important;
            padding-right: 50px !important;
            text-align: right !important;
        }

        [dir="rtl"] .menu-btn:hover {
            transform: translateX(-5px) !important;
        }

        /* RTL Table adjustments - Text starts from right */
        [dir="rtl"] .sub-table th,
        [dir="rtl"] .sub-table td {
            text-align: right !important;
        }

        [dir="rtl"] table th,
        [dir="rtl"] table td {
            text-align: right !important;
        }
    </style>
</head>

<body>
    <div class="language-switcher-header">
        <?php include("../language-switcher.php"); ?>
    </div>
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    <div class="menu-overlay" id="menuOverlay" onclick="toggleMenu()"></div>
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
                    <td class="menu-btn menu-icon-home menu-active menu-icon-home-active">
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active">
                            <div style="position: relative;">
                                <p class="menu-text"><?php echo t('home'); ?></p>
                                <span class="menu-notif-badge" id="homeChatBadge"></span>
                            </div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu">
                            <div>
                                <p class="menu-text"><?php echo t('all_doctors'); ?></p>
                        </a>
        </div>
        </td>
        </tr>

        <tr class="menu-row">
            <td class="menu-btn menu-icon-session">
                <a href="schedule.php" class="non-style-link-menu">
                    <div>
                        <p class="menu-text"><?php echo t('schedule'); ?></p>
                    </div>
                </a>
            </td>
        </tr>
        <tr class="menu-row">
            <td class="menu-btn menu-icon-appoinment">
                <a href="appointment.php" class="non-style-link-menu">
                    <div>
                        <p class="menu-text"><?php echo t('my_appointments'); ?></p>
                </a>
    </div>
    </td>
    </tr>
    <tr class="menu-row">
        <td class="menu-btn menu-icon-appoinment">
            <a href="specialties.php" class="non-style-link-menu">
                <div>
                    <p class="menu-text"><?php echo t('specialties'); ?></p>
            </a></div>
        </td>
    </tr>
    <tr class="menu-row">
        <td class="menu-btn menu-icon-ai">
            <a href="chatbot.php" class="non-style-link-menu">
                <div>
                    <p class="menu-text"><?php echo isArabic() ? 'مساعد' : 'Chat Bot'; ?></p>
            </a></div>
        </td>
    </tr>
    <tr class="menu-row">
        <td class="menu-btn menu-icon-settings">
            <a href="settings.php" class="non-style-link-menu">
                <div style="position: relative;">
                    <p class="menu-text"><?php echo t('settings'); ?></p>
                    <span class="menu-notif-badge" id="settingsNotifBadge"></span>
                </div>
            </a>
        </td>
    </tr>

    </table>
    </div>
    <div class="dash-body" style="margin-top: 15px">
        <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;">

            <tr>

                <td class="nav-bar" style="width: 50%; vertical-align: middle;">
                    <p style="font-size: 32px;padding-left:12px;font-weight: 900;margin: 0;line-height: 1.2;">
                        <?php echo t('home'); ?>
                    </p>

                </td>
                <td width="25%" style="text-align: right; vertical-align: middle;">

                </td>
                <td width="15%" style="text-align: right; vertical-align: middle; padding-right: 10px;">
                    <p
                        style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: <?php echo isArabic() ? 'left' : 'right'; ?>; margin-bottom: 5px;">
                        <?php echo t('todays_date'); ?>
                    </p>
                    <p class="heading-sub12"
                        style="padding: 0;margin: 0;font-size: 16px;font-weight: 600;color: #4a31b9;">
                        <?php echo $today; ?>
                    </p>
                </td>
                <td width="10%" style="text-align: center; vertical-align: middle; padding-left: 5px;">
                    <button class="btn-label"
                        style="display: flex;justify-content: center;align-items: center;background: transparent;border: none;cursor: pointer;"><img
                            src="../img/calendar.svg" width="100%"></button>
                </td>


            </tr>
            <tr>
                <td colspan="4">

                    <center>
                        <table class="filter-container doctor-header patient-header" style="border: none;width:95%"
                            border="0">
                            <tr>
                                <td>
                                    <h3><?php echo isArabic() ? 'مرحباً!' : 'Welcome!'; ?></h3>
                                    <h1><?php echo $username ?>.</h1>
                                    <p><?php echo isArabic() ? 'ليس لديك فكرة عن الأطباء؟ لا مشكلة، دعنا ننتقل إلى قسم ' : "Haven't any idea about doctors? no problem let's jumping to "; ?>
                                        <a href="doctors.php"
                                            class="non-style-link"><b>"<?php echo t('all_doctors'); ?>"</b></a>
                                        <?php echo isArabic() ? 'أو ' : 'section or '; ?>
                                        <a href="schedule.php"
                                            class="non-style-link"><b>"<?php echo t('schedule'); ?>"</b> </a><br>
                                        <?php echo isArabic() ? 'تتبع تاريخ مواعيدك السابقة والمستقبلية.' : 'Track your past and future appointments history.'; ?><br>
                                    </p>

                                    <form action="schedule.php" method="post" style="display: flex">

                                        <input type="search" name="search" class="input-text "
                                            placeholder="<?php echo isArabic() ? 'ابحث عن الطبيب وسنجد الجلسات المتاحة' : 'Search Doctor and We will Find The Session Available'; ?>"
                                            list="doctors" style="width:45%;">&nbsp;&nbsp;

                                        <?php
                                        echo '<datalist id="doctors">';
                                        $list11 = $database->query("select  docname,docemail from  doctor;");

                                        for ($y = 0; $y < $list11->num_rows; $y++) {
                                            $row00 = $list11->fetch_assoc();
                                            $d = $row00["docname"];

                                            echo "<option value='$d'><br/>";

                                        }
                                        ;

                                        echo ' </datalist>';
                                        ?>


                                        <input type="Submit" value="<?php echo t('search'); ?>"
                                            class="login-btn btn-primary btn"
                                            style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">

                                        <br>
                                        <br>

                                </td>
                            </tr>
                        </table>
                    </center>

                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 0;">
                    <table border="0" width="100%" style="border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <td width="50%"
                                style="width: 50% !important; display: table-cell !important; vertical-align: top !important; padding-right: 10px;">






                                <center>
                                    <table class="filter-container" style="border: none;" border="0">
                                        <tr>
                                            <td colspan="4">
                                                <p style="font-size: 20px;font-weight:600;padding-left: 12px;">
                                                    <?php echo t('status'); ?>
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%; padding: 5px;">
                                                <div class="dashboard-items"
                                                    style="padding:15px;margin:auto;width:100%;display: flex; align-items: center; justify-content: space-between;">
                                                    <div style="flex: 1;">
                                                        <div class="h1-dashboard">
                                                            <?php echo $doctorrow->num_rows ?>
                                                        </div>
                                                        <div class="h3-dashboard">
                                                            <?php echo t('total_doctors'); ?>
                                                        </div>
                                                    </div>
                                                    <div class="btn-icon-back dashboard-icons"
                                                        style="background-image: url('../img/icons/doctors-hover.svg'); width: 20px; height: 20px; background-size: contain; background-repeat: no-repeat; flex-shrink: 0; margin-left: 10px;">
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="width: 25%; padding: 5px;">
                                                <div class="dashboard-items"
                                                    style="padding:15px;margin:auto;width:100%;display: flex; align-items: center; justify-content: space-between;">
                                                    <div style="flex: 1;">
                                                        <div class="h1-dashboard">
                                                            <?php echo $patientrow->num_rows ?>
                                                        </div>
                                                        <div class="h3-dashboard">
                                                            <?php echo t('total_patients'); ?>
                                                        </div>
                                                    </div>
                                                    <div class="btn-icon-back dashboard-icons"
                                                        style="background-image: url('../img/icons/patients-hover.svg'); width: 20px; height: 20px; background-size: contain; background-repeat: no-repeat; flex-shrink: 0; margin-left: 10px;">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%; padding: 5px;">
                                                <div class="dashboard-items"
                                                    style="padding:15px;margin:auto;width:100%;display: flex; align-items: center; justify-content: space-between;">
                                                    <div style="flex: 1;">
                                                        <div class="h1-dashboard">
                                                            <?php echo $appointmentrow->num_rows ?>
                                                        </div>
                                                        <div class="h3-dashboard">
                                                            <?php echo t('total_appointments'); ?>
                                                        </div>
                                                    </div>
                                                    <div class="btn-icon-back dashboard-icons"
                                                        style="background-image: url('../img/icons/book-hover.svg'); width: 20px; height: 20px; background-size: contain; background-repeat: no-repeat; flex-shrink: 0; margin-left: 10px;">
                                                    </div>
                                                </div>

                                            </td>

                                            <td style="width: 25%; padding: 5px;">
                                                <div class="dashboard-items"
                                                    style="padding:15px;margin:auto;width:100%;display: flex; align-items: center; justify-content: space-between;">
                                                    <div style="flex: 1;">
                                                        <div class="h1-dashboard">
                                                            <?php echo $schedulerow->num_rows ?>
                                                        </div>
                                                        <div class="h3-dashboard">
                                                            <?php echo t('today_sessions'); ?>
                                                        </div>
                                                    </div>
                                                    <div class="btn-icon-back dashboard-icons"
                                                        style="background-image: url('../img/icons/session-iceblue.svg'); width: 20px; height: 20px; background-size: contain; background-repeat: no-repeat; flex-shrink: 0; margin-left: 10px;">
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>
                                    </table>
                                </center>








                            </td>
                            <td width="50%"
                                style="width: 50% !important; display: table-cell !important; vertical-align: top !important; padding-left: 10px; padding-top: 0;">



                                <?php
                                $nextweek = date("Y-m-d", strtotime("+1 week"));
                                $sqlmain = "select * from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  where  patient.pid=$userid  and schedule.scheduledate>='$today' order by schedule.scheduledate asc";
                                $result = $database->query($sqlmain);
                                $has_bookings = ($result && $result->num_rows > 0);
                                ?>
                                <div style="margin-bottom: 15px;">
                                    <p style="font-size: 20px;font-weight:600;padding-left: 0;margin: 0 0 15px 0;"
                                        class="anime">
                                        <?php echo isArabic() ? 'حجوزاتك القادمة' : 'Your Upcoming Booking'; ?>
                                    </p>
                                </div>
                                <center>
                                    <?php
                                    $scroll_style = $has_bookings ? "max-height: 200px;padding: 0;margin: 0;overflow-y: auto;" : "padding: 0;margin: 0;overflow: visible;";
                                    ?>
                                    <div class="abc scroll" style="<?php echo $scroll_style; ?>">
                                        <table width="100%" class="sub-table scrolldown" border="0">
                                            <thead>

                                                <tr>
                                                    <th class="table-headin">


                                                        <?php echo t('appointment_number'); ?>

                                                    </th>
                                                    <th class="table-headin">


                                                        <?php echo t('title'); ?>

                                                    </th>

                                                    <th class="table-headin">
                                                        <?php echo t('doctors'); ?>
                                                    </th>
                                                    <th class="table-headin">

                                                        <?php echo t('session_date_time'); ?>

                                                    </th>

                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php
                                                if ($result->num_rows == 0) {
                                                    echo '<tr>
                                                    <td colspan="4" style="padding: 20px;">
                                                    <center>
                                                    <img src="../img/notfound.svg" width="20%" style="margin-bottom: 10px;">
                                                    <p class="heading-main12" style="font-size:16px;color:rgb(49, 49, 49);margin: 10px 0;">' . t('no_results') . '</p>
                                                    <a class="non-style-link" href="schedule.php"><button  class="login-btn btn-primary btn"  style="padding: 10px 25px; font-size: 14px; margin-top: 10px;">&nbsp; ' . t('book_appointment') . ' &nbsp;</button>
                                                    </a>
                                                    </center>
                                                    </td>
                                                    </tr>';

                                                } else {
                                                    for ($x = 0; $x < $result->num_rows; $x++) {
                                                        $row = $result->fetch_assoc();
                                                        $scheduleid = $row["scheduleid"];
                                                        $title = $row["title"];
                                                        $apponum = $row["apponum"];
                                                        $docname = $row["docname"];
                                                        $scheduledate = $row["scheduledate"];
                                                        $scheduletime = $row["scheduletime"];

                                                        echo '<tr>
                                                        <td style="padding:15px 12px;font-size:14px;font-weight:700;color:#333;">' .
                                                            $apponum
                                                            . '</td>
                                                        <td style="padding:15px 12px;font-size:14px;color:#333;">' .
                                                            substr($title, 0, 30)
                                                            . '</td>
                                                        <td style="padding:15px 12px;font-size:14px;color:#333;">
                                                        ' . substr($docname, 0, 20) . '
                                                        </td>
                                                        <td style="padding:15px 12px;font-size:14px;color:#333;text-align:left;">
                                                            ' . substr($scheduledate, 0, 10) . ' ' . substr($scheduletime, 0, 5) . '
                                                        </td>
                                                    </tr>';

                                                    }
                                                }

                                                ?>

                                            </tbody>

                                        </table>
                                    </div>
                                </center>
                            </td>







                </td>
            </tr>
        </table>
        </td>
        <tr>
            </table>
    </div>
    </div>
    <div id="chatButton" title="Chat With Us">
        💬
        <span id="chatNotificationBadge" class="notification-badge" style="display:none;">0</span>
    </div>

    <div id="chatPopup" style="display:none;">
        <div id="chatHeader">
            <div>
                <h3>💬 <?php echo isArabic() ? 'الدردشة المباشرة' : 'Live Chat'; ?></h3>
                <?php if ($doctors_count > 0): ?>
                    <div class="doctor-selector-chat">
                        <select id="doctorSelectChat" class="doctor-select-chat" onchange="changeDoctorChat()">
                            <option value="0"><?php echo t('select_doctor'); ?></option>
                            <?php
                            $doctors_list->data_seek(0); // Reset pointer
                            while ($doctor = $doctors_list->fetch_assoc()) {
                                $selected = ($doctor['docid'] == $selected_doctor_id) ? 'selected' : '';
                                echo "<option value='{$doctor['docid']}' data-doctor-id='{$doctor['docid']}' data-original-text='{$doctor['docname']}' $selected>{$doctor['docname']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" id="closeChat">&times;</button>
        </div>
        <div id="chatBox"></div>
        <div class="chat-input-container">
            <input type="text" id="messageInput" placeholder="<?php echo t('write_your_message'); ?>">
            <button type="button" class="send-btn-chat"
                onclick="sendChatMessage(event)"><?php echo t('send'); ?></button>
        </div>
    </div>

    <script>
        const chatBtn = document.getElementById('chatButton');
        const chatPopup = document.getElementById('chatPopup');
        const closeChat = document.getElementById('closeChat');
        const sender = <?php echo $userid; ?>;
        let receiver = <?php echo $selected_doctor_id; ?>;

        if (closeChat) {
            closeChat.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                chatPopup.style.display = 'none';
                if (chatUpdateInterval) {
                    clearInterval(chatUpdateInterval);
                    chatUpdateInterval = null;
                }
                return false;
            });
        }

        function changeDoctorChat() {
            const select = document.getElementById('doctorSelectChat');
            receiver = parseInt(select.value);
            if (receiver > 0) {
                // Hide badges when doctor is selected
                const chatBadge = document.getElementById('chatNotificationBadge');
                const homeBadge = document.getElementById('homeChatBadge');
                if (chatBadge) chatBadge.style.display = 'none';
                if (homeBadge) homeBadge.style.display = 'none';

                loadChatMessages();

                // Remove unread indicator from selected doctor BEFORE marking as read
                const selectedOption = select.querySelector(`option[value="${receiver}"]`);
                if (selectedOption) {
                    const originalText = selectedOption.getAttribute('data-original-text') || selectedOption.textContent.replace(/\s*\(\d+\)\s*$/, '').trim();
                    selectedOption.classList.remove('has-unread');
                    selectedOption.textContent = originalText;
                }

                // Now mark as read
                markAsRead();

                // Update notifications
                setTimeout(() => {
                    checkChatNotifications();
                    updateUnreadIndicators();
                }, 400);
            } else {
                showEmptyChatDesign();
            }
        }

        let lastMessageCount = 0;
        let isScrolledToBottom = true;

        function showEmptyChatDesign() {
            const chatBox = document.getElementById('chatBox');
            <?php if ($doctors_count == 0): ?>
                chatBox.innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%; min-height: 100%; padding: 40px 25px; text-align: center;">
            <div style="background: white; border-radius: 20px; padding: 35px 25px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15); max-width: 320px; width: 100%; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -20px; left: -20px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(118, 75, 162, 0.1) 0%, transparent 70%); border-radius: 50%;"></div>
                <div style="font-size: 56px; margin-bottom: 15px; animation: float 3s ease-in-out infinite; filter: drop-shadow(0 6px 12px rgba(102, 126, 234, 0.3)); position: relative; z-index: 1;">📅</div>
                <p style="font-size: 16px; font-weight: 800; color: #4a31b9; margin-bottom: 20px; line-height: 1.6; letter-spacing: 0.3px; position: relative; z-index: 1; text-shadow: 0 2px 4px rgba(74, 49, 185, 0.1);"><?php echo t('you_must_book_to_chat'); ?></p>
                <a href="schedule.php" class="non-style-link" style="position: relative; z-index: 1; display: inline-block;">
                    <button class="btn-primary" style="box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4), 0 4px 10px rgba(118, 75, 162, 0.2); transform: scale(1); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); font-size: 15px; padding: 14px 35px;">
                        <?php echo t('book_appointment'); ?>
                    </button>
                </a>
            </div>
        </div>
    `;
            <?php else: ?>
                chatBox.innerHTML = '';
            <?php endif; ?>
        }

        function loadChatMessages() {
            if (receiver <= 0) {
                <?php if ($doctors_count == 0): ?>
                    showEmptyChatDesign();
                <?php else: ?>
                    const chatBox = document.getElementById('chatBox');
                    chatBox.innerHTML = '';
                <?php endif; ?>
                return;
            }

            // Check if user is at bottom before updating
            const chatBox = document.getElementById('chatBox');
            isScrolledToBottom = (chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 50);

            fetch(`fetch_messages.php?sender=${sender}&receiver=${receiver}`)
                .then(res => res.text())
                .then(data => {
                    // Only update if content changed
                    const currentContent = chatBox.innerHTML;
                    if (currentContent !== data) {
                        chatBox.innerHTML = data;
                        // Only auto-scroll if user was at bottom
                        if (isScrolledToBottom) {
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    }
                })
                .catch(err => {
                    console.error('Error loading messages:', err);
                });
        }

        function markAsRead() {
            if (receiver <= 0) return;
            fetch(`mark_read.php?sender=${receiver}&receiver=${sender}`)
                .then(() => {
                    // Update after delay to ensure DB update completes
                    setTimeout(() => {
                        checkChatNotifications();
                        updateUnreadIndicators();
                    }, 300);
                })
                .catch(err => {
                    console.error('Error marking as read:', err);
                });
        }

        // ✅ متغيرات لتتبع العدد السابق لمنع الوميض
        let lastChatCount = -1;
        let lastAdminCount = -1;

        // ✅ تحديث Badge رسائل الدردشة
        function checkChatNotifications() {
            const chatBadge = document.getElementById('chatNotificationBadge');
            const homeBadge = document.getElementById('homeChatBadge');

            if (!sender || sender <= 0) {
                if (chatBadge) chatBadge.style.display = 'none';
                if (homeBadge) homeBadge.style.display = 'none';
                return;
            }

            fetch(`check_notifications.php?user_id=${sender}&user_type=patient&t=${Date.now()}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return res.json();
                })
                .then(data => {
                    const count = parseInt(data.count) || 0;

                    // ✅ فقط قم بالتحديث إذا تغير عدد الرسائل (لمنع الوميض)
                    if (count !== lastChatCount) {
                        lastChatCount = count;

                        // Update chat button badge
                        if (chatBadge) {
                            if (count > 0) {
                                chatBadge.textContent = count > 99 ? '99+' : count.toString();
                                chatBadge.style.display = 'flex';
                            } else {
                                chatBadge.style.display = 'none';
                            }
                        }

                        // Update home menu badge
                        if (homeBadge) {
                            if (count > 0) {
                                homeBadge.textContent = count > 99 ? '99+' : count.toString();
                                homeBadge.style.display = 'flex';
                            } else {
                                homeBadge.style.display = 'none';
                            }
                        }
                    }
                })
                .catch(err => {
                    console.error('Error checking notifications:', err);
                });
        }


        function updateUnreadIndicators() {
            const select = document.getElementById('doctorSelectChat');
            if (!select) return;

            const options = select.querySelectorAll('option[data-doctor-id]');
            options.forEach(option => {
                const doctorId = parseInt(option.getAttribute('data-doctor-id'));
                if (doctorId > 0) {
                    const originalText = option.getAttribute('data-original-text') || option.textContent.replace(/\s*\(\d+\)\s*$/, '').trim();
                    if (!option.getAttribute('data-original-text')) {
                        option.setAttribute('data-original-text', originalText);
                    }

                    // Fetch unread count for this specific doctor
                    fetch(`check_unread_by_doctor.php?user_id=${sender}&doctor_id=${doctorId}&get_count=1`)
                        .then(res => res.json())
                        .then(data => {
                            const count = parseInt(data.count) || 0;

                            if (count > 0) {
                                option.classList.add('has-unread');
                                option.textContent = `${originalText} (${count})`;
                            } else {
                                option.classList.remove('has-unread');
                                option.textContent = originalText;
                            }
                        })
                        .catch(err => {
                            console.error('Error checking unread:', err);
                        });
                }
            });
        }
        // ✅ تحديث Badge رسائل الأدمن في Settings
        function updateAdminMessages() {
            const settingsBadge = document.getElementById('settingsNotifBadge');

            fetch(`get_admin_messages_count.php?pid=${sender}`)
                .then(r => r.json())
                .then(data => {
                    const count = parseInt(data.admin_unread) || 0;

                    if (count !== lastAdminCount) {
                        lastAdminCount = count;

                        if (settingsBadge) {
                            if (data.status === "ok" && count > 0) {
                                settingsBadge.textContent = count > 99 ? '99+' : count.toString();
                                settingsBadge.style.display = "flex";
                            } else {
                                settingsBadge.style.display = "none";
                            }
                        }
                    }
                })
                .catch(err => console.error("❌ خطأ:", err));
        }


        function sendChatMessage(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            <?php if ($doctors_count == 0): ?>
                alert('<?php echo t("you_must_book_to_chat"); ?>');
                return false;
            <?php endif; ?>

            if (receiver <= 0) {
                alert('<?php echo t("please_select_doctor_first"); ?>');
                return false;
            }

            const msg = document.getElementById('messageInput').value.trim();
            if (msg === "") return false;

            const formData = new FormData();
            formData.append('sender', sender);
            formData.append('receiver', receiver);
            formData.append('message', msg);
            formData.append('sender_type', 'patient');
            formData.append('receiver_type', 'doctor');

            fetch('send_message.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('messageInput').value = '';
                        loadChatMessages();
                        checkChatNotifications();
                    } else {
                        console.error('Error sending message:', data.message);
                        alert('Error sending message: ' + (data.message || 'Please try again.'));
                    }
                })
                .catch(err => {
                    console.error('Error sending message:', err);
                    alert('Error sending message. Please try again.');
                });

            return false;
        }

        // Update messages every 2 seconds when popup is open
        let chatUpdateInterval = null;

        if (chatBtn && chatPopup) {
            chatBtn.addEventListener('click', () => {
                const isOpen = chatPopup.style.display === 'flex';
                chatPopup.style.display = isOpen ? 'none' : 'flex';

                if (!isOpen) {
                    // Open chat - DON'T hide badges yet, only when doctor is selected

                    loadChatMessages();

                    // If doctor selected, hide badges and mark as read
                    if (receiver > 0) {
                        // Hide badges when opening with selected doctor
                        const chatBadge = document.getElementById('chatNotificationBadge');
                        const homeBadge = document.getElementById('homeChatBadge');
                        if (chatBadge) chatBadge.style.display = 'none';
                        if (homeBadge) homeBadge.style.display = 'none';

                        const select = document.getElementById('doctorSelectChat');
                        const selectedOption = select.querySelector(`option[value="${receiver}"]`);
                        if (selectedOption) {
                            const originalText = selectedOption.getAttribute('data-original-text') || selectedOption.textContent.replace(/\s*\(\d+\)\s*$/, '').trim();
                            selectedOption.classList.remove('has-unread');
                            selectedOption.textContent = originalText;
                        }
                        markAsRead();
                    }

                    // Update all unread indicators
                    updateUnreadIndicators();

                    // Start interval
                    if (chatUpdateInterval) clearInterval(chatUpdateInterval);
                    chatUpdateInterval = setInterval(() => {
                        if (chatPopup.style.display === 'flex') {
                            loadChatMessages();
                            if (receiver > 0) {
                                markAsRead();
                            } else {
                                checkChatNotifications();
                            }
                            updateUnreadIndicators();
                        }
                    }, 3000);
                } else {
                    // Close chat
                    if (chatUpdateInterval) {
                        clearInterval(chatUpdateInterval);
                        chatUpdateInterval = null;
                    }
                }
            });
        }
        // ✅ تحديث تلقائي كل 3 ثوانٍ
        document.addEventListener("DOMContentLoaded", function () {
            // تحديث فوري عند تحميل الصفحة
            checkChatNotifications();
            updateAdminMessages();

            // تحديث دوري كل 3 ثوانٍ
            setInterval(function () {
                checkChatNotifications();
                updateAdminMessages();
            }, 3000);
        });

        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            // Send message on Enter key
            messageInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendChatMessage(e);
                }
            });
        }

        // Toggle mobile menu
        function toggleMenu() {
            const menu = document.getElementById('sidebarMenu');
            const overlay = document.getElementById('menuOverlay');
            menu.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Close menu when clicking outside on mobile
        document.addEventListener('click', function (event) {
            const menu = document.getElementById('sidebarMenu');
            const toggle = document.querySelector('.menu-toggle');
            const overlay = document.getElementById('menuOverlay');

            if (window.innerWidth <= 768) {
                if (menu && !menu.contains(event.target) && !toggle.contains(event.target) && overlay && overlay.contains(event.target)) {
                    menu.classList.remove('active');
                    overlay.classList.remove('active');
                }
            }
        });

    </script>
</body>

</html>