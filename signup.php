<?php
session_start();

$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

date_default_timezone_set('Asia/Amman');
$_SESSION["date"] = date('Y-m-d');

$error = "";

$fname_val = $_POST['fname'] ?? "";
$lname_val = $_POST['lname'] ?? "";
$address_val = $_POST['address'] ?? "";
$nic_val = $_POST['nic'] ?? "";
$dob_val = $_POST['dob'] ?? "";
$gender_val = $_POST['gender'] ?? "";

if ($_POST) {

    include("connection.php");

    if ($dob_val > date('Y-m-d')) {
$error = "❌ You cannot select a future date!";

    } else {

        $check = $database->query("SELECT * FROM patient WHERE pnic='$nic_val'");
        if ($check->num_rows > 0) {
$error = "❌ The ID number is already in use";

        } else {

            $_SESSION["personal"] = [
                "fname"=>$fname_val,
                "lname"=>$lname_val,
                "address"=>$address_val,
                "nic"=>$nic_val,
                "dob"=>$dob_val,
                "gender"=>$gender_val
            ];

            header("Location: create-account.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>

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
    width: 520px;
    max-width: 90%;
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 20px;
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
    text-align: center;
    margin-bottom: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.5px;
}

label{
    display: block;
    margin: 15px 0 8px 0;
    color: #333;
    font-weight: 600;
    font-size: 14px;
    text-align: left;
}

.input-text{
    width: 100%;
    padding: 14px 18px;
    margin: 0 0 5px 0;
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
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.container a:hover {
    color: #764ba2;
    text-decoration: underline;
}
</style>

</head>

<body>

<div class="container">

    <p class="header-text">Sign Up</p>

    <?php if($error) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">

        <label>First Name</label>
        <input type="text" name="fname" class="input-text" required value="<?php echo $fname_val; ?>">

        <label>Last Name</label>
        <input type="text" name="lname" class="input-text" required value="<?php echo $lname_val; ?>">

        <label>Address</label>
        <input type="text" name="address" class="input-text" required value="<?php echo $address_val; ?>">

        <label>National ID (NIC)</label>
        <input type="text" name="nic" class="input-text" required value="<?php echo $nic_val; ?>">

        <label>Date of Birth</label>
        <input type="date"
               name="dob"
               class="input-text"
               required
               max="<?php echo date('Y-m-d'); ?>"
               value="<?php echo $dob_val; ?>"
               oninput="validateDOB(this)">

        <label>Gender</label>
        <select name="gender" class="input-text" required>
            <option value="">Select</option>
            <option value="Male" <?php if($gender_val=="Male") echo "selected"; ?>>Male</option>
            <option value="Female" <?php if($gender_val=="Female") echo "selected"; ?>>Female</option>
        </select>

        <button class="btn" type="submit">Next</button>
    </form>

    <p style="text-align:center;margin-top:15px;">
        Already have an account? <a href="login.php">Login</a>
    </p>

</div>


<script>

function validateDOB(input) {
    let today = new Date().toISOString().split("T")[0];

    if (input.value > today) {
    alert("❌ You cannot select a future date!");
        input.value = today;
    }
}
</script>

</body>
</html>