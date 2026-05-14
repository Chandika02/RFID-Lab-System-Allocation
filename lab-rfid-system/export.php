<?php
$conn = new mysqli("localhost", "root", "", "lab_rfid_system");

/* GET FILTER VALUES */
$date = $_GET['date'] ?? '';
$class = $_GET['class'] ?? '';
$search = $_GET['search'] ?? '';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=lab_report.xls");

/* BASE QUERY */
$query = "
SELECT 
    studentss.name,
    studentss.reg_no,
    CONCAT(studentss.year,' ',studentss.class,' ',studentss.section) AS class,
    systems.system_name,
    usage_log.login_time,
    usage_log.logout_time
FROM usage_log
JOIN studentss ON usage_log.student_id = studentss.id
JOIN systems ON usage_log.system_id = systems.system_id
WHERE 1=1
";

/* APPLY FILTERS */

if (!empty($date)) {
    $query .= " AND DATE(usage_log.login_time) = '$date'";
}

if (!empty($class)) {
    $query .= " AND CONCAT(studentss.year,' ',studentss.class,' ',studentss.section) = '$class'";
}

if (!empty($search)) {
    $query .= " AND studentss.reg_no LIKE '%$search%'";
}

$query .= " ORDER BY usage_log.login_time DESC";

$result = $conn->query($query);

/* HEADER */
echo "Name\tRegister No\tClass\tSystem\tLogin Time\tLogout Time\n";

/* DATA */
while($row = $result->fetch_assoc()){
    echo $row['name']."\t".
         $row['reg_no']."\t".
         $row['class']."\t".
         $row['system_name']."\t".
         $row['login_time']."\t".
         $row['logout_time']."\n";
}
?>