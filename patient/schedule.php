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

    <title><?php echo t('schedule'); ?></title>
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
            background: rgba(24, 25, 129, 0.1) !important;
            transform: translateX(5px) !important;
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
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%) !important;
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
            background: rgba(24, 25, 129, 0.05) !important;
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
            border: 1px solid rgba(24, 25, 129, 0.1) !important;
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

        .btn-primary,
        .login-btn,
        .btn-primary-soft {
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%) !important;
            border: none !important;
            border-radius: 25px !important;
            padding: 12px 30px !important;
            color: white !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 15px rgba(24, 25, 129, 0.3) !important;
            transition: all 0.3s ease !important;
        }

        .btn-primary:hover,
        .login-btn:hover,
        .btn-primary-soft:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 12px 30px rgba(24, 25, 129, 0.5) !important;
        }

        .heading-main12 {
            font-size: 28px !important;
            font-weight: 800 !important;
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            margin: 5px 0 !important;
            line-height: 1.2 !important;
        }

        .popup {
            animation: transitionIn-Y-bottom 0.5s;
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
            <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active">
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
                    <p class="menu-text">Chat Bot</p>
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
    <?php

    $sqlmain = "select * from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduledate>='$today'  order by schedule.scheduledate asc";
    $sqlpt1 = "";
    $insertkey = "";
    $q = '';
    $searchtype = t('all');
    if ($_POST) {
        //print_r($_POST);
    
        if (!empty($_POST["search"])) {

            $keyword = $_POST["search"];
            $sqlmain = "select * from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduledate>='$today' and (doctor.docname='$keyword' or doctor.docname like '$keyword%' or doctor.docname like '%$keyword' or doctor.docname like '%$keyword%' or schedule.title='$keyword' or schedule.title like '$keyword%' or schedule.title like '%$keyword' or schedule.title like '%$keyword%' or schedule.scheduledate like '$keyword%' or schedule.scheduledate like '%$keyword' or schedule.scheduledate like '%$keyword%' or schedule.scheduledate='$keyword' )  order by schedule.scheduledate asc";
            //echo $sqlmain;
            $insertkey = $keyword;
            $searchtype = t('search_result');
            $q = '"';
        }

    }


    $result = $database->query($sqlmain)


        ?>

    <div class="dash-body">
        <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
            <tr>
                <td width="13%">
                    <a href="index.php"><button class="login-btn btn-primary-soft btn btn-icon-back"
                            style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px">
                            <font class="tn-in-text"><?php echo t('back'); ?></font>
                        </button></a>
                </td>
                <td>
                    <form action="" method="post" class="header-search">

                        <input type="search" name="search" class="input-text header-searchbar"
                            placeholder="<?php echo t('search_doctor_or_date'); ?>" list="doctors"
                            value="<?php echo $insertkey ?>">&nbsp;&nbsp;

                        <?php
                        echo '<datalist id="doctors">';
                        $list11 = $database->query("select DISTINCT * from  doctor;");
                        $list12 = $database->query("select DISTINCT * from  schedule GROUP BY title;");





                        for ($y = 0; $y < $list11->num_rows; $y++) {
                            $row00 = $list11->fetch_assoc();
                            $d = $row00["docname"];

                            echo "<option value='$d'><br/>";

                        }
                        ;


                        for ($y = 0; $y < $list12->num_rows; $y++) {
                            $row00 = $list12->fetch_assoc();
                            $d = $row00["title"];

                            echo "<option value='$d'><br/>";
                        }
                        ;

                        echo ' </datalist>';
                        ?>


                        <input type="Submit" value="<?php echo t('search'); ?>" class="login-btn btn-primary btn"
                            style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                    </form>
                </td>
                <td width="15%">
                    <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                        <?php echo t('todays_date'); ?>
                    </p>
                    <p class="heading-sub12" style="padding: 0;margin: 0;">
                        <?php


                        echo $today;



                        ?>
                    </p>
                </td>
                <td width="10%">
                    <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img
                            src="../img/calendar.svg" width="100%"></button>
                </td>


            </tr>


            <tr>
                <td colspan="4" style="padding-top:10px;width: 100%;">
                    <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">
                        <?php echo $searchtype . " " . t('sessions') . "(" . $result->num_rows . ")"; ?>
                    </p>
                    <p class="heading-main12" style="margin-left: 45px;font-size:22px;color:rgb(49, 49, 49)">
                        <?php echo $q . $insertkey . $q; ?>
                    </p>
                </td>

            </tr>



            <tr>
                <td colspan="4">
                    <center>
                        <div class="abc scroll">
                            <table width="100%" class="sub-table scrolldown" border="0"
                                style="padding: 50px;border:none">

                                <tbody>

                                    <?php




                                    if ($result->num_rows == 0) {
                                        echo '<tr>
                                    <td colspan="4">
                                    <br><br><br><br>
                                    <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    
                                    <br>
                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">' . t('no_sessions_found') . '</p>
                                    <a class="non-style-link" href="schedule.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; ' . t('show_all_sessions') . ' &nbsp;</font></button>
                                    </a>
                                    </center>
                                    <br><br><br><br>
                                    </td>
                                    </tr>';

                                    } else {
                                        //echo $result->num_rows;
                                        for ($x = 0; $x < ($result->num_rows); $x++) {
                                            echo "<tr>";
                                            for ($q = 0; $q < 3; $q++) {
                                                $row = $result->fetch_assoc();
                                                if (!isset($row)) {
                                                    break;
                                                }
                                                ;
                                                $scheduleid = $row["scheduleid"];
                                                $title = $row["title"];
                                                $docname = $row["docname"];
                                                $scheduledate = $row["scheduledate"];
                                                $scheduletime = $row["scheduletime"];

                                                if ($scheduleid == "") {
                                                    break;
                                                }

                                                echo '
                                        <td style="width: 25%;">
                                                <div  class="dashboard-items search-items"  >
                                                
                                                    <div style="width:100%">
                                                            <div class="h1-search">
                                                                ' . substr($title, 0, 21) . '
                                                            </div><br>
                                                            <div class="h3-search">
                                                                ' . substr($docname, 0, 30) . '
                                                            </div>
                                                            <div class="h4-search">
                                                                ' . $scheduledate . '<br>' . t('starts') . ' <b>@' . substr($scheduletime, 0, 5) . '</b> (24h)
                                                            </div>
                                                            <br>
                                                            <a href="booking.php?id=' . $scheduleid . '" ><button  class="login-btn btn-primary-soft btn "  style="padding-top:11px;padding-bottom:11px;width:100%"><font class="tn-in-text">' . t('book_now') . '</font></button></a>
                                                    </div>
                                                            
                                                </div>
                                            </td>';

                                            }
                                            echo "</tr>";


                                            // echo '<tr>
                                            //     <td> &nbsp;'.
                                            //     substr($title,0,30)
                                            //     .'</td>
                                    
                                            //     <td style="text-align:center;">
                                            //         '.substr($scheduledate,0,10).' '.substr($scheduletime,0,5).'
                                            //     </td>
                                            //     <td style="text-align:center;">
                                            //         '.$nop.'
                                            //     </td>
                                    
                                            //     <td>
                                            //     <div style="display:flex;justify-content: center;">
                                    
                                            //     <a href="?action=view&id='.$scheduleid.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                            //    &nbsp;&nbsp;&nbsp;
                                            //    <a href="?action=drop&id='.$scheduleid.'&name='.$title.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Cancel Session</font></button></a>
                                            //     </div>
                                            //     </td>
                                            // </tr>';
                                    
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

        // ✅ متغيرات لتتبع العدد السابق لمنع الوميض
        let lastChatCount = -1;
        let lastAdminCount = -1;

        // ✅ تحديث Badge رسائل الدردشة
        function checkChatNotifications() {
            const chatBadge = document.getElementById('chatNotificationBadge');
            const homeBadge = document.getElementById('homeChatBadge');

            const sender = <?php echo $userid; ?>;

            if (!sender || sender <= 0) {
                if (chatBadge) chatBadge.style.display = 'none';
                if (homeBadge) homeBadge.style.display = 'none';
                return;
            }

            fetch(`check_notifications.php?user_id=${sender}&user_type=patient&t=${Date.now()}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    const count = parseInt(data.count) || 0;

                    if (count !== lastChatCount) {
                        lastChatCount = count;

                        // Update chat button badge (if exists)
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

        // ✅ تحديث Badge رسائل الأدمن في Settings
        function updateAdminMessages() {
            const settingsBadge = document.getElementById('settingsNotifBadge');
            const sender = <?php echo $userid; ?>;

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
    </script>
</body>

</html>