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

    if (!isset($_GET['id'])) {
        header("location: specialties.php");
        exit();
    }

    $specialty_id = intval($_GET['id']);

    $spec_query = "SELECT sname FROM specialties WHERE id = $specialty_id";
    $spec_result = mysqli_query($database, $spec_query);
    
    if (mysqli_num_rows($spec_result) == 0) {
        header("location: specialties.php");
        exit();
    }
    
    $specialty = mysqli_fetch_assoc($spec_result)['sname'];

    $query = "SELECT * FROM doctor WHERE specialties = $specialty_id";
    $result = mysqli_query($database, $query);

    $userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];
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
  <title>Doctors in <?php echo $specialty; ?></title>

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
        margin: 0;
        padding: 0;
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
        color: #667eea !important;
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
    }

    .dash-body h1 {
      text-align: center;
      font-size: 42px;
      font-weight: 800;
      margin-bottom: 60px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 2px;
    }

    .doctors {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 25px;
      padding: 0;
    }

    .card {
      background: white;
      padding: 35px 30px;
      border-radius: 20px;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
      transition: all 0.4s ease;
      border: 2px solid rgba(102, 126, 234, 0.1);
      position: relative;
      overflow: hidden;
      cursor: pointer;
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      min-height: 280px;
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      transform: scaleX(0);
      transition: transform 0.4s;
    }

    .card:hover::before {
      transform: scaleX(1);
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 40px rgba(102, 126, 234, 0.25);
      border-color: #667eea;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
    }

    .card-icon {
      font-size: 64px;
      margin-bottom: 20px;
      filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
      transition: transform 0.4s;
      display: block;
    }

    .card:hover .card-icon {
      transform: scale(1.1) rotate(5deg);
    }

    .card h3 {
      margin: 0 0 15px 0;
      font-size: 24px;
      font-weight: 700;
      color: #333;
      letter-spacing: -0.3px;
      transition: color 0.4s;
    }

    .card:hover h3 {
      color: #667eea;
    }

    .card p {
      margin: 8px 0;
      font-size: 14px;
      color: #666;
      line-height: 1.6;
    }

    .card .doctor-info {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      width: 100%;
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

    .book-btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 12px;
      padding: 12px 30px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 20px;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
      text-decoration: none;
      display: inline-block;
    }

    .book-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    .no-doctors {
      text-align: center;
      font-size: 18px;
      color: #666;
      padding: 60px 40px;
      background: white;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }

    @media (max-width: 768px) {
      .menu {
        position: relative;
        width: 100%;
        height: auto;
        box-shadow: none;
      }

      .dash-body {
        margin-left: 0;
        padding: 20px;
      }

      .doctors {
        grid-template-columns: 1fr;
      }
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
      
      .doctors {
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
      
      .doctors {
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
              <p class="profile-title"><?php echo substr($username,0,13); ?>..</p>
              <p class="profile-subtitle"><?php echo substr($useremail,0,22); ?></p>
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

    <tr class="menu-row">
      <td class="menu-btn menu-icon-home">
        <a href="index.php">
          <div><p class="menu-text">Home</p></div>
        </a>
      </td>
    </tr>
    <tr class="menu-row">
      <td class="menu-btn menu-icon-doctor">
        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></div></a>
      </td>
    </tr>
    <tr class="menu-row">
      <td class="menu-btn menu-icon-session">
        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Scheduled Sessions</p></div></a>
      </td>
    </tr>
    <tr class="menu-row">
      <td class="menu-btn menu-icon-appoinment">
        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></div></a>
      </td>
    </tr>
    <tr class="menu-row">
      <td class="menu-btn menu-icon-appoinment menu-active menu-icon-specialties-active ">
        <a href="specialties.php" class="non-style-link-menu"><div><p class="menu-text">Specialties</p></div></a>
      </td>
    </tr>
    <tr class="menu-row">
      <td class="menu-btn menu-icon-settings">
        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a>
      </td>
    </tr>
  </table>
</div>


<div class="dash-body">
  <h1>Doctors in <?php echo htmlspecialchars($specialty); ?></h1>

  <div class="doctors">
    <?php
    if (mysqli_num_rows($result) > 0) {
      $icons = ['👨‍⚕️', '👩‍⚕️', '🩺', '💊', '🫀', '🧠', '👁️', '🦷'];
      $iconIndex = 0;
      while($row = mysqli_fetch_assoc($result)) {
        $icon = $icons[$iconIndex % count($icons)];
        $iconIndex++;
        echo '
          <div class="card">
            <div class="doctor-info">
              <div class="card-icon">'.$icon.'</div>
              <h3>'.htmlspecialchars($row['docname']).'</h3>
              <p><strong>Email:</strong> '.htmlspecialchars($row['docemail']).'</p>
              <p><strong>Phone:</strong> '.htmlspecialchars($row['doctel']).'</p>
            </div>
            <a href="schedule.php?doctor_id='.$row['docid'].'" class="book-btn">Book Appointment</a>
          </div>
        ';
      }
    } else {
      echo '<div class="no-doctors">
              <p style="font-size: 24px; margin-bottom: 10px;">👨‍⚕️</p>
              <p>No doctors found in this specialty.</p>
              <p style="margin-top: 20px;"><a href="specialties.php" style="color: #667eea; text-decoration: underline;">← Back to Specialties</a></p>
            </div>';
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
