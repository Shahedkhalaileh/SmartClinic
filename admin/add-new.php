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


function redirectWithData($error, $name, $email, $nic, $tele, $spec){
    $params = http_build_query([
        'action' => 'add',
        'error'  => $error,
        'name'   => $name,
        'email'  => $email,
        'nic'    => $nic,
        'tele'   => $tele,
        'spec'   => $spec,
    ]);
    header("Location: doctors.php?".$params);
    exit();
}

if($_POST){
    $name     = trim($_POST['name'] ?? '');
    $nic      = trim($_POST['nic'] ?? '');
    $spec     = intval($_POST['spec'] ?? 0);
    $email    = trim($_POST['email'] ?? '');
    $tele     = trim($_POST['Tele'] ?? '');
    $password = $_POST['password'] ?? '';
    $cpassword= $_POST['cpassword'] ?? '';

    // Sanitize inputs
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirectWithData(7, $name, $email, $nic, $tele, $spec);
    } else {
        // Check if email domain has valid MX records
        $email_parts = explode('@', $email);
        if (count($email_parts) == 2) {
            $domain = trim($email_parts[1]);
            
            // More comprehensive domain validation
            $domain_valid = false;
            
            // List of common valid email providers
            $valid_providers = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com', 
                              'aol.com', 'mail.com', 'protonmail.com', 'yandex.com', 'zoho.com',
                              'live.com', 'msn.com', 'ymail.com', 'rocketmail.com', 'inbox.com',
                              'gmx.com', 'mail.ru', 'qq.com', '163.com', 'sina.com'];
            
            $domain_lower = strtolower($domain);
            
            // Check if it's a known valid provider
            if (in_array($domain_lower, $valid_providers)) {
                $domain_valid = true;
            }
            // Check MX records using dns_get_record (works better on Windows)
            elseif (function_exists('dns_get_record')) {
                $mx_records = @dns_get_record($domain, DNS_MX);
                if ($mx_records && count($mx_records) > 0) {
                    $domain_valid = true;
                } else {
                    // Try A records as fallback
                    $a_records = @dns_get_record($domain, DNS_A);
                    if ($a_records && count($a_records) > 0) {
                        $domain_valid = true;
                    }
                }
            }
            // Fallback to checkdnsrr
            elseif (function_exists('checkdnsrr')) {
                if (checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A')) {
                    $domain_valid = true;
                }
            }
            // Last resort: try gethostbyname
            elseif (function_exists('gethostbyname')) {
                $ip = @gethostbyname($domain);
                if ($ip !== $domain && filter_var($ip, FILTER_VALIDATE_IP)) {
                    $domain_valid = true;
                }
            }
            
            // Block obviously fake domains
            $fake_domains = ['test.com', 'example.com', 'fake.com', 'invalid.com', 'test.test', 
                           '123.com', 'abc.com', 'xyz.com', 'temp.com', 'dummy.com'];
            if (in_array($domain_lower, $fake_domains)) {
                $domain_valid = false;
            }
            
            // Block domains that are too short or suspicious
            if (strlen($domain) < 4 || preg_match('/^[0-9]+$/', $domain)) {
                $domain_valid = false;
            }
            
            if (!$domain_valid) {
                redirectWithData(7, $name, $email, $nic, $tele, $spec);
            }
        }
    }

    if (empty($name) || empty($email) || empty($nic) || empty($tele) || empty($password)) {
        redirectWithData(3, $name, $email, $nic, $tele, $spec);
    }

    if ($password != $cpassword){
        redirectWithData(2, $name, $email, $nic, $tele, $spec);
    }

    if (strlen($password) < 6) {
        redirectWithData(2, $name, $email, $nic, $tele, $spec);
    }

    $stmt = $database->prepare("SELECT * FROM webuser WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 1){
        redirectWithData(1, $name, $email, $nic, $tele, $spec);
    }

    $stmt = $database->prepare("SELECT doctel FROM doctor WHERE doctel=?");
    $stmt->bind_param("s", $tele);
    $stmt->execute();
    $checkTele = $stmt->get_result();
    
    if ($checkTele->num_rows > 0) {
        redirectWithData(5, $name, $email, $nic, '', $spec);
    }

    $stmt = $database->prepare("SELECT docnic FROM doctor WHERE docnic=?");
    $stmt->bind_param("s", $nic);
    $stmt->execute();
    $checkNIC = $stmt->get_result();
    
    if ($checkNIC->num_rows > 0) {
        redirectWithData(6, $name, $email, '', $tele, $spec);
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $database->prepare("INSERT INTO doctor(docemail,docname,docpassword,docnic,doctel,specialties) VALUES(?,?,?,?,?,?)");
    $stmt->bind_param("sssssi", $email, $name, $hashed_password, $nic, $tele, $spec);
    $stmt->execute();

    $stmt = $database->prepare("INSERT INTO webuser VALUES(?, 'd')");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    header("Location: doctors.php?action=add&error=4");
    exit();

} else {
    header("Location: doctors.php?action=add&error=3");
    exit();
}
?>