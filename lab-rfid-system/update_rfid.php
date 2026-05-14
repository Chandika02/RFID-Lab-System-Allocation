<?php
include "db_connect.php";

if(isset($_POST['update'])) {
    $reg_no = $_POST['reg_no'];
    $rfid = $_POST['rfid'];

    $sql = "UPDATE studentss SET rfid_uid='$rfid' WHERE reg_no='$reg_no'";

    if(mysqli_query($conn, $sql)) {
        echo "RFID Updated Successfully";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<form method="POST">
    <h2>Update RFID</h2>
    Register No: <input type="text" name="reg_no"><br><br>
    New RFID: <input type="text" name="rfid"><br><br>
    <button name="update">Update</button>
</form>