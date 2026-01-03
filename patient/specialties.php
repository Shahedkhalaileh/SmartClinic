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

$query = "SELECT * FROM specialties";
$result = mysqli_query($database, $query);

$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];
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
  <title><?php echo t('specialties'); ?></title>

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
      margin: 0;
      padding: 0;
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

    .menu-text {
      color: #444 !important;
      font-weight: 600 !important;
      font-size: 15px !important;
    }

    .menu-btn:hover .menu-text,
    .menu-active .menu-text {
      color: white !important;
    }

    .profile-title {
      color: #333 !important;
      font-weight: 700 !important;
      font-size: 16px !important;
    }

    .profile-subtitle {
      color: #666 !important;
      font-size: 13px !important;
    }

    .non-style-link-menu {
      color: #444 !important;
    }

    .non-style-link-menu:hover {
      color: #4a31b9 !important;
    }

    .menu-active .non-style-link-menu {
      color: white !important;
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

    .dash-body h1 {
      text-align: center;
      font-size: 42px;
      font-weight: 800;
      margin-bottom: 60px;
      background: linear-gradient(135deg, #4a31b9 0%, #0c0242 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      letter-spacing: -1px;
      position: relative;
      padding-bottom: 20px;
    }

    .dash-body h1::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(135deg, #4a31b9 0%, #0c0242 100%);
      border-radius: 2px;
    }

    .specialties {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
      padding: 0;
    }

    .card {
      background: white;
      padding: 40px 30px;
      border-radius: 20px;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
      transition: all 0.4s ease;
      border: 2px solid rgba(24, 25, 129, 0.1);
      position: relative;
      overflow: hidden;
      cursor: pointer;
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 200px;
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(135deg, #4a31b9 0%, #0c0242 100%);
      transform: scaleX(0);
      transition: transform 0.4s;
    }

    .card:hover::before {
      transform: scaleX(1);
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 40px rgba(24, 25, 129, 0.25);
      border-color: #4a31b9;
      background: linear-gradient(135deg, rgba(24, 25, 129, 0.02) 0%, rgba(16, 2, 66, 0.02) 100%);
    }

    .card-icon {
      font-size: 56px;
      margin-bottom: 20px;
      filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
      transition: transform 0.4s;
      display: block;
    }

    .card:hover .card-icon {
      transform: scale(1.1) rotate(5deg);
    }

    .card h3 {
      margin: 0;
      font-size: 22px;
      font-weight: 700;
      color: #333;
      letter-spacing: -0.3px;
      transition: color 0.4s;
    }

    .card:hover h3 {
      color: #4a31b9;
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

    a {
      text-decoration: none;
      color: inherit;
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

    @media (max-width: 1024px) {
      .dash-body {
        margin: 15px !important;
        padding: 15px !important;
      }

      .specialties {
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
        width: 280px !important;
      }

      .menu.active {
        left: 0 !important;
      }

      .menu-overlay.active {
        display: block;
      }

      .dash-body {
        margin-left: 0 !important;
        margin: 10px !important;
        padding: 20px !important;
        width: 100% !important;
      }

      .container {
        flex-direction: column !important;
      }

      .specialties {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
      }
    }

    @media (max-width: 480px) {
      .menu {
        width: 100% !important;
      }

      .dash-body {
        margin: 5px !important;
        padding: 15px !important;
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
                <td>
                  <p class="profile-title"><?php echo substr($username, 0, 13); ?>..</p>
                  <p class="profile-subtitle"><?php echo substr($useremail, 0, 22); ?></p>
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
              </div>
            </a>
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
              </div>
            </a>
          </td>
        </tr>
        <tr class="menu-row">
          <td class="menu-btn menu-icon-appoinment menu-active menu-icon-specialties-active ">
            <a href="specialties.php" class="non-style-link-menu">
              <div>
                <p class="menu-text"><?php echo t('specialties'); ?></p>
              </div>
            </a>
          </td>
        </tr>
        <tr class="menu-row">
          <td class="menu-btn menu-icon-ai">
            <a href="chatbot.php" class="non-style-link-menu">
              <div>
                <p class="menu-text">Chat Bot</p>
              </div>
            </a>
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


    <div class="dash-body">
      <h1><?php echo t('select_a_specialty'); ?></h1>

      <div class="specialties">
        <?php
        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $icon = getSpecialtyIcon($row['sname']);
            echo '
          <a href="doctors_by_specialty.php?id=' . $row['id'] . '">
            <div class="card">
              <div class="card-icon">' . $icon . '</div>
              <h3>' . translateSpecialty($row['sname']) . '</h3>
            </div>
          </a>
        ';
          }
        } else {
          echo "<p style='text-align:center; font-size:18px; color:#666; padding: 40px;'>" . t('no_specialties') . "</p>";
        }
        ?>
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
        if (menu && !menu.contains(event.target) && toggle && !toggle.contains(event.target) && overlay && overlay.contains(event.target)) {
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