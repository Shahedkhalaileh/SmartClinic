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
        
    <title>Doctors</title>
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
                                <a href="../logout.php" >
                                    <input type="button" value="Log out" class="logout-btn btn-primary-soft btn">
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="menu-row" >
                <td class="menu-btn menu-icon-dashbord" >
                    <a href="index.php" class="non-style-link-menu">
                        <div><p class="menu-text">Dashboard</p></div>
                    </a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-doctor menu-active menu-icon-doctor-active">
                    <a href="doctors.php" class="non-style-link-menu non-style-link-menu-active">
                        <div><p class="menu-text">Doctors</p></div>
                    </a>
                </td>
            </tr>
            <tr class="menu-row" >
                <td class="menu-btn menu-icon-schedule">
                    <a href="schedule.php" class="non-style-link-menu">
                        <div><p class="menu-text">Schedule</p></div>
                    </a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-appoinment">
                    <a href="appointment.php" class="non-style-link-menu">
                        <div><p class="menu-text">Appointment</p></div>
                    </a>
                </td>
            </tr>
            <tr class="menu-row" >
                <td class="menu-btn menu-icon-patient">
                    <a href="patient.php" class="non-style-link-menu">
                        <div><p class="menu-text">Patients</p></div>
                    </a>
                </td>
            </tr>

        </table>
    </div>
    <div class="dash-body">
        <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
            <tr>
                <td width="13%">
                    <a href="index.php">
                        <button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px">
                            <font class="tn-in-text">Back</font>
                        </button>
                    </a>
                </td>
                <td>
                    <form action="" method="post" class="header-search">
                        <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Email" list="doctors">&nbsp;&nbsp;
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
                        <input type="Submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                    </form>
                </td>
                <td width="15%">
                    <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                        Today's Date
                    </p>
                    <p class="heading-sub12" style="padding: 0;margin: 0;">
                        <?php 
                            date_default_timezone_set('Asia/Amman');
                            $date = date('Y-m-d');
                            echo $date;
                        ?>
                    </p>
                </td>
                <td width="10%">
                    <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;">
                        <img src="../img/calendar.svg" width="100%">
                    </button>
                </td>
            </tr>
           
            <tr>
                <td colspan="2" style="padding-top:30px;">
                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Add New Doctor</p>
                </td>
                <td colspan="2">
                    <a href="?action=add&id=none&error=0" class="non-style-link">
                        <button  class="login-btn btn-primary btn button-icon"  style="display: flex;justify-content: center;align-items: center;margin-left:75px;background-image: url('../img/icons/add.svg');">
                            Add New
                        </button>
                    </a>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding-top:10px;">
                    <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Doctors (<?php echo $list11->num_rows; ?>)</p>
                </td>
            </tr>
            <?php
                if($_POST){
                    $keyword=$_POST["search"];
                    $sqlmain= "select * from doctor where docemail='$keyword' or docname='$keyword' or docname like '$keyword%' or docname like '%$keyword' or docname like '%$keyword%'";
                }else{
                    $sqlmain= "select * from doctor order by docid desc";
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
                                            Doctor Name
                                        </th>
                                        <th class="table-headin">
                                            Email
                                        </th>
                                        <th class="table-headin">
                                            Specialties
                                        </th>
                                        <th class="table-headin">
                                            Events
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $result= $database->query($sqlmain);

                                    if($result->num_rows==0){
                                        echo '<tr>
                                        <td colspan="4">
                                        <br><br><br><br>
                                        <center>
                                        <img src="../img/notfound.svg" width="25%">
                                        
                                        <br>
                                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We  couldnt find anything related to your keywords !</p>
                                        <a class="non-style-link" href="doctors.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Doctors &nbsp;</button>
                                        </a>
                                        </center>
                                        <br><br><br><br>
                                        </td>
                                        </tr>';
                                        
                                    }
                                    else{
                                        for ( $x=0; $x<$result->num_rows;$x++){
                                            $row=$result->fetch_assoc();
                                            $docid=$row["docid"];
                                            $name=$row["docname"];
                                            $email=$row["docemail"];
                                            $spe=$row["specialties"];
                                            $spcil_res= $database->query("select sname from specialties where id='$spe'");
                                            $spcil_array= $spcil_res->fetch_assoc();
                                            $spcil_name=$spcil_array["sname"];
                                            echo '<tr>
                                                <td> &nbsp;'.substr($name,0,30).'</td>
                                                <td>'.substr($email,0,20).'</td>
                                                <td>'.substr($spcil_name,0,20).'</td>
                                                <td>
                                                    <div style="display:flex;justify-content: center;">
                                                        <a href="?action=edit&id='.$docid.'&error=0" class="non-style-link">
                                                            <button  class="btn-primary-soft btn button-icon btn-edit"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;">
                                                                <font class="tn-in-text">Edit</font>
                                                            </button>
                                                        </a>
                                                        &nbsp;&nbsp;&nbsp;
                                                        <a href="?action=view&id='.$docid.'" class="non-style-link">
                                                            <button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;">
                                                                <font class="tn-in-text">View</font>
                                                            </button>
                                                        </a>
                                                        &nbsp;&nbsp;&nbsp;
                                                        <a href="?action=drop&id='.$docid.'&name='.$name.'" class="non-style-link">
                                                            <button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;">
                                                                <font class="tn-in-text">Remove</font>
                                                            </button>
                                                        </a>
                                                    </div>
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

    if($action=='drop'){
        $nameget=$_GET["name"];
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <h2>Are you sure?</h2>
                    <a class="close" href="doctors.php">&times;</a>
                    <div class="content">
                        You want to delete this record<br>('.substr($nameget,0,40).').
                    </div>
                    <div style="display: flex;justify-content: center;">
                        <a href="delete-doctor.php?id='.$id.'" class="non-style-link">
                            <button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;">
                                <font class="tn-in-text">&nbsp;Yes&nbsp;</font>
                            </button>
                        </a>&nbsp;&nbsp;&nbsp;
                        <a href="doctors.php" class="non-style-link">
                            <button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;">
                                <font class="tn-in-text">&nbsp;&nbsp;No&nbsp;&nbsp;</font>
                            </button>
                        </a>
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
                                    <a href="doctors.php">
                                        <input type="button" value="OK" class="login-btn btn-primary-soft btn" >
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </center>
                <br><br>
            </div>
        </div>
        ';
    }elseif($action=='add'){


        $name_val = $_GET['name'] ?? '';
        $email_val = $_GET['email'] ?? '';
        $nic_val = $_GET['nic'] ?? '';
        $tele_val = $_GET['tele'] ?? '';
        $spec_val = $_GET['spec'] ?? '';

        $error_1 = $_GET["error"] ?? '0';


        if($error_1 == '5'){
            $tele_val = '';
        }

        if($error_1 == '6'){
            $nic_val = '';
        }

        $errorlist= array(
            '1'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>',
            '2'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Conformation Error! Reconform Password</label>',
            '3'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>',
            '4'=>"",
           '5' => '<label for="promter" class="form-label" style="color:red;text-align:center;">The phone number is already in use.</label>',
          '6' => '<label for="promter" class="form-label" style="color:red;text-align:center;">The Identification Number is already in use.</label>',
          '7' => '<label for="promter" class="form-label" style="color:red;text-align:center;">Please enter a valid email address.</label>',
           '0'=>'',
        );

        if($error_1!='4'){
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <a class="close" href="doctors.php">&times;</a> 
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
                                            <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Add New Doctor.</p><br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <form action="add-new.php" method="POST" class="add-new-form">
                                        <td class="label-td" colspan="2">
                                            <label for="name" class="form-label">Name: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" 
                                                   name="name" 
                                                   class="input-text" 
                                                   placeholder="Doctor Name" 
                                                   value="'.$name_val.'" 
                                                   required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="Email" class="form-label">Email: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="email" 
                                                   name="email" 
                                                   class="input-text" 
                                                   placeholder="Email Address" 
                                                   value="'.$email_val.'" 
                                                   required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="nic" class="form-label">Identification Number: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" 
                                                   name="nic" 
                                                   class="input-text" 
                                                   placeholder="Identification Number" 
                                                   pattern="\d{10}" 
                                                   maxlength="10" 
                                                   minlength="10" 
                                                   title="Identification Number must be exactly 10 digits" 
                                                   value="'.$nic_val.'"
                                                   required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            Telephone:
                                            <input type="tel" 
                                                   name="Tele" 
                                                   class="input-text" 
                                                   placeholder="ex: 0712345678" 
                                                   pattern="07\d{8}" 
                                                   maxlength="10" 
                                                   value="'.$tele_val.'"
                                                   required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="spec" class="form-label">Choose specialties: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <select name="spec" id="" class="box" >';
                                            
                                                $list11 = $database->query("select  * from  specialties order by sname asc;");
            
                                                for ($y=0;$y<$list11->num_rows;$y++){
                                                    $row00=$list11->fetch_assoc();
                                                    $sn=$row00["sname"];
                                                    $id00=$row00["id"];

                                                    if($spec_val == $id00){
                                                        echo "<option value='$id00' selected>$sn</option><br/>";
                                                    }else{
                                                        echo "<option value='$id00'>$sn</option><br/>";
                                                    }
                                                };
                                            
                        echo       '         </select><br>
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
                                    </tr>
                                    <tr>
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
                                            <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="submit" value="Add" class="login-btn btn-primary btn">
                                        </td>
                                    </tr>
                                    </form>
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
                            <h2>New Record Added Successfully!</h2>
                            <a class="close" href="doctors.php">&times;</a>
                            <div class="content">
                            </div>
                            <div style="display: flex;justify-content: center;">
                                <a href="doctors.php" class="non-style-link">
                                    <button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;">
                                        <font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font>
                                    </button>
                                </a>
                            </div>
                            <br><br>
                        </center>
                    </div>
                </div>
        ';
        }
    }elseif($action=='edit'){
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

        $error_1=$_GET["error"];
        $errorlist= array(
            '1'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>',
            '2'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Conformation Error! Reconform Password</label>',
            '3'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>',
            '4'=>"",
            '5'=>'<label for="promter" class="form-label" style="color:red;text-align:center;">The phone number is already in use.</label>',
            '6'=>'<label for="promter" class="form-label" style="color:red;text-align:center;">The Identification Number is already in use.</label>',
            '7'=>'<label for="promter" class="form-label" style="color:red;text-align:center;">Please enter a valid email address.</label>',
            '0'=>'',
        );

        if($error_1!='4'){
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <a class="close" href="doctors.php">&times;</a> 
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
                                            <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Edit Doctor Details.</p>
                                            Doctor ID : '.$id.' (Auto Generated)<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <form action="edit-doc.php" method="POST" class="add-new-form">
                                            <label for="Email" class="form-label">Email: </label>
                                            <input type="hidden" value="'.$id.'" name="id00">
                                            <input type="hidden" name="oldemail" value="'.$email.'" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
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
                                            <label for="spec" class="form-label">Choose specialties: (Current '.$spcil_name.')</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <select name="spec" id="" class="box">';
                                                
                                                $list11 = $database->query("select  * from  specialties;");
        
                                                for ($y=0;$y<$list11->num_rows;$y++){
                                                    $row00=$list11->fetch_assoc();
                                                    $sn=$row00["sname"];
                                                    $id00=$row00["id"];
                                                    echo "<option value=".$id00.">$sn</option><br/>";
                                                };
                                echo       '         </select><br><br>
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
                                    </tr>
                                    <tr>
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
                                            <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="submit" value="Save" class="login-btn btn-primary btn">
                                        </td>
                                    </tr>
                                    </form>
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
                            <a class="close" href="doctors.php">&times;</a>
                            <div class="content">
                            </div>
                            <div style="display: flex;justify-content: center;">
                                <a href="doctors.php" class="non-style-link">
                                    <button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;">
                                        <font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font>
                                    </button>
                                </a>
                            </div>
                            <br><br>
                        </center>
                    </div>
                </div>
            ';
        }
    }
}

?>
</body>
</html>