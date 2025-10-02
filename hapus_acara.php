<?php
include 'koneksi.php';

session_start();

// Pastikan ada parameter id
if (!isset($_GET['id'])) {
  $_SESSION['error'] = 'ID acara tidak ditemukan.';
  header('Location: tambah_acara.php');
  exit;
}

$id = intval($_GET['id']);

// Cek apakah data acara ada
$check = mysqli_query($koneksi, "SELECT * FROM acara WHERE id = $id");
if (mysqli_num_rows($check) == 0) {
  $_SESSION['error'] = 'Acara tidak ditemukan.';
  header('Location: tambah_acara.php');
  exit;
}

// Hapus data
$delete = mysqli_query($koneksi, "DELETE FROM acara WHERE id = $id");

if ($delete) {
  $_SESSION['success'] = 'Acara berhasil dihapus.';
} else {
  $_SESSION['error'] = 'Gagal menghapus acara.';
}

header('Location: dashboard.php');
exit;
