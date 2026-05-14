<?php
$conn = new mysqli("localhost","root","","lab_rfid_system");

$query = "
SELECT system_id, system_name, status
FROM systems
ORDER BY system_id
";

$result = $conn->query($query);

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
?>