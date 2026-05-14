<?php
session_start();

$conn = new mysqli("localhost","root","","lab_rfid_system");

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $user = $_POST['username'];
    $pass = $_POST['password'];

    // SIMPLE LOGIN (you can improve later)
    $query = "SELECT * FROM admin WHERE username='$user' AND password='$pass'";
    $result = $conn->query($query);

    if($result->num_rows > 0){
        $_SESSION['admin'] = $user;

        header("Location: dashboard.php");
        exit();
    }
    else{
        echo "<script>alert('Invalid Login');window.location='index.php';</script>";
    }
}
?>