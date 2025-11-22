<?php
include("connection.php");
include("translations.php");

// Get statistics
$stats = [
    'doctors' => 0,
    'specialties' => 0,
    'patients' => 0,
    'appointments' => 0
];

// Count doctors
$result = $database->query("SELECT COUNT(*) as count FROM doctor");
if ($result) {
    $stats['doctors'] = $result->fetch_assoc()['count'];
}

// Count specialties
$result = $database->query("SELECT COUNT(*) as count FROM specialties");
if ($result) {
    $stats['specialties'] = $result->fetch_assoc()['count'];
}

// Count patients
$result = $database->query("SELECT COUNT(*) as count FROM patient");
if ($result) {
    $stats['patients'] = $result->fetch_assoc()['count'];
}

// Count appointments
$result = $database->query("SELECT COUNT(*) as count FROM appointment");
if ($result) {
    $stats['appointments'] = $result->fetch_assoc()['count'];
}

// Get specialties with images (check if image column exists)
$check_image_column = $database->query("SHOW COLUMNS FROM specialties LIKE 'image'");
if ($check_image_column && $check_image_column->num_rows > 0) {
    // If image column exists, get only specialties with images
    $specialties_result = $database->query("SELECT * FROM specialties WHERE image IS NOT NULL AND image != '' ORDER BY id LIMIT 6");
} else {
    // If no image column, get all specialties
    $specialties_result = $database->query("SELECT * FROM specialties LIMIT 6");
}

// Get featured doctors
$doctors_result = $database->query("SELECT d.*, s.sname as specialty_name 
                                    FROM doctor d 
                                    LEFT JOIN specialties s ON d.specialties = s.id 
                                    LIMIT 6");
?>
<!DOCTYPE html>
<html lang="<?php echo getLang(); ?>" dir="<?php echo isArabic() ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Clinic - <?php echo t('hero_title'); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
        }


        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Navigation */
        nav {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            padding: 20px 0;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 30px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            text-decoration: none;
            color: #444;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 15px;
        }
        
        /* Ensure language switcher is visible */
        .nav-links .language-switcher {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .nav-links a:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.12);
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 32px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 100px 20px;
            color: white;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero h1 {
            font-size: 64px;
            font-weight: 900;
            margin-bottom: 25px;
            text-shadow: 3px 3px 15px rgba(0, 0, 0, 0.3);
            letter-spacing: -1px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 24px;
            margin-bottom: 50px;
            opacity: 0.98;
            font-weight: 400;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            padding: 14px 32px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            border: 2px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            display: inline-block;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary:hover {
            background: white;
            color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
            border-color: white;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Statistics */
        .stats {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 60px 50px;
            margin: -60px auto 100px;
            max-width: 1200px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.15);
            animation: slideUp 0.6s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .stat-card {
            text-align: left;
            padding: 20px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
            transition: all 0.4s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 100px;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        .stat-card > div:first-child {
            flex: 1;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 900;
            margin-bottom: 8px;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.95;
            font-weight: 600;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 1;
            margin: 0;
        }
        
        .stat-icon {
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            margin-left: 10px;
            opacity: 0.9;
        }

        /* Sections */
        .section {
            padding: 90px 30px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            margin: 50px auto;
            border-radius: 25px;
            max-width: 1200px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .section-title {
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

        .section-title::after {
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

        /* Specialties Grid */
        .specialties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            align-items: stretch;
        }

        .specialty-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            border: 2px solid rgba(102, 126, 234, 0.1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            min-height: 280px;
            height: 100%;
        }

        .specialty-card::before {
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

        .specialty-card:hover::before {
            transform: scaleX(1);
        }

        .specialty-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.25);
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
        }

        .specialty-icon {
            font-size: 56px;
            margin-bottom: 20px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
            transition: transform 0.4s;
            flex-shrink: 0;
        }

        .specialty-card:hover .specialty-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .specialty-image {
            width: 80px !important;
            height: 80px !important;
            object-fit: cover !important;
            border-radius: 50% !important;
            margin-bottom: 20px !important;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
            transition: transform 0.4s;
            flex-shrink: 0;
        }

        .specialty-card:hover .specialty-image {
            transform: scale(1.1);
        }

        .specialty-name {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            letter-spacing: -0.3px;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 60px;
        }

        .specialty-card .btn-primary {
            flex-shrink: 0;
            margin-top: auto;
        }

        /* Doctors Grid */
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .doctor-card {
            background: white;
            padding: 35px 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            border: 2px solid rgba(102, 126, 234, 0.1);
        }

        .doctor-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.25);
            border-color: #667eea;
        }

        .doctor-avatar {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            font-weight: 800;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            transition: all 0.4s ease;
            position: relative;
        }

        .doctor-card:hover .doctor-avatar {
            transform: scale(1.1);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        }

        .doctor-name {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 12px;
            letter-spacing: -0.3px;
        }

        .doctor-specialty {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 16px;
        }

        /* Features */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: white;
            padding: 45px 35px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            border: 2px solid rgba(102, 126, 234, 0.1);
            position: relative;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.25);
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
        }

        .feature-icon {
            font-size: 56px;
            margin-bottom: 25px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
            transition: transform 0.4s;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.15) rotate(-5deg);
        }

        .feature-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #333;
            letter-spacing: -0.3px;
        }

        .feature-desc {
            color: #666;
            line-height: 1.7;
            font-size: 15px;
        }

        /* Footer */
        footer {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            padding: 50px 20px;
            text-align: center;
            margin-top: 100px;
            border-top: 1px solid rgba(102, 126, 234, 0.1);
        }

        footer p {
            color: #666;
            font-size: 15px;
            margin: 8px 0;
        }

        footer a {
            transition: color 0.3s;
        }

        footer a:hover {
            color: #667eea;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .nav-links {
                gap: 15px;
            }
            
            .nav-links a {
                padding: 8px 14px;
                font-size: 14px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            
            .specialties-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .doctors-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 15px;
                padding: 15px 20px;
            }
            
            .logo {
                font-size: 24px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
                width: 100%;
            }
            
            .nav-links a {
                padding: 8px 12px;
                font-size: 13px;
            }
            
            .hero {
                padding: 60px 20px;
            }
            
            .hero h1 {
                font-size: 36px;
            }

            .hero p {
                font-size: 18px;
                margin-bottom: 30px;
            }
            
            .hero-buttons {
                flex-direction: column;
                gap: 15px;
            }
            
            .hero-buttons a {
                width: 100%;
                max-width: 300px;
            }

            .stats {
                margin: -30px 20px 50px;
                padding: 30px 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .section {
                padding: 50px 20px;
                margin: 30px auto;
            }
            
            .section-title {
                font-size: 32px;
                margin-bottom: 40px;
            }
            
            .specialties-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .specialty-card {
                min-height: 250px;
                padding: 30px 20px;
            }
            
            .doctors-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .doctor-card {
                padding: 25px 20px;
            }
        }
        
        @media (max-width: 480px) {
            .logo {
                font-size: 20px;
            }
            
            .nav-links {
                flex-direction: column;
                width: 100%;
            }
            
            .nav-links a {
                width: 100%;
                text-align: center;
            }
            
            .hero h1 {
                font-size: 28px;
            }
            
            .hero p {
                font-size: 16px;
            }
            
            .stats {
                margin: -20px 15px 40px;
                padding: 25px 15px;
            }
            
            .section {
                padding: 40px 15px;
            }
            
            .section-title {
                font-size: 26px;
                margin-bottom: 30px;
            }
            
            .specialty-card {
                padding: 25px 15px;
                min-height: 220px;
            }
            
            .specialty-icon {
                font-size: 48px;
            }
            
            .specialty-name {
                font-size: 18px;
            }
        }
    </style>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/language.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="nav-container">
            <div class="logo">🏥 Smart Clinic</div>
            <div class="nav-links">
                <a href="#home"><?php echo t('home'); ?></a>
                <a href="#specialties"><?php echo t('specialties'); ?></a>
                <a href="#doctors"><?php echo t('doctors'); ?></a>
                <a href="#about"><?php echo t('about'); ?></a>
                <?php include("language-switcher.php"); ?>
                <a href="login.php" class="btn-primary" style="padding: 10px 24px; font-size: 14px;"><?php echo strtoupper(t('login')); ?></a>
                <a href="signup.php" class="btn-secondary" style="padding: 10px 24px; font-size: 14px;"><?php echo t('signup'); ?></a>
            </div>
        </div>
    </nav>
    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <h1><?php echo t('hero_title'); ?></h1>
            <p><?php echo t('hero_subtitle'); ?></p>
            <div class="hero-buttons">
                <a href="login.php" class="btn-primary"><?php echo t('book_appointment'); ?></a>
                <a href="#specialties" class="btn-secondary"><?php echo t('view_specialties'); ?></a>
            </div>
        </div>
    </section>

    <!-- Statistics -->
    <div class="container">
        <div class="stats">
            <div class="stats-grid">
                <div class="stat-card">
                    <div>
                        <div class="stat-number"><?php echo $stats['doctors']; ?></div>
                        <div class="stat-label"><?php echo t('expert_doctors'); ?></div>
                    </div>
                    <div class="stat-icon" style="background-image: url('img/icons/doctors-hover.svg'); background-size: contain; background-repeat: no-repeat;"></div>
                </div>
                <div class="stat-card">
                    <div>
                        <div class="stat-number"><?php echo $stats['specialties']; ?></div>
                        <div class="stat-label"><?php echo t('medical_specialties'); ?></div>
                    </div>
                    <div class="stat-icon" style="background-image: url('img/icons/session-iceblue.svg'); background-size: contain; background-repeat: no-repeat;"></div>
                </div>
                <div class="stat-card">
                    <div>
                        <div class="stat-number"><?php echo $stats['patients']; ?></div>
                        <div class="stat-label"><?php echo t('happy_patients'); ?></div>
                    </div>
                    <div class="stat-icon" style="background-image: url('img/icons/patients-hover.svg'); background-size: contain; background-repeat: no-repeat;"></div>
                </div>
                <div class="stat-card">
                    <div>
                        <div class="stat-number"><?php echo $stats['appointments']; ?></div>
                        <div class="stat-label"><?php echo t('appointments'); ?></div>
                    </div>
                    <div class="stat-icon" style="background-image: url('img/icons/book-hover.svg'); background-size: contain; background-repeat: no-repeat;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Specialties Section -->
    <section class="section" id="specialties">
        <div class="container">
            <h2 class="section-title"><?php echo t('our_medical_specialties'); ?></h2>
            <div class="specialties-grid">
                <?php 
                if ($specialties_result && $specialties_result->num_rows > 0) {
                    while ($specialty = $specialties_result->fetch_assoc()) {
                        // Check if image exists in the specialty data
                        $hasImage = isset($specialty['image']) && !empty($specialty['image']);
                        $iconHtml = '';
                        
                        if ($hasImage) {
                            // Display image if available
                            $iconHtml = '<img src="' . htmlspecialchars($specialty['image']) . '" alt="' . htmlspecialchars(translateSpecialty($specialty['sname'])) . '" class="specialty-image">';
                        } else {
                            // Get icon based on specialty name
                            $icon = getSpecialtyIcon($specialty['sname']);
                            $iconHtml = '<div class="specialty-icon">' . $icon . '</div>';
                        }
                        
                        echo '
                        <div class="specialty-card">
                            ' . $iconHtml . '
                            <div class="specialty-name">' . htmlspecialchars(translateSpecialty($specialty['sname'])) . '</div>
                            <a href="patient/specialties.php" class="btn-primary" style="padding: 8px 20px; font-size: 14px; margin-top: 10px;">' . t('view_doctors') . '</a>
                        </div>';
                    }
                } else {
                    echo '<p style="text-align: center; color: #666;">' . t('no_specialties') . '</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Doctors Section -->
    <section class="section" id="doctors">
        <div class="container">
            <h2 class="section-title"><?php echo t('our_expert_doctors'); ?></h2>
            <div class="doctors-grid">
                <?php 
                if ($doctors_result && $doctors_result->num_rows > 0) {
                    while ($doctor = $doctors_result->fetch_assoc()) {
                        $initial = strtoupper(substr($doctor['docname'], 0, 1));
                        echo '
                        <div class="doctor-card">
                            <div class="doctor-avatar">' . $initial . '</div>
                            <div class="doctor-name">Dr. ' . htmlspecialchars($doctor['docname']) . '</div>
                            <div class="doctor-specialty">' . htmlspecialchars(translateSpecialty($doctor['specialty_name'] ?? 'General Medicine')) . '</div>
                            <a href="login.php" class="btn-primary" style="padding: 8px 20px; font-size: 14px;">' . t('book_appointment') . '</a>
                        </div>';
                    }
                } else {
                    echo '<p style="text-align: center; color: #666;">' . t('no_doctors') . '</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section" id="about">
        <div class="container">
            <h2 class="section-title"><?php echo t('why_choose_us'); ?></h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <div class="feature-title"><?php echo t('quick_appointments'); ?></div>
                    <div class="feature-desc"><?php echo t('quick_appointments_desc'); ?></div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👨‍⚕️</div>
                    <div class="feature-title"><?php echo t('expert_doctors_feature'); ?></div>
                    <div class="feature-desc"><?php echo t('expert_doctors_desc'); ?></div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <div class="feature-title"><?php echo t('secure_private'); ?></div>
                    <div class="feature-desc"><?php echo t('secure_private_desc'); ?></div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <div class="feature-title"><?php echo t('easy_management'); ?></div>
                    <div class="feature-desc"><?php echo t('easy_management_desc'); ?></div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💬</div>
                    <div class="feature-title"><?php echo t('direct_communication'); ?></div>
                    <div class="feature-desc"><?php echo t('direct_communication_desc'); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p><?php echo t('copyright'); ?></p>
            <p style="margin-top: 10px;">
                <a href="login.php" style="color: #667eea; text-decoration: none; margin: 0 10px;"><?php echo t('login'); ?></a> |
                <a href="signup.php" style="color: #667eea; text-decoration: none; margin: 0 10px;"><?php echo t('signup'); ?></a>
            </p>
        </div>
    </footer>

    <script>
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>

