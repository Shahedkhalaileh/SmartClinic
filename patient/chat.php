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
    $userrow = $database->query("select * from patient where pemail='$useremail'");
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];
    
    // Get selected doctor ID from GET parameter or default
    $receiver_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;
    
    // Get list of all doctors
    $doctors_list = $database->query("SELECT docid, docname FROM doctor ORDER BY docname ASC");
    
    // If no doctor selected, get first doctor or set to 0
    if ($receiver_id == 0 && $doctors_list->num_rows > 0) {
        $first_doctor = $doctors_list->fetch_assoc();
        $receiver_id = $first_doctor['docid'];
        $doctors_list->data_seek(0); // Reset pointer
    }
    
    // Get current doctor name
    $current_doctor_name = "Select Doctor";
    if ($receiver_id > 0) {
        $doctor_info = $database->query("SELECT docname FROM doctor WHERE docid = $receiver_id");
        if ($doctor_info->num_rows > 0) {
            $current_doctor_name = $doctor_info->fetch_assoc()['docname'];
        }
    }
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
    <title>Live Chat</title>
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
            height: 100vh !important;
            overflow: hidden !important;
            position: relative !important;
        }
        
        .menu {
            width: 280px !important;
            min-width: 280px !important;
            max-width: 280px !important;
            height: 100vh !important;
            overflow-y: auto !important;
            position: relative !important;
            flex-shrink: 0 !important;
            z-index: 100 !important;
            margin: 20px 0 0 20px !important;
            padding: 0 !important;
            border-radius: 25px 25px 0 0 !important;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(15px) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1) !important;
            border-right: 1px solid rgba(102, 126, 234, 0.1) !important;
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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
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
        
        .chat-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 800px;
            height: 600px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 20px 20px 0 0;
        }
        
        .chat-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        
        .doctor-selector {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .doctor-select {
            padding: 10px 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            outline: none;
        }
        
        .doctor-select:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .doctor-select option {
            background: #667eea;
            color: white;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }
        
        #chatBox {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .message {
            padding: 12px 18px;
            border-radius: 18px;
            max-width: 70%;
            word-wrap: break-word;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .message.sent {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }
        
        .message.received {
            background: white;
            color: #333;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .chat-input-container {
            padding: 20px 30px;
            background: white;
            border-top: 1px solid rgba(102, 126, 234, 0.1);
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        #messageInput {
            flex: 1;
            padding: 14px 20px;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 25px;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
        }
        
        #messageInput:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .send-btn {
            padding: 14px 35px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 25px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .send-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .send-btn:active {
            transform: translateY(0);
        }
        
        .back-btn {
            position: absolute;
            top: 30px;
            left: 30px;
            padding: 12px 25px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 25px;
            color: #667eea;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .back-btn:hover {
            background: white;
            transform: translateX(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
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
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></div></a>
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
                    <td class="menu-btn menu-icon-appoinment">
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
            <a href="index.php" class="back-btn">← Back</a>
            <div class="chat-container">
                <div class="chat-header">
                    <h2>💬 Live Chat</h2>
                    <div class="doctor-selector">
                        <select id="doctorSelect" class="doctor-select" onchange="changeDoctor()">
                            <option value="0">Select Doctor</option>
                            <?php
                            $doctors_list->data_seek(0); // Reset pointer
                            while($doctor = $doctors_list->fetch_assoc()) {
                                $selected = ($doctor['docid'] == $receiver_id) ? 'selected' : '';
                                echo "<option value='{$doctor['docid']}' $selected>{$doctor['docname']}</option>";
                            }
                            ?>
                        </select>
                        <span id="notificationBadge" class="notification-badge" style="display:none;">0</span>
                    </div>
                </div>
                <div id="chatBox"></div>
                <div class="chat-input-container">
                    <input type="text" id="messageInput" placeholder="Write your message...">
                    <button class="send-btn" onclick="sendMessage()">Send</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const sender = <?php echo $userid; ?>;
        let receiver = <?php echo $receiver_id; ?>;

        function changeDoctor() {
            const select = document.getElementById('doctorSelect');
            receiver = parseInt(select.value);
            if (receiver > 0) {
                window.location.href = `chat.php?doctor_id=${receiver}`;
            }
        }

        function loadMessages() {
            if (receiver <= 0) {
                document.getElementById('chatBox').innerHTML = '<div style="text-align:center;padding:40px;color:#999;">Please select a doctor to start chatting</div>';
                return;
            }
            
            fetch(`fetch_messages.php?sender=${sender}&receiver=${receiver}`)
                .then(res => res.text())
                .then(data => {
                    document.getElementById('chatBox').innerHTML = data;
                    document.getElementById('chatBox').scrollTop = document.getElementById('chatBox').scrollHeight;
                });
        }

        function checkNotifications() {
            if (receiver <= 0) return;
            
            fetch(`check_notifications.php?user_id=${sender}&user_type=patient`)
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                });
        }

        // Update messages every 2 seconds
        setInterval(loadMessages, 2000);
        setInterval(checkNotifications, 3000);
        loadMessages();
        checkNotifications();

        function sendMessage() {
            if (receiver <= 0) {
                alert('Please select a doctor first');
                return;
            }
            
            const msg = document.getElementById('messageInput').value.trim();
            if (msg === "") return;

            const formData = new FormData();
            formData.append('sender', sender);
            formData.append('receiver', receiver);
            formData.append('message', msg);
            formData.append('sender_type', 'patient');
            formData.append('receiver_type', 'doctor');

            fetch('send_message.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                document.getElementById('messageInput').value = '';
                loadMessages();
                checkNotifications();
            });
        }
        
        // Send message on Enter key
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>
</html>
