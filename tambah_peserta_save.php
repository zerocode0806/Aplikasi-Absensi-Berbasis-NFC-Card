<?php
require 'koneksi.php';

$uid    = strtoupper(trim($_POST['uid'] ?? ''));
$nama   = trim($_POST['nama'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$ttd    = $_POST['ttd'] ?? '';

if ($uid==='' || $nama==='') {
  die("UID dan Nama wajib diisi. <a href='tambah_peserta.php'>Kembali</a>");
}

$stmt = $koneksi->prepare("INSERT INTO peserta_main(uid, nama, alamat, ttd) VALUES(?,?,?,?)");
$stmt->bind_param("ssss", $uid, $nama, $alamat, $ttd);
if ($stmt->execute()) {
  header("Location: peserta.php?msg=ok");
} else {
  if ($koneksi->errno == 1062) {
    die("UID sudah terdaftar. <a href='tambah_peserta.php'>Kembali</a>");
  }
  die("Error: ".$koneksi->error);
}
