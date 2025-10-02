<?php
require 'vendor/autoload.php';
include 'koneksi.php';

use PhpOffice\PhpWord\TemplateProcessor;

$id_acara = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id_acara) {
    die("ID acara tidak ditemukan.");
}

// Ambil data acara
$acara = mysqli_query($koneksi, "SELECT * FROM acara WHERE id = $id_acara");
$acara_data = mysqli_fetch_assoc($acara);
if (!$acara_data) {
    die("Data acara tidak ditemukan.");
}

// Ambil data peserta
$peserta = mysqli_query($koneksi, "SELECT * FROM peserta WHERE id_acara = $id_acara ORDER BY waktu_isi ASC");

// Gunakan template final
$templatePath = 'Absensi IP(P)NU Terik.docx';
$copiedPath = tempnam(sys_get_temp_dir(), 'absen_template_') . '.docx';
copy($templatePath, $copiedPath);

// Buka template
$template = new TemplateProcessor($copiedPath);

// Set detail acara (lebih aman)
$template->setValue('nama_acara', $acara_data['nama_acara'] ?: '-');

$tanggal = '-';
if (!empty($acara_data['tanggal']) && strtotime($acara_data['tanggal'])) {
    $tanggal = date('d/m/Y', strtotime($acara_data['tanggal']));
}
$template->setValue('tanggal', $tanggal);

$template->setValue('tempat', $acara_data['tempat'] ?: '-');


// Persiapkan peserta
$rows = [];
$no = 1;
$tempSignatures = [];

while ($p = mysqli_fetch_assoc($peserta)) {
    $filename = null;

    if (!empty($p['signature']) && str_starts_with($p['signature'], 'data:image')) {
        $imageData = explode(',', $p['signature'])[1];
        $imageRaw = base64_decode($imageData);
        $filename = sys_get_temp_dir() . '/ttd_' . uniqid() . '.png';

        // Buka gambar asli
        $src = imagecreatefromstring($imageRaw);
        if ($src !== false) {
            // Perbesar resolusi canvas (misal 600x200)
            $dst = imagecreatetruecolor(600, 200);
            imagesavealpha($dst, true);
            $trans = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefill($dst, 0, 0, $trans);

            // Salin gambar ke ukuran besar (HD)
            imagecopyresampled($dst, $src, 0, 0, 0, 0, 600, 200, imagesx($src), imagesy($src));
            imagepng($dst, $filename);
            imagedestroy($dst);
            imagedestroy($src);
        } else {
            file_put_contents($filename, $imageRaw); // fallback biasa
        }

        $tempSignatures[] = $filename;
    }


    $rows[] = [
        'no' => $no++,
        'nama' => $p['nama'],
        'alamat' => $p['alamat'],
        'ttd' => $filename
    ];
}

// Clone dan isi baris peserta
$template->cloneRow('no', count($rows));
$index = 1;
foreach ($rows as $row) {
    $template->setValue("no#$index", $row['no']);
    $template->setValue("nama#$index", $row['nama']);
    $template->setValue("alamat#$index", $row['alamat']);

    if ($row['ttd']) {
        $template->setImageValue("ttd#$index", [
        'path' => $row['ttd'],
        'width' => 80,    // lebih besar
        'height' => 50,    // lebih tajam, tapi masih pas
        'ratio' => true
    ]);

    } else {
        $template->setValue("ttd#$index", '');
    }

    $index++;
}

// Simpan dan kirim
$outputFile = sys_get_temp_dir() . '/absensi_export_' . time() . '.docx';
$template->saveAs($outputFile);

// Output
header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="Absensi_' . date('Ymd_His') . '.docx"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Length: ' . filesize($outputFile));
readfile($outputFile);

// Bersihkan file sementara
@unlink($outputFile);
foreach ($tempSignatures as $ts) @unlink($ts);
@unlink($copiedPath);
exit;
