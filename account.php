<?php
session_start();
require "db.php";

/* ✅ Check login */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];

/* UPDATE PROFILE */
/* UPDATE PROFILE */
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Default query (without image)
    $query = "UPDATE users SET name=?, email=?, phone=? WHERE id=?";
    $types = "sssi";
    $params = [$name, $email, $phone, $id];

    // If image uploaded
    if (!empty($_FILES['profile_pic']['name'])) {

        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $fileName = time() . "." . $ext;
        $target = "uploads/" . $fileName;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {

            $query = "UPDATE users SET name=?, email=?, phone=?, profile_pic=? WHERE id=?";
            $types = "ssssi";
            $params = [$name, $email, $phone, $fileName, $id];
        }
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $_SESSION['user'] = $name;
    $success = "Profile updated successfully!";
}

/* GET USER DATA */
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>My Account</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

*{
  box-sizing:border-box;
  margin:0;
  padding:0;
  font-family:"Poppins",sans-serif;
}

body{
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  background:
  linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.85)),
  url("https://img.freepik.com/premium-photo/realistic-photo-blurred-restaurant-background-with-some-people-eating-chefs-waiters-working-high-resolution-superdetail-16k_967785-42409.jpg")
  no-repeat center center fixed;
  background-size:cover;
}

.container{
  width:90%;
  max-width:450px;
  padding:30px;
  border-radius:25px;
  backdrop-filter:blur(18px);
  background:rgba(255,255,255,.12);
  box-shadow:0 25px 60px rgba(0,0,0,.5);
  text-align:center;
}

/* PROFILE IMAGE */
.profile-img{
  width:100px;
  height:100px;
  border-radius:50%;
  object-fit:cover;
  border:3px solid white;
  margin-bottom:15px;
}

/* INPUT */
input{
  width:100%;
  padding:12px;
  margin:10px 0;
  border-radius:10px;
  border:none;
  outline:none;
}

/* BUTTON */
button {
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 25px;
  background: linear-gradient(90deg,#ff5f6d,#ffc371);
  color: white;
  cursor: pointer;
  margin-bottom: 15px; /* add space below the button */
}

button:hover {
  color: black;
  background: radial-gradient(circle, rgba(209,35,50,1), rgba(255,5,5,1));
  box-shadow:
      0 0 10px rgba(255,255,255,0.8),
      0 0 20px rgba(255,255,255,0.6),
      0 0 35px rgba(255,255,255,0.4);
  transform: translateY(-2px);
}


/* SUCCESS */
.success{
  color:#00ffcc;
  margin-bottom:10px;
}
/* Wrapper for inputs to place the pencil icon */
.input-wrapper {
    position: relative;
    width: 100%;
    margin-bottom: 12px;
}

/* Pencil icon on right */
.input-wrapper input {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: none;
    outline: none;
    background: rgba(255, 255, 255, 0.27);
    color: white;
    cursor: default; /* show not editable by default */
}

/* Pencil icon */
.edit-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 16px;
    color: #fff;
}

/* Profile picture wrapper */
.profile-pic-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 2px;
}

/* Pencil icon for profile pic */
.profile-pic-wrapper .edit-icon {
    position: absolute;
    bottom: 100;
    right: 0;
    background: none;       /* remove red background */
    border-radius: 0;       /* remove any rounding */
    padding: 0;             /* remove extra padding */
    font-size: 18px;
    cursor: pointer;
    color: white;           /* keep pencil white */
    z-index: 2;
}

/* Profile image */
.profile-img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid white;
}
.backBtn {
  display: inline-block;
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 25px;
  text-decoration: none;
  color: white;
  background: linear-gradient(90deg,#ff5f6d,#ffc371);
  font-size: 16px;
  transition: 0.3s;
  margin-top: 1px; /* space from previous button */
}

.backBtn:hover {
  color: black;
  background: radial-gradient(circle, rgba(209,35,50,1), rgba(255,5,5,1));
  box-shadow:
      0 0 10px rgba(255,255,255,0.8),
      0 0 20px rgba(255,255,255,0.6),
      0 0 35px rgba(255,255,255,0.4);
  transform: translateY(-2px);
}
</style>

</head>

<body>

<div class="container">

<h2 style="color:white;"> My Profile</h2>

<!-- SUCCESS MESSAGE -->
<?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>



<form method="POST" enctype="multipart/form-data">

<div class="profile-pic-wrapper">
    <?php if(!empty($user['profile_pic'])): ?>
        <img src="uploads/<?= $user['profile_pic'] ?>" class="profile-img" id="profileImg">
    <?php else: ?>
        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" class="profile-img" id="profileImg">
    <?php endif; ?>

    <label for="profile_pic" class="edit-icon">✏️</label>
    <input type="file" name="profile_pic" id="profile_pic" style="display:none;">
</div>

<div class="input-wrapper">
    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" readonly>
    <span class="edit-icon" data-target="name" title="Edit Name">✏️</span>
</div>

<div class="input-wrapper">
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
    <span class="edit-icon" data-target="email" title="Edit Email">✏️</span>
</div>

<div class="input-wrapper">
    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" readonly>
    <span class="edit-icon" data-target="phone" title="Edit Phone">✏️</span>
</div>

<button type="submit">Update Profile</button>
<a class="backBtn" href="javascript:history.back()">Back</a>
</form>
</div>
<script>// Enable editing when clicking pencil icon
document.querySelectorAll('.input-wrapper .edit-icon').forEach(icon => {
    icon.addEventListener('click', function() {
        const targetName = this.getAttribute('data-target');
        const input = document.querySelector(`input[name="${targetName}"]`);
        input.removeAttribute('readonly');    // make editable
        input.focus();
    });
});

// Optional: make input readonly again when losing focus
document.querySelectorAll('.input-wrapper input').forEach(input => {
    input.addEventListener('blur', function() {
        this.setAttribute('readonly', true);
    });
});



// Open file selector when clicking profile image
document.querySelector('#profileImg').addEventListener('click', () => {
    document.getElementById('profile_pic').click();
});

// Preview image instantly
document.getElementById('profile_pic').addEventListener('change', function(e){
    const file = e.target.files[0];
    if(file){
        document.getElementById('profileImg').src = URL.createObjectURL(file);
    }
});
</script>
</body>
</html>