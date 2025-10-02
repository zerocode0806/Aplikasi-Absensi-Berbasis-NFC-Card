<?php
require 'koneksi.php';
header('Content-Type: application/json');

$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$id_acara = isset($_POST['id_acara']) ? (int)$_POST['id_acara'] : 0;

if ($uid === '' || !$id_acara) {
  echo json_encode(['status'=>'error','message'=>'UID atau ID Acara kosong']); 
  exit;
}

// cek apakah uid ada di peserta_main
$stmt = $koneksi->prepare("SELECT nama, alamat, ttd FROM peserta_main WHERE uid = ?");
$stmt->bind_param("s", $uid);
$stmt->execute();
$stmt->bind_result($nama, $alamat, $ttd);
$found = $stmt->fetch();
$stmt->close();

if ($found) {
    // cek apakah sudah ada di peserta (untuk acara ini)
    $cek = $koneksi->prepare("SELECT id FROM peserta WHERE uid=? AND id_acara=?");
    $cek->bind_param("si", $uid, $id_acara);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows == 0) {
        // insert ke tabel peserta
        $now = date("Y-m-d H:i:s");
        $ins = $koneksi->prepare("INSERT INTO peserta (id_acara, uid, nama, alamat, signature, waktu_isi) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->bind_param("isssss", $id_acara, $uid, $nama, $alamat, $ttd, $now);
        $ins->execute();
        $ins->close();
    } else {
        // kalau sudah ada, cukup update waktu absen
        $now = date("Y-m-d H:i:s");
        $upd = $koneksi->prepare("UPDATE peserta SET waktu_isi=? WHERE uid=? AND id_acara=?");
        $upd->bind_param("ssi", $now, $uid, $id_acara);
        $upd->execute();
        $upd->close();
    }

    $cek->close();

    echo json_encode([
      'status'=>'registered',
      'nama'=>$nama,
      'alamat'=>$alamat,
      'signature'=>$ttd,
      'waktu_isi'=>date('d-m-Y H:i', strtotime($now))
    ]);

} else {
    echo json_encode(['status'=>'unregistered']);
}
