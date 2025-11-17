<?php



    $database= new mysqli("localhost","root","","DB");
    if ($database->connect_error){
        die("Connection failed:  ".$database->connect_error);
    }



    
?>