<?php
$host="localhost";
$user="root";
$password="";
$db="lab_rfid_system";

$conn = new mysqli($host,$user,$password,$db);

if($conn->connect_error){
die("Database connection failed");
}
?>