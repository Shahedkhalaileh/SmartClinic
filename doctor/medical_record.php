<?php
session_start();
include("../connection.php");
include("../translations.php");

if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'd') {
  header("location: ../login.php");
  exit();
}

$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$docid = $userfetch["docid"];
$username = $userfetch["docname"];

if (isset($_POST["save"])) {
  $pid = mysqli_real_escape_string($database, $_POST["pid"]);
  $diagnosis = mysqli_real_escape_string($database, $_POST["diagnosis"]);
  $treatment = mysqli_real_escape_string($database, $_POST["treatment"]);
  $notes = mysqli_real_escape_string($database, $_POST["notes"]);
  $weight = mysqli_real_escape_string($database, $_POST["weight"]);
  $height = mysqli_real_escape_string($database, $_POST["height"]);
  $allergy = isset($_POST["allergy"]) ? mysqli_real_escape_string($database, $_POST["allergy"]) : '';
  $surgical_history = mysqli_real_escape_string($database, $_POST["surgical_history"]);
  $diabetes = mysqli_real_escape_string($database, $_POST["diabetes"]);
  $hypertension = mysqli_real_escape_string($database, $_POST["hypertension"]);

  // --- تحقق من القيود على الطول والوزن ---
  $validation_error = '';

  // الطول: 3 أرقام فقط، من 0 إلى 270 سم، بدون فاصلة
  if ($height !== '') {
    if (!preg_match('/^\d{1,3}$/', $height)) {
      $validation_error = isArabic()
        ? "الطول يجب أن يكون عدداً صحيحاً مكوناً من 3 أرقام كحد أقصى بدون فاصلة"
        : "Height must be an integer with up to 3 digits and no decimals";
    } else {
      $h_val = (int) $height;
      if ($h_val < 0 || $h_val > 270) {
        $validation_error = isArabic()
          ? "الطول يجب أن يكون بين 0 و 270 سم"
          : "Height must be between 0 and 270 cm";
      }
    }
  }

  // الوزن: لا يزيد عن 500، وبه رقمين كحد أقصى بعد الفاصلة
  if ($validation_error === '' && $weight !== '') {
    if (!preg_match('/^\d{1,3}(\.\d{1,2})?$/', $weight)) {
      $validation_error = isArabic()
        ? "الوزن يجب أن يكون رقماً صحيحاً أو عشرياً برقمين كحد أقصى بعد الفاصلة"
        : "Weight must be a number with up to 2 decimal places";
    } else {
      $w_val = (float) $weight;
      if ($w_val < 0 || $w_val > 500) {
        $validation_error = isArabic()
          ? "الوزن يجب أن لا يزيد عن 500 كغ"
          : "Weight must not exceed 500 kg";
      }
    }
  }

  if ($validation_error !== '') {
    $msg = "❌ " . $validation_error;
  } else {
    // Check if medicalrecords table exists and has the allergy column
    $check_table = $database->query("SHOW TABLES LIKE 'medicalrecords'");
    if ($check_table && $check_table->num_rows > 0) {
      $check_column = $database->query("SHOW COLUMNS FROM medicalrecords LIKE 'allergy'");
      if ($check_column && $check_column->num_rows > 0) {
        $sql = "INSERT INTO medicalrecords (pid, docid, diagnosis, treatment, notes, weight, height, allergy, surgical_history, diabetes, hypertension) 
                  VALUES ('$pid', '$docid', '$diagnosis', '$treatment', '$notes', '$weight', '$height', '$allergy', '$surgical_history', '$diabetes', '$hypertension')";
      } else {
        $sql = "INSERT INTO medicalrecords (pid, docid, diagnosis, treatment, notes, weight, height, surgical_history, diabetes, hypertension) 
                  VALUES ('$pid', '$docid', '$diagnosis', '$treatment', '$notes', '$weight', '$height', '$surgical_history', '$diabetes', '$hypertension')";
      }
    } else {
      $sql = "INSERT INTO medicalrecords (pid, docid, diagnosis, treatment, notes, weight, height, surgical_history, diabetes, hypertension) 
                VALUES ('$pid', '$docid', '$diagnosis', '$treatment', '$notes', '$weight', '$height', '$surgical_history', '$diabetes', '$hypertension')";
    }

    if ($database->query($sql)) {
      $msg = t("medical_record_saved_successfully");
    } else {
      $msg = "Error: " . $database->error;
    }
  }
}

