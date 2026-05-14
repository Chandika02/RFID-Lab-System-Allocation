<?php
include "db_connect.php";

$rfid = isset($_POST['rfid_uid']) ? $_POST['rfid_uid'] : $_GET['rfid'];

// 1. Find student
$sql = "SELECT * FROM studentss WHERE rfid_uid='$rfid'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Student Not Found";
    exit;
}

$student = mysqli_fetch_assoc($result);
$student_id = $student['id'];

// 2. Check if already logged in
$check = mysqli_query($conn, "
    SELECT * FROM usage_log 
    WHERE student_id='$student_id' 
    AND logout_time IS NULL
");

if (mysqli_num_rows($check) > 0) {

    // 🔴 LOGOUT
    $row = mysqli_fetch_assoc($check);
    $system_id = $row['system_id'];

    // Update log
    mysqli_query($conn, "
       UPDATE usage_log 
       SET logout_time=NOW() 
       WHERE student_id='$student_id' AND logout_time IS NULL
    ");

    // Free system
    mysqli_query($conn, "
        UPDATE systems 
        SET status='FREE' 
        WHERE system_id='$system_id'
    ");

    echo "LOGOUT SUCCESS";

} else {

    // 🟢 LOGIN
  $already = mysqli_query($conn, "
        SELECT * FROM usage_log 
        WHERE student_id='$student_id' 
        AND logout_time IS NULL
    ");

    if(mysqli_num_rows($already) > 0){
        echo "Already Logged In";
        exit;
    }
    // Find free system
    $sys = mysqli_query($conn, "
        SELECT * FROM systems 
        WHERE status='FREE' 
        LIMIT 1
    ");

    if (mysqli_num_rows($sys) == 0) {
        echo "No Systems Available";
        exit;
    }

    $system = mysqli_fetch_assoc($sys);
    $system_id = $system['system_id'];

    // Insert login record
    mysqli_query($conn, "
        INSERT INTO usage_log 
        (student_id, system_id, login_time)
        VALUES ('$student_id', '$system_id', NOW())
    ");

    // Update system status
    mysqli_query($conn, "
        UPDATE systems 
        SET status='OCCUPIED' 
        WHERE system_id='$system_id'
    ");

    echo "LOGIN SUCCESS - System $system_id";
}
?>