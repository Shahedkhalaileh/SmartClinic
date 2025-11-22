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
    $userrow = $database->query("select * from doctor where docemail='$useremail'");
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["docid"];
    $username=$userfetch["docname"];

    
    if($_GET && isset($_GET["id"])){

        include("../connection.php");
        $id = intval($_GET["id"]);
        
        // Verify that the user is deleting their own account
        if ($id > 0 && $id == $userid) {
            $result001 = $database->query("select * from doctor where docid=$id;");
            if ($result001 && $result001->num_rows > 0) {
                $email = ($result001->fetch_assoc())["docemail"];
                
                // Delete related data first (appointments, messages, schedules, etc.)
                // Delete messages
                $database->query("DELETE FROM messages WHERE sender_id = $id OR receiver_id = $id");
                
                // Delete appointments
                $database->query("DELETE FROM appointment WHERE scheduleid IN (SELECT scheduleid FROM schedule WHERE docid = $id)");
                
                // Delete schedules
                $database->query("DELETE FROM schedule WHERE docid = $id");
                
                // Delete from webuser
                $database->query("DELETE FROM webuser WHERE email='$email';");
                
                // Delete from doctor
                $database->query("DELETE FROM doctor WHERE docemail='$email';");
                
                // Destroy session
                $_SESSION = array();
                if (isset($_COOKIE[session_name()])) {
                    setcookie(session_name(), '', time()-86400, '/');
                }
                session_destroy();
            }
        }
        
        // Redirect to home page
        header("location: ../index.php");
        exit();
    }


?>



