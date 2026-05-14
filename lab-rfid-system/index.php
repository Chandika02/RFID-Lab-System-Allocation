<?php
if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if($username == "9276A" && $password == "1234"){
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid Login');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login Page</title>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{
    margin:0;
    font-family:'Segoe UI';
    background: linear-gradient(135deg, #eaf2ff, #dbeafe);
    overflow:hidden;
    position:relative;
}

/* BACKGROUND SVG */
svg{
    position:absolute;
    top:0;
    left:0;
    z-index:-1;
}

/* CENTER */
.container{
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

/* LOGIN BOX */
.login-box{
    width:380px;
    background:#f9fbff;
    padding:40px;
    border-radius:15px;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
}

/* TITLE */
h2{
    text-align:center;
    color:#2b4c7e;
    margin-bottom:30px;
}

/* INPUT BOX */
.input-box{
    display:flex;
    align-items:center;
    border:1px solid #d0d8e8;
    border-radius:10px;
    padding:10px;
    margin-bottom:20px;
    background:white;
}

.input-box i{
    color:#7a8ca5;
    margin-right:10px;
}

.input-box input{
    border:none;
    outline:none;
    width:100%;
    font-size:15px;
}

/* EYE ICON */
#toggleEye{
    margin-left:auto;
    cursor:pointer;
}

/* OPTIONS */
.options{
    display:flex;
    justify-content:space-between;
    font-size:14px;
    margin-bottom:20px;
    color:#6b7c93;
}

/* BUTTON */
button{
    width:100%;
    padding:12px;
    background:linear-gradient(to right,#3b82f6,#2563eb);
    border:none;
    color:white;
    border-radius:10px;
    font-size:16px;
    cursor:pointer;
}

button:hover{
    opacity:0.9;
}

/* BOTTOM TEXT */
.bottom{
    text-align:center;
    margin-top:20px;
    color:#7a8ca5;
    font-size:14px;
}

</style>
</head>

<body>

<!-- BACKGROUND DESIGN -->
<svg width="100%" height="100%" viewBox="0 0 1440 800">

    <circle cx="200" cy="300" r="250" fill="#c7dbff" opacity="0.4"/>
    <circle cx="1200" cy="500" r="300" fill="#b6ccff" opacity="0.3"/>
    <circle cx="900" cy="200" r="150" fill="#aac4ff" opacity="0.25"/>

    <circle cx="1300" cy="150" r="10" fill="#8fb3ff"/>
    <circle cx="1350" cy="200" r="6" fill="#8fb3ff"/>
    <circle cx="1250" cy="180" r="8" fill="#8fb3ff"/>

</svg>

<div class="container">

<form action="login.php" method="POST" class="login-box">

    <h2>Login Page</h2>

    <!-- USER -->
    <div class="input-box">
        <i class="fa fa-user"></i>
        <input type="text" name="username" placeholder="User ID" required>
    </div>

    <!-- PASSWORD -->
    <div class="input-box">
        <i class="fa fa-lock"></i>
        <input type="password" name="password" placeholder="Password" id="password" required>
        <i class="fa fa-eye" id="toggleEye" onclick="togglePassword()"></i>
    </div>

    <!-- OPTIONS -->
    <div class="options">
        <label><input type="checkbox"> Remember me</label>
        <span style="color:#3b82f6;cursor:pointer;">Forgot password?</span>
    </div>

    <!-- BUTTON -->
    <button name="login">Log In</button>

    <div class="bottom">
        <input type="checkbox"> Keep me logged in your setting.
    </div>

</form>

</div>

<!-- SCRIPT -->
<script>
function togglePassword() {
    var pass = document.getElementById("password");
    var eye = document.getElementById("toggleEye");

    if (pass.type === "password") {
        pass.type = "text";
        eye.classList.remove("fa-eye");
        eye.classList.add("fa-eye-slash");
    } else {
        pass.type = "password";
        eye.classList.remove("fa-eye-slash");
        eye.classList.add("fa-eye");
    }
}
</script>

</body>
</html>