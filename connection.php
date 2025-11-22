<?php
    // Database connection settings
    $host = "localhost";
    $username = "root";
    $password = "";
    $database_name = "DB";
    
    // First, connect to MySQL server (without database)
    $server = @new mysqli($host, $username, $password);
    
    // Check server connection
    if ($server->connect_error) {
        $error_msg = $server->connect_error;
        if (strpos($error_msg, "refused") !== false || strpos($error_msg, "No connection") !== false) {
            die("❌ خطأ في الاتصال بقاعدة البيانات<br>" .
                "MySQL غير قيد التشغيل!<br><br>" .
                "الرجاء:<br>" .
                "1. افتح XAMPP Control Panel<br>" .
                "2. تأكد من تشغيل MySQL (يجب أن يكون باللون الأخضر)<br>" .
                "3. انتظر حتى يبدأ MySQL بالكامل<br>" .
                "4. أعد تحميل الصفحة<br><br>" .
                "Error: " . $error_msg);
        } else {
            die("❌ خطأ في الاتصال بقاعدة البيانات: " . $error_msg);
        }
    }
    
    // Check if database exists, if not create it
    $result = $server->query("SHOW DATABASES LIKE '$database_name'");
    if ($result->num_rows == 0) {
        // Database doesn't exist, create it
        if ($server->query("CREATE DATABASE IF NOT EXISTS `$database_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
            // Database created successfully
        } else {
            die("❌ خطأ في إنشاء قاعدة البيانات: " . $server->error);
        }
    }
    $server->close();
    
    // Now connect to the database
    $database = @new mysqli($host, $username, $password, $database_name);
    
    // Check database connection
    if ($database->connect_error) {
        die("❌ خطأ في الاتصال بقاعدة البيانات: " . $database->connect_error);
    }
    
    // Set charset to UTF-8 for proper Arabic support
    $database->set_charset("utf8mb4");
?>