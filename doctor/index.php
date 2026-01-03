<?php
    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
            header("location: ../login.php");
            exit();
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
        exit();
    }

    include("../connection.php");
    include("../translations.php");
    $userrow = $database->query("select * from doctor where docemail='$useremail'");
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["docid"];
    $username=$userfetch["docname"];

    date_default_timezone_set('Asia/Amman');
    $today = date('Y-m-d');
    $patientrow = $database->query("select  * from  patient;");
    $doctorrow = $database->query("select  * from  doctor;");
    $appointmentrow = $database->query("select  * from  appointment where appodate>='$today';");
    $schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");
    
    // Get selected patient ID from GET parameter or default
    $selected_patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;
    
    // Get list of ALL patients who have messaged this doctor OR have appointments
    // This ensures we show all patients who have communicated with the doctor
    $doctorid_int = intval($userid); // Sanitize to prevent SQL injection
    $patients_query = "SELECT DISTINCT p.pid, p.pname 
                       FROM patient p 
                       LEFT JOIN messages m ON ((p.pid = m.sender_id AND m.receiver_id = ?)
                          OR (p.pid = m.receiver_id AND m.sender_id = ?))
                       LEFT JOIN appointment a ON p.pid = a.pid 
                       LEFT JOIN schedule s ON a.scheduleid = s.scheduleid AND s.docid = ?
                       WHERE (m.sender_id = ? OR m.receiver_id = ? OR s.docid = ?)
                       ORDER BY p.pname ASC";
    $stmt_patients = $database->prepare($patients_query);
    if ($stmt_patients) {
        $stmt_patients->bind_param("iiiiii", $doctorid_int, $doctorid_int, $doctorid_int, $doctorid_int, $doctorid_int, $doctorid_int);
        $stmt_patients->execute();
        $patients_list = $stmt_patients->get_result();
        $patients_count = $patients_list->num_rows;
        $stmt_patients->close();
    } else {
        // Fallback: get patients from messages only
        $patients_list = $database->query("SELECT DISTINCT p.pid, p.pname 
                                           FROM patient p 
                                           LEFT JOIN messages m ON ((p.pid = m.sender_id AND m.receiver_id = $userid)
                                              OR (p.pid = m.receiver_id AND m.sender_id = $userid))
                                           WHERE (m.sender_id = $userid OR m.receiver_id = $userid)
                                           ORDER BY p.pname ASC");
        $patients_count = $patients_list->num_rows;
    }
    
    // Validate that the selected patient has messages or appointments with this doctor
    if ($selected_patient_id > 0) {
        $valid_patient = false;
        $patients_list->data_seek(0);
        while($patient = $patients_list->fetch_assoc()) {
            if ($patient['pid'] == $selected_patient_id) {
                $valid_patient = true;
                break;
            }
        }
        if (!$valid_patient) {
            $selected_patient_id = 0; // Reset to 0 if invalid
        }
    }
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
    <link rel="stylesheet" href="../css/doctor/common.css">
        
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
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .container {
    display: flex !important;
    flex-direction: row !important;
    width: 100% !important;
    min-height: 100vh !important;
    overflow-x: hidden !important;
    overflow-y: auto !important;
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
    border-radius: 25px !important;
    margin: 20px !important;
    padding: 30px !important;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1) !important;
    flex: 1 !important;
    overflow-y: auto !important;
    max-width: calc(100% - 320px) !important;
}
        
        .filter-container, .doctor-header {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px) !important;
            border-radius: 20px !important;
            padding: 30px !important;
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
        
        .dashboard-items {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
        
        .dashboard-items > div:first-child {
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
        
        .btn-primary, .login-btn, .btn-primary-soft {
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%) !important;
            border: none !important;
            border-radius: 25px !important;
            padding: 12px 30px !important;
            color: white !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 15px rgba(74, 49, 185, 0.3) !important;
            transition: all 0.3s ease !important;
        }
        
        .btn-primary:hover, .login-btn:hover, .btn-primary-soft:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 12px 30px rgba(74, 49, 185, 0.4) !important;
        }
        
        .logout-btn {
            width: 100% !important;
            margin-top: 15px !important;
        }
        
        .sub-table {
            background: white !important;
            border-radius: 15px !important;
            overflow: hidden !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08) !important;
        }
        
        .table-headin {
            background: linear-gradient(277deg, #181981ff 0%, #100242ff 100%) !important;
            color: white !important;
            font-weight: 700 !important;
            padding: 15px !important;
        }
        
        .sub-table tbody tr {
            transition: all 0.3s ease !important;
        }
        
        .sub-table tbody tr:hover {
            background: rgba(24, 25, 129, 0.08) !important;
            transform: scale(1.01) !important;
        }
        
        .nav-bar {
            background: transparent !important;
        }
        
        .nav-bar p {
            font-size: 28px !important;
            font-weight: 800 !important;
            background: linear-gradient(135deg, #4a31b9 0%, #0c0242 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
        }
        
        h1, h3 {
            color: #333 !important;
        }
        
        h1 {
            font-size: 36px !important;
            font-weight: 800 !important;
            background: linear-gradient(135deg, #4a31b9 0%, #0c0242 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
        }
        
        h3 {
            font-size: 24px !important;
            font-weight: 700 !important;
            color: #4a31b9 !important;
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
        .dashbord-tables,.doctor-heade{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-Y-bottom  0.5s;
        }
        .sub-table,#anim{
            animation: transitionIn-Y-bottom 0.5s;
        }
        
        #chatButton {
            position: fixed !important;
            bottom: 25px !important;
            right: 25px !important;
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%) !important;
            color: white !important;
            font-size: 24px !important;
            border-radius: 50% !important;
            width: 60px !important;
            height: 60px !important;
            text-align: center !important;
            line-height: 60px !important;
            cursor: pointer !important;
            box-shadow: 0 4px 15px rgba(74, 49, 185, 0.4) !important;
            z-index: 999 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        /* Move chat button to left side for Arabic (RTL) */
        [dir="rtl"] #chatButton {
            right: auto !important;
            left: 25px !important;
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
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
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
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: nowrap;
        }
        
        #chatHeader > div {
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
        
        .patient-selector-chat {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .patient-select-chat {
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
        
  .patient-select-chat option {
    background: #4a31b9;
    color: white;
    padding: 10px;
    transition: all 0.3s ease;
}

.patient-select-chat option.has-unread {
    font-weight: 700 !important;
    background: rgba(255, 68, 68, 0.9) !important;
    color: #ffffff !important;
    text-shadow: 0 0 10px rgba(255, 68, 68, 0.5);
}

/* For selected option with unread messages */
.patient-select-chat option.has-unread:checked {
    background: rgba(255, 68, 68, 1) !important;
}
/* Patient notification badge in dropdown */
.patient-option-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}

.patient-option-badge {
    background: #ff4444;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: bold;
    margin-left: 8px;
    display: inline-block;
}

[dir="rtl"] .patient-option-badge {
    margin-left: 0;
    margin-right: 8px;
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
        
        .message {
            padding: 12px 18px;
            border-radius: 18px;
            max-width: 75%;
            word-wrap: break-word;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
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
        .doctor-heade{
            animation: transitionIn-Y-over 0.5s;
        }
        
      
        
        .menu-row {
            position: relative;
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
    0%, 100% {
        transform: translateY(-50%) scale(1);
    }
    50% {
        transform: translateY(-50%) scale(1.1);
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
            right: auto;
            left: 15px;
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
        
        [dir="rtl"] .menu {
            border-right: none !important;
            border-left: 1px solid rgba(102, 126, 234, 0.1) !important;
            box-shadow: -4px 0 30px rgba(0, 0, 0, 0.08) !important;
        }
        
        [dir="rtl"] .menu-active {
            border-right: none !important;
            border-left: 7px solid var(--primarycolor) !important;
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
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php" ><input type="button" value="<?php echo t('logout'); ?>" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                    </table>
                    </td>
                </tr>
              <tr class="menu-row">
    <td class="menu-btn menu-icon-dashbord menu-active menu-icon-dashbord-active">
        <a href="index.php" class="non-style-link-menu non-style-link-menu-active">
            <div style="position: relative;">
                <p class="menu-text"><?php echo t('dashboard'); ?></p>
                <span class="menu-notif-badge" id="dashboardChatBadge"></span>
            </div>
        </a>
    </td>
</tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('my_appointments'); ?></p></a></div>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('my_sessions'); ?></p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('my_patients'); ?></p></a></div>
                    </td>
                </tr>
                 <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="medical_record.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('medical_record_for_patient'); ?></p></a></div>
                    </td>
                <tr class="menu-row">
    <td class="menu-btn menu-icon-settings">
        <a href="settings.php" class="non-style-link">
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
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
                        
                        <tr >
                            
                            <td colspan="1" class="nav-bar" >
                            <p style="font-size: 23px;padding-left:12px;font-weight: 600;margin-left:20px;">     <?php echo t('dashboard'); ?></p>
                          
                            </td>
                            <td width="25%">

                            </td>
                            <td width="15%">
                                <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: <?php echo isArabic() ? 'left' : 'right'; ?>;">
                                    <?php echo t('todays_date'); ?>
                                </p>
                                <p class="heading-sub12" style="padding: 0;margin: 0;">
                                    <?php echo $today; ?>
                                </p>
                            </td>
                            <td width="10%">
                                <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                            </td>
        
        
                        </tr>
                <tr>
                    <td colspan="4" >
                        
                    <center>
                    <table class="filter-container doctor-header" style="border: none;width:95%" border="0" >
                    <tr>
                        <td >
                            <h3><?php echo t('welcome'); ?></h3>
                            <h1><?php echo $username  ?>.</h1>
                            <p><?php echo t('thanks_for_joining'); ?>
                            </p>
                            <a href="appointment.php" class="non-style-link"><button class="btn-primary btn" style="width:30%"><?php echo t('view_my_appointments'); ?></button></a>
                            <br>
                            <br>
                        </td>
                    </tr>
                    </table>
                    </center>
                    
                </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table border="0" width="100%"">
                            <tr>
                                <td width="50%">

                                    




                                    <center>
                                        <table class="filter-container" style="border: none;" border="0">
                                            <tr>
                                                <td colspan="4">
                                                    <p style="font-size: 20px;font-weight:600;padding-left: 12px;"><?php echo t('status'); ?></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%; padding: 5px;">
                                                    <div  class="dashboard-items"  style="padding:15px;margin:auto;width:100%;display: flex; align-items: center; justify-content: space-between;">
                                                        <div style="flex: 1;">
                                                                <div class="h1-dashboard">
                                                                    <?php    echo $doctorrow->num_rows  ?>
                                                                </div>
                                                                <div class="h3-dashboard">
                                                                    <?php echo t('total_doctors'); ?>
                                                                </div>
                                                        </div>
                                                                <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                                    </div>
                                                </td>
                                                <td style="width: 25%; padding: 5px;">
                                                    <div  class="dashboard-items"  style="padding:15px;margin:auto;width:100%;display: flex; align-items: center; justify-content: space-between;">
                                                        <div style="flex: 1;">
                                                                <div class="h1-dashboard">
                                                                    <?php    echo $patientrow->num_rows  ?>
                                                                </div>
                                                                <div class="h3-dashboard">
                                                                    <?php echo t('total_patients'); ?>
                                                                </div>
                                                        </div>
                                                                <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                                                    </div>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td style="width: 25%; padding: 5px;">
                                                    <div  class="dashboard-items"  style="padding:15px;margin:auto;width:100%;display: flex; align-items: center; justify-content: space-between;">
                                                        <div style="flex: 1;">
                                                                <div class="h1-dashboard" >
                                                                    <?php    echo $appointmentrow ->num_rows  ?>
                                                                </div>
                                                                <div class="h3-dashboard" >
                                                                    <?php echo t('total_appointments'); ?>
                                                                </div>
                                                        </div>
                                                                <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/book-hover.svg');"></div>
                                                    </div>
                                                    
                                                </td>

                                                <td style="width: 25%; padding: 5px;">
                                                    <div  class="dashboard-items"  style="padding:15px;margin:auto;width:100%;display: flex; align-items: center; justify-content: space-between;">
                                                        <div style="flex: 1;">
                                                                <div class="h1-dashboard">
                                                                    <?php    echo $schedulerow ->num_rows  ?>
                                                                </div>
                                                                <div class="h3-dashboard">
                                                                    <?php echo t('today_sessions'); ?>
                                                                </div>
                                                        </div>
                                                                <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/session-iceblue.svg');"></div>
                                                    </div>
                                                </td>
                                                
                                            </tr>
                                        </table>
                                    </center>








                                </td>
                                <td>


                            
                                    <p id="anim" style="font-size: 20px;font-weight:600;padding-left: 40px;"><?php echo t('upcoming_sessions_title'); ?></p>
                                    <center>
                                        <div class="abc scroll" style="height: 250px;padding: 0;margin: 0;">
                                        <table width="85%" class="sub-table scrolldown" border="0" >
                                        <thead>
                                            
                                        <tr>
                                                <th class="table-headin">
                                                    
                                                
                                                <?php echo t('title'); ?>
                                                
                                                </th>
                                                
                                                <th class="table-headin">
                                                <?php echo t('date'); ?>
                                                </th>
                                                <th class="table-headin">
                                                    
                                                     <?php echo t('time'); ?>
                                                    
                                                </th>
                                                    
                                                </tr>
                                        </thead>
                                        <tbody>
                                        
                                            <?php
                                            $nextweek=date("Y-m-d",strtotime("+1 week"));
                                            $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid  where schedule.scheduledate>='$today' and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc"; 
                                                $result= $database->query($sqlmain);
                
                                                if($result->num_rows==0){
                                                    echo '<tr>
                                                    <td colspan="4">
                                                    <br><br><br><br>
                                                    <center>
                                                    <img src="../img/notfound.svg" width="25%">
                                                    
                                                    <br>
                                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">'.t('no_sessions_found').'</p>
                                                    <a class="non-style-link" href="schedule.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; '.t('show_all_sessions').' &nbsp;</font></button>
                                                    </a>
                                                    </center>
                                                    <br><br><br><br>
                                                    </td>
                                                    </tr>';
                                                    
                                                }
                                                else{
                                                for ( $x=0; $x<$result->num_rows;$x++){
                                                    $row=$result->fetch_assoc();
                                                    $scheduleid=$row["scheduleid"];
                                                    $title=$row["title"];
                                                    $docname=$row["docname"];
                                                    $scheduledate=$row["scheduledate"];
                                                    $scheduletime=$row["scheduletime"];
                                                    $nop=$row["nop"];
                                                    echo '<tr>
                                                        <td style="padding:20px;"> &nbsp;'.
                                                        substr($title,0,30)
                                                        .'</td>
                                                        <td style="padding:20px;font-size:13px;">
                                                        '.substr($scheduledate,0,10).'
                                                        </td>
                                                        <td style="text-align:center;">
                                                            '.substr($scheduletime,0,5).'
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
                            </tr>
                        </table>
                    </td>
                <tr>
            </table>
        </div>
    </div>

<div id="chatButton" title="<?php echo t('chat_with_patients'); ?>">
    💬
    <span id="chatNotificationBadge" class="notification-badge" style="display:none;">0</span>
</div>

<div id="chatPopup" style="display:none;">
    <div id="chatHeader">
        <div>
            <h3>💬 <?php echo t('live_chat_with_patients'); ?></h3>
            <?php if ($patients_count > 0): ?>
            <div class="patient-selector-chat">
                <select id="patientSelectChat" class="patient-select-chat" onchange="changePatientChat()">
                    <option value="0"><?php echo t('select_patient'); ?></option>
                    <?php
                    $patients_list->data_seek(0); // Reset pointer
                    while($patient = $patients_list->fetch_assoc()) {
                        $selected = ($patient['pid'] == $selected_patient_id) ? 'selected' : '';
                        echo "<option value='{$patient['pid']}' data-patient-id='{$patient['pid']}' data-original-text='{$patient['pname']}' $selected>{$patient['pname']}</option>";
                    }
                    ?>
                </select>
            </div>
            <?php endif; ?>
        </div>
        <button type="button" id="closeChat">&times;</button>
    </div>
    <div id="chatBox"><?php if ($patients_count == 0): ?>
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%; min-height: 100%; padding: 40px 25px; text-align: center;">
            <div style="background: white; border-radius: 20px; padding: 35px 25px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15); max-width: 320px; width: 100%; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -20px; left: -20px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(118, 75, 162, 0.1) 0%, transparent 70%); border-radius: 50%;"></div>
                <div style="font-size: 56px; margin-bottom: 15px; animation: float 3s ease-in-out infinite; filter: drop-shadow(0 6px 12px rgba(102, 126, 234, 0.3)); position: relative; z-index: 1;">👥</div>
                <p style="font-size: 16px; font-weight: 800; color: #4a31b9; line-height: 1.6; letter-spacing: 0.3px; position: relative; z-index: 1; text-shadow: 0 2px 4px rgba(74, 49, 185, 0.1);"><?php echo t('no_patients_booked'); ?></p>
            </div>
        </div>
    <?php endif; ?></div>
    <div class="chat-input-container" style="<?php echo $patients_count == 0 ? 'display:none;' : ''; ?>">
        <input type="text" id="messageInput" placeholder="<?php echo t('write_your_message'); ?>">
        <button type="button" class="send-btn-chat" onclick="sendChatMessage(event)"><?php echo t('send'); ?></button>
    </div>
</div>

<script>
const chatBtn = document.getElementById('chatButton');
const chatPopup = document.getElementById('chatPopup');
const closeChat = document.getElementById('closeChat');
const sender = <?php echo $userid; ?>;
let receiver = <?php echo $selected_patient_id; ?>;

// Update messages every 2 seconds when popup is open
let chatUpdateInterval = null;

chatBtn.addEventListener('click', () => {
    const isOpen = chatPopup.style.display === 'flex';
    chatPopup.style.display = isOpen ? 'none' : 'flex';
    
    if (!isOpen) {
        // ✅ لا تخفي أي Badge عند فتح صندوق الدردشة فقط
        // سيتم إخفاء Badge فقط عند اختيار مريض
        
        loadChatMessages();
        // Mark messages as read ONLY if patient is selected
        if (receiver > 0) {
            markAsRead();
        }
        // Update unread indicators
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

function changePatientChat() {
    const select = document.getElementById('patientSelectChat');
    
    receiver = parseInt(select.value);
    if (receiver > 0) {
        loadChatMessages();
        
        // ✅ هنا فقط يتم إخفاء Badge عند اختيار مريض محدد
        const chatBadge = document.getElementById('chatNotificationBadge');
        const dashboardBadge = document.getElementById('dashboardChatBadge');
        
        // Mark as read and update notifications
        markAsRead();
        
        // Small delay to ensure DB update completes
        setTimeout(() => {
            checkChatNotifications();
            updateUnreadIndicators();
            
            // ✅ إخفاء Badge بعد تحديث الإشعارات
            if (chatBadge && parseInt(chatBadge.textContent) === 0) {
                chatBadge.style.display = 'none';
            }
            if (dashboardBadge && parseInt(dashboardBadge.textContent) === 0) {
                dashboardBadge.style.display = 'none';
            }
        }, 400);
        
       
    } else {
        <?php if ($patients_count == 0): ?>
        showEmptyChatDesign();
        <?php else: ?>
        const chatBox = document.getElementById('chatBox');
        chatBox.innerHTML = '';
        <?php endif; ?>
    }
}
function showEmptyChatDesign() {
    const chatBox = document.getElementById('chatBox');
    <?php if ($patients_count == 0): ?>
    chatBox.innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%; min-height: 100%; padding: 40px 25px; text-align: center;">
            <div style="background: white; border-radius: 20px; padding: 35px 25px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15); max-width: 320px; width: 100%; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -20px; left: -20px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(118, 75, 162, 0.1) 0%, transparent 70%); border-radius: 50%;"></div>
                <div style="font-size: 56px; margin-bottom: 15px; animation: float 3s ease-in-out infinite; filter: drop-shadow(0 6px 12px rgba(102, 126, 234, 0.3)); position: relative; z-index: 1;">👥</div>
                <p style="font-size: 16px; font-weight: 800; color: #4a31b9; line-height: 1.6; letter-spacing: 0.3px; position: relative; z-index: 1; text-shadow: 0 2px 4px rgba(74, 49, 185, 0.1);"><?php echo t('no_patients_booked'); ?></p>
            </div>
        </div>
    `;
    <?php else: ?>
    chatBox.innerHTML = '';
    <?php endif; ?>
}

let lastMessageCount = 0;
let isScrolledToBottom = true;

function loadChatMessages() {
    if (receiver <= 0) {
        <?php if ($patients_count == 0): ?>
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
            // ✅ تحديث الإشعارات بعد قراءة الرسائل
            setTimeout(() => {
                checkChatNotifications();
                
                // ✅ إخفاء Badge إذا لم تعد هناك رسائل جديدة
                const chatBadge = document.getElementById('chatNotificationBadge');
                const dashboardBadge = document.getElementById('dashboardChatBadge');
                
                if (chatBadge && parseInt(chatBadge.textContent || '0') === 0) {
                    chatBadge.style.display = 'none';
                }
                if (dashboardBadge && parseInt(dashboardBadge.textContent || '0') === 0) {
                    dashboardBadge.style.display = 'none';
                }
            }, 500);
        })
        .catch(err => {
            console.error('Error marking as read:', err);
        });
}
// ✅ متغيرات لتتبع العدد السابق لمنع الوميض
let lastChatCount = -1;
let lastAdminCount = -1;

// ✅ تحديث Badge رسائل الدردشة في Dashboard
function checkChatNotifications() {
    const chatBadge = document.getElementById('chatNotificationBadge');
    const dashboardBadge = document.getElementById('dashboardChatBadge');
    
    if (!sender || sender <= 0) {
        if (chatBadge) chatBadge.style.display = 'none';
        if (dashboardBadge) dashboardBadge.style.display = 'none';
        return;
    }
    
    fetch(`check_notifications.php?user_id=${sender}&user_type=doctor&t=${Date.now()}`)
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
                
                // Update dashboard menu badge
                if (dashboardBadge) {
                    if (count > 0) {
                        dashboardBadge.textContent = count > 99 ? '99+' : count.toString();
                        dashboardBadge.style.display = 'flex';
                    } else {
                        dashboardBadge.style.display = 'none';
                    }
                }
            }
        })
        .catch(err => {
            console.error('Error checking notifications:', err);
        });
}

// ✅ تحديث Badge رسائل الأدمن في Settings
function updateAdminMessages() {
    const settingsBadge = document.getElementById('settingsNotifBadge');
    
    fetch(`get_admin_messages_count.php?docid=${sender}`)
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

function updateUnreadIndicators() {
    const select = document.getElementById('patientSelectChat');
    if (!select) return;
    
    const options = select.querySelectorAll('option[data-patient-id]');
    options.forEach(option => {
        const patientId = parseInt(option.getAttribute('data-patient-id'));
        if (patientId > 0) {
            fetch(`check_unread_by_patient.php?user_id=${sender}&patient_id=${patientId}`)
                .then(res => res.json())
                .then(data => {
                    const originalText = option.getAttribute('data-original-text');
                    
                    if (data.has_unread && data.unread_count > 0) {
                        // Show badge with count
                        option.classList.add('has-unread');
                        option.textContent = `${originalText} (${data.unread_count})`;
                    } else {
                        // Remove badge
                        option.classList.remove('has-unread');
                        option.textContent = originalText;
                    }
                })
                .catch(err => {
                    console.error('Error checking unread for patient:', err);
                });
        }
    });
}
function sendChatMessage(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    <?php if ($patients_count == 0): ?>
    alert('<?php echo t("no_patients_booked"); ?>');
    return false;
    <?php endif; ?>
    
    if (receiver <= 0) {
        alert('<?php echo t("please_select_patient_first"); ?>');
        return false;
    }
    
    const msg = document.getElementById('messageInput').value.trim();
    if (msg === "") return false;

    const formData = new FormData();
    formData.append('sender', sender);
    formData.append('receiver', receiver);
    formData.append('message', msg);
    formData.append('sender_type', 'doctor');
    formData.append('receiver_type', 'patient');

    fetch('send_message.php', {
        method: 'POST',
        body: formData
    }).then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('messageInput').value = '';
            loadChatMessages();
            checkChatNotifications();
        } else {
            console.error('Error sending message:', data.message);
            alert('Error: ' + data.message);
        }
    }).catch(err => {
        console.error('Error sending message:', err);
        alert('Error sending message. Please try again.');
    });
    
    return false;
}


// ✅ تحديث تلقائي كل 3 ثوانٍ
document.addEventListener("DOMContentLoaded", function() {
    // تحديث فوري عند تحميل الصفحة
    checkChatNotifications();
    updateAdminMessages();
    
    // تحديث دوري كل 3 ثوانٍ
    setInterval(function() {
        checkChatNotifications();
        updateAdminMessages();
    }, 3000);
});

// Send message on Enter key
const messageInput = document.getElementById('messageInput');
messageInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        sendChatMessage(e);
    }
});

// Toggle mobile menu
function toggleMenu() {
    const menu = document.getElementById('sidebarMenu');
    const overlay = document.getElementById('menuOverlay');
    menu.classList.toggle('active');
    overlay.classList.toggle('active');
}

// Close menu when clicking outside on mobile
document.addEventListener('click', function(event) {
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