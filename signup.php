<?php
session_start();
include("translations.php");

$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

date_default_timezone_set('Asia/Amman');
$_SESSION["date"] = date('Y-m-d');

$error = "";

$fname_val   = $_POST['fname']   ?? "";
$lname_val   = $_POST['lname']   ?? "";
$address_val = $_POST['address'] ?? "";
$nic_val     = $_POST['nic']     ?? "";
$dob_val     = $_POST['dob']     ?? "";
$gender_val  = $_POST['gender']  ?? "";

if ($_POST) {

    include("connection.php");

    if (!preg_match('/^[0-9]{10}$/', $nic_val)) {
        $error = "❌ " . (isArabic() ? "رقم الهوية يجب أن يتكون من 10 أرقام" : "NIC must be exactly 10 digits");
    }

    elseif ($dob_val > date('Y-m-d')) {
        $error = "❌ " . (isArabic() ? "لا يمكنك اختيار تاريخ في المستقبل!" : "You cannot select a future date!");
    }
    else {

      
        $check = $database->query("SELECT * FROM patient WHERE pnic='$nic_val'");
        if ($check->num_rows > 0) {
            $error = "❌ " . (isArabic() ? "رقم الهوية مستخدم بالفعل" : "The ID number is already in use");
        } else {

            $_SESSION["personal"] = [
                "fname"  => $fname_val,
                "lname"  => $lname_val,
                "address"=> $address_val,
                "nic"    => $nic_val,
                "dob"    => $dob_val,
                "gender" => $gender_val
            ];

            header("Location: create-account.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo getLang(); ?>" dir="<?php echo isArabic() ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('signup'); ?></title>

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

    [dir="rtl"] label {
        text-align: right;
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
        box-shadow: 0 20px 60px rgba(20, 20, 20, 0.04);
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
        background: linear-gradient(135deg, #ececeeff 0%, #e8e1f0ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.5px;
    }

    label{
        display: block;
        margin: 15px 0 8px 0;
        color: #ebe5e5d7;
        font-weight: 600;
        font-size: 14px;
        text-align: left;
    }

    .input-text{
        width: 100%;
        padding: 14px 18px;
        margin: 0 0 5px 0;
        border-radius: 12px;
        border: 1px solid #e0e0e080;
        font-size: 15px;
        transition: all 0.3s ease;
        background: #f8f9fa0c;
        font-family: inherit;
    }

    .input-text:focus{
        outline: none;
        border-color: #c5cae2ff;
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
    }

    .input-text::placeholder{
        color: #ddd9d9ff;
    }

    .btn{
        width: 100%;
        padding: 14px;
        background: linear-gradient(240deg, #6b209cff 0%, #27034bff 100%);
        color: white;
        border: none;
        font-size: 17px;
        font-weight: 600;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px;
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

    .container p {
        text-align: center;
        margin-top: 20px;
        color: #666;
        font-size: 14px;
    }

    .container a {
        color: #8f9fe4ff;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .container a:hover {
        color: #8f9fe4ff;
        text-decoration: underline;
    }
    </style>

    <link rel="stylesheet" href="css/language.css">
</head>

<body>

<div class="language-switcher-container">
    <?php include("language-switcher.php"); ?>
</div>

<div class="container">

    <p class="header-text"><?php echo t('signup'); ?></p>

    <?php if($error) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">

        <label><?php echo isArabic() ? 'الاسم الأول' : 'First Name'; ?></label>
        <input type="text" name="fname" class="input-text" required
               value="<?php echo htmlspecialchars($fname_val, ENT_QUOTES, 'UTF-8'); ?>">

        <label><?php echo isArabic() ? 'اسم العائلة' : 'Last Name'; ?></label>
        <input type="text" name="lname" class="input-text" required
               value="<?php echo htmlspecialchars($lname_val, ENT_QUOTES, 'UTF-8'); ?>">

        <label><?php echo isArabic() ? 'العنوان' : 'Address'; ?></label>
        <input type="text" name="address" class="input-text" required
               value="<?php echo htmlspecialchars($address_val, ENT_QUOTES, 'UTF-8'); ?>">

        <label><?php echo isArabic() ? 'رقم الهوية الوطنية (NIC)' : 'National ID (NIC)'; ?></label>
        <input type="text"
               name="nic"
               class="input-text"
               required
               maxlength="10"
               minlength="10"
               pattern="[0-9]{10}"
               title="<?php echo isArabic() ? 'رقم الهوية يجب أن يكون 10 أرقام' : 'NIC must be exactly 10 digits'; ?>"
               value="<?php echo htmlspecialchars($nic_val, ENT_QUOTES, 'UTF-8'); ?>">

        <label><?php echo isArabic() ? 'تاريخ الميلاد' : 'Date of Birth'; ?></label>
        <input type="date"
               name="dob"
               class="input-text"
               required
               max="<?php echo date('Y-m-d'); ?>"
               value="<?php echo $dob_val; ?>"
               oninput="validateDOB(this)">

        <label><?php echo isArabic() ? 'الجنس' : 'Gender'; ?></label>
        <select name="gender" class="input-text" required>
            <option value=""><?php echo isArabic() ? 'اختر' : 'Select'; ?></option>
            <option value="Male"   <?php if($gender_val=="Male")   echo "selected"; ?>><?php echo isArabic() ? 'ذكر' : 'Male'; ?></option>
            <option value="Female" <?php if($gender_val=="Female") echo "selected"; ?>><?php echo isArabic() ? 'أنثى' : 'Female'; ?></option>
        </select>

        <button class="btn" type="submit"><?php echo isArabic() ? 'التالي' : 'Next'; ?></button>
    </form>

    <p style="text-align:center;margin-top:15px;">
        <?php echo isArabic() ? 'لديك حساب بالفعل؟' : 'Already have an account?'; ?>
        <a href="login.php"><?php echo t('login'); ?></a>
    </p>

</div>

<script>
var alertMessage = "❌ <?php echo isArabic() ? 'لا يمكنك اختيار تاريخ في المستقبل!' : 'You cannot select a future date!'; ?>";

function validateDOB(input) {
    let today = new Date().toISOString().split("T")[0];

    if (input.value > today) {
        alert(alertMessage);
        input.value = today;
    }
}
</script>

</body>
</html>