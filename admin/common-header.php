<?php
// This file should be included after translations.php
// It provides the common sidebar menu and language switcher
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
    <link rel="stylesheet" href="../css/admin/common.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="stylesheet" href="../css/language.css">
        
    <title><?php echo isset($page_title) ? $page_title : t('dashboard'); ?></title>
    <style>
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
        
        .profile-title {
            color: #333 !important;
            font-weight: 600 !important;
        }
        
        .profile-subtitle {
            color: #666 !important;
            font-weight: 400 !important;
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
        
        .logout-btn {
            width: 100% !important;
            margin-top: 15px !important;
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
                                    <p class="profile-title"><?php echo t('administrator'); ?></p>
                                    <p class="profile-subtitle">admin@gmail.com</p>
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
                    <td class="menu-btn menu-icon-dashbord <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'menu-active menu-icon-dashbord-active' : ''; ?>" >
                        <a href="index.php" class="non-style-link-menu <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'non-style-link-menu-active' : ''; ?>"><div><p class="menu-text"><?php echo t('dashboard'); ?></p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor <?php echo (basename($_SERVER['PHP_SELF']) == 'doctors.php') ? 'menu-active menu-icon-doctor-active' : ''; ?>">
                        <a href="doctors.php" class="non-style-link-menu <?php echo (basename($_SERVER['PHP_SELF']) == 'doctors.php') ? 'non-style-link-menu-active' : ''; ?>"><div><p class="menu-text"><?php echo t('doctors'); ?></p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-schedule <?php echo (basename($_SERVER['PHP_SELF']) == 'schedule.php' || basename($_SERVER['PHP_SELF']) == 'schedule_temp.php' || basename($_SERVER['PHP_SELF']) == 'schedule_fixed.php') ? 'menu-active' : ''; ?>">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('schedule'); ?></p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment <?php echo (basename($_SERVER['PHP_SELF']) == 'appointment.php') ? 'menu-active menu-icon-appoinment-active' : ''; ?>">
                        <a href="appointment.php" class="non-style-link-menu <?php echo (basename($_SERVER['PHP_SELF']) == 'appointment.php') ? 'non-style-link-menu-active' : ''; ?>"><div><p class="menu-text"><?php echo t('appointment'); ?></p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient <?php echo (basename($_SERVER['PHP_SELF']) == 'patient.php') ? 'menu-active menu-icon-patient-active' : ''; ?>">
                        <a href="patient.php" class="non-style-link-menu <?php echo (basename($_SERVER['PHP_SELF']) == 'patient.php') ? 'non-style-link-menu-active' : ''; ?>"><div><p class="menu-text"><?php echo t('patients'); ?></p></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">


