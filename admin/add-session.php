<?php

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
            exit();
        }

    }else{
        header("location: ../login.php");
        exit();
    }
    
    
    if($_POST){
 
        include("../connection.php");
        $title = trim($_POST["title"] ?? '');
        $docid = intval($_POST["docid"] ?? 0);
        $nop = intval($_POST["nop"] ?? 0);
        $date = trim($_POST["date"] ?? '');
        $time = trim($_POST["time"] ?? '');
        
        // Sanitize title
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        
        // Validate inputs
        if (empty($title) || $docid <= 0 || $nop <= 0 || empty($date) || empty($time)) {
            header("location: schedule.php?action=error");
            exit();
        }
        
        $stmt = $database->prepare("INSERT INTO schedule (docid,title,scheduledate,scheduletime,nop) VALUES (?,?,?,?,?)");
        $stmt->bind_param("isssi", $docid, $title, $date, $time, $nop);
        $stmt->execute();
        
        $title_encoded = urlencode($title);
        header("location: schedule.php?action=session-added&title=$title_encoded");
        exit();
    }


?>