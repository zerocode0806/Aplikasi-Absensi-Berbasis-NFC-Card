<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
  $id_user = (int) $_GET['id'];

  // Cegah penghapusan diri sendiri (opsional)
  session_start();
  if ($_SESSION['user']['id_user'] == $id_user) {
    echo "<script>alert('Anda tidak dapat menghapus akun Anda sendiri.'); window.location='manajemen_user.php';</script>";
    exit;
  }

  // Jalankan query hapus
  $stmt = $koneksi->prepare("DELETE FROM users WHERE id_user = ?");
  $stmt->bind_param("i", $id_user);

  if ($stmt->execute()) {
    header("Location: manajemen_user.php");
    exit;
  } else {
    echo "Gagal menghapus user!";
  }
} else {
  echo "ID tidak ditemukan.";
}
?>
