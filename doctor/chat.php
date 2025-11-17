<?php
    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
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
    $doctorrow = $database->query("select * from doctor where docemail='$useremail'");
    $doctorfetch=$doctorrow->fetch_assoc();
    $doctorid= $doctorfetch["docid"];
    $doctorname=$doctorfetch["docname"];
    
    // Get selected patient ID from GET parameter
    $receiver_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;
    
    // Get list of all patients who have messaged this doctor
    $patients_list = $database->query("SELECT DISTINCT p.pid, p.pname 
                                       FROM patient p 
                                       INNER JOIN messages m ON p.pid = m.sender_id 
                                       WHERE m.receiver_id = $doctorid 
                                       ORDER BY p.pname ASC");
    
    // If no patient selected, get first patient or set to 0
    if ($receiver_id == 0 && $patients_list->num_rows > 0) {
        $first_patient = $patients_list->fetch_assoc();
        $receiver_id = $first_patient['pid'];
        $patients_list->data_seek(0); // Reset pointer
    }
    
    // Get current patient name
    $current_patient_name = "Select Patient";
    if ($receiver_id > 0) {
        $patient_info = $database->query("SELECT pname FROM patient WHERE pid = $receiver_id");
        if ($patient_info->num_rows > 0) {
            $current_patient_name = $patient_info->fetch_assoc()['pname'];
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
    <title>Live Chat - Doctor</title>
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
        
        .patient-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
        }
        
        .patient-select {
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
        
        .patient-select:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
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
                                    <p class="profile-title"><?php echo substr($doctorname,0,13); ?></p>
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
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></div></a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <div class="chat-container">
                <div class="chat-header">
                    <h2>💬 Live Chat with Patients</h2>
                    <div class="patient-selector">
                        <select id="patientSelect" class="patient-select" onchange="changePatient()">
                            <option value="0">Select Patient</option>
                            <?php
                            $patients_list->data_seek(0); // Reset pointer
                            while($patient = $patients_list->fetch_assoc()) {
                                $selected = ($patient['pid'] == $receiver_id) ? 'selected' : '';
                                echo "<option value='{$patient['pid']}' $selected>{$patient['pname']}</option>";
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
        const sender = <?php echo $doctorid; ?>;
        let receiver = <?php echo $receiver_id; ?>;

        function changePatient() {
            const select = document.getElementById('patientSelect');
            receiver = parseInt(select.value);
            if (receiver > 0) {
                window.location.href = `chat.php?patient_id=${receiver}`;
            }
        }

        function loadMessages() {
            if (receiver <= 0) {
                document.getElementById('chatBox').innerHTML = '<div style="text-align:center;padding:40px;color:#999;">Please select a patient to view messages</div>';
                return;
            }
            
            fetch(`fetch_messages.php?sender=${sender}&receiver=${receiver}`)
                .then(res => res.text())
                .then(data => {
                    document.getElementById('chatBox').innerHTML = data;
                    document.getElementById('chatBox').scrollTop = document.getElementById('chatBox').scrollHeight;
                    // Mark messages as read
                    markAsRead();
                });
        }

        function markAsRead() {
            if (receiver <= 0) return;
            fetch(`mark_read.php?sender=${receiver}&receiver=${sender}`);
        }

        function checkNotifications() {
            fetch(`check_notifications.php?user_id=${sender}&user_type=doctor`)
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
                alert('Please select a patient first');
                return;
            }
            
            const msg = document.getElementById('messageInput').value.trim();
            if (msg === "") return;

            const formData = new FormData();
            formData.append('sender', sender);
            formData.append('receiver', receiver);
            formData.append('message', msg);
            formData.append('sender_type', 'doctor');
            formData.append('receiver_type', 'patient');

            fetch('send_message.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                document.getElementById('messageInput').value = '';
                loadMessages();
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



