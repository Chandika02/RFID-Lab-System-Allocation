<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<link rel="stylesheet" href="assets/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{
    margin:0;
    font-family:'Segoe UI';
    background:#eef4ff;
}

/* SIDEBAR */

.menu a.active{
    background:#3b82f6;
    color:white;
}

.menu a:hover{
    background:#dbeafe;
}

.logout a{
    color:#ef4444;
    text-decoration:none;
}

/* MAIN */
.main{
    margin-left:250px;
    padding:20px;
    transition:0.3s;
}

.main.full{
    margin-left:20px;
}

/* TOP */
.top{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.top h2{
    color:#0f172a !important; /* DARK */
    font-weight:700;
}

.admin{
    background:#e0e7ff;
    padding:10px 15px;
    border-radius:12px;
}

/* HERO */
.hero{
    margin-top:20px;
    border-radius:25px;
    overflow:hidden;
    position:relative;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

.hero img{
    width:100%;
    height:320px;
    object-fit:cover;
    display:block;
    transition:0.5s;
}

.hero:hover img{
    transform:scale(1.05);
}

/* DSP Laboratory pill */
.hero span,
.hero .tag,
.hero .label{
    background:#ffffff !important;
    color:#0f172a !important;
    font-weight:600;
    padding:8px 16px;
    border-radius:20px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

/* CURVED OVERLAY - FINAL FIX */
.overlay{
    position:absolute;
    right:0;
    top:0;
    height:100%;
    width:45%;

    display:flex;
    flex-direction:column;
    justify-content:center;

    padding:40px;

    background:linear-gradient(
        to left,
        rgba(255,255,255,0.85),
        rgba(255,255,255,0.5),
        rgba(255,255,255,0.1),
        transparent
    );

    backdrop-filter: blur(2px);

    border-top-left-radius:150px;
    border-bottom-left-radius:150px;
}

/* TEXT COLORS - FORCE CLEAR VISIBILITY */
.overlay h1{
    color:##0b1f3a !important; /* DARK STRONG */
    font-size:32px;
    font-weight:800;
}

.overlay h2{
    color:##0b1f3a !important; /* BRIGHT BLUE */
    font-size:22px;
    font-weight:700;
}

.overlay p{
    color:##0b1f3a !important; /* DARK TEXT */
    font-size:16px;
    font-weight:500;
}
/* TAG */
.tag{
    position:absolute;
    bottom:20px;
    left:20px;
    background:white;
    padding:10px 20px;
    border-radius:25px;
    font-weight:bold;
}

/* SMALL LABEL */
.lab-tag{
    position:absolute;
    top:20px;
    right:20px;
    background:#3b82f6;
    color:white;
    padding:6px 14px;
    border-radius:15px;
    font-size:12px;
}

/* CARDS */
.cards{
    display:flex;
    gap:20px;
    margin-top:20px;
}

.card{
    flex:1;
    background:white;
    padding:25px;
    border-radius:15px;
    text-align:center;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
    transition:0.3s;
    cursor:pointer;
}

.card:hover{
    transform:translateY(-10px) scale(1.03);
    box-shadow:0 15px 30px rgba(0,0,0,0.15);
}

.card i{
    font-size:30px;
    margin-bottom:10px;
}

.blue{color:#3b82f6;}
.green{color:#22c55e;}
.red{color:#ef4444;}
.purple{color:#8b5cf6;}

/* FIX TEXT VISIBILITY */
.card h2{
    color:#0f172a; /* DARK */
    font-weight:800;
    font-size:28px;
    margin:10px 0;
}

.card p{
    color:#111827; /* DARK */
    font-weight:600;
    font-size:15px;
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
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <div class="logo">
        <img src="assets/images/logo.png">
    </div>

    <div class="menu">
        <a href="dashboard.php" class="active">
            <i class="fa fa-home"></i> Dashboard
        </a>

        <a href="system.php">
            <i class="fa fa-desktop"></i> System Status
        </a>

        <a href="report.php">
            <i class="fa fa-file"></i> Reports
        </a>

        <a href="settings.php">
            <i class="fa fa-gear"></i> Settings
        </a>

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
            <i class="fa fa-bars" onclick="toggleSidebar()" style="cursor:pointer;"></i>
            Dashboard
        </h2>
        <div class="admin"><i class="fa fa-user"></i> Welcome, Admin</div>
    </div>

    <!-- HERO -->
    <div class="hero">

        <img src="assets/images/clgg.png">

        <div class="lab-tag">ECE Lab</div>

        <div class="overlay">
            <h1>DSP LABORATORY</h1>
            <h2>M.Kumarasamy College of Engineering</h2>
            <p>(Autonomous)</p>
            <p>Karur, India</p>
        </div>

        <div class="tag">DSP Laboratory</div>

    </div>

    <!-- CARDS -->
    <div class="cards">

        <div class="card">
            <i class="fa fa-desktop blue"></i>
            <h2>63</h2>
            <p>Total Systems</p>
        </div>

        <div class="card">
            <i class="fa fa-check-circle green"></i>
            <h2>62</h2>
            <p>Working Systems</p>
        </div>

        <div class="card">
            <i class="fa fa-times-circle red"></i>
            <h2>1</h2>
            <p>Not Working Systems</p>
        </div>

        <div class="card" onclick="window.location.href='report.php'">
            <i class="fa fa-file purple"></i>
            <h2>View</h2>
            <p>Reports</p>
        </div>

    </div>

</div>

<!-- JS -->
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