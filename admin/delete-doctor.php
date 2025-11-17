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
    
    
    if($_GET && isset($_GET["id"])){

        include("../connection.php");
        $id = intval($_GET["id"]);
        
        if ($id <= 0) {
            header("location: doctors.php");
            exit();
        }
        
        $stmt = $database->prepare("SELECT docemail FROM doctor WHERE docid=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result001 = $stmt->get_result();
        
        if ($result001->num_rows > 0) {
            $email = $result001->fetch_assoc()["docemail"];
            
            $stmt = $database->prepare("DELETE FROM webuser WHERE email=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            $stmt = $database->prepare("DELETE FROM doctor WHERE docemail=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
        }
        
        header("location: doctors.php");
        exit();
    }


?>