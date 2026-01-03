<?php
session_start();
include("connection.php");
include("translations.php");

$error = "";
$typed_email = $_POST['useremail'] ?? "";

if ($_POST) {

    $email = trim($_POST['useremail'] ?? '');
    $password = $_POST['userpassword'] ?? '';

    $typed_email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

    if (empty($email) || empty($password)) {
        $error = "⚠ " . (isArabic() ? "الرجاء ملء جميع الحقول" : "Please fill in all fields");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "⚠ " . (isArabic() ? "الرجاء إدخال عنوان بريد إلكتروني صحيح" : "Please enter a valid email address");
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
                $error = "⚠ " . (isArabic() ? "نطاق البريد الإلكتروني هذا غير موجود. يرجى التحقق من عنوان بريدك الإلكتروني." : "This email domain does not exist. Please check your email address.");
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
                        $error = "⚠ " . (isArabic() ? "كلمة المرور غير صحيحة" : "The password is incorrect");
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
                        $error = "⚠ " . (isArabic() ? "كلمة المرور غير صحيحة" : "The password is incorrect");
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
                        $error = "⚠ " . (isArabic() ? "كلمة المرور غير صحيحة" : "The password is incorrect");
                    }
                }
            }

        } else {
            $error = "⚠ " . (isArabic() ? "لم يتم العثور على حساب بهذا البريد الإلكتروني" : "No account found with this email");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo getLang(); ?>" dir="<?php echo isArabic() ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('login'); ?></title>

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

.language-switcher-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
}

[dir="rtl"] .language-switcher-container {
    right: auto;
    left: 20px;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.container{
    width: 420px;
    background: rgba(255, 255, 255, 0);
    padding: 40px;
    border-radius: 20px;
    text-align: center;
    border: 2px solid rgba(255, 255, 255, 0.11);
    backdrop-filter: blur(10px);
    animation: slideUp 0.5s ease-out;

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
    background: linear-gradient(135deg, #e8e9ecff 0%, #e9e4eeff 100%);
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
    border: 1px solid #e0e0e07c;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #f8f9fa07;
    font-family: inherit;
}

.input-text:focus{
    outline: none;
    border-color: #dddee694;
  background: white;
    box-shadow: 0 0 0 4px rgba(207, 211, 230, 0.1);
    transform: translateY(-2px);
}

.input-text::placeholder{
    color: #9b9696ff;
}

.btn{
    width: 100%;
    padding: 14px;
     background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%);
    border: none;
    color: white;
    font-size: 16px;
    font-weight: 600;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 8px;
    box-shadow: 0 4px 15px rgba(214, 215, 219, 0.27);
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
    color: #dfd8d8ff;
    font-size: 14px;
}

.container a {
    color: #ba0feeff;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.container a:hover {
       color: #ba0feeff;
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
var emailErrorMsg = '⚠️ <?php echo isArabic() ? "الرجاء إدخال عنوان بريد إلكتروني صحيح (مثال: user@example.com)" : "Please enter a valid email address (e.g., user@example.com)"; ?>';

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
        errorDiv.textContent = emailErrorMsg;
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

    <link rel="stylesheet" href="css/language.css">
</head>
<body>

<div class="language-switcher-container">
    <?php include("language-switcher.php"); ?>
</div>

<div class="container">

    <p class="header-text"><?php echo t('login'); ?></p>

    <?php if($error) echo "<div class='error'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</div>"; ?>

    <form method="POST" id="loginForm" onsubmit="return validateEmail()">

        <input type="email" 
               name="useremail"
               id="useremail"
               class="input-text"
               placeholder="<?php echo isArabic() ? 'عنوان البريد الإلكتروني' : 'Email Address'; ?>"
               value="<?php echo htmlspecialchars($typed_email, ENT_QUOTES, 'UTF-8'); ?>" 
               required
               onblur="validateEmailField(this)">

        <div id="email-error" style="color: #e74c3c; font-size: 12px; text-align: <?php echo isArabic() ? 'right' : 'left'; ?>; margin-top: -5px; margin-bottom: 10px; display: none;"></div>

        <input type="password" name="userpassword" class="input-text" placeholder="<?php echo isArabic() ? 'كلمة المرور' : 'Password'; ?>" required>

        <button class="btn" type="submit"><?php echo t('login'); ?></button>

    </form>

    <p style="margin-top:15px;">
        <?php echo isArabic() ? 'ليس لديك حساب؟' : "Don't have an account?"; ?> <a href="signup.php"><?php echo t('signup'); ?></a>
    </p>

</div>

</body>
</html>