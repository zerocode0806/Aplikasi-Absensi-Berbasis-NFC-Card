<?php
include 'koneksi.php';

session_start();
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

// Pastikan ID peserta diterima
$id_peserta = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_peserta <= 0) {
  echo "<script>alert('ID peserta tidak valid.'); window.location='dashboard.php';</script>";
  exit;
}

// Cek data peserta
$q = mysqli_query($koneksi, "SELECT * FROM peserta WHERE id = $id_peserta");
$peserta = mysqli_fetch_assoc($q);

if (!$peserta) {
  echo "<script>alert('Data peserta tidak ditemukan.'); window.location='dashboard.php';</script>";
  exit;
}

// Ambil ID acara untuk redirect kembali ke edit acara
$id_acara = $peserta['id_acara'];

// Hapus data
$delete = mysqli_query($koneksi, "DELETE FROM peserta WHERE id = $id_peserta");

if ($delete) {
  echo "<script>alert('Data peserta berhasil dihapus.'); window.location='edit_acara.php?id=$id_acara';</script>";
} else {
  echo "<script>alert('Gagal menghapus data peserta.'); window.location='edit_acara.php?id=$id_acara';</script>";
}
