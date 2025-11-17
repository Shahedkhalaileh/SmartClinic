<?php
session_start();
include("connection.php");

$error = "";
$typed_email = $_POST['useremail'] ?? "";

if ($_POST) {

    $email = trim($_POST['useremail'] ?? '');
    $password = $_POST['userpassword'] ?? '';

    $typed_email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

    if (empty($email) || empty($password)) {
        $error = "⚠ Please fill in all fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "⚠ Please enter a valid email address";
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
                $error = "⚠ This email domain does not exist. Please check your email address.";
            }
        }
    }
    
    if (empty($error)) {
        $stmt = $database->prepare("SELECT * FROM webuser WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $type = $result->fetch_assoc()['usertype'];

            if ($type == 'p') {
                $stmt = $database->prepare("SELECT * FROM patient WHERE pemail=?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $check = $stmt->get_result();
                
                if ($check->num_rows == 1) {
                    $user = $check->fetch_assoc();
                    // Check if password is hashed or plain text (for backward compatibility)
                    if (password_verify($password, $user['ppassword']) || $user['ppassword'] == $password) {
                        $_SESSION['user'] = $email;
                        $_SESSION['usertype'] = $type;
                        header("Location: patient/index.php");
                        exit;
                    } else {
                        $error = "⚠ The password is incorrect";
                    }
                }
            } elseif ($type == 'a') {
                $stmt = $database->prepare("SELECT * FROM admin WHERE aemail=?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $check = $stmt->get_result();
                
                if ($check->num_rows == 1) {
                    $user = $check->fetch_assoc();
                    if (password_verify($password, $user['apassword']) || $user['apassword'] == $password) {
                        $_SESSION['user'] = $email;
                        $_SESSION['usertype'] = $type;
                        header("Location: admin/index.php");
                        exit;
                    } else {
                        $error = "⚠ The password is incorrect";
                    }
                }
            } elseif ($type == 'd') {
                $stmt = $database->prepare("SELECT * FROM doctor WHERE docemail=?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $check = $stmt->get_result();
                
                if ($check->num_rows == 1) {
                    $user = $check->fetch_assoc();
                    if (password_verify($password, $user['docpassword']) || $user['docpassword'] == $password) {
                        $_SESSION['user'] = $email;
                        $_SESSION['usertype'] = $type;
                        header("Location: doctor/index.php");
                        exit;
                    } else {
                        $error = "⚠ The password is incorrect";
                    }
                }
            }

        } else {
            $error = "⚠ No account found with this email";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body{
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
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
    width: 420px;
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    animation: slideUp 0.5s ease-out;
    border: 1px solid rgba(255, 255, 255, 0.3);
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
    margin-bottom: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.5px;
}

.input-text{
    width: 100%;
    padding: 14px 18px;
    margin: 12px 0;
    border-radius: 12px;
    border: 2px solid #e0e0e0;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #f8f9fa;
    font-family: inherit;
}

.input-text:focus{
    outline: none;
    border-color: #667eea;
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    font-size: 16px;
    font-weight: 600;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 8px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn:hover{
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.btn:active{
    transform: translateY(0);
}

.error{
    color: #e74c3c;
    margin: 15px 0;
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

.container p {
    margin-top: 20px;
    color: #666;
    font-size: 14px;
}

.container a {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.container a:hover {
    color: #764ba2;
    text-decoration: underline;
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
        errorDiv.style.display = 'none';
        input.classList.add('valid');
        input.classList.remove('invalid');
        return true;
    }
}

function validateEmail() {
    const emailInput = document.getElementById('useremail');
    return validateEmailField(emailInput);
}
</script>

</head>
<body>

<div class="container">

    <p class="header-text">Login</p>

    <?php if($error) echo "<div class='error'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</div>"; ?>

    <form method="POST" id="loginForm" onsubmit="return validateEmail()">

        <input type="email" 
               name="useremail"
               id="useremail"
               class="input-text"
               placeholder="Email Address"
               value="<?php echo htmlspecialchars($typed_email, ENT_QUOTES, 'UTF-8'); ?>" 
               required
               onblur="validateEmailField(this)">

        <div id="email-error" style="color: #e74c3c; font-size: 12px; text-align: left; margin-top: -5px; margin-bottom: 10px; display: none;"></div>

        <input type="password" name="userpassword" class="input-text" placeholder="Password" required>

        <button class="btn" type="submit">Login</button>

    </form>

    <p style="margin-top:15px;">
        Don't have an account? <a href="signup.php">Sign Up</a>
    </p>

</div>

</body>
</html>