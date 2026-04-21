<?php
session_start();
require "db.php";

/* Only caterer */
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != "caterer") {
    header("Location: index.php");
    exit();
}

$caterer_id = $_SESSION['user_id'];

/* Get only this user's events */
$stmt = $conn->prepare("SELECT * FROM events WHERE caterer_id=? ORDER BY id DESC");
$stmt->bind_param("i", $caterer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Events</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Poppins,sans-serif;
}

/* Background */
body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:30px;
    background:
       linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
        url("https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg")
        no-repeat center center/cover;
}

/* Glass Container */
.container{
    width:700px;
    max-height:85vh;
    padding:30px;
    border-radius:30px;
   background:rgba(35, 33, 33, 0.12);
  backdrop-filter:blur(18px);
    color:white;
    text-align:center;
    box-shadow:0 10px 40px rgba(0,0,0,.6);

    display:flex;
    flex-direction:column;
}

.username{
    font-size: 32px;
    font-weight: 600;
    color: #ffffff;

    margin-bottom: 5px;

    /* subtle underline highlight */
    display: inline-block;
    padding-bottom: 5px;
    border-bottom: 2px solid rgba(255,255,255,0.4);
}

/* small subtitle look */
.username::after{
    content: "Organizer";
    display: block;
    font-size: 13px;
    color: #bbb;
    font-weight: 400;
    margin-top: 3px;
    letter-spacing: 1px;
}

/* Scroll */
.events-wrapper{
    max-height:400px;
    overflow-y:auto;
    padding-right:10px;
    margin-bottom:20px;
}

/* Event Card */
.event-card{
    background:#f2f2f2;
    color:#333;
    padding:20px;
    border-radius:20px;
    margin-bottom:20px;
    text-align:left;
    box-shadow:0 5px 15px rgba(0,0,0,.3);
    transition:0.3s ease;
}

.event-card:hover{
    transform:translateY(-5px);
}

/* Text */
.event-title{
    font-size:22px;
    font-weight:bold;
    color:#ff5f6d;
    margin-bottom:10px;
}

.event-info{
    margin:6px 0;
    font-size:15px;
}

/* Back button */
.back-btn{
    display:inline-block;
    padding:12px 30px;
    border-radius:30px;
    text-decoration:none;
    color:white;
    background:linear-gradient(90deg,#ff5f6d,#ffc371);
    transition:0.3s;
}

.back-btn:hover{
    color:black;
    background: radial-gradient(circle, rgba(209,35,50,1), rgba(255,5,5,1));
    box-shadow:
        0 0 10px rgba(255,255,255,0.8),
        0 0 20px rgba(255,255,255,0.6),
        0 0 35px rgba(255,255,255,0.4);
    transform:translateY(-2px);
}
</style>

</head>

<body>

<div class="container">
<h1 class="username">
    👨‍🍳 <?= strtoupper(htmlspecialchars($_SESSION['user'])) ?>
</h1>
<h2 style="color:white; margin-bottom:10px; font-size:18px;">📅 My Events</h2>


<div class="events-wrapper">

<?php if($result->num_rows > 0): ?>
  <?php while($row = $result->fetch_assoc()): ?>

    <div class="event-card">

      <div class="event-title">🎉 <?= htmlspecialchars($row['event_name']) ?></div>

      <div class="event-info">📍 <b>Location:</b> <?= $row['location'] ?></div>

      <div class="event-info">📅 <b>Date:</b> <?= $row['date'] ?></div>

      <div class="event-info">⏰ <b>Time:</b> <?= $row['time'] ?></div>

      <div class="event-info">👷 <b>Workers:</b> <?= $row['workers_needed'] ?></div>

    </div>

  <?php endwhile; ?>
<?php else: ?>
  <p>No events added yet.</p>
<?php endif; ?>

</div>

<a href="addEvent.php" class="back-btn">⬅ Back</a>

</div>

</body>
</html>