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
    $userrow = $database->query("select * from patient where pemail='$useremail'");
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];
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
        


    <title><?php echo t('settings'); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
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
            background-color: rgba(102, 126, 234, 0.1) !important;
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
            color: #667eea !important;
        }
        
        .non-style-link-menu {
            text-decoration: none !important;
            color: inherit !important;
        }
        
        .dash-body {
            flex: 1 !important;
            margin: 20px 20px 0 15px !important;
            padding: 30px !important;
            overflow-y: auto !important;
            height: 100vh !important;
            width: auto !important;
            border-radius: 25px 25px 0 0 !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(15px) !important;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1) !important;
        }
        
        .menu-btn {
            transition: all 0.3s ease !important;
            border-radius: 12px !important;
            margin: 5px 10px !important;
        }
        
        .menu-btn:hover {
            background: rgba(102, 126, 234, 0.1) !important;
            transform: translateX(5px) !important;
        }
        
        .menu-active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
        }
        
        .profile-container {
            background: rgba(102, 126, 234, 0.05) !important;
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
            color: #333 !important;
            font-weight: 600 !important;
        }
        
        .profile-subtitle {
            color: #666 !important;
            font-weight: 400 !important;
        }
        
        .sub-table {
            background: white !important;
            border-radius: 15px !important;
            overflow: hidden !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08) !important;
            border-collapse: collapse !important;
            width: 100% !important;
        }
        
        .table-headin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            font-weight: 700 !important;
            padding: 15px 12px !important;
            text-align: left !important;
            font-size: 14px !important;
        }
        
        .sub-table tbody tr {
            transition: all 0.3s ease !important;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1) !important;
        }
        
        .sub-table tbody tr:last-child {
            border-bottom: none !important;
        }
        
        .sub-table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05) !important;
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
            padding: 20px !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(102, 126, 234, 0.1) !important;
        }
        
        .input-text {
            border-radius: 12px !important;
            border: 2px solid rgba(102, 126, 234, 0.2) !important;
            padding: 12px 18px 12px 45px !important;
            transition: all 0.3s ease !important;
        }
        
        .input-text:focus {
            outline: none !important;
            border-color: #667eea !important;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
        }
        
        .btn-primary, .login-btn, .btn-primary-soft {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            border-radius: 25px !important;
            padding: 12px 30px !important;
            color: white !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
            transition: all 0.3s ease !important;
        }
        
        .btn-primary:hover, .login-btn:hover, .btn-primary-soft:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5) !important;
        }
        
        .dashbord-tables {
            animation: transitionIn-Y-over 0.5s;
        }
        
        .overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: rgba(0, 0, 0, 0.5) !important;
            backdrop-filter: blur(5px) !important;
            z-index: 1000 !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        
        .popup {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(15px) !important;
            border-radius: 25px !important;
            padding: 30px !important;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2) !important;
            border: 1px solid rgba(102, 126, 234, 0.2) !important;
            max-width: 600px !important;
            width: 90% !important;
            max-height: 90vh !important;
            overflow-y: auto !important;
            position: relative !important;
            animation: transitionIn-Y-bottom 0.5s !important;
        }
        
        .popup .close {
            position: absolute !important;
            top: 15px !important;
            right: 20px !important;
            font-size: 32px !important;
            font-weight: bold !important;
            color: #667eea !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
            line-height: 1 !important;
        }
        
        .popup .close:hover {
            color: #764ba2 !important;
            transform: scale(1.2) !important;
        }
        
        .popup h2 {
            color: #667eea !important;
            font-size: 28px !important;
            font-weight: 700 !important;
            margin-bottom: 20px !important;
            text-align: center !important;
        }
        
        .popup .content {
            margin: 20px 0 !important;
        }
        
        .add-doc-form-container {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }
        
        .add-doc-form-container .label-td {
            padding: 10px 0 !important;
        }
        
        .add-doc-form-container .form-label {
            color: #667eea !important;
            font-weight: 600 !important;
            font-size: 16px !important;
            margin-bottom: 5px !important;
        }
        
        .add-doc-form-container td {
            color: #333 !important;
            font-size: 15px !important;
            padding: 8px 0 !important;
        }
        
        .filter-container {
            animation: transitionIn-X 0.5s;
        }
        
        .sub-table {
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
    
    
</head>
<body>
    <div class="language-switcher-header">
        <?php include("../language-switcher.php"); ?>
    </div>
    <style>
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
                                    <a href="../logout.php"><input type="button" value="<?php echo t('logout'); ?>" class="logout-btn btn-primary-soft btn"></a>
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
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('schedule'); ?></p></div></a>
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
                    <td class="menu-btn menu-icon-settings  menu-active menu-icon-settings-active">
                        <a href="settings.php" class="non-style-link-menu  non-style-link-menu-active"><div><p class="menu-text"><?php echo t('settings'); ?></p></a></div>
                    </td>
                </tr>
                
            </table>
        </div>
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
                        
                        <tr >
                            
                        <td width="13%" >
                    <a href="index.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text"><?php echo t('back'); ?></font></button></a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;"><?php echo t('settings'); ?></p>
                                           
                    </td>
                    
                            <td width="15%">
                                <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: <?php echo isArabic() ? 'left' : 'right'; ?>;">
                                    <?php echo t('todays_date'); ?>
                                </p>
                                <p class="heading-sub12" style="padding: 0;margin: 0;">
                                    <?php 
                        date_default_timezone_set('Asia/Amman');
        
                                $today = date('Y-m-d');
                                echo $today;


                                $patientrow = $database->query("select  * from  patient;");
                                $doctorrow = $database->query("select  * from  doctor;");
                                $appointmentrow = $database->query("select  * from  appointment where appodate>='$today';");
                                $schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");


                                ?>
                                </p>
                            </td>
                            <td width="10%">
                                <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                            </td>
        
        
                        </tr>
                <tr>
                    <td colspan="4">
                        
                        <center>
                        <table class="filter-container" style="border: none;" border="0">
                            <tr>
                                <td colspan="4">
                                    <p style="font-size: 20px">&nbsp;</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%;">
                                    <a href="?action=edit&id=<?php echo $userid ?>&error=0" class="non-style-link">
                                    <div  class="dashboard-items setting-tabs"  style="padding:20px;margin:auto;width:95%;display: flex">
                                        <div class="btn-icon-back dashboard-icons-setting" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                        <div>
                                                <div class="h1-dashboard">
                                                    <?php echo t('account_settings'); ?>  &nbsp;

                                                </div><br>
                                                <div class="h3-dashboard" style="font-size: 15px;">
                                                    <?php echo t('edit_account_details'); ?>
                                                </div>
                                        </div>
                                                
                                    </div>
                                    </a>
                                </td>
                                
                                
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <p style="font-size: 5px">&nbsp;</p>
                                </td>
                            </tr>
                            <tr>
                            <td style="width: 25%;">
                                    <a href="?action=view&id=<?php echo $userid ?>" class="non-style-link">
                                    <div  class="dashboard-items setting-tabs"  style="padding:20px;margin:auto;width:95%;display: flex;">
                                        <div class="btn-icon-back dashboard-icons-setting " style="background-image: url('../img/icons/view-iceblue.svg');"></div>
                                        <div>
                                                <div class="h1-dashboard" >
                                                    <?php echo t('view_account_details'); ?>
                                                    
                                                </div><br>
                                                <div class="h3-dashboard"  style="font-size: 15px;">
                                                    <?php echo t('view_personal_information'); ?>
                                                </div>
                                        </div>
                                                
                                    </div>
                                    </a>
                                </td>
                                
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <p style="font-size: 5px">&nbsp;</p>
                                </td>
                            </tr>
                            <tr>
                            <td style="width: 25%;">
                                    <a href="?action=drop&id=<?php echo $userid.'&name='.$username ?>" class="non-style-link">
                                    <div  class="dashboard-items setting-tabs"  style="padding:20px;margin:auto;width:95%;display: flex;">
                                        <div class="btn-icon-back dashboard-icons-setting" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                                        <div>
                                                <div class="h1-dashboard" style="color: #ff5050;">
                                                    <?php echo t('delete_account'); ?>
                                                    
                                                </div><br>
                                                <div class="h3-dashboard"  style="font-size: 15px;">
                                                    <?php echo t('will_permanently_remove_account'); ?>
                                                </div>
                                        </div>
                                                
                                    </div>
                                    </a>
                                </td>
                                
                            </tr>
                        </table>
                    </center>
                    </td>
                </tr>
            
            </table>
        </div>
    </div>
    <?php 
    if($_GET){
        
        $id=$_GET["id"];
        $action=$_GET["action"];
        if($action=='drop'){
            $nameget=$_GET["name"];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2>'.t('are_you_sure').'</h2>
                        <a class="close" href="settings.php">&times;</a>
                        <div class="content">
                            '.t('you_want_to_delete_your_account').'<br>('.substr($nameget,0,40).').
                            
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <a href="delete-account.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;'.t('yes').'&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                        <a href="settings.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;'.t('no').'&nbsp;&nbsp;</font></button></a>

                        </div>
                    </center>
            </div>
            </div>
            ';
        }elseif($action=='view'){
            $sqlmain= "select * from patient where pid='$id'";
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $name=$row["pname"];
            $email=$row["pemail"];
            $address=$row["paddress"];
            
           
            $dob=$row["pdob"];
            $nic=$row['pnic'];
            $tele=$row['ptel'];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                        <a class="close" href="settings.php">&times;</a>
                        <h2>Account Details</h2>
                        <div class="content">
                            <table width="100%" class="add-doc-form-container" border="0" style="border-collapse: collapse;">
                                <tr>
                                    <td class="label-td" style="padding: 15px 0 5px 0;">
                                        <label for="name" class="form-label">Name:</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 0 15px 0; font-size: 18px; color: #333; font-weight: 500;">
                                        '.htmlspecialchars($name).'
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" style="padding: 15px 0 5px 0;">
                                        <label for="Email" class="form-label">Email:</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 0 15px 0; font-size: 18px; color: #333; font-weight: 500;">
                                        '.htmlspecialchars($email).'
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" style="padding: 15px 0 5px 0;">
                                        <label for="nic" class="form-label">NIC:</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 0 15px 0; font-size: 18px; color: #333; font-weight: 500;">
                                        '.htmlspecialchars($nic).'
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" style="padding: 15px 0 5px 0;">
                                        <label for="Tele" class="form-label">Telephone:</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 0 15px 0; font-size: 18px; color: #333; font-weight: 500;">
                                        '.htmlspecialchars($tele).'
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" style="padding: 15px 0 5px 0;">
                                        <label for="address" class="form-label">Address:</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 0 15px 0; font-size: 18px; color: #333; font-weight: 500;">
                                        '.htmlspecialchars($address).'
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" style="padding: 15px 0 5px 0;">
                                        <label for="dob" class="form-label">Date of Birth:</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 0 25px 0; font-size: 18px; color: #333; font-weight: 500;">
                                        '.htmlspecialchars($dob).'
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center; padding-top: 20px;">
                                        <a href="settings.php"><button class="login-btn btn-primary btn" style="padding: 12px 40px; font-size: 16px; border-radius: 25px;">'.t('ok').'</button></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
            </div>
            </div>
            ';
        }elseif($action=='edit'){
            $sqlmain= "select * from patient where pid='$id'";
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $name=$row["pname"];
            $email=$row["pemail"];
           
            
            
            $address=$row["paddress"];
            $nic=$row['pnic'];
            $tele=$row['ptel'];

            $error_1=$_GET["error"];
                $errorlist= array(
                    '1'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>',
                    '2'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Conformation Error! Reconform Password</label>',
                    '3'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>',
                    '4'=>"",
                    '0'=>'',

                );

            if($error_1!='4'){
                    echo '
                    <div id="popup1" class="overlay">
                            <div class="popup">
                            <center>
                            
                                <a class="close" href="settings.php">&times;</a> 
                                <div style="display: flex;justify-content: center;">
                                <div class="abc">
                                <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                <tr>
                                        <td class="label-td" colspan="2">'.
                                            $errorlist[$error_1]
                                        .'</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">'.t('edit_user_account_details').'</p>
                                        User ID : '.$id.' (Auto Generated)<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <form action="edit-user.php" method="POST" class="add-new-form">
                                            <label for="Email" class="form-label">Email: </label>
                                            <input type="hidden" value="'.$id.'" name="id00">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                        <input type="hidden" name="oldemail" value="'.$email.'" >
                                        <input type="email" name="email" class="input-text" placeholder="Email Address" value="'.$email.'" required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        
                                        <td class="label-td" colspan="2">
                                            <label for="name" class="form-label">Name: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" name="name" class="input-text" placeholder="Doctor Name" value="'.$name.'" required><br>
                                        </td>
                                        
                                    </tr>
                                    
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="nic" class="form-label">NIC: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" name="nic" class="input-text" placeholder="NIC Number" value="'.$nic.'" required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="Tele" class="form-label">Telephone: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="tel" name="Tele" class="input-text" placeholder="Telephone Number" value="'.$tele.'" required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="spec" class="form-label">Address</label>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                        <input type="text" name="address" class="input-text" placeholder="Address" value="'.$address.'" required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="password" class="form-label">Password: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="password" name="password" class="input-text" placeholder="Defind a Password" required><br>
                                        </td>
                                    </tr><tr>
                                        <td class="label-td" colspan="2">
                                            <label for="cpassword" class="form-label">Conform Password: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="password" name="cpassword" class="input-text" placeholder="Conform Password" required><br>
                                        </td>
                                    </tr>
                                    
                        
                                    <tr>
                                        <td colspan="2">
                                            <input type="reset" value="'.t('reset').'" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                            <input type="submit" value="'.t('save').'" class="login-btn btn-primary btn">
                                        </td>
                        
                                    </tr>
                                
                                    </form>
                                    </tr>
                                </table>
                                </div>
                                </div>
                            </center>
                            <br><br>
                    </div>
                    </div>
                    ';
        }else{
            echo '
                <div id="popup1" class="overlay">
                        <div class="popup">
                        <center>
                        <br><br><br><br>
                            <h2>Edit Successfully!</h2>
                            <a class="close" href="settings.php">&times;</a>
                            <div class="content">
                                If You change your email also Please logout and login again with your new email
                                
                            </div>
                            <div style="display: flex;justify-content: center;">
                            
                            <a href="settings.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;'.t('ok').'&nbsp;&nbsp;</font></button></a>
                            <a href="../logout.php" class="non-style-link"><button  class="btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;'.t('logout').'&nbsp;&nbsp;</font></button></a>

                            </div>
                            <br><br>
                        </center>
                </div>
                </div>
    ';



        }; }

    }
        ?>

</body>
</html>