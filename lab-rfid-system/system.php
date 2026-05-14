<?php
$conn = new mysqli("localhost","root","","lab_rfid_system");

$query = "
SELECT s.system_id,s.system_name,s.status,
st.name,st.reg_no,st.year,st.class,st.section,u.login_time
FROM systems s
LEFT JOIN usage_log u 
ON s.system_id = u.system_id 
AND u.logout_time IS NULL
LEFT JOIN studentss st ON u.student_id = st.id
ORDER BY s.system_id
";

$result = $conn->query($query);

$total = $conn->query("SELECT COUNT(*) as t FROM systems")->fetch_assoc()['t'];
$occupied = $conn->query("SELECT COUNT(*) as o FROM systems WHERE status='OCCUPIED'")->fetch_assoc()['o'];
$free = $conn->query("SELECT COUNT(*) as f FROM systems WHERE status='FREE'")->fetch_assoc()['f'];

$usage_percent = ($occupied/$total)*100;
?>

<!DOCTYPE html>
<html>
<head>
<title>System Status</title>

<link rel="stylesheet" href="assets/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    #popup {
    background: #ffffff !important;
}

    .top h2{
    color:#1e293b !important;
    opacity:1 !important;
}

.wave, .stats, .stat, p, h3{
    opacity:1 !important;
    color:#1e293b;
}

.menu a:hover{
    background:#3b82f6;
    color:white;
}

.menu a.active{
    background:#3b82f6;
    color:white;
    box-shadow:0 6px 15px rgba(59,130,246,0.4);
}

body{
    margin:0;
    font-family:'Segoe UI';
    background:#eef4ff;
}

.main{
    margin-left:230px;
    padding:20px;
}

.top{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.admin{
    background:#e0e7ff;
    padding:10px 15px;
    border-radius:12px;
}

/* KEEP ONLY BELOW UI PARTS */

.wave{
    background:linear-gradient(135deg,#dbeafe,#eef4ff);
    padding:25px;
    border-radius:20px;
    margin-top:15px;
}

.stats{
    display:flex;
    gap:20px;
}

.stat{
    flex:1;
    padding:20px;
    border-radius:15px;
    text-align:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

.stat:nth-child(1){background:#e0f2fe;}
.stat:nth-child(2){background:#ffedd5;}
.stat:nth-child(3){background:#dcfce7;}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(130px,1fr));
    gap:18px;
    margin-top:25px;
}

.card{
    padding:15px;
    border-radius:12px;
    color:white;
    text-align:center;
    cursor:pointer;
    font-weight:bold;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px) scale(1.05);
}

.free{
    background:linear-gradient(135deg,#22c55e,#16a34a);
}

.occupied{
    background:linear-gradient(135deg,#ef4444,#dc2626);
}

.admin{
    background:#e0e7ff;
    padding:10px 18px;
    border-radius:20px;
    font-weight:600;
    color:#1e293b;
}

.admin i{
    color:#1e293b; /* FORCE ICON COLOR */
    margin-right:6px;
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

<script>

let popupOpen=false;
var currentSystem = "";
function showInfo(system,name,reg,year,dept,sec,time){
    if(name==""){
return;
}

popupOpen=true;
console.log(document.getElementById("popup"));
document.getElementById("popup").style.display="block";
document.getElementById("popup").style.background="red";
document.getElementById("details").innerHTML=
"<b>System:</b> "+system+"<br>"+
"<b>Name:</b> "+name+"<br>"+
"<b>Reg No:</b> "+reg+"<br>"+
"<b>Year:</b> "+year+"<br>"+
"<b>Department:</b> "+dept+"<br>"+
"<b>Section:</b> "+sec+"<br>"+
"<b>Login Time:</b> "+time;

}

function closePopup(){
popupOpen=false;
document.getElementById("popup").style.display="none";
}

/* AUTO REFRESH */
function loadSystems(){
fetch("fetch_status.php")
.then(res => res.json())
.then(data => {

data.forEach(sys => {

let card = document.getElementById("sys-"+sys.system_id);

if(card){
card.className = "card " + (sys.status === "FREE" ? "free" : "occupied");
card.innerHTML = sys.system_name + "<br>" + sys.status;
}

});

});
}

setInterval(loadSystems, 2000);

function toggleSidebar(){
    let sidebar = document.getElementById("sidebar");
    let main = document.querySelector(".main");

    sidebar.classList.toggle("hide");
    main.classList.toggle("full");
}

</script>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <div class="logo">
        <img src="assets/images/logo.png">
    </div>

    <div class="menu">
        <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
        <a href="system.php" class="active"><i class="fa fa-desktop"></i> System Status</a>
        <a href="report.php"><i class="fa fa-file"></i> Reports</a>
        <a href="settings.php"><i class="fa fa-gear"></i> Settings</a>
        <a href="logout.php" class="logout-btn">
    <i class="fa fa-power-off"></i> Logout
</a>
    </div>

</div>

<!-- MAIN -->
<div class="main">

<div class="top">
<h2>
    <i class="fa fa-bars" onclick="toggleSidebar()" style="cursor:pointer;"></i>
    DSP LABORATORY SYSTEM STATUS
</h2>
<div class="admin"><i class="fa fa-user"></i> Welcome, Admin</div>
</div>

<div class="wave">

<div class="stats">

<div class="stat">
<i class="fa fa-desktop"></i>
<h2><?php echo $total ?></h2>
<p>Total Systems</p>
</div>

<div class="stat">
<i class="fa fa-user-check"></i>
<h2><?php echo $occupied ?></h2>
<p>Occupied Systems</p>
</div>

<div class="stat">
<i class="fa fa-check-circle"></i>
<h2><?php echo $free ?></h2>
<p>Free Systems</p>
</div>

</div>

<h3>Lab Usage</h3>

<div class="bar">
<div class="fill" style="width:<?php echo $usage_percent ?>%"></div>
</div>

<p><?php echo round($usage_percent) ?>% Systems Occupied</p>

</div>

<div class="grid">

<?php
while($row=$result->fetch_assoc()){

$status=$row['status'];
$class=($status=="FREE")?"free":"occupied";

$name=$row['name']??"";
$reg=$row['reg_no']??"";
$year=$row['year']??"";
$dept=$row['class']??"";
$sec=$row['section']??"";
$time=$row['login_time']??"";

echo "<div id='sys-".$row['system_id']."' class='card $class'
onclick=\"showInfo('".$row['system_name']."','$name','$reg','$year','$dept','$sec','$time')\"
".$row['system_name']."<br>$status
</div>";
}
?>

</div>

</div>

<!-- MODAL -->
<div id="popup" style="
display:none;
position:fixed;
top:50%;
left:50%;
transform:translate(-50%,-50%);
background:#ffffff !important;
color:#1e293b !important;
padding:20px;
border-radius:12px;
box-shadow:0 10px 25px rgba(0,0,0,0.2);
z-index:99999;
min-width:300px;
">
<div><h3>Student Details</h3>
<div id="details"></div>
<br>
<button onclick="closePopup()">Close</button>
</div>
</div>

</body>
</html>