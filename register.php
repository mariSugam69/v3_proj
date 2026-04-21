<?php
session_start();
require "db.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){

$name = $_POST['name'];
$type = $_POST['type']; // user or caterer
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

if($password != $confirm){
$error = "Passwords do not match!";
}else{

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users(name,email,phone,password,type) VALUES(?,?,?,?,?)");

if(!$stmt){
die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("sssss",$name,$email,$phone,$hashed,$type);

if($stmt->execute()){

$_SESSION['user'] = $name;
$_SESSION['type'] = $type;

/* Redirect based on type */

if($type === "user"){
header("Location: searchEvents.php");
}
elseif($type === "caterer"){
header("Location: addEvent.php");
}

exit();

}else{
$error = "Registration failed!";
}

}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Register</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
box-sizing:border-box;
font-family:"Poppins",sans-serif;
margin:0;
padding:0;
}

/* SAME BACKGROUND AS INDEX */
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

/* GLASS REGISTER BOX */
.container{
width:100%;
max-width:340px;   /* 🔻 smaller width */
padding:18px;
backdrop-filter: blur(18px);
    background:rgba(255,255,255,.12);
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
padding:10px;
border:none;
border-radius:25px;
font-size:14px;
cursor:pointer;
color:white;

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
}

h2{
font-size:18px;
margin-bottom:6px;
}
/* SAME BUTTON STYLE AS index.php */

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
.btn-group{
display:flex;
flex-direction:column; /* vertical buttons */
gap:15px; /* ⭐ THIS CREATES SPACE */
margin-top:10px;
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
   padding:10px;     /* 🔻 smaller */
  font-size:14px;
  width: 100%;
 
 margin:8px 0; 
  border-radius: 12px;
  border: none;
  outline: none;
 
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
p{
font-size:13px;
margin-top:5px;
}
input, select{
padding:8px;       /* 🔻 smaller fields */
font-size:13px;
margin:6px 0;      /* 🔻 less gap */
}
</style>
</head>

<body>

<div class="container">

<h2>Register</h2>

<?php
if(isset($error)){
echo "<p class='error'>$error</p>";
}
?>

<form method="POST">

<input type="text" name="name" placeholder="Name" required>
<select name="type" required>
            <option value="" disabled selected>Select Account Type</option>
            <option value="user">User </option>
            <option value="caterer">Caterer</option>
        </select>
<input type="email" name="email" placeholder="Email" required>

<input type="tel" name="phone" placeholder="Phone"
       maxlength="10" pattern="[0-9]{10}" required>

<input type="password" name="password" placeholder="Password" required>

<input type="password" name="confirm_password" placeholder="Confirm Password" required>

<div class="btn-group">
    <button type="submit">Register</button>

    <button type="button" onclick="window.location.href='index.php'">
        ⬅ Back
    </button>
</div>
<p>Already have account?
<a href="login.php" style="text-decoration: underline;">
 Login
</a>
</p>
</form>

</div>

</body>
</html>