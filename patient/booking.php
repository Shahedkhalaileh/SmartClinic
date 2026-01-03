<?php
    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
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
        
    <title><?php echo isArabic() ? 'حجز موعد' : 'Book Appointment'; ?></title>
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
            overflow: hidden !important;
            position: relative !important;
        }
        
        .menu {
            width: 280px !important;
            min-width: 280px !important;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(15px) !important;
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.08) !important;
            border-right: 1px solid rgba(24, 25, 129, 0.1) !important;
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
            margin: 15px 15px 0 10px !important;
            padding: 10px !important;
            overflow: visible !important;
            width: auto !important;
            border-radius: 25px 25px 0 0 !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(15px) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
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
            background-color: rgba(24, 25, 129, 0.1) !important;
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
        
        .profile-container {
            background: rgba(24, 25, 129, 0.05) !important;
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
        
        .profile-title {
            font-size: 18px !important;
            font-weight: 700 !important;
            color: #333 !important;
            margin: 10px 0 5px 0 !important;
        }
        
        .profile-subtitle {
            font-size: 14px !important;
            color: #666 !important;
            margin: 0 !important;
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
            border-collapse: collapse !important;
            display: table !important;
            width: 100% !important;
        }
        
        .abc.scroll {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px) !important;
            border-radius: 20px !important;
            padding: 15px !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(24, 25, 129, 0.1) !important;
        }
        
        .dashboard-items {
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%) !important;
            border-radius: 15px !important;
            color: white !important;
            box-shadow: 0 8px 25px rgba(24, 25, 129, 0.3) !important;
            transition: all 0.4s ease !important;
            border: none !important;
            padding: 15px !important;
        }
        
        .dashboard-items:hover {
            transform: translateY(-8px) scale(1.02) !important;
            box-shadow: 0 15px 40px rgba(24, 25, 129, 0.4) !important;
        }
        
        .h1-search {
            color: white !important;
            font-weight: 700 !important;
            font-size: 20px !important;
        }
        
        .h3-search {
            color: rgba(255, 255, 255, 0.9) !important;
            font-size: 14px !important;
            line-height: 1.5 !important;
        }
        
        .btn-primary, .login-btn, .btn-primary-soft {
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%) !important;
            border: none !important;
            border-radius: 25px !important;
            padding: 12px 30px !important;
            color: white !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 15px rgba(24, 25, 129, 0.3) !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
        }
        
        .btn-primary:hover, .login-btn:hover, .btn-primary-soft:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 12px 30px rgba(24, 25, 129, 0.5) !important;
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
            box-shadow: 0 0 0 3px rgba(24, 25, 129, 0.1) !important;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .popup, .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
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
            
            .filter-container {
                padding: 15px !important;
                overflow-x: auto;
            }
            
            .sub-table {
                overflow-x: auto;
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
        }
    </style>
    <style>
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
             <tr class="menu-row" >
                    <td class="menu-btn menu-icon-home " >
                        <a href="index.php" class="non-style-link-menu "><div><p class="menu-text"><?php echo t('home'); ?></p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('all_doctors'); ?></p></a></div>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text"><?php echo t('schedule'); ?></p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('my_appointments'); ?></p></a></div>
                    </td>
                </tr>
                  <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="specialties.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('specialties'); ?></p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-ai">
                        <a href="chatbot.php" class="non-style-link-menu"><div><p class="menu-text">Chat Bot</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('settings'); ?></p></a></div>
                    </td>
                </tr>
                
            </table>
        </div>
        
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:10px; ">
                <tr >
                    <td width="13%" >
                    <a href="index.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text"><?php echo t('back'); ?></font></button></a>
                    </td>
                    <td >
                            <form action="schedule.php" method="post" class="header-search">

                                        <input type="search" name="search" class="input-text header-searchbar" placeholder="<?php echo isArabic() ? 'ابحث عن اسم الطبيب أو البريد الإلكتروني أو التاريخ (YYYY-MM-DD)' : 'Search Doctor name or Email or Date (YYYY-MM-DD)'; ?>" list="doctors" >&nbsp;&nbsp;
                                        
                                        <?php
                                            echo '<datalist id="doctors">';
                                            $list11 = $database->query("select DISTINCT * from  doctor;");
                                            $list12 = $database->query("select DISTINCT * from  schedule GROUP BY title;");
                                            

                                            


                                            for ($y=0;$y<$list11->num_rows;$y++){
                                                $row00=$list11->fetch_assoc();
                                                $d=$row00["docname"];
                                               
                                                echo "<option value='$d'><br/>";
                                               
                                            };


                                            for ($y=0;$y<$list12->num_rows;$y++){
                                                $row00=$list12->fetch_assoc();
                                                $d=$row00["title"];
                                               
                                                echo "<option value='$d'><br/>";
                                                                                         };

                                        echo ' </datalist>';
            ?>
                                        
                                
                                        <input type="Submit" value="<?php echo t('search'); ?>" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: <?php echo isArabic() ? 'left' : 'right'; ?>;">
                            <?php echo t('todays_date'); ?>
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 

                                
                                echo $today;

                                

                        ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>


                </tr>
                
                
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;" >
                        <!-- <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49);font-weight:400;">Scheduled Sessions / Booking / <b>Review Booking</b></p> -->
                        
                    </td>
                    
                </tr>
                
                
                
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="100%" class="sub-table scrolldown" border="0" style="padding: 20px;border:none">
                            
                        <tbody>
                        
                            <?php
                            
                            if(($_GET)){
                                
                                
                                if(isset($_GET["id"])){
                                    

                                    $id = intval($_GET["id"]);
                                    
                                    if ($id <= 0) {
                                        echo '<tr><td colspan="4"><center><p>Invalid session ID</p></center></td></tr>';
                                    } else {
                                        $stmt = $database->prepare("SELECT * FROM schedule INNER JOIN doctor ON schedule.docid=doctor.docid WHERE schedule.scheduleid=? ORDER BY schedule.scheduledate DESC");
                                        $stmt->bind_param("i", $id);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                    $scheduleid=$row["scheduleid"];
                                    $title=$row["title"];
                                    $docname=$row["docname"];
                                    $docemail=$row["docemail"];
                                    $scheduledate=$row["scheduledate"];
                                    $scheduletime=$row["scheduletime"];
                                    $nop = intval($row["nop"]);
                                    
                                    // Check if user already booked this appointment
                                    $stmt_check = $database->prepare("SELECT * FROM appointment WHERE pid=? AND scheduleid=?");
                                    $stmt_check->bind_param("ii", $userid, $id);
                                    $stmt_check->execute();
                                    $userBooking = $stmt_check->get_result();
                                    $userAlreadyBooked = ($userBooking->num_rows > 0);
                                    
                                    // Count existing appointments for this schedule
                                    $stmt2 = $database->prepare("SELECT COUNT(*) as count FROM appointment WHERE scheduleid=?");
                                    $stmt2->bind_param("i", $id);
                                    $stmt2->execute();
                                    $result12 = $stmt2->get_result();
                                    $countData = $result12->fetch_assoc();
                                    $currentBookings = intval($countData['count']);
                                    $isFull = ($currentBookings >= $nop);
                                    $apponum = $currentBookings + 1;
                                    
                                    // Display error messages if any
                                    $error_msg = "";
                                    if (isset($_GET['error'])) {
                                        $error = $_GET['error'];
                                        switch($error) {
                                            case 'already_booked':
                                                $error_msg = '<div style="background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%); color: #856404; padding: 15px; border-radius: 12px; margin: 10px; text-align: center; font-weight: 600; font-size: 14px; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3); border-left: 5px solid #ffc107; animation: slideIn 0.3s ease-out;">✨ You have already booked this appointment!</div>';
                                                break;
                                            case 'full':
                                                $error_msg = '<div style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); color: #c62828; padding: 15px; border-radius: 12px; margin: 10px; text-align: center; font-weight: 600; font-size: 14px; box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3); border-left: 5px solid #f44336; animation: slideIn 0.3s ease-out;">🔒 This appointment is full! Cannot book.</div>';
                                                break;
                                            case 'invalid':
                                                $error_msg = '<div style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); color: #c62828; padding: 15px; border-radius: 12px; margin: 10px; text-align: center; font-weight: 600; font-size: 14px; box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3); border-left: 5px solid #f44336; animation: slideIn 0.3s ease-out;">❌ Invalid data!</div>';
                                                break;
                                            case 'invalid_date':
                                                $error_msg = '<div style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); color: #c62828; padding: 15px; border-radius: 12px; margin: 10px; text-align: center; font-weight: 600; font-size: 14px; box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3); border-left: 5px solid #f44336; animation: slideIn 0.3s ease-out;">📅 Invalid date!</div>';
                                                break;
                                            case 'database_error':
                                                $error_msg = '<div style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); color: #c62828; padding: 15px; border-radius: 12px; margin: 10px; text-align: center; font-weight: 600; font-size: 14px; box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3); border-left: 5px solid #f44336; animation: slideIn 0.3s ease-out;">⚠️ Database error occurred!</div>';
                                                break;
                                        }
                                    }
                                    
                                    if ($error_msg) {
                                        echo '<tr><td colspan="4">' . $error_msg . '</td></tr>';
                                    }
                                    
                                    // Show warning if user already booked or appointment is full
                                    if ($userAlreadyBooked) {
                                        echo '<tr><td colspan="4"><div style="background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%); color: #856404; padding: 15px; border-radius: 12px; margin: 10px; text-align: center; font-weight: 600; font-size: 14px; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3); border-left: 5px solid #ffc107; animation: slideIn 0.3s ease-out;">✨ You have already booked this appointment!</div></td></tr>';
                                    } elseif ($isFull) {
                                        echo '<tr><td colspan="4"><div style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); color: #c62828; padding: 15px; border-radius: 12px; margin: 10px; text-align: center; font-weight: 600; font-size: 14px; box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3); border-left: 5px solid #f44336; animation: slideIn 0.3s ease-out;">🔒 This appointment is full! ('.$currentBookings.'/'.$nop.')</div></td></tr>';
                                    }
                                    
                                    // Sanitize output
                                    $scheduleid = htmlspecialchars($scheduleid, ENT_QUOTES, 'UTF-8');
                                    $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
                                    $docname = htmlspecialchars($docname, ENT_QUOTES, 'UTF-8');
                                    $docemail = htmlspecialchars($docemail, ENT_QUOTES, 'UTF-8');
                                    $scheduledate = htmlspecialchars($scheduledate, ENT_QUOTES, 'UTF-8');
                                    $scheduletime = htmlspecialchars($scheduletime, ENT_QUOTES, 'UTF-8');
                                    
                                    echo '
                                        <form action="booking-complete.php" method="post">
                                            <input type="hidden" name="scheduleid" value="'.$scheduleid.'" >
                                            <input type="hidden" name="apponum" value="'.$apponum.'" >
                                            <input type="hidden" name="date" value="'.$today.'" >

                                        
                                    
                                    ';
                                     

                                    echo '
                                    <td style="width: 50%;" rowspan="2">
                                            <div  class="dashboard-items search-items"  >
                                            
                                                <div style="width:100%">
                                                        <div class="h1-search" style="font-size:25px;">
                                                            Session Details
                                                        </div><br><br>
                                                        <div class="h3-search" style="font-size:18px;line-height:30px">
                                                            Doctor name:  &nbsp;&nbsp;<b>'.$docname.'</b><br>
                                                            Doctor Email:  &nbsp;&nbsp;<b>'.$docemail.'</b> 
                                                        </div>
                                                        <div class="h3-search" style="font-size:18px;">
                                                          
                                                        </div><br>
                                                        <div class="h3-search" style="font-size:18px;">
                                                            Session Title: '.$title.'<br>
                                                            Session Scheduled Date: '.$scheduledate.'<br>
                                                            Session Starts : '.$scheduletime.'<br>
                                                            Channeling fee : <b>LKR.2 000.00</b><br>
                                                            Available Slots: <b>'.($nop - $currentBookings).' / '.$nop.'</b>

                                                        </div>
                                                        <br>
                                                        
                                                </div>
                                                        
                                            </div>
                                        </td>
                                        
                                        
                                        
                                        <td style="width: 25%;">
                                            <div  class="dashboard-items search-items"  >
                                            
                                                <div style="width:100%;padding-top: 15px;padding-bottom: 15px;">
                                                        <div class="h1-search" style="font-size:20px;line-height: 35px;margin-left:8px;text-align:center;">
                                                            Your Appointment Number
                                                        </div>
                                                        <center>
                                                        <div class=" dashboard-icons" style="margin-left: 0px;width:90%;font-size:50px;font-weight:800;text-align:center;color:var(--btnnictext);background-color: var(--btnice)">'.$apponum.'</div>
                                                    </center>
                                                       
                                                        </div><br>
                                                        
                                                        <br>
                                                        <br>
                                                </div>
                                                        
                                            </div>
                                        </td>
                                        </tr>
                                        <tr>
                                            <td>';
                                            
                                    // Only show book button if user hasn't booked and appointment is not full
                                    if (!$userAlreadyBooked && !$isFull) {
                                        echo '<input type="Submit" class="login-btn btn-primary btn btn-book" style="margin-left:10px;padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;width:95%;text-align: center;" value="Book now" name="booknow">';
                                    } elseif ($userAlreadyBooked) {
                                        echo '<div style="text-align: center; padding: 20px; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); color: #1565c0; border-radius: 15px; margin: 10px; box-shadow: 0 6px 20px rgba(33, 150, 243, 0.2); border: 2px solid #2196f3; animation: slideIn 0.3s ease-out;">
                                                <div style="font-size: 36px; margin-bottom: 8px;">✨</div>
                                                <strong style="font-size: 16px; display: block; margin-bottom: 10px;">You have already booked this appointment</strong>
                                                <a href="appointment.php" style="color: #fff; background: linear-gradient(135deg, #4e6ed6 0%, #3d59c0 100%); text-decoration: none; padding: 12px 30px; border-radius: 25px; display: inline-block; margin-top: 10px; font-weight: 600; box-shadow: 0 4px 15px rgba(78, 110, 214, 0.4); transition: transform 0.2s;" onmouseover="this.style.transform=\'scale(1.05)\'" onmouseout="this.style.transform=\'scale(1)\'">View My Appointments</a>
                                              </div>';
                                    } elseif ($isFull) {
                                        echo '<div style="text-align: center; padding: 20px; background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); color: #c62828; border-radius: 15px; margin: 10px; box-shadow: 0 6px 20px rgba(244, 67, 54, 0.2); border: 2px solid #f44336; animation: slideIn 0.3s ease-out;">
                                                <div style="font-size: 36px; margin-bottom: 8px;">🔒</div>
                                                <strong style="font-size: 16px;">This appointment is full</strong>
                                              </div>';
                                    }
                                    
                                    echo '</form>
                                            </td>
                                        </tr>
                                        '; 
                                        } else {
                                            echo '<tr><td colspan="4"><center><p>Session not found</p></center></td></tr>';
                                        }
                                    }
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
        </div>
    </div>
    
    
   
    </div>
    
    <script>
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