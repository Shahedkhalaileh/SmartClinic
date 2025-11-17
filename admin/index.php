<?php
    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
            exit();
        }
    }else{
        header("location: ../login.php");
        exit();
    }

    include("../connection.php");
    
    date_default_timezone_set('Asia/Amman');
    $today = date('Y-m-d');
    $patientrow = $database->query("select  * from  patient;");
    $doctorrow = $database->query("select  * from  doctor;");
    $appointmentrow = $database->query("select  * from  appointment where appodate>='$today';");
    $schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Dashboard</title>
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
        }
        
        .dash-body {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(15px) !important;
            border-radius: 25px !important;
            margin: 20px !important;
            padding: 30px !important;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1) !important;
        }
        
        .filter-container {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px) !important;
            border-radius: 20px !important;
            padding: 30px !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(102, 126, 234, 0.1) !important;
        }
        
        .filter-container tr td {
            vertical-align: top !important;
        }
        
        .filter-container tr td[style*="width: 25%"] {
            height: 100% !important;
        }
        
        .dashboard-items {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border-radius: 20px !important;
            color: white !important;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3) !important;
            transition: all 0.4s ease !important;
            border: none !important;
            min-height: 140px !important;
            height: 100% !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
        
        .dashboard-items > div:first-child {
            flex: 1 !important;
        }
        
        .dashboard-icons {
            width: 60px !important;
            height: 60px !important;
            background-size: contain !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            flex-shrink: 0 !important;
            margin-left: auto !important;
            align-self: center !important;
        }
        
        .dashboard-items:hover {
            transform: translateY(-8px) scale(1.02) !important;
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4) !important;
        }
        
        .h1-dashboard {
            color: white !important;
            font-weight: 900 !important;
            font-size: 42px !important;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2) !important;
        }
        
        .h3-dashboard {
            color: rgba(255, 255, 255, 0.95) !important;
            font-weight: 600 !important;
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
        
        .sub-table, .dashbord-tables {
            background: white !important;
            border-radius: 15px !important;
            overflow: hidden !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08) !important;
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
        
        .header-searchbar {
            border-radius: 12px !important;
            border: 2px solid rgba(102, 126, 234, 0.2) !important;
            padding: 12px 45px 12px 18px !important;
            transition: all 0.3s ease !important;
            color: #333 !important;
            background-color: #fff !important;
            width: 100% !important;
        }
        
        .header-searchbar:focus {
            outline: none !important;
            border-color: #667eea !important;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
        }
        
        /* تنسيق أيقونة البحث في input type="search" */
        input[type="search"]::-webkit-search-decoration,
        input[type="search"]::-webkit-search-cancel-button,
        input[type="search"]::-webkit-search-results-button,
        input[type="search"]::-webkit-search-results-decoration {
            -webkit-appearance: none;
            appearance: none;
        }
        
        input[type="search"]::-webkit-search-cancel-button {
            position: relative;
            right: -10px;
        }
        
        .header-search {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
            position: relative !important;
        }
        
        .header-search::before {
            content: "";
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            width: 20px;
            height: 20px;
            background-image: url('../img/search.svg');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            z-index: 1;
            opacity: 0.6;
        }
        
        .header-search input[type="search"] {
            padding-left: 45px !important;
        }
        
        .nav-bar {
            background: transparent !important;
        }
        
        .nav-bar p {
            font-size: 28px !important;
            font-weight: 800 !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
        }
        
        p {
            color: #666 !important;
            line-height: 1.7 !important;
        }
        
        .non-style-link {
            color: #667eea !important;
            transition: color 0.3s !important;
        }
        
        .non-style-link:hover {
            color: #764ba2 !important;
        }
        .dashbord-tables{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-Y-bottom  0.5s;
        }
        .sub-table{
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
            
            .dashboard-items {
                gap: 10px !important;
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
                                    <p class="profile-title">Administrator</p>
                                    <p class="profile-subtitle">admin@gmail.com</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                    </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord menu-active menu-icon-dashbord-active" >
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Dashboard</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor ">
                        <a href="doctors.php" class="non-style-link-menu "><div><p class="menu-text">Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
                        
                        <tr >
                            
                            <td colspan="2" class="nav-bar" >
                                
                                <form action="doctors.php" method="post" class="header-search">
        
                                    <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Email" list="doctors" style="flex: 1;min-width: 200px;">
                                    
                                    <?php
                                        echo '<datalist id="doctors">';
                                        $list11 = $database->query("select  docname,docemail from  doctor;");
        
                                        for ($y=0;$y<$list11->num_rows;$y++){
                                            $row00=$list11->fetch_assoc();
                                            $d=$row00["docname"];
                                            $c=$row00["docemail"];
                                            echo "<option value='$d'><br/>";
                                            echo "<option value='$c'><br/>";
                                        };
        
                                    echo ' </datalist>';
                                    ?>
                                    
                               
                                    <input type="Submit" value="Search" class="login-btn btn-primary-soft btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;white-space: nowrap;">
                                
                                </form>
                                
                            </td>
                            <td width="15%">
                                <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                                    Today's Date
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
                    <td colspan="4">
                        
                        <center>
                        <table class="filter-container" style="border: none;" border="0">
                            <tr>
                                <td colspan="4">
                                    <p style="font-size: 20px;font-weight:600;padding-left: 12px;">Status</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%;">
                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex">
                                        <div>
                                                <div class="h1-dashboard">
                                                    <?php    echo $doctorrow->num_rows  ?>
                                                </div><br>
                                                <div class="h3-dashboard">
                                                    Doctors &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </div>
                                        </div>
                                                <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                    </div>
                                </td>
                                <td style="width: 25%;">
                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex;">
                                        <div>
                                                <div class="h1-dashboard">
                                                    <?php    echo $patientrow->num_rows  ?>
                                                </div><br>
                                                <div class="h3-dashboard">
                                                    Patients &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </div>
                                        </div>
                                                <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                                    </div>
                                </td>
                                <td style="width: 25%;">
                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex; ">
                                        <div>
                                                <div class="h1-dashboard" >
                                                    <?php    echo $appointmentrow ->num_rows  ?>
                                                </div><br>
                                                <div class="h3-dashboard" >
                                                    NewBooking &nbsp;&nbsp;
                                                </div>
                                        </div>
                                                <div class="btn-icon-back dashboard-icons" style="margin-left: 0px;background-image: url('../img/icons/book-hover.svg');"></div>
                                    </div>
                                </td>
                                <td style="width: 25%;">
                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex;">
                                        <div>
                                                <div class="h1-dashboard">
                                                    <?php    echo $schedulerow ->num_rows  ?>
                                                </div><br>
                                                <div class="h3-dashboard">
                                                    Today Sessions
                                                </div>
                                        </div>
                                                <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/session-iceblue.svg');"></div>
                                    </div>
                                </td>
                                
                            </tr>
                        </table>
                    </center>
                    </td>
                </tr>






                <tr>
                    <td colspan="4">
                        <table width="100%" border="0" class="dashbord-tables">
                            <tr>
                    
                           
                            </tr>
                            <tr>
                                <td width="50%">
                                    <center>
                                        <div class="abc scroll" style="height: 200px;">
                                        <table width="85%" class="sub-table scrolldown" border="0">
                                        <thead>
                                        <tr>    
                                                <th class="table-headin" style="font-size: 12px;">
                                                        
                                                    Appointment number
                                                    
                                                </th>
                                                <th class="table-headin">
                                                    Patient name
                                                </th>
                                                <th class="table-headin">
                                                    
                                                
                                                    Doctor
                                                    
                                                </th>
                                                <th class="table-headin">
                                                    
                                                
                                                    Session
                                                    
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        
                                            <?php
                                            $nextweek=date("Y-m-d",strtotime("+1 week"));
                                            $sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  where schedule.scheduledate>='$today'  and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc";

                                                $result= $database->query($sqlmain);
                
                                                if($result->num_rows==0){
                                                    echo '<tr>
                                                    <td colspan="3">
                                                    <br><br><br><br>
                                                    <center>
                                                    <img src="../img/notfound.svg" width="25%">
                                                    
                                                    <br>
                                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We  couldnt find anything related to your keywords !</p>
                                                    <a class="non-style-link" href="appointment.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Appointments &nbsp;</font></button>
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
                                                    echo '<tr>


                                                        <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);padding:20px;">
                                                            '.$apponum.'
                                                            
                                                        </td>

                                                        <td style="font-weight:600;"> &nbsp;'.
                                                        
                                                        substr($pname,0,25)
                                                        .'</td >
                                                        <td style="font-weight:600;"> &nbsp;'.
                                                        
                                                            substr($docname,0,25)
                                                            .'</td >
                                                           
                                                        
                                                        <td>
                                                        '.substr($title,0,15).'
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
                                <td width="50%" style="padding: 0;">
                                    <center>
                                        <div class="abc scroll" style="height: 200px;padding: 0;margin: 0;">
                                        <table width="85%" class="sub-table scrolldown" border="0" >
                                        <thead>
                                        <tr>
                                                <th class="table-headin">
                                                    
                                                
                                                Session Title
                                                
                                                </th>
                                                
                                                <th class="table-headin">
                                                    Doctor
                                                </th>
                                                <th class="table-headin">
                                                    
                                                    Sheduled Date & Time
                                                    
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
                                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We  couldnt find anything related to your keywords !</p>
                                                    <a class="non-style-link" href="schedule.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Sessions &nbsp;</font></button>
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
                                                        <td>
                                                        '.substr($docname,0,20).'
                                                        </td>
                                                        <td style="text-align:center;">
                                                            '.substr($scheduledate,0,10).' '.substr($scheduletime,0,5).'
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
                            <tr>
                                <td>
                                    <center>
                                        <a href="appointment.php" class="non-style-link"><button class="btn-primary btn" style="width:85%">Show all Appointments</button></a>
                                    </center>
                                </td>
                                <td>
                                    <center>
                                        <a href="schedule.php" class="non-style-link"><button class="btn-primary btn" style="width:85%">Show all Sessions</button></a>
                                    </center>
                                </td>
                            </tr>
                        </table>
                    </td>

                </tr>
                        </table>
                        </center>
                        </td>
                </tr>
            </table>
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