<?php
session_start();
require "db.php";

/* Allow only USER accounts */
if(!isset($_SESSION['user']) || $_SESSION['type'] != "user"){
    header("Location: index.php");
    exit();
}

$events = [];

/* If search is used */
if(isset($_GET['search']) && $_GET['search'] != ""){
    $keyword = "%".$_GET['search']."%";
    $stmt = $conn->prepare("SELECT * FROM events WHERE (event_name LIKE ? OR location LIKE ?) ORDER BY date ASC");
    $stmt->bind_param("ss",$keyword,$keyword);
    $stmt->execute();
    $events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    /* Show all events if no search */
    $result = $conn->query("SELECT * FROM events ORDER BY date ASC");
    $events = $result->fetch_all(MYSQLI_ASSOC);
}

/* GET USER PROFILE PIC */
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id=?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Events | Catering Connect</title>
   <style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
  box-sizing:border-box;
  font-family:"Poppins",sans-serif;
  margin:0;
  padding:0;
}

body{
  min-height:100vh;
  display:flex;
  overflow:hidden;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  background:transparent;
  padding-bottom:60px;
}

/* Background after load */
body.loaded{
  background:
 linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
  url("https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg")
  no-repeat center center fixed;
  background-size:cover;
}

/* HEADER */
header{
  color:white;
  padding:15px;
  text-align:center;
  font-size:2rem;
  position:fixed;
  top:0;
  left:0;
  width:100%;
  z-index:1000;
}

/* MAIN */
main{
  width:80%;
  max-width:700px;
  padding:25px;
  margin-top:120px;
  background:rgba(255,255,255,.12);
  backdrop-filter:blur(18px);
  border-radius:25px;
  box-shadow:0 25px 60px rgba(0,0,0,.5);
  max-height:80vh;
  overflow-y:auto;
}

main::-webkit-scrollbar{ display:none; }

h2{ color:white; text-align:center; margin-bottom:10px; }
p{ color:#ccc; }

/* INPUTS */
input,select{
  width:100%;
  padding:12px;
  margin:8px 0;
  border-radius:10px;
  border:none;
  outline:none;
  transition:all .3s ease;
}

input:hover,select:hover{
  border:1.5px solid black;
  box-shadow:0 0 10px rgba(255,255,255,.6);
}

input:focus,select:focus{
  border:1.5px solid black;
  box-shadow:0 0 12px rgba(255,255,255,.7);
  transform:scale(1.01);
}

/* BUTTON */
button{
  background: linear-gradient(90deg, rgba(255,95,109,1), rgba(255,195,113,1));
  color:white;
  border:none;
  padding:12px 20px;
  border-radius:25px;
  font-size:1rem;
  cursor:pointer;
  transition:.3s;
}

button:hover{
  color:black;
  background: radial-gradient(circle, rgba(209,35,50,1), rgba(255,5,5,1));

  /* ✨ WHITE GLOW */
  box-shadow:
    0 0 10px rgba(255,255,255,0.8),
    0 0 20px rgba(255,255,255,0.6),
    0 0 35px rgba(255,255,255,0.4);

  transform:translateY(-2px);
}

/* NAV LINKS */
.nav-links{
  display:flex;
  flex-direction:column;
  gap:10px;
  margin-top:20px;
}

.viewBtn,.backBtn{
  display:block;
  text-align:center;
  padding:12px;
  gap:10px;
  border-radius:25px;
  text-decoration:none;
  color:white;
  background:linear-gradient(90deg,#ff5f6d,#ffc371);
}

.viewBtn:hover,
.backBtn:hover{
  color:black;
  background:radial-gradient(circle,#d12332,#ff0505);

  /* ✨ SAME WHITE GLOW */
  box-shadow:
    0 0 10px rgba(255,255,255,0.8),
    0 0 20px rgba(255,255,255,0.6),
    0 0 35px rgba(255,255,255,0.4);

  transform:translateY(-2px);
}

/* PROFILE ICON */
.avatar{
  position:fixed;
  top:20px;
  right:20px;
  width:55px;
  height:55px;
  border-radius:50%;
  overflow:hidden;              /* 🔥 IMPORTANT */
  cursor:pointer;
  z-index:99999;
  border:2px solid white;       /* optional nice border */
}

/* Fix image inside avatar */
.avatar-img{
  width:100%;
  height:100%;
  object-fit:cover;             /* 🔥 keeps image perfect */
  display:block;
}
.avatar:hover{ transform:scale(1.08); }

/* PROFILE PANEL */
.profile-panel{
  position:fixed;
  top:0;
  right:-380px;
  width:360px;
  height:100%;
  background:#000;
  padding:25px;
  transition:.35s;
  z-index:9999;
}

.profile-panel.active{ right:0; }

/* HEADER INSIDE PANEL */
.profile-header h2{
    font-size:28px;
    margin-bottom:20px;
    margin-bottom: 8px;
}



.username{
    
    color:#aaa;
    margin-top: 30px;
}

/* CARDS */
.profile-card{
  display:block;
  background:linear-gradient(145deg,#1f1f1f,#151515);
  padding:15px;
  margin:12px 0;
  border-radius:18px;
  color:#ddd;
  text-decoration:none;
  font-size:16px;
}

.profile-card:hover{
  transform:translateX(6px);
}

.profile-card.danger{ color:#ff5252; }
.profile-card.logout{ color:#ffb74d; }

.search-box{
  display:flex;
  flex-direction:column;   /* 🔥 makes vertical */
  gap:10px;
}

.search-box input,
.search-box select,
.search-box button{
  flex:1;
}
.close-btn{
  display:block;
  margin:20px auto 0 auto;
  width:80%;
  padding:12px;
  border-radius:25px;

  background:transparent;        /* 🔥 transparent */
  color: #ffffff;;
  border:2px solid #565252;        /* 🔥 white border */

  font-size:15px;
  cursor:pointer;
  transition:all .3s ease;
  text-align:center;
}

/* ✨ Hover Glow Effect */
.close-btn:hover{
  background:rgba(255,255,255,0.1);
  color: #ffffff;;
  box-shadow:none;   /* ❌ removes glow */
  transform:none;    /* ❌ removes zoom */
}
</style>
    
</head>
<body>


<header id="mainHeader">🍽 Catering Connect</header>



<!-- ⭐ SMALL CLICK ICON -->
<div class="avatar" onclick="toggleProfile()">
    <?php if(!empty($userData['profile_pic'])): ?>
        <img src="uploads/<?= $userData['profile_pic'] ?>" class="avatar-img">
    <?php else: ?>
        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" class="avatar-img">
    <?php endif; ?>
</div>

<!-- ⭐ SLIDE PANEL -->
<div class="profile-panel" id="profilePanel">

    <div class="profile-header">
        <h2>My Profile</h2>

        

      <p class="username"
   style="color:white; font-size:20px; font-weight:700; text-align:center; text-transform: uppercase;">
   <?= htmlspecialchars($_SESSION['user']) ?>
</p>
    </div>

    <hr>

    <a href="account.php" class="profile-card">👤 My Account</a>
    <a href="help.php" class="profile-card">❓ Help</a>
    <a href="delete.php" onclick="return confirmDelete()" class="profile-card danger">
🗑 Delete Account
</a>
    <a href="logout.php" class="profile-card logout">🚪 Logout</a>
<button class="close-btn" onclick="toggleProfile()">❌ Close</button>
</div>
<main>
    <h2>Available Events</h2>
    <p style="color: #ccc; text-align: center; margin-bottom: 20px;">Find catering opportunities near you</p>

<form action="viewEvents.php" method="GET" class="search-box">

<input type="date" name="date">

<select name="location">
<option value="">Select Location</option>
<option>Udupi</option>
<option>Kapu</option>
<option>Bynduru</option>
<option>Karkala</option>
<option>Kundapura</option>
<option>Hebri</option>
<option>Brahmavara</option>
<option>Mangaluru</option>
<option>Ullal</option>
<option>Mulki</option>
<option>Moodbidri</option>
<option>Bantwala</option>
<option>Belathangadi</option>
<option>Putturu</option>
<option>Sulya</option>
<option>Kadaba</option>
</select>


<button type="submit" id="searchBtn" disabled>Search</button>

</form>

<div class="nav-links">

<a href="viewEvents.php" class="viewBtn"> View All Events</a>

<a href="index.php" class="backBtn">Back</a>

</div>
</main>

<script>
    window.onload = function() {
        document.body.classList.add('loaded');
        setTimeout(() => {
            document.getElementById('mainHeader').classList.add('show');
        }, 300);
    };
    
function toggleProfile(){
    document.getElementById("profilePanel").classList.toggle("active");
}

/* close when clicking outside */
document.addEventListener("click", function(e){
    const panel = document.getElementById("profilePanel");
    const avatar = document.querySelector(".avatar");

    if(!panel.contains(e.target) && !avatar.contains(e.target)){
        panel.classList.remove("active");
    }
});

function confirmDelete() {
    return confirm("⚠️ Are you sure you want to delete your account?\nThis action cannot be undone!");
}

const dateInput = document.querySelector('input[name="date"]');
const locationInput = document.querySelector('select[name="location"]');
const btn = document.getElementById("searchBtn");

function checkInputs() {
    if (dateInput.value !== "" || locationInput.value !== "") {
        btn.disabled = false;
        btn.style.opacity = "1";
    } else {
        btn.disabled = true;
        btn.style.opacity = "0.5";
    }
}

dateInput.addEventListener("input", checkInputs);
locationInput.addEventListener("change", checkInputs);

</script>


</body>
</html>