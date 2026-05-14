<?php
$conn = new mysqli("localhost", "root", "", "lab_rfid_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* GET FILTER VALUES */
$date = $_GET['date'] ?? '';
$class = $_GET['class'] ?? '';
$search = $_GET['search'] ?? '';

/* BASE QUERY */
$query = "
SELECT 
    studentss.name,
    studentss.reg_no,
    CONCAT(studentss.year,' ',studentss.class,' ',studentss.section) AS full_class,
    systems.system_name,
    usage_log.login_time,
    usage_log.logout_time
FROM usage_log
JOIN studentss ON usage_log.student_id = studentss.id
JOIN systems ON usage_log.system_id = systems.system_id
WHERE 1=1
";

/* APPLY FILTERS */

// ✅ DATE FILTER
if (!empty($date)) {
    $query .= " AND DATE(usage_log.login_time) = '$date'";
}

// ✅ CLASS FILTER (FIXED)
if (!empty($class)) {
    $query .= " AND CONCAT(studentss.year,' ',studentss.class,' ',studentss.section) = '$class'";
}

// ✅ SEARCH FILTER
if (!empty($search)) {
    $query .= " AND studentss.reg_no LIKE '%$search%'";
}

/* ORDER */
$query .= " ORDER BY usage_log.login_time DESC";

$result = $conn->query($query);

if (!$result) {
    die("Query Error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>DSP Lab Report</title>

<link rel="stylesheet" href="assets/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

/* BODY */
body{
    margin:0;
    font-family:'Segoe UI';
    background:#eef4ff;
}

/* SIDEBAR (same as dashboard) */

.menu a.active,
.menu a:hover{
    background:#3b82f6;
    color:white;
}

.sidebar.hide{
    transform: translateX(-100%);
}

.main.full{
    margin-left:0;
}

/* MAIN */
.main{
    margin-left:250px;
    padding:20px;
}

/* TOP BAR */
.top{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.top h2{
    color:#1e293b !important;
    opacity:1 !important;
}

.admin{
     background:#e0e7ff;
    padding:10px 15px;
    border-radius:12px;

    color:#1e293b !important;   /* 🔥 DARK TEXT */
    font-weight:600;
}

.admin i{
    color:#1e293b !important;   /* make icon visible */
    margin-right:5px;
}

.admin, .admin *{
    opacity:1 !important;
}

/* CARD CONTAINER */
.report-box{
    margin-top:20px;
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

/* FILTER BAR */
.filters{
    display:flex;
    gap:15px;
    margin-bottom:20px;
}

.filter{
    flex:1;
    background:#ffffff;   /* brighter */
    padding:12px;
    border-radius:12px;
    display:flex;
    align-items:center;
    gap:10px;
    box-shadow:0 4px 10px rgba(0,0,0,0.05);
    border:1px solid #e2e8f0;
}

.filter input,
.filter select{
    border:none;
    background:transparent;
    outline:none;
    width:100%;
    color:#1e293b;         /* DARK TEXT */
    font-weight:500;
}

.filter input::placeholder{
    color:#64748b;
    opacity:1;
}

.filter i{
    color:#3b82f6;   /* blue icons */
    font-size:16px;
}

.filter:hover{
    box-shadow:0 8px 20px rgba(59,130,246,0.15);
    transition:0.3s;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}

th{
    text-align:left;
    padding:12px;
    color:#1e293b;   /* DARK TEXT */
    font-weight:600;
}

td{
    padding:14px;
    background:#ffffff;   /* pure white for clarity */
    border-radius:10px;
    color:#111827;        /* strong dark */
    font-weight:500;
}

tr{
    margin-bottom:10px;
}

table, th, td {
    opacity: 1 !important;
}

tr:hover td{
    background:#e2e8f0;
    transition:0.3s;
}

/* DOWNLOAD BUTTON */
.download{
    text-align:center;
    margin-top:20px;
}

.download button{
    background:#3b82f6;
    color:white;
    padding:12px 25px;
    border:none;
    border-radius:20px;
    cursor:pointer;
    font-weight:600;
}
.logout-btn {
    position: absolute;
    bottom: 20px;
    left: 20px;
    right: 20px;
}
.logout-btn {
    color: #ef4444 !important;   /* strong red */
    font-weight: 600;
    margin-top: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.logout-btn i {
    color: #ef4444 !important;
}

.logout-btn:hover {
    background: #fee2e2;   /* light red background */
    border-radius: 8px;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="logo">
        <img src="assets/images/logo.png">
    </div>

    <div class="menu">
        <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
        <a href="system.php"><i class="fa fa-desktop"></i> System Status</a>
        <a href="report.php" class="active"><i class="fa fa-file"></i> Reports</a>
        <a href="settings.php"><i class="fa fa-gear"></i> Settings</a>
        <a href="logout.php" class="logout-btn">
    <i class="fa fa-power-off"></i> Logout
</a>
    </div>
</div>

<!-- MAIN -->
<div class="main">

    <!-- TOP -->
    <div class="top">
        <h2>
            <i class="fa fa-bars" onclick="toggleSidebar()" style="cursor:pointer;margin-right:10px;"></i>
            DSP Laboratory Usage Report
</h2>
        <div class="admin"><i class="fa fa-user"></i> Welcome, Admin</div>
    </div>

    <!-- REPORT BOX -->
    <div class="report-box">

        <!-- FILTERS -->
         <form method="GET">
<div class="filters">

    <!-- DATE -->
    <div class="filter">
        <input type="date" name="date" value="<?php echo $_GET['date'] ?? ''; ?>">
    </div>

    <!-- CLASS -->
    <div class="filter">
        <select name="class">
            <option value="">Select Class</option>
            <option value="III ECE A">III ECE A</option>
            <option value="III ECE B">III ECE B</option>
            <option value="III ECE B">III ECE C</option>
            <option value="III ECE B">III ECE D</option>


        </select>
    </div>

    <!-- SEARCH -->
    <div class="filter">
        <input type="text" name="search" placeholder="Search register number..." 
        value="<?php echo $_GET['search'] ?? ''; ?>">
    </div>

    <!-- SUBMIT BUTTON -->
    <div class="filter">
        <button type="submit">Filter</button>
    </div>

</div>
</form>
        <!-- TABLE -->
        <table>
    <tr>
        <th>Name</th>
        <th>Register Number</th>
        <th>Class</th>
        <th>System</th>
        <th>Login Time</th>
        <th>Logout Time</th>
    </tr>

<?php while($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['reg_no']; ?></td>
        <td><?php echo $row['full_class']; ?></td>
        <td><?php echo $row['system_name']; ?></td>
        <td><?php echo date("h:i A", strtotime($row['login_time'])); ?></td>
        <td><?php echo $row['logout_time'] ? date("h:i A", strtotime($row['logout_time'])) : "—"; ?></td>
    </tr>
<?php } ?>

</table>

        <!-- DOWNLOAD -->
        <div class="download">
           <button onclick="downloadReport()">Download Report</button>
        </div>

    </div>

</div>

<script>
function toggleSidebar(){
    let sidebar = document.getElementById("sidebar");
    let main = document.querySelector(".main");

    sidebar.classList.toggle("hide");
    main.classList.toggle("full");
}

function downloadReport(){
    let date = document.querySelector("input[name='date']").value;
    let classVal = document.querySelector("select[name='class']").value;
    let search = document.querySelector("input[name='search']").value;

    let url = "export.php?date=" + date + "&class=" + classVal + "&search=" + search;

    window.location.href = url;
}
</script>

</body>
</html>