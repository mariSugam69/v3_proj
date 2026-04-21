<?php
session_start();
require "db.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){

$login = $_POST['login'];      // ✅ changed
$password = $_POST['password'];

/* Check email OR phone */
$stmt = $conn->prepare("SELECT * FROM users WHERE email=? OR phone=?");
$stmt->bind_param("ss", $login, $login);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if($user && password_verify($password,$user['password'])){

   $_SESSION['user_id'] = $user['id'];   // ⭐ MUST ADD THIS
$_SESSION['email']   = $user['email']; 
$_SESSION['user']    = $user['name'];
$_SESSION['type']    = $user['type'];

    if($user['type']=="user"){
        header("Location: searchEvents.php");
    } else {
        header("Location: addEvent.php");
    }

    exit();

}else{
    $error = "Invalid Login";  // ✅ better than echo
}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
box-sizing:border-box;
font-family:"Poppins",sans-serif;
margin:0;
padding:0;
}

/* SAME BACKGROUND AS index.php */
body{
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
background:
linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
url('https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg')
no-repeat center center fixed;
background-size:cover;
color:white;
}

/* GLASS LOGIN BOX */
.login-box{
width:350px;
padding:40px;
background:rgba(255,255,255,.12);
backdrop-filter:blur(18px);
border-radius:25px;
box-shadow:0 25px 60px rgba(0,0,0,.5);
text-align:center;
}

/* INPUT STYLE SAME AS INDEX */
input{
width:100%;
padding:12px;
margin:10px 0;
border-radius:10px;
border:none;
outline:none;
transition:all .3s ease;
color:black;
}

input::placeholder{
color:#444;
}

/* INPUT GLOW EFFECT */
input:hover{
box-shadow:0 0 10px rgba(255,255,255,.6);
}

input:focus{
box-shadow:
0 0 8px rgba(255,255,255,.7),
0 0 16px rgba(255,255,255,.5);
transform:scale(1.01);
}

/* BUTTON SAME AS INDEX */
button{
width:100%;
padding:15px;
border:none;
border-radius:25px;
font-size:1.1rem;
cursor:pointer;
color:white;
gap: 10px;
background: linear-gradient(90deg,
rgba(255,95,109,1),
rgba(255,195,113,1));
transition:all .3s ease;
}

/* BUTTON HOVER GLOW */
button:hover{
color:black;
background: radial-gradient(circle,
rgba(209,35,50,1),
rgba(255,5,5,1));

box-shadow:
0 0 10px rgb(202,202,201),
0 0 20px rgb(204,201,201),
0 0 35px rgb(201,198,198);

transform:translateY(-2px);
}

a{
color:#ffc371;
text-decoration:none;
}

.error{
color:#ff4d4d;
margin-bottom:10px;
} /* SAME BUTTON STYLE AS index.php */

button{
background: linear-gradient(90deg, rgba(255, 95, 109, 1), rgba(255, 195, 113, 1));
-webkit-background: linear-gradient(90deg, rgba(255, 95, 109, 1), rgba(255, 195, 113, 1));
-moz-background: linear-gradient(90deg, rgba(255, 95, 109, 1), rgba(255, 195, 113, 1));

color: white;
border: none;
padding: 15px 25px;
border-radius: 25px;
font-size: 1.1rem;
cursor: pointer;
position: relative;
transition: background 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
}

/* 🔥 SAME HOVER EFFECT */

button:hover{
color: black;

background: radial-gradient(circle, rgba(209, 35, 50, 1), rgba(255, 5, 5, 1));
-webkit-background: radial-gradient(circle, rgba(209, 35, 50, 1), rgba(255, 5, 5, 1));
-moz-background: radial-gradient(circle, rgba(209, 35, 50, 1), rgba(255, 5, 5, 1));

box-shadow:
0 0 10px rgb(202, 202, 201),
0 0 20px rgb(204, 201, 201),
0 0 35px rgb(201, 198, 198);

transform: translateY(-2px);
}
/* INPUT BOX STYLE — SAME AS INDEX */

input, select {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border-radius: 12px;
  border: none;
  outline: none;
  font-size: 16px;
  color: #000;
  transition: box-shadow 0.3s ease, transform 0.2s ease, border 0.3s ease;
}

/* Placeholder color */
input::placeholder {
  color: #3b3838;
  opacity: 1;
}

/* Hover glow */
input:hover, select:hover {
  border: 1.5px solid black;
  box-shadow: 0 0 10px rgba(255,255,255,0.6);
}

/* Focus glow when typing */
input:focus, select:focus {
  border: 1.5px solid black;
  box-shadow:
    0 0 8px rgba(255,255,255,0.7),
    0 0 16px rgba(255,255,255,0.5);
  transform: scale(1.01);
}

/* Date & time text dark */
input[type="date"],
input[type="time"] {
  color: #000;
}
body {
  background:
    linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
    url('https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg')
    no-repeat center center fixed;
  background-size: cover;
  font-family: "Poppins", sans-serif;
}

.btn-group{
display:flex;
flex-direction:column; /* vertical buttons */
gap:15px; /* ⭐ THIS CREATES SPACE */
margin-top:10px;
}
</style>

</head>

<body>

<div class="login-box">

<h2>Login</h2>

<?php
if(isset($error)){
echo "<p class='error'>$error</p>";
}
?>

<form method="POST" autocomplete="off">

<input type="text" name="login" placeholder="Email or Phone Number" required autocomplete="off">

<input type="password" name="password" placeholder="Password" required autocomplete="new-password">

<div class="btn-group">
    <button type="submit">Login</button>

    <button type="button" onclick="window.location.href='index.php'">
        ⬅ Back
    </button>
</div>


</form>

<p>Don't have account?</p>

<a href="register.php" style="text-decoration: underline;">Register
</a>

</div>

</body>
</html>