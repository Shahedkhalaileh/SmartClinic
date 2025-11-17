<?php
session_start();
if(isset($_SESSION["user"])){
    if($_SESSION["user"]=="" || $_SESSION['usertype']!='d'){
        header("location: ../login.php");
        exit();
    } else {
        $useremail=$_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

include("../connection.php");
$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch=$userrow->fetch_assoc();
$userid= $userfetch["docid"];
$username=$userfetch["docname"];
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
    <title>Patients</title>
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
        
        .btn-primary-soft {
            background: rgba(102, 126, 234, 0.1) !important;
            color: #667eea !important;
            border: 2px solid rgba(102, 126, 234, 0.2) !important;
        }
        
        .btn-primary-soft:hover {
            background: rgba(102, 126, 234, 0.2) !important;
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
        .popup {
            animation: transitionIn-Y-bottom 0.5s;
        }
 
        .sub-table-container {
    max-height: 777px;
    overflow-y: auto;  
    animation: transitionIn-Y-bottom 0.5s;
    border: 1px solid #ccc; 
    border-radius: 7px;    
}

.sub-table {
    width: 100%;
    border-spacing: 0;
        animation: transitionIn-Y-bottom 0.s;
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
                            <td>
                                <p class="profile-title"><?php echo substr($username,0,13) ?>..</p>
                                <p class="profile-subtitle"><?php echo substr($useremail,0,22) ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="menu-row"><td class="menu-btn menu-icon-dashbord"><a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-appoinment"><a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Appointments</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-session"><a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-patient menu-active menu-icon-patient-active"><a href="patient.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">My Patients</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-patient"><a href="medical_record.php" class="non-style-link-menu"><div><p class="menu-text">Medical Record for patient</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-settings"><a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a></td></tr>
        </table>
    </div>

<?php
$selecttype="My";
$current="My patients Only";

if($_POST){
    if(isset($_POST["search"])){
        $keyword=$_POST["search12"];
        $sqlmain= "SELECT * FROM patient WHERE pemail='$keyword' OR pname='$keyword' OR pname LIKE '$keyword%' OR pname LIKE '%$keyword' OR pname LIKE '%$keyword%'";
        $selecttype="my";
    }

    if(isset($_POST["filter"])){
        if($_POST["showonly"]=='all'){
            $sqlmain= "SELECT * FROM patient";
            $selecttype="All";
            $current="All patients";
        } else {
            $sqlmain= "SELECT * FROM appointment INNER JOIN patient ON patient.pid=appointment.pid INNER JOIN schedule ON schedule.scheduleid=appointment.scheduleid WHERE schedule.docid=$userid;";
            $selecttype="My";
            $current="My patients Only";
        }
    }
} else {
    $sqlmain= "SELECT * FROM appointment INNER JOIN patient ON patient.pid=appointment.pid INNER JOIN schedule ON schedule.scheduleid=appointment.scheduleid WHERE schedule.docid=$userid;";
    $selecttype="My";
}
?>
<div class="dash-body">
    <table border="0" width="100%" style="border-spacing:0;margin-top:25px;">
        <tr>
            <td width="13%">
                <a href="index.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding:11px;margin-left:20px;width:125px">Back</button></a>
            </td>
            <td>
                <form action="" method="post" class="header-search">
                    <input type="search" name="search12" class="input-text header-searchbar" placeholder="Search Patient name or Email" list="patient">
                    &nbsp;&nbsp;
                    <?php
                    echo '<datalist id="patient">';
                    $list11 = $database->query($sqlmain);
                    for ($y=0;$y<$list11->num_rows;$y++){
                        $row00=$list11->fetch_assoc();
                        $d=$row00["pname"];
                        $c=$row00["pemail"];
                        echo "<option value='$d'><option value='$c'>";
                    }
                    echo '</datalist>';
                    ?>
                    <input type="Submit" value="Search" name="search" class="login-btn btn-primary btn" style="padding:10px 25px;">
                </form>
            </td>
            <td width="15%">
                <p style="font-size:14px;color:rgb(119,119,119);text-align:right;">Today's Date</p>
                <p class="heading-sub12"><?php date_default_timezone_set('Asia/Amman'); echo date('Y-m-d'); ?></p>
            </td>
            <td width="10%">
                <button class="btn-label" style="display:flex;justify-content:center;align-items:center;"><img src="../img/calendar.svg" width="100%"></button>
            </td>
        </tr>

        <tr>
            <td colspan="4" style="padding-top:10px;">
                <p class="heading-main12" style="margin-left:45px;font-size:18px;color:rgb(49,49,49)">
                    <?php echo $selecttype." Patients (".$list11->num_rows.")"; ?>
                </p>
            </td>
        </tr>

        <tr>
            <td colspan="4">
                <center>
                <table class="filter-container" border="0">
                <form action="" method="post">
                    <td style="text-align:right;">Show Details About : &nbsp;</td>
                    <td width="30%">
                        <select name="showonly" class="box filter-container-items" style="width:90%;height:37px;">
                            <option value="" disabled selected hidden><?php echo $current ?></option>
                            <option value="my">My Patients Only</option>
                            <option value="all">All Patients</option>
                        </select>
                    </td>
                    <td width="12%">
                        <input type="submit" name="filter" value=" Filter" class="btn-primary-soft btn button-icon btn-filter" style="padding:15px;width:100%">
                    </td>
                </form>
                </table>
                </center>
            </td>
        </tr>

        <tr>
            <td colspan="4">
                <center>
                <div class="abc scroll">
                <table width="93%" class="sub-table scrolldown" style="border-spacing:0;">
                    <thead>
                        <tr>
                            <td class="table-headin">Name</td>
                            <td class="table-headin">NIC</td>
                            <td class="table-headin">Telephone</td>
                            <td class="table-headin">Email</td>
                            <td class="table-headin">Date of Birth</td>
                             <td class="table-headin">Gender</td>
                            <td class="table-headin">Medical Record</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $result= $database->query($sqlmain);
                    if($result->num_rows==0){
                        echo '<tr><td colspan="6"><br><br><center><img src="../img/notfound.svg" width="25%"><br>
                        <p class="heading-main12">We couldnt find anything related!</p>
                        <a class="non-style-link" href="patient.php"><button class="login-btn btn-primary-soft btn">Show all Patients</button></a></center><br><br></td></tr>';
                    } else {
                        while($row=$result->fetch_assoc()){
                            $pid=$row["pid"];
                            echo '<tr>
                                <td>'.substr($row["pname"],0,35).'</td>
                                <td>'.substr($row["pnic"],0,12).'</td>
                                <td>'.substr($row["ptel"],0,10).'</td>
                                <td>'.substr($row["pemail"],0,20).'</td>
                                <td>'.substr($row["pdob"],0,10).'</td>
                                <td>'.substr($row["gender"],0,10).'</td>
                                <td><div>
                                   <center> <a href="patient.php?id='.$pid.'&action=view" class="non-style-link"><button class="btn-primary-soft btn">View</button></a></center>
                                </div></td>
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
if(isset($_GET["id"])){
    $id = $_GET["id"];

    
    $sql_patient = "SELECT * FROM patient WHERE pid='$id'";
    $result_patient = $database->query($sql_patient);
    $patient = $result_patient->fetch_assoc();

    $name = $patient["pname"];
    $email = $patient["pemail"];
    $nic = $patient["pnic"];
    $dob = $patient["pdob"];
    $tele = $patient["ptel"];
    $address = $patient["paddress"];


    $sql_medical = "SELECT * FROM medicalrecords WHERE pid='$id' ORDER BY id DESC LIMIT 1";
    $result_medical = $database->query($sql_medical);

    if($result_medical->num_rows > 0){
        $medical = $result_medical->fetch_assoc();
        $diabetes = $medical["diabetes"];
        $pressure = $medical["hypertension"];
        $height = $medical["height"];
        $weight = $medical["weight"];
        $diagnosis = $medical["diagnosis"];
        $treatment = $medical["treatment"];
        $notes = $medical["notes"];
    } else {
        $diabetes = $pressure = $height = $weight = $diagnosis = $treatment = $notes = "N/A";
    }

    echo '
    <div id="popup1" class="overlay">
        <div class="popup">
        <center>
            <a class="close" href="patient.php">&times;</a>
            <div class="sub-table-container" >
            <table width="80%" class="sub-table" border="2">
                <tr><td><p style="font-size:25px;font-weight:500;"><center><h2>'.$name.'<br> Medical Record</h2></center></p><br></td></tr>
                <tr><td class="label-td">Patient ID:</td></tr><tr><td>P-'.$id.'<br><br></td></tr>
                <tr><td class="label-td">Name:</td></tr><tr><td>'.$name.'<br><br></td></tr>
                <tr><td class="label-td">Email:</td></tr><tr><td>'.$email.'<br><br></td></tr>
                <tr><td class="label-td">NIC:</td></tr><tr><td>'.$nic.'<br><br></td></tr>
                <tr><td class="label-td">Telephone:</td></tr><tr><td>'.$tele.'<br><br></td></tr>
                <tr><td class="label-td">Address:</td></tr><tr><td>'.$address.'<br><br></td></tr>
                <tr><td class="label-td">Date of Birth:</td></tr><tr><td>'.$dob.'<br><br></td></tr>
                <tr><td class="label-td">Diabetes:</td></tr><tr><td>'.$diabetes.'<br><br></td></tr>
                <tr><td class="label-td">Hypertension:</td></tr><tr><td>'.$pressure.'<br><br></td></tr>
                <tr><td class="label-td">Height (cm):</td></tr><tr><td>'.$height.'<br><br></td></tr>
                <tr><td class="label-td">Weight (kg):</td></tr><tr><td>'.$weight.'<br><br></td></tr>
                <tr><td class="label-td">Diagnosis:</td></tr><tr><td>'.$diagnosis.'<br><br></td></tr>
                  <tr><td class="label-td">Treatment:</td></tr><tr><td>'.$treatment.'<br><br></td></tr> 
                    <tr><td class="label-td">Notes:</td></tr><tr><td>'.$notes.'<br><br></td></tr>                                      
                <tr><td>   <center><a href="patient.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn"></a></center></td></tr>
            </table>
            </div>
        </center>
        </div>
    </div>';
}
?>

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