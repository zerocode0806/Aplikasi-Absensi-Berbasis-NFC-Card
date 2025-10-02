<?php
include 'koneksi.php';

session_start();
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

// Pastikan ID peserta diterima
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
  echo "<script>alert('ID peserta tidak valid.'); window.location='dashboard.php';</script>";
  exit;
}

// Cek data peserta
$q = mysqli_query($koneksi, "SELECT * FROM peserta_main WHERE id = $id");
$peserta = mysqli_fetch_assoc($q);

if (!$peserta) {
  echo "<script>alert('Data peserta tidak ditemukan.'); window.location='dashboard.php';</script>";
  exit;
}

// Ambil ID acara untuk redirect kembali ke edit acara
$id_acara = $peserta['id_acara'];

// Hapus data
$delete = mysqli_query($koneksi, "DELETE FROM peserta_main WHERE id = $id");

if ($delete) {
  echo "<script>alert('Data peserta berhasil dihapus.'); window.location='peserta.php';</script>";
} else {
  echo "<script>alert('Gagal menghapus data peserta.'); window.location='peserta.php';</script>";
}
