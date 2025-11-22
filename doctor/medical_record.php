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
  <title><?php echo t('medical_record'); ?></title>
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
    border: 1px solid rgba(102, 126, 234, 0.1);
  }
  
  .form-card h2 {
    text-align: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
  
  select, textarea, input {
    width: 100%;
    padding: 12px 15px;
    margin-top: 8px;
    border: 2px solid rgba(102, 126, 234, 0.2);
    border-radius: 10px;
    font-size: 15px;
    background: #fff;
    transition: all 0.3s ease;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  
  select:focus, textarea:focus, input:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }
  
  textarea {
    resize: vertical;
    min-height: 80px;
  }
  
  button {
    width: 100%;
    padding: 14px;
    margin-top: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }
  
  button:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5);
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
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
              <a href="../logout.php"><input type="button" value="<?php echo t('logout'); ?>" class="logout-btn btn-primary-soft btn"></a>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr class="menu-row"><td class="menu-btn menu-icon-dashbord"><a href="index.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('dashboard'); ?></p></div></a></td></tr>
    <tr class="menu-row"><td class="menu-btn menu-icon-appoinment"><a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('my_appointments'); ?></p></div></a></td></tr>
    <tr class="menu-row"><td class="menu-btn menu-icon-session"><a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('my_sessions'); ?></p></div></a></td></tr>
    <tr class="menu-row"><td class="menu-btn menu-icon-patient"><a href="patient.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('my_patients'); ?></p></div></a></td></tr>
    <tr class="menu-row"><td class="menu-btn menu-icon-patient menu-active"><a href="medical_record.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text"><?php echo t('medical_records'); ?></p></div></a></td></tr>
    <tr class="menu-row"><td class="menu-btn menu-icon-settings"><a href="settings.php" class="non-style-link-menu"><div><p class="menu-text"><?php echo t('settings'); ?></p></div></a></td></tr>
  </table>
</div>

<div class="dash-body">
  <div class="form-card">
    <?php if (isset($msg)) echo "<p class='msg'>$msg</p>"; ?>
    <h2><?php echo t('medical_record'); ?></h2>

    <form action="" method="POST">
      <label><?php echo t('select_patient'); ?>:</label>
      <select name="pid" required>
        <option value=""><?php echo t('select_the_patient'); ?></option>
        <?php while($p = $patients->fetch_assoc()) echo "<option value='{$p['pid']}'>{$p['pname']}</option>"; ?>
      </select>

      <label><?php echo t('weight_kg'); ?></label>
      <input type="text" name="weight" placeholder="<?php echo t('enter_weight'); ?>">

      <label><?php echo t('height_cm'); ?></label>
      <input type="text" name="height" placeholder="<?php echo t('enter_height'); ?>">

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
  document.addEventListener('click', function(event) {
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
</script>
</body>
</html>
