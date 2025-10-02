<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $level = 'operator'; // sudah otomatis dari form

  $stmt = $koneksi->prepare("INSERT INTO users (username, password, level) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $username, $password, $level);
  
  if ($stmt->execute()) {
    header('Location: user.php');
    exit;
  } else {
    echo "Gagal menambah user!";
  }
}
?>
