<?php
$conn = new mysqli("localhost", "root", "", "lab_rfid_system");

/* FETCH ADMIN */
$res = mysqli_query($conn,"SELECT * FROM admin WHERE id=1");
$row = mysqli_fetch_assoc($res);

/* ADMIN NAME UPDATE */
if(isset($_POST['admin_name'])){
    $name = $_POST['admin_name'];
    mysqli_query($conn,"UPDATE admin SET username='$name' WHERE id=1");

    echo "<script>
    alert('Admin name updated');
    window.location.href='settings.php';
    </script>";
}

/* PASSWORD UPDATE */
if(isset($_POST['current_password'])){

    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if($current != $row['password']){
        echo "<script>alert('Current password incorrect');</script>";
    }
    else if($new != $confirm){
        echo "<script>alert('Passwords do not match');</script>";
    }
    else{
        mysqli_query($conn,"UPDATE admin SET password='$new' WHERE id=1");

        echo "<script>
        alert('Password updated successfully');
        window.location.href='settings.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Settings</title>

<!-- ICONS -->
 <link rel="stylesheet" href="assets/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- MAIN CSS (same as dashboard if exists) -->
<link rel="stylesheet" href="style.css">

<style>
    label{
    color:#1e293b;   /* dark text */
    font-weight:600;
    font-size:14px;
}
    /* SIDEBAR FULL STYLE */
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
    height:100vh;
    overflow:hidden;   /* 🚫 no scroll */
}

/* MAIN CONTENT */
.main{
    height:100vh;
    overflow:hidden;
}

.top{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.admin{
    background:#e0e7ff;
    padding:10px 18px;
    border-radius:20px;
    font-weight:600;
    color:#1e293b;
}

/* CARD */
.container{
    max-width:700px;
    height:80vh;
    overflow-y:auto;
    margin-top:0px;
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}
/* SECTIONS */
.section{
    margin-bottom:30px;
}

h2{
    color:#1e293b;
}

/* INPUTS */
input{
    width:100%;
    padding:12px;
    margin-top:5px;
    border-radius:10px;
    border:1px solid #ccc;
    outline:none;
}

input:focus{
    border-color:#3b82f6;
}

/* BUTTON */
button{
    margin-top:10px;
    padding:10px 18px;
    border:none;
    border-radius:10px;
    background:#3b82f6;
    color:white;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#2563eb;
    transform:scale(1.05);
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
        <a href="report.php"><i class="fa fa-file"></i> Reports</a>
        <a href="settings.php" class="active"><i class="fa fa-gear"></i> Settings</a>
<a href="logout.php" class="logout-btn">
    <i class="fa fa-power-off"></i> Logout
</a>
    </div>

</div>

<!-- MAIN -->
<div class="main">

<!-- TOP BAR -->
<div class="top">
    <h2>
        <i class="fa fa-bars" onclick="toggleSidebar()" style="cursor:pointer;"></i>
        Settings
    </h2>

    <div class="admin">
<i class="fa fa-user"></i> Welcome, Admin
    </div>
</div>

<!-- SETTINGS CARD -->
<div class="container">

<div class="section">
<h2><i class="fa fa-user"></i> Admin Profile</h2>

<form method="POST">
<label>Admin Name</label>
<input type="text" name="admin_name"
value="<?php echo $row['username']; ?>" required>

<button type="submit">Save</button>
</form>
</div>

<div class="section">
<h2><i class="fa fa-lock"></i> Change Password</h2>

<form method="POST">

<label>Current Password</label>
<input type="password" name="current_password" required>

<label>New Password</label>
<input type="password" name="new_password" required>

<label>Confirm Password</label>
<input type="password" name="confirm_password" required>

<button type="submit">Update Password</button>

</form>
</div>

</div>

</div>

<!-- SIDEBAR TOGGLE -->
<script>
function toggleSidebar(){
    let sidebar = document.getElementById("sidebar");
    let main = document.querySelector(".main");

    sidebar.classList.toggle("hide");
    main.classList.toggle("full");
}
</script>

</body>
</html>