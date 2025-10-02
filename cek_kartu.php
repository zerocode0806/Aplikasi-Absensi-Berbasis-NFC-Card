<?php
// cek_kartu.php

// sambungkan ke database
include "koneksi.php"; // pastikan file ini berisi $conn = new mysqli(...);

// Ambil parameter dari request
$uid = $_GET['uid'] ?? '';
$id_acara = $_GET['id'] ?? '';

$response = [];

if ($uid && $id_acara) {
    // cek apakah kartu sudah terdaftar di tabel peserta
    $stmt = $conn->prepare("SELECT nama FROM peserta WHERE uid_kartu = ? AND id_acara = ?");
    $stmt->bind_param("si", $uid, $id_acara);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // jika sudah terdaftar
        $response = [
            "status" => "terdaftar",
            "nama" => $row['nama']
        ];
    } else {
        // cek apakah kartu ini pernah dipakai di acara lain
        $stmt2 = $conn->prepare("SELECT nama FROM peserta WHERE uid_kartu = ?");
        $stmt2->bind_param("s", $uid);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($row2 = $result2->fetch_assoc()) {
            // kartu sudah dikenal, tinggal absen ke acara ini
            $conn->query("INSERT INTO peserta (id_acara, nama, alamat, signature, waktu_isi, uid_kartu)
                          SELECT ?, nama, alamat, signature, NOW(), uid_kartu FROM peserta WHERE uid_kartu = ? LIMIT 1");
            $stmt3 = $conn->prepare("INSERT INTO peserta (id_acara, nama, alamat, signature, waktu_isi, uid_kartu) 
                                     SELECT ?, nama, alamat, signature, NOW(), uid_kartu FROM peserta WHERE uid_kartu = ? LIMIT 1");
            $stmt3->bind_param("is", $id_acara, $uid);
            $stmt3->execute();

            $response = [
                "status" => "absen",
                "nama" => $row2['nama']
            ];
        } else {
            // kartu baru
            $response = [
                "status" => "baru"
            ];
        }
    }
} else {
    $response = [
        "status" => "error",
        "message" => "UID atau ID acara tidak ditemukan"
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