$patients = $database->query("SELECT * FROM patient");
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
  <title><?php echo t('medical_record'); ?></title>
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

    .dash-body {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(15px) !important;
      border-radius: 25px !important;
      margin: 20px !important;
      padding: 30px !important;
      box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1) !important;
      flex: 1 !important;
      overflow-y: auto !important;
    }

    .form-card {
      background: white;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
      width: 100%;
      border: 1px solid rgba(24, 25, 129, 0.1);
    }

    .form-card h2 {
      text-align: center;
      background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      font-size: 28px;
      font-weight: 800;
      margin-bottom: 30px;
    }

    label {
      font-weight: 600;
      color: #333;
      margin-top: 20px;
      display: block;
      font-size: 15px;
    }

    select,
    textarea,
    input {
      width: 100%;
      padding: 12px 15px;
      margin-top: 8px;
      border: 2px solid rgba(24, 25, 129, 0.2);
      border-radius: 10px;
      font-size: 15px;
      background: #fff;
      transition: all 0.3s ease;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    select:focus,
    textarea:focus,
    input:focus {
      border-color: #4a31b9;
      outline: none;
      box-shadow: 0 0 0 3px rgba(24, 25, 129, 0.1);
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    button {
      width: 100%;
      padding: 14px;
      margin-top: 30px;
      background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%);
      color: #fff;
      border: none;
      border-radius: 25px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(24, 25, 129, 0.3);
    }

    button:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(24, 25, 129, 0.5);
      background: linear-gradient(240deg, #0c0242ff 0%, #4a31b9ff 100%);
    }

    .logout-btn {
      width: 100% !important;
      margin-top: 15px !important;
    }

    p.msg {
      text-align: center;
      color: #388e3c;
      font-weight: bold;
      margin-bottom: 20px;
      font-size: 16px;
      padding: 12px;
      background: rgba(56, 142, 60, 0.1);
      border-radius: 10px;
      border: 1px solid rgba(56, 142, 60, 0.3);
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
      animation: pulse 1.5s infinite;
      z-index: 10;
    }

    [dir="rtl"] .menu-notif-badge {
      right: auto;
      left: 12px;
    }

    .menu-row {
      position: relative;
    }

    @keyframes pulse {

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
          <td class="menu-btn menu-icon-dashbord">
            <a href="index.php" class="non-style-link-menu">
              <div style="position: relative;">
                <p class="menu-text"><?php echo t('dashboard'); ?></p>
                <span class="menu-notif-badge" id="dashboardChatBadge"></span>
              </div>
            </a>
          </td>
        </tr>
        <tr class="menu-row">
          <td class="menu-btn menu-icon-appoinment"><a href="appointment.php" class="non-style-link-menu">
              <div>
                <p class="menu-text"><?php echo t('my_appointments'); ?></p>
              </div>
            </a></td>
        </tr>
        <tr class="menu-row">
          <td class="menu-btn menu-icon-session"><a href="schedule.php" class="non-style-link-menu">
              <div>
                <p class="menu-text"><?php echo t('my_sessions'); ?></p>
              </div>
            </a></td>
        </tr>
        <tr class="menu-row">
          <td class="menu-btn menu-icon-patient"><a href="patient.php" class="non-style-link-menu">
              <div>
                <p class="menu-text"><?php echo t('my_patients'); ?></p>
              </div>
            </a></td>
        </tr>
        <tr class="menu-row">
          <td class="menu-btn menu-icon-patient menu-active"><a href="medical_record.php"
              class="non-style-link-menu non-style-link-menu-active">
              <div>
                <p class="menu-text"><?php echo t('medical_records'); ?></p>
              </div>
            </a></td>
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
      <div class="form-card">
        <?php if (isset($msg))
          echo "<p class='msg'>$msg</p>"; ?>
        <h2><?php echo t('medical_record'); ?></h2>

        <form action="" method="POST">
          <label><?php echo t('select_patient'); ?>:</label>
          <select name="pid" required>
            <option value=""><?php echo t('select_the_patient'); ?></option>
            <?php while ($p = $patients->fetch_assoc())
              echo "<option value='{$p['pid']}'>{$p['pname']}</option>"; ?>
          </select>

          <label><?php echo t('weight_kg'); ?></label>
          <input type="number" name="weight" placeholder="<?php echo t('enter_weight'); ?>" min="0" max="500"
            step="0.01" oninput="validateWeight(this)">

          <label><?php echo t('height_cm'); ?></label>
          <input type="number" name="height" placeholder="<?php echo t('enter_height'); ?>" min="0" max="270" step="1"
            oninput="validateHeight(this)">

          <label><?php echo t('allergy'); ?></label>
          <input type="text" name="allergy" placeholder="<?php echo t('enter_any_allergies'); ?>">

          <label><?php echo t('surgical_history'); ?></label>
          <textarea name="surgical_history" placeholder="<?php echo t('enter_surgical_history'); ?>"></textarea>

          <label><?php echo t('diabetes'); ?></label>
          <select name="diabetes" required>
            <option value="No"><?php echo t('no'); ?></option>
            <option value="Yes"><?php echo t('yes'); ?></option>
          </select>

          <label><?php echo t('hypertension'); ?></label>
          <select name="hypertension" required>
            <option value="No"><?php echo t('no'); ?></option>
            <option value="Yes"><?php echo t('yes'); ?></option>
          </select>

          <label><?php echo t('diagnosis'); ?></label>
          <textarea name="diagnosis" required placeholder="<?php echo t('enter_diagnosis'); ?>"></textarea>

          <label><?php echo t('treatment'); ?></label>
          <textarea name="treatment" placeholder="<?php echo t('enter_treatment'); ?>"></textarea>

          <label><?php echo t('additional_notes'); ?></label>
          <textarea name="notes" placeholder="<?php echo t('enter_additional_notes'); ?>"></textarea>

          <button type="submit" name="save"><?php echo t('save_record'); ?></button>
        </form>
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


    function validateWeight(input) {
      let value = input.value;

      if (value.includes(".")) {
        let parts = value.split(".");
        if (parts[1].length > 2) {
          parts[1] = parts[1].substring(0, 2);
          input.value = parts.join(".");
        }
      }

      if (parseFloat(input.value) > 500) {
        input.value = 500;
      }
      if (parseFloat(input.value) < 0) {
        input.value = 0;
      }
    }


    function validateHeight(input) {

      let value = input.value.replace(/[^0-9]/g, '');


      if (value.length > 3) {
        value = value.slice(0, 3);
      }

      if (value !== '') {
        let num = parseInt(value, 10);


        if (num > 270) {
          num = 270;
        }

        input.value = num;
      } else {
        input.value = value;
      }
    }

    const docid = <?php echo $docid; ?>;
    console.log("Doctor ID:", docid);



    // ✅ متغيرات لتتبع العدد السابق لمنع الوميض
    let lastChatCount = -1;
    let lastAdminCount = -1;

    // ✅ تحديث Badge رسائل الدردشة في Dashboard
    function updateChatNotifications() {
      fetch(`check_notifications.php?user_id=${docid}&user_type=doctor&t=${Date.now()}`)
        .then(res => {
          if (!res.ok) {
            throw new Error('Network response was not ok');
          }
          return res.json();
        })
        .then(data => {
          const count = parseInt(data.count) || 0;
          const dashboardBadge = document.getElementById('dashboardChatBadge');

          // ✅ فقط قم بالتحديث إذا تغير عدد الرسائل (لمنع الوميض)
          if (count !== lastChatCount) {
            lastChatCount = count;

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
          console.error('Error checking chat notifications:', err);
        });
    }

    // ✅ تحديث Badge رسائل الأدمن في Settings
    function updateAdminMessages() {
      fetch(`get_admin_messages_count.php?docid=${docid}`)
        .then(r => r.json())
        .then(data => {
          const count = parseInt(data.admin_unread) || 0;
          const settingsBadge = document.getElementById('settingsNotifBadge');

          // ✅ فقط قم بالتحديث إذا تغير عدد الرسائل (لمنع الوميض)
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
      updateChatNotifications();
      updateAdminMessages();

      // تحديث دوري كل 3 ثوانٍ
      setInterval(function () {
        updateChatNotifications();
        updateAdminMessages();
      }, 3000);
    });


  </script>
</body>

</html>