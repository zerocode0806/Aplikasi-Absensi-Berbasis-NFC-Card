<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_user = $_POST['id_user'];
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  if (!empty($password)) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $koneksi->prepare("UPDATE users SET username = ?, password = ? WHERE id_user = ?");
    $stmt->bind_param("ssi", $username, $password_hash, $id_user);
  } else {
    $stmt = $koneksi->prepare("UPDATE users SET username = ? WHERE id_user = ?");
    $stmt->bind_param("si", $username, $id_user);
  }

  if ($stmt->execute()) {
    header('Location: user.php');
    exit;
  } else {
    echo "Gagal mengedit user!";
  }
}
?>
