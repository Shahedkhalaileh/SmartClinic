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
    
    

       //import database
       include("../connection.php");
       include("../translations.php");
       
       // Cleanup old messages and appointments (older than 7 days)
       @include("../cleanup_old_data.php");
       
       $userrow = $database->query("select * from doctor where docemail='$useremail'");
       $userfetch=$userrow->fetch_assoc();
       $userid= $userfetch["docid"];
       $username=$userfetch["docname"];
    //echo $userid;
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
        
    <title><?php echo t('my_appointments'); ?></title>
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
        
        .dash-body {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(15px) !important;
            border-radius: 25px !important;
            margin: 20px !important;
            padding: 30px !important;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1) !important;
            flex: 1 !important;
        }
        
        .filter-container {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px) !important;
            border-radius: 20px !important;
            padding: 30px !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(102, 126, 234, 0.1) !important;
        }
        
        .sub-table {
            background: white !important;
            border-radius: 15px !important;
            overflow: hidden !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08) !important;
            animation: transitionIn-Y-bottom 0.5s;
        }
        
        .table-headin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            font-weight: 700 !important;
            padding: 15px !important;
        }
        
        .sub-table tbody tr {
            transition: all 0.3s ease !important;
        }
        
        .sub-table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05) !important;
            transform: scale(1.01) !important;
        }
        
        .btn-primary, .login-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            border-radius: 25px !important;
            padding: 12px 30px !important;
            color: white !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
            transition: all 0.3s ease !important;
        }
        
        .btn-primary:hover, .login-btn:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5) !important;
        }
        
        .logout-btn {
            width: 100% !important;
            margin-top: 15px !important;
        }
        
        .input-text, .filter-container-items {
            border: 2px solid rgba(102, 126, 234, 0.2) !important;
            border-radius: 10px !important;
            padding: 12px 15px !important;
            font-size: 15px !important;
            transition: all 0.3s ease !important;
        }
        
        .input-text:focus, .filter-container-items:focus {
            outline: none !important;
            border-color: #667eea !important;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
        }
        
        h1, h3, p {
            color: #333 !important;
        }
        
        h1 {
            font-size: 36px !important;
            font-weight: 800 !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
        }
        
        .heading-main12 {
            font-size: 24px !important;
            font-weight: 700 !important;
            color: #667eea !important;
        }
        
        .popup {
            animation: transitionIn-Y-bottom 0.5s;
        }
        
        .overlay {
            background: rgba(0, 0, 0, 0.5) !important;
            backdrop-filter: blur(5px) !important;
        }
        
        .popup {
            background: white !important;
            border-radius: 20px !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
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
    <div class="language-switcher-header" style="position: absolute; top: 15px; right: 15px; z-index: 1001;">
        <?php include("../language-switcher.php"); ?>
    </div>
    <style>
        [dir="rtl"] .language-switcher-header {
            right: auto;
            left: 15px;
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
                                    <a href="../logout.php" ><input type="button" value="<?php echo t('logout'); ?>" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                    </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord " >
                        <a href="index.php" class="non-style-link-menu "><div><p class="menu-text"><?php echo t('dashboard'); ?></p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment  menu-active menu-icon-appoinment-active">
                        <a href="appointment.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text"><?php echo t('my_appointments'); ?></p></a></div>
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
                        <a href="medical_record.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('medical_record_for_patient'); ?></p></div></a>
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
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <td width="13%" >
                    <a href="index.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text"><?php echo t('back'); ?></font></button></a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;"><?php echo t('appointment_manager'); ?></p>
                                           
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

                        $list110 = $database->query("select * from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  where  doctor.docid=$userid ");

                        ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>


                </tr>
               
                <!-- <tr>
                    <td colspan="4" >
                        <div style="display: flex;margin-top: 40px;">
                        <div class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49);margin-top: 5px;">Schedule a Session</div>
                        <a href="?action=add-session&id=none&error=0" class="non-style-link"><button  class="login-btn btn-primary btn button-icon"  style="margin-left:25px;background-image: url('../img/icons/add.svg');">Add a Session</font></button>
                        </a>
                        </div>
                    </td>
                </tr> -->
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;" >
                    
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)"><?php echo t('my_appointments'); ?> (<?php echo $list110->num_rows; ?>)</p>
                    </td>
                    
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;" >
                        <center>
                        <table class="filter-container" border="0" >
                        <tr>
                           <td width="10%">

                           </td> 
                        <td width="5%" style="text-align: center;">
                        <?php echo t('date_label'); ?>
                        </td>
                        <td width="30%">
                        <form action="" method="post">
                            
                            <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="margin: 0;width: 95%;">

                        </td>
                        
                    <td width="12%">
                        <input type="submit"  name="filter" value=" <?php echo t('filter_button'); ?>" class=" btn-primary-soft btn button-icon btn-filter"  style="padding: 15px; margin :0;width:100%">
                        </form>
                    </td>

                    </tr>
                            </table>

                        </center>
                    </td>
                    
                </tr>
                
                <?php


                    // Calculate date 7 days ago to filter out old appointments
                    date_default_timezone_set('Asia/Amman');
                    $seven_days_ago = date('Y-m-d', strtotime('-7 days'));
                    
                    $sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  where  doctor.docid=$userid AND appointment.appodate >= '$seven_days_ago' ";

                    if($_POST){
                        //print_r($_POST);
                        


                        
                        if(!empty($_POST["sheduledate"])){
                            $sheduledate=$_POST["sheduledate"];
                            $sqlmain.=" and schedule.scheduledate='$sheduledate' ";
                        };

                        

                        //echo $sqlmain;

                    }


                ?>
                  
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0">
                        <thead>
                        <tr>
                                <th class="table-headin">
                                    <?php echo t('patient_name'); ?>
                                </th>
                                <th class="table-headin">
                                    <?php echo t('appointment_number'); ?>
                                </th>
                               
                                <th class="table-headin">
                                    <?php echo t('session_title'); ?>
                                </th>
                                
                                <th class="table-headin" >
                                    <?php echo t('session_date_time'); ?>
                                </th>
                                
                                <th class="table-headin">
                                    <?php echo t('appointment_date'); ?>
                                </th>
                                
                                <th class="table-headin">
                                    <?php echo t('events'); ?>
                                </tr>
                        </thead>
                        <tbody>
                        
                            <?php

                                
                                $result= $database->query($sqlmain);

                                if($result->num_rows==0){
                                    echo '<tr>
                                    <td colspan="7">
                                    <br><br><br><br>
                                    <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    
                                    <br>
                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">'.t('no_sessions_found').'</p>
                                    <a class="non-style-link" href="appointment.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; '.t('show_all_appointments').' &nbsp;</font></button>
                                    </a>
                                    </center>
                                    <br><br><br><br>
                                    </td>
                                    </tr>';
                                    
                                }
                                else{
                                for ( $x=0; $x<$result->num_rows;$x++){
                                    $row=$result->fetch_assoc();
                                    $appoid=$row["appoid"];
                                    $scheduleid=$row["scheduleid"];
                                    $title=$row["title"];
                                    $docname=$row["docname"];
                                    $scheduledate=$row["scheduledate"];
                                    $scheduletime=$row["scheduletime"];
                                    $pname=$row["pname"];
                                    $apponum=$row["apponum"];
                                    $appodate=$row["appodate"];
                                    echo '<tr >
                                        <td style="font-weight:600;"> &nbsp;'.
                                        
                                        substr($pname,0,25)
                                        .'</td >
                                        <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">
                                        '.$apponum.'
                                        
                                        </td>
                                        <td>
                                        '.substr($title,0,15).'
                                        </td>
                                        <td style="text-align:center;;">
                                            '.substr($scheduledate,0,10).' @'.substr($scheduletime,0,5).'
                                        </td>
                                        
                                        <td style="text-align:center;">
                                            '.$appodate.'
                                        </td>

                                        <td>
                                        <div style="display:flex;justify-content: center;">
                                        
                                        <!--<a href="?action=view&id='.$appoid.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                       &nbsp;&nbsp;&nbsp;-->
                                       <a href="?action=drop&id='.$appoid.'&name='.$pname.'&session='.$title.'&apponum='.$apponum.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">'.t('cancel').'</font></button></a>
                                       &nbsp;&nbsp;&nbsp;</div>
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
        </div>
    </div>
    <?php
    
    if($_GET){
        $id=$_GET["id"];
        $action=$_GET["action"];
        if($action=='add-session'){

            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                    
                    
                        <a class="close" href="schedule.php">&times;</a> 
                        <div style="display: flex;justify-content: center;">
                        <div class="abc">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        <tr>
                                <td class="label-td" colspan="2">'.
                                   ""
                                
                                .'</td>
                            </tr>

                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Add New Session.</p><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                <form action="add-session.php" method="POST" class="add-new-form">
                                    <label for="title" class="form-label">Session Title : </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="text" name="title" class="input-text" placeholder="Name of this Session" required><br>
                                </td>
                            </tr>
                            <tr>
                                
                                <td class="label-td" colspan="2">
                                    <label for="docid" class="form-label">Select Doctor: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <select name="docid" id="" class="box" >
                                    <option value="" disabled selected hidden>Choose Doctor Name from the list</option><br/>';
                                        
        
                                        $list11 = $database->query("select  * from  doctor;");
        
                                        for ($y=0;$y<$list11->num_rows;$y++){
                                            $row00=$list11->fetch_assoc();
                                            $sn=$row00["docname"];
                                            $id00=$row00["docid"];
                                            echo "<option value=".$id00.">$sn</option><br/>";
                                        };
        
        
        
                                        
                        echo     '       </select><br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="nop" class="form-label">Number of Patients/Appointment Numbers : </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="number" name="nop" class="input-text" min="0"  placeholder="The final appointment number for this session depends on this number" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="date" class="form-label">Session Date: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="date" name="date" class="input-text" min="'.date('Y-m-d').'" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="time" class="form-label">Schedule Time: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="time" name="time" class="input-text" placeholder="Time" required><br>
                                </td>
                            </tr>
                           
                            <tr>
                                <td colspan="2">
                                    <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                
                                    <input type="submit" value="Place this Session" class="login-btn btn-primary btn" name="shedulesubmit">
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
        }elseif($action=='session-added'){
            $titleget=$_GET["title"];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                    <br><br>
                        <h2>Session Placed.</h2>
                        <a class="close" href="schedule.php">&times;</a>
                        <div class="content">
                        '.substr($titleget,0,40).' was scheduled.<br><br>
                            
                        </div>
                        <div style="display: flex;justify-content: center;">
                        
                        <a href="schedule.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>
                        <br><br><br><br>
                        </div>
                    </center>
            </div>
            </div>
            ';
        }elseif($action=='drop'){
            $nameget=$_GET["name"];
            $session=$_GET["session"];
            $apponum=$_GET["apponum"];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2>'.t('are_you_sure').'</h2>
                        <a class="close" href="appointment.php">&times;</a>
                        <div class="content">
                            '.t('you_want_to_delete_this_record').'<br><br>
                            '.t('patient_name_label').' &nbsp;<b>'.substr($nameget,0,40).'</b><br>
                            '.t('appointment_number').' &nbsp; : <b>'.substr($apponum,0,40).'</b><br><br>
                            
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <a href="delete-appointment.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;'.t('yes').'&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                        <a href="appointment.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;'.t('no').'&nbsp;&nbsp;</font></button></a>

                        </div>
                    </center>
            </div>
            </div>
            '; 
        }elseif($action=='view'){
            $sqlmain= "select * from doctor where docid='$id'";
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $name=$row["docname"];
            $email=$row["docemail"];
            $spe=$row["specialties"];
            
            $spcil_res= $database->query("select sname from specialties where id='$spe'");
            $spcil_array= $spcil_res->fetch_assoc();
            $spcil_name=$spcil_array["sname"];
            $nic=$row['docnic'];
            $tele=$row['doctel'];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2></h2>
                        <a class="close" href="doctors.php">&times;</a>
                        <div class="content">
                            eDoc Web App<br>
                            
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        
                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details.</p><br><br>
                                </td>
                            </tr>
                            
                            <tr>
                                
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Name: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    '.$name.'<br><br>
                                </td>
                                
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Email" class="form-label">Email: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$email.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="nic" class="form-label">NIC: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$nic.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Tele" class="form-label">Telephone: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$tele.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="spec" class="form-label">Specialties: </label>
                                    
                                </td>
                            </tr>
                            <tr>
                            <td class="label-td" colspan="2">
                            '.$spcil_name.'<br><br>
                            </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="doctors.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn" ></a>
                                
                                    
                                </td>
                
                            </tr>
                           

                        </table>
                        </div>
                    </center>
                    <br><br>
            </div>
            </div>
            ';  
    }
}

    ?>
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