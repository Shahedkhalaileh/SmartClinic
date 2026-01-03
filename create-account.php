<?php
session_start();
include("connection.php");

$err_email = $err_tele = $err_pass = "";

$typed_email = $_POST['email'] ?? "";
$typed_tele = $_POST['tele'] ?? "";

if ($_POST) {

    $fname = $_SESSION['personal']['fname'];
    $lname = $_SESSION['personal']['lname'];
    $name = "$fname $lname";

    $address = $_SESSION['personal']['address'];
    $nic = $_SESSION['personal']['nic'];
    $dob = $_SESSION['personal']['dob'];
    $gender = $_SESSION['personal']['gender'];

    $email = trim($_POST['email'] ?? '');
    $tele = trim($_POST['tele'] ?? '');
    $pass = $_POST['password'] ?? '';
    $cpass = $_POST['cpassword'] ?? '';

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err_email = "❌ Please enter a valid email address";
    } else {
        // Check if email domain has valid MX records (email server exists)
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
                $err_email = "❌ This email domain does not exist. Please check your email address.";
            } else {
                $typed_email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
            }
        } else {
            $err_email = "❌ Please enter a valid email address";
        }
    }
    
    $typed_tele = htmlspecialchars($tele, ENT_QUOTES, 'UTF-8');

    // Sanitize other inputs
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($address, ENT_QUOTES, 'UTF-8');
    $nic = htmlspecialchars($nic, ENT_QUOTES, 'UTF-8');
    $gender = htmlspecialchars($gender, ENT_QUOTES, 'UTF-8');
  
    if (!empty($err_email)) {
        // Email validation error already set
    }
    elseif (!preg_match("/^07[0-9]{8}$/", $tele)) {
        $err_tele = "❌ The phone number must start with 07 and be 10 digits long";
    } 
    elseif ($pass != $cpass) {
        $err_pass = "❌ The passwords do not match";
    } 
    elseif (strlen($pass) < 6) {
        $err_pass = "❌ Password must be at least 6 characters long";
    }
    else {

        $stmt = $database->prepare("SELECT * FROM webuser WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $emailCheck = $stmt->get_result();
        
        if ($emailCheck->num_rows > 0) {
            $err_email = "❌ The email is already in use";
        } else {

            $stmt = $database->prepare("SELECT * FROM patient WHERE ptel=?");
            $stmt->bind_param("s", $tele);
            $stmt->execute();
            $phoneCheck = $stmt->get_result();
            
            if ($phoneCheck->num_rows > 0) {
                $err_tele = "❌ The phone number is already in use";
            } else {

                // Hash password
                $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

                $stmt = $database->prepare("INSERT INTO patient (pemail, pname, ppassword, paddress, pnic, pdob, gender, ptel) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssss", $email, $name, $hashed_password, $address, $nic, $dob, $gender, $tele);
                $stmt->execute();

                $stmt = $database->prepare("INSERT INTO webuser VALUES(?, 'p')");
                $stmt->bind_param("s", $email);
                $stmt->execute();

                $_SESSION["user"] = $email;
                $_SESSION["usertype"] = "p";

                header("Location: patient/index.php");
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create Account</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body{
            background: linear-gradient(277deg, #e4e4e9ff 0%, #171677ff 50%, #0f0966ff 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: gradientShift 15s ease infinite;
    background-size: 200% 200%;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.container{
    width: 520px;
    max-width: 90%;
    background: rgba(255, 255, 255, 0);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(206, 204, 204, 0.01);
    backdrop-filter: blur(10px);
    animation: slideUp 0.5s ease-out;
    border: 2px solid rgba(255, 255, 255, 0.11);
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.header-text{
    font-size: 36px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 30px;
    background: linear-gradient(135deg, #dfe0e6ff 0%, #dcd1e7ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.5px;
}

label{
    display: block;
    margin: 15px 0 8px 0;
    color: #e9e2e2bb;
    font-weight: 600;
    font-size: 14px;
    text-align: left;
}

.input-text{
    width: 100%;
    padding: 14px 18px;
    margin: 0 0 5px 0;
    border-radius: 12px;
    border: 1px solid #e0e0e049;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #f8f9fa09;
    font-family: inherit;
}

.input-text:focus{
    outline: none;
    border-color: #e0e2e7ad;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}

.input-text::placeholder{
    color: #999;
}

.btn{
    width: 100%;
    padding: 14px;
    background: linear-gradient(240deg, #2c2fc4ff 0%, #070569ff 100%);
    color: white;
    border: none;
    font-size: 17px;
    font-weight: 600;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 20px;
    box-shadow: 0 4px 15px rgba(211, 214, 228, 0.17);
}

.btn:hover{
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.19);
}

.btn:active{
    transform: translateY(0);
}

.error{
    color: #e74c3c;
    text-align: center;
    margin-bottom: 15px;
    padding: 12px;
    background: #fee;
    border-radius: 10px;
    border-left: 4px solid #e74c3c;
    font-size: 14px;
    animation: shake 0.3s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

#email-error {
    animation: shake 0.3s ease;
}

.input-text.invalid {
    border-color: #e74c3c;
    background: #fee;
}

.input-text.valid {
    border-color: #27ae60;
    background: #efe;
}
</style>

<script>
function validateEmailField(input) {
    const email = input.value.trim();
    const errorDiv = document.getElementById('email-error');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email === '') {
        errorDiv.style.display = 'none';
        input.classList.remove('invalid', 'valid');
        return true;
    }
    
    if (!emailRegex.test(email)) {
        errorDiv.textContent = '⚠️ Please enter a valid email address (e.g., user@example.com)';
        errorDiv.style.display = 'block';
        input.classList.add('invalid');
        input.classList.remove('valid');
        return false;
    } else {
        // Check domain exists (basic check)
        const emailParts = email.split('@');
        if (emailParts.length === 2) {
            const domain = emailParts[1];
            // Show checking message
            errorDiv.textContent = '⏳ Verifying email domain...';
            errorDiv.style.display = 'block';
            errorDiv.style.color = '#667eea';
            
            // Note: Full domain verification requires server-side check
            // This is just a format check
            setTimeout(() => {
                errorDiv.style.display = 'none';
                input.classList.add('valid');
                input.classList.remove('invalid');
            }, 500);
        }
        return true;
    }
}

function validateForm() {
    const emailInput = document.getElementById('email');
    return validateEmailField(emailInput);
}
</script>

</head>

<body>

<div class="container">

    <p class="header-text">Create Account</p>

    <?php
    if($err_email) echo "<div class='error'>" . htmlspecialchars($err_email, ENT_QUOTES, 'UTF-8') . "</div>";
    if($err_tele) echo "<div class='error'>" . htmlspecialchars($err_tele, ENT_QUOTES, 'UTF-8') . "</div>";
    if($err_pass) echo "<div class='error'>" . htmlspecialchars($err_pass, ENT_QUOTES, 'UTF-8') . "</div>";
    ?>

    <form method="POST" id="createForm" onsubmit="return validateForm()">

        <label>Email</label>
        <input type="email" 
               name="email" 
               id="email"
               class="input-text"
               required 
               value="<?php echo htmlspecialchars($typed_email, ENT_QUOTES, 'UTF-8'); ?>"
               onblur="validateEmailField(this)">
        <div id="email-error" style="color: #e74c3c; font-size: 12px; text-align: left; margin-top: -5px; margin-bottom: 10px; display: none;"></div>

        <label>Mobile Number</label>
        <input type="tel" name="tele" class="input-text"
               required 
               maxlength="10"
               pattern="07[0-9]{8}"
        title="Must start with 07 and be 10 digits long"

               value="<?php echo htmlspecialchars($typed_tele, ENT_QUOTES, 'UTF-8'); ?>">

        <label>Password</label>
        <input type="password" name="password" class="input-text" required>

        <label>Confirm Password</label>
        <input type="password" name="cpassword" class="input-text" required>

        <button class="btn" type="submit">Sign Up</button>

    </form>

</div>
</body>
</html>