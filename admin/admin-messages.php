<?php
session_start();

if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" or $_SESSION['usertype'] != 'a') {
        header("location: ../login.php");
        exit();
    }
} else {
    header("location: ../login.php");
    exit();
}

include("../connection.php");
include("../translations.php");

date_default_timezone_set('Asia/Amman');
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="<?php echo getLang(); ?>" dir="<?php echo isArabic() ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/admin/common.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="stylesheet" href="../css/language.css">

    <title><?php echo t('admin_messages'); ?></title>

    <style>
        .language-switcher-header {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1001;
        }

        [dir="rtl"] .language-switcher-header {
            right: auto;
            left: 15px;
        }

        .language-switcher-header .language-switcher {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .profile-title {
            color: #333 !important;
            font-weight: 600 !important;
        }

        .profile-subtitle {
            color: #666 !important;
            font-weight: 400 !important;
        }

        /* RTL Menu adjustments */
        [dir="rtl"] .menu-btn {
            background-position: calc(100% - 20px) 50% !important;
            text-align: right !important;
        }

        [dir="rtl"] .menu-text {
            padding-left: 0 !important;
            padding-right: 50px !important;
            text-align: right !important;
        }

        [dir="rtl"] .menu {
            border-right: none !important;
            border-left: 1px solid rgba(102, 126, 234, 0.1) !important;
            box-shadow: -4px 0 30px rgba(0, 0, 0, 0.08) !important;
        }

        [dir="rtl"] .menu-active {
            border-right: none !important;
            border-left: 7px solid var(--primarycolor) !important;
        }

        [dir="rtl"] .menu-btn:hover {
            transform: translateX(-5px) !important;
        }

        .logout-btn {
            width: 100% !important;
            margin-top: 15px !important;
        }

        /* User Card Styles - بنفس أسلوب Dashboard Cards */
        .messages-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        [dir="rtl"] .messages-container {
            flex-direction: row-reverse;
        }

        .messages-column {
            flex: 1;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .messages-column h3 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primarycolor);
        }

        .user-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: white;
            transition: all 0.3s;
        }

        [dir="rtl"] .user-card {
            flex-direction: row-reverse;
        }

        .user-card:hover {
            background: #f8f9ff;
            border-color: var(--primarycolor);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .user-info {
            flex: 1;
        }

        [dir="rtl"] .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: #212529;
            margin-bottom: 4px;
            font-size: 15px;
        }

        .user-email {
            font-size: 13px;
            color: #6c757d;
        }

        /* Chat Box Styles */
        .chat-box {
            position: fixed;
            bottom: -600px;
            left: 50%;
            transform: translateX(-50%);
            width: 380px;
            max-width: 90vw;
            height: 550px;
            background: white;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            transition: bottom 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 10000;
            overflow: hidden;
        }

        .chat-box.open {
            bottom: 0;
        }

        .chat-header {
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%);
            color: white;
            padding: 20px;
            font-size: 17px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        [dir="rtl"] .chat-header {
            flex-direction: row-reverse;
        }

        .chat-header span {
            color: white !important;
        }

        .close-chat {
            cursor: pointer;
            font-size: 28px;
            font-weight: bold;
            transition: all 0.3s;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: transparent;
            border: none;
            color: white;
            line-height: 1;
        }

        .close-chat:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: linear-gradient(277deg, #181981ff 0%, #100242ff 100%);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        .message-bubble {
            margin-bottom: 15px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-content {
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%);
            color: white;
            padding: 14px 18px;
            border-radius: 18px;
            display: inline-block;
            max-width: 85%;
            word-wrap: break-word;
            font-size: 14px;
            line-height: 1.6;
            box-shadow: 0 3px 12px rgba(102, 126, 234, 0.3);
        }

        [dir="rtl"] .message-content {
            border-radius: 18px 18px 4px 18px;
        }

        [dir="ltr"] .message-content {
            border-radius: 18px 18px 18px 4px;
        }

        .message-time {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 5px;
            display: block;
        }

        [dir="rtl"] .message-time {
            text-align: right;
        }

        .chat-input-wrapper {
            padding: 20px 25px;
            background: white;
            border-top: 1px solid rgba(102, 126, 234, 0.1);
            display: flex;
            gap: 10px;
            align-items: center;
        }

        [dir="rtl"] .chat-input-wrapper {
            flex-direction: row-reverse;
        }

        #messageInput {
            flex: 1;
            min-height: 42px;
            max-height: 120px;
            padding: 12px 18px;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 25px;
            outline: none;
            font-size: 14px;
            font-family: 'Inter', 'Cairo', sans-serif;
            resize: none;
            overflow-y: auto;
            transition: border-color 0.3s;
        }

        #messageInput::-webkit-scrollbar {
            width: 4px;
        }

        #messageInput::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        #messageInput:focus {
            border-color: #4a31b9;
            box-shadow: 0 0 0 3px rgba(74, 49, 185, 0.1);
        }

        .send-button {
            padding: 12px 25px;
            background: linear-gradient(240deg, #4a31b9ff 0%, #0c0242ff 100%);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            transition: all 0.3s;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(74, 49, 185, 0.3);
        }

        .send-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 49, 185, 0.4);
        }

        .send-button:active {
            transform: translateY(0);
        }

        .send-button:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .empty-chat {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        /* Notification styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            z-index: 10001;
            animation: slideInRight 0.3s ease-out;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        [dir="rtl"] .notification {
            right: auto;
            left: 20px;
            animation: slideInLeft 0.3s ease-out;
        }

        .notification.success {
            background: #28a745;
        }

        .notification.error {
            background: #dc3545;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .messages-container {
                flex-direction: column;
            }

            .chat-box {
                width: 95vw;
                height: 70vh;
            }
        }
    </style>
</head>

<body>
    <div class="language-switcher-header">
        <?php include("../language-switcher.php"); ?>
    </div>
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    <div class="menu-overlay" id="menuOverlay" onclick="toggleMenu()"></div>

    <div class="container">
        <!-- القائمة الجانبية -->
        <div class="menu" id="sidebarMenu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo t('administrator'); ?></p>
                                    <p class="profile-subtitle">admin@gmail.com</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php"><input type="button" value="<?php echo t('logout'); ?>"
                                            class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu">
                            <div>
                                <p class="menu-text"><?php echo t('dashboard'); ?></p>
                            </div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu">
                            <div>
                                <p class="menu-text"><?php echo t('doctors'); ?></p>
                            </div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu">
                            <div>
                                <p class="menu-text"><?php echo t('schedule'); ?></p>
                            </div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu">
                            <div>
                                <p class="menu-text"><?php echo t('appointment'); ?></p>
                            </div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu">
                            <div>
                                <p class="menu-text"><?php echo t('patients'); ?></p>
                            </div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row menu-active">
                    <td class="menu-btn menu-icon-session menu-icon-session-active">
                        <a href="admin-messages.php" class="non-style-link-menu non-style-link-menu-active">
                            <div>
                                <p class="menu-text"><?php echo t('admin_messages'); ?></p>
                            </div>
                        </a>
                    </td>
                </tr>
            </table>
        </div>

        <!-- المحتوى الرئيسي -->
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                <tr>
                    <td width="13%">
                        <a href="index.php"><button class="login-btn btn-primary-soft btn btn-icon-back"
                                style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px">
                                <font class="tn-in-text"><?php echo t('back'); ?></font>
                            </button></a>
                    </td>
                    <td>
                        <form action="" method="post" class="header-search" id="searchForm">
                            <input type="search" name="search" id="searchInput" class="input-text header-searchbar"
                                placeholder="<?php echo t('search_doctor_patient'); ?>">&nbsp;&nbsp;
                            <input type="Submit" value="<?php echo t('search'); ?>" class="login-btn btn-primary btn"
                                style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                        </form>
                    </td>
                    <td width="15%">
                        <p
                            style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: <?php echo isArabic() ? 'left' : 'right'; ?>;">
                            <?php echo t('todays_date'); ?>
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php echo $today; ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;">
                            <img src="../img/calendar.svg" width="100%">
                        </button>
                    </td>
                </tr>

                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">
                            <?php echo t('admin_messages'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="93%" class="sub-table scrolldown" style="border-spacing:0;">
                                    <thead>
                                        <tr>
                                            <th class="table-headin" style="width: 50%;"><?php echo t('doctors'); ?>
                                            </th>
                                            <th class="table-headin" style="width: 50%;"><?php echo t('patients'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <!-- عمود الأطباء -->
                                            <td style="vertical-align: top; padding: 15px; background: transparent;">
                                                <div id="doctorsContainer">
                                                    <?php
                                                    $doctor_query = "SELECT docid, docname, docemail FROM doctor ORDER BY docname ASC";
                                                    $doctors_result = $database->query($doctor_query);

                                                    if ($doctors_result->num_rows == 0) {
                                                        echo '<center>
                                                            <br><br>
                                                            <img src="../img/notfound.svg" width="40%">
                                                            <br>
                                                            <p style="font-size:16px;color:rgb(49, 49, 49)">' . t('no_doctors_found') . '</p>
                                                            <br><br>
                                                          </center>';
                                                    } else {
                                                        while ($doctor = $doctors_result->fetch_assoc()) {
                                                            $docname_safe = htmlspecialchars($doctor['docname'], ENT_QUOTES, 'UTF-8');
                                                            $docemail_safe = htmlspecialchars($doctor['docemail'], ENT_QUOTES, 'UTF-8');
                                                            $docid = intval($doctor['docid']);

                                                            echo '
                                                        <div class="user-card" data-name="' . strtolower($docname_safe) . '" data-email="' . strtolower($docemail_safe) . '">
                                                            <div class="user-info">
                                                                <div class="user-name">' . substr($docname_safe, 0, 25) . '</div>
                                                                <div class="user-email">' . substr($docemail_safe, 0, 30) . '</div>
                                                            </div>
                                                            <button class="btn-primary-soft btn" onclick="openChat(\'doctor\', ' . $docid . ', \'' . addslashes($docname_safe) . '\')" style="padding:10px 18px; font-size:14px;">
                                                                💬 ' . t('chat') . '
                                                            </button>
                                                        </div>';
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </td>

                                            <!-- عمود المرضى -->
                                            <td style="vertical-align: top; padding: 15px; background: transparent;">
                                                <div id="patientsContainer">
                                                    <?php
                                                    $patient_query = "SELECT pid, pname, pemail FROM patient ORDER BY pname ASC";
                                                    $patients_result = $database->query($patient_query);

                                                    if ($patients_result->num_rows == 0) {
                                                        echo '<center>
                                                            <br><br>
                                                            <img src="../img/notfound.svg" width="40%">
                                                            <br>
                                                            <p style="font-size:16px;color:rgb(49, 49, 49)">' . t('no_patients_found') . '</p>
                                                            <br><br>
                                                          </center>';
                                                    } else {
                                                        while ($patient = $patients_result->fetch_assoc()) {
                                                            $pname_safe = htmlspecialchars($patient['pname'], ENT_QUOTES, 'UTF-8');
                                                            $pemail_safe = htmlspecialchars($patient['pemail'], ENT_QUOTES, 'UTF-8');
                                                            $pid = intval($patient['pid']);

                                                            echo '
                                                        <div class="user-card" data-name="' . strtolower($pname_safe) . '" data-email="' . strtolower($pemail_safe) . '">
                                                            <div class="user-info">
                                                                <div class="user-name">' . substr($pname_safe, 0, 25) . '</div>
                                                                <div class="user-email">' . substr($pemail_safe, 0, 30) . '</div>
                                                            </div>
                                                            <button class="btn-primary-soft btn" onclick="openChat(\'patient\', ' . $pid . ', \'' . addslashes($pname_safe) . '\')" style="padding:10px 18px; font-size:14px;">
                                                                💬 ' . t('chat') . '
                                                            </button>
                                                        </div>';
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- صندوق المحادثة -->
    <div id="chatBox" class="chat-box">
        <div class="chat-header">
            <span id="chatUserName"><?php echo t('chat'); ?></span>
            <span class="close-chat" onclick="closeChat()">✕</span>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="empty-chat"><?php echo t('no_messages_yet'); ?></div>
        </div>
        <div class="chat-input-wrapper">
            <textarea id="messageInput" placeholder="<?php echo t('type_message'); ?>" rows="1"></textarea>
            <button class="send-button" onclick="sendMessage()"><?php echo t('send'); ?></button>
        </div>
    </div>

    <script src="../js/menu-toggle.js"></script>
    <script>

        // Auto-resize textarea
        const messageInput = document.getElementById('messageInput');

        messageInput.addEventListener('input', function () {
            this.style.height = '42px';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Live Search
        const searchInput = document.getElementById('searchInput');
        const doctorsContainer = document.getElementById('doctorsContainer');
        const patientsContainer = document.getElementById('patientsContainer');

        document.getElementById('searchForm').addEventListener('submit', function (e) {
            e.preventDefault();
        });

        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase().trim();

            // بحث في الأطباء
            const doctorCards = doctorsContainer.querySelectorAll('.user-card');
            let visibleDoctors = 0;

            doctorCards.forEach(card => {
                const name = card.getAttribute('data-name');
                const email = card.getAttribute('data-email');

                if (searchTerm === '' || name.includes(searchTerm) || email.includes(searchTerm)) {
                    card.style.display = 'flex';
                    visibleDoctors++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleDoctors === 0 && searchTerm !== '') {
                if (!doctorsContainer.querySelector('.no-results')) {
                    doctorsContainer.innerHTML = `<center class="no-results">
                        <br><br>
                        <img src="../img/notfound.svg" width="40%">
                        <br>
                        <p style="font-size:16px;color:rgb(49, 49, 49)"><?php echo t('no_doctors_found'); ?></p>
                        <br><br>
                    </center>`;
                }
            } else if (searchTerm === '') {
                const noResults = doctorsContainer.querySelector('.no-results');
                if (noResults) location.reload();
            }

            // بحث في المرضى
            const patientCards = patientsContainer.querySelectorAll('.user-card');
            let visiblePatients = 0;

            patientCards.forEach(card => {
                const name = card.getAttribute('data-name');
                const email = card.getAttribute('data-email');

                if (searchTerm === '' || name.includes(searchTerm) || email.includes(searchTerm)) {
                    card.style.display = 'flex';
                    visiblePatients++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visiblePatients === 0 && searchTerm !== '') {
                if (!patientsContainer.querySelector('.no-results')) {
                    patientsContainer.innerHTML = `<center class="no-results">
                        <br><br>
                        <img src="../img/notfound.svg" width="40%">
                        <br>
                        <p style="font-size:16px;color:rgb(49, 49, 49)"><?php echo t('no_patients_found'); ?></p>
                        <br><br>
                    </center>`;
                }
            } else if (searchTerm === '') {
                const noResults = patientsContainer.querySelector('.no-results');
                if (noResults) location.reload();
            }
        });

        searchInput.addEventListener('search', function () {
            if (this.value === '') location.reload();
        });

        // وظائف المحادثة
        let currentReceiverType = '';
        let currentReceiverId = 0;
        let messageInterval = null;

        function openChat(type, id, name) {
            currentReceiverType = type;
            currentReceiverId = id;

            document.getElementById('chatUserName').textContent = '<?php echo t('chat_with'); ?> ' + name;
            document.getElementById('chatBox').classList.add('open');

            loadMessages();

            if (messageInterval) clearInterval(messageInterval);
            messageInterval = setInterval(loadMessages, 3000);
        }

        function closeChat() {
            document.getElementById('chatBox').classList.remove('open');
            if (messageInterval) clearInterval(messageInterval);
            messageInput.style.height = '42px';
            messageInput.value = '';
        }

        function loadMessages() {
            if (!currentReceiverId) return;

            fetch('admin-messages-api.php?action=load&type=' + currentReceiverType + '&id=' + currentReceiverId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const chatMessages = document.getElementById('chatMessages');
                    const previousScrollHeight = chatMessages.scrollHeight;
                    const previousScrollTop = chatMessages.scrollTop;
                    const wasScrolledToBottom = (chatMessages.scrollHeight - chatMessages.scrollTop) === chatMessages.clientHeight;

                    if (!Array.isArray(data)) {
                        console.error('Invalid data format:', data);
                        if (!chatMessages.querySelector('.empty-chat')) {
                            chatMessages.innerHTML = '<div class="empty-chat"><?php echo t('connection_error'); ?></div>';
                        }
                        return;
                    }

                    if (data.length === 0) {
                        if (!chatMessages.querySelector('.empty-chat')) {
                            chatMessages.innerHTML = '<div class="empty-chat"><?php echo t('no_messages_yet'); ?></div>';
                        }
                    } else {
                        let html = '';
                        data.forEach(msg => {
                            html += `
                        <div class="message-bubble">
                            <div class="message-content">${escapeHtml(msg.message)}</div>
                            <div class="message-time">${msg.sent_at}</div>
                        </div>
                    `;
                        });

                        // فقط تحديث إذا كان المحتوى مختلف
                        if (chatMessages.innerHTML !== html) {
                            chatMessages.innerHTML = html;

                            // الانتقال للأسفل فقط إذا كان المستخدم في الأسفل أصلاً
                            if (wasScrolledToBottom) {
                                chatMessages.scrollTop = chatMessages.scrollHeight;
                            } else {
                                chatMessages.scrollTop = previousScrollTop;
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const chatMessages = document.getElementById('chatMessages');
                    chatMessages.innerHTML = '<div class="empty-chat"><?php echo t('connection_error'); ?></div>';
                });
        }

        function sendMessage() {
            const message = messageInput.value.trim();

            if (!message || !currentReceiverId) return;

            // تعطيل زر الإرسال أثناء الإرسال
            const sendBtn = document.querySelector('.send-button');
            const originalText = sendBtn.textContent;
            sendBtn.disabled = true;
            sendBtn.textContent = '<?php echo t('sending'); ?>';

            fetch('admin-messages-api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=send&type=${currentReceiverType}&id=${currentReceiverId}&message=${encodeURIComponent(message)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageInput.value = '';
                        messageInput.style.height = '42px';
                        loadMessages();
                        showNotification('<?php echo t('message_sent_success'); ?>', 'success');
                    } else {
                        showNotification('<?php echo t('failed_send_message'); ?>', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('<?php echo t('connection_error'); ?>', 'error');
                })
                .finally(() => {
                    sendBtn.disabled = false;
                    sendBtn.textContent = originalText;
                });
        }

        // إرسال الرسالة عند الضغط على Enter (بدون Shift)
        messageInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // دالة عرض الإشعارات
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);

            const isRTL = document.documentElement.getAttribute('dir') === 'rtl';

            setTimeout(() => {
                notification.style.animation = isRTL ? 'slideOutLeft 0.3s ease-out' : 'slideOutRight 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // دالة حماية من XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // إغلاق المحادثة عند الضغط على Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeChat();
            }
        });
    </script>
</body>

</html>