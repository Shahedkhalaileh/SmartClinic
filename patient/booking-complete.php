<?php



    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

 
    include("../connection.php");
    $stmt = $database->prepare("SELECT * FROM patient WHERE pemail=?");
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    
    if ($userrow->num_rows > 0) {
        $userfetch = $userrow->fetch_assoc();
        $userid = $userfetch["pid"];
        $username = $userfetch["pname"];
    } else {
        header("location: ../login.php");
        exit();
    }


    if($_POST){
        if(isset($_POST["booknow"])){
            $apponum = intval($_POST["apponum"] ?? 0);
            $scheduleid = intval($_POST["scheduleid"] ?? 0);
            $date = trim($_POST["date"] ?? '');
            
            // Validate inputs
            if ($apponum <= 0 || $scheduleid <= 0 || empty($date)) {
                header("location: booking.php?id=".$scheduleid."&error=invalid");
                exit();
            }
            
            // Validate date format
            if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
                header("location: booking.php?id=".$scheduleid."&error=invalid_date");
                exit();
            }
            
            // Check if user already booked this appointment
            $stmt = $database->prepare("SELECT * FROM appointment WHERE pid=? AND scheduleid=?");
            $stmt->bind_param("ii", $userid, $scheduleid);
            $stmt->execute();
            $existingBooking = $stmt->get_result();
            
            if ($existingBooking->num_rows > 0) {
                header("location: booking.php?id=".$scheduleid."&error=already_booked");
                exit();
            }
            
            // Check if appointment is full (get nop from schedule)
            $stmt = $database->prepare("SELECT nop FROM schedule WHERE scheduleid=?");
            $stmt->bind_param("i", $scheduleid);
            $stmt->execute();
            $scheduleResult = $stmt->get_result();
            
            if ($scheduleResult->num_rows > 0) {
                $scheduleData = $scheduleResult->fetch_assoc();
                $maxAppointments = intval($scheduleData['nop']);
                
                // Count existing appointments for this schedule
                $stmt = $database->prepare("SELECT COUNT(*) as count FROM appointment WHERE scheduleid=?");
                $stmt->bind_param("i", $scheduleid);
                $stmt->execute();
                $countResult = $stmt->get_result();
                $countData = $countResult->fetch_assoc();
                $currentBookings = intval($countData['count']);
                
                // Check if appointment is full
                if ($currentBookings >= $maxAppointments) {
                    header("location: booking.php?id=".$scheduleid."&error=full");
                    exit();
                }
                
                // Recalculate appointment number based on current bookings
                $apponum = $currentBookings + 1;
            } else {
                header("location: booking.php?id=".$scheduleid."&error=schedule_not_found");
                exit();
            }
            
            // Insert the appointment
            $stmt = $database->prepare("INSERT INTO appointment(pid,apponum,scheduleid,appodate) VALUES (?,?,?,?)");
            $stmt->bind_param("iiis", $userid, $apponum, $scheduleid, $date);
            
            if ($stmt->execute()) {
                header("location: appointment.php?action=booking-added&id=".$apponum."&titleget=none");
            } else {
                header("location: booking.php?id=".$scheduleid."&error=database_error");
            }
            exit();
        }
    }
 ?>