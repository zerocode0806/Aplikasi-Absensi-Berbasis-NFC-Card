<?php
include 'koneksi.php';

$id_acara = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$last_check = isset($_GET['last_check']) ? $_GET['last_check'] : null;

if ($last_check) {
    // Ambil hanya peserta yang lebih baru dari last_check
    $sql = "SELECT * FROM peserta 
            WHERE id_acara = $id_acara 
              AND waktu_isi > '$last_check'
            ORDER BY waktu_isi DESC";
} else {
    // Pertama kali ambil semua
    $sql = "SELECT * FROM peserta 
            WHERE id_acara = $id_acara 
            ORDER BY waktu_isi DESC";
}

$peserta = mysqli_query($koneksi, $sql);

$data = [];
$no = 1;
while ($row = mysqli_fetch_assoc($peserta)) {
    $data[] = [
        'no'        => $no++,
        'nama'      => htmlspecialchars($row['nama']),
        'alamat'    => htmlspecialchars($row['alamat']),
        'signature' => $row['signature'],
        'waktu_isi' => date('Y-m-d H:i:s', strtotime($row['waktu_isi'])) // format standar
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
