<?php
include 'koneksi.php';

session_start();
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

// Ambil ID peserta
$id_peserta = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id_peserta) {
  echo "<script>alert('ID peserta tidak ditemukan'); window.location='dashboard.php';</script>";
  exit;
}

// Ambil data peserta
$q = mysqli_query($koneksi, "SELECT * FROM peserta WHERE id = $id_peserta");
$peserta = mysqli_fetch_assoc($q);

if (!$peserta) {
  echo "<script>alert('Peserta tidak ditemukan'); window.location='dashboard.php';</script>";
  exit;
}

$id_acara = $peserta['id_acara'];

// Proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
  $nim = intval($_POST['nim']); // hanya integer
  $signature = mysqli_real_escape_string($koneksi, $_POST['signature']);

  $update = mysqli_query($koneksi, "UPDATE peserta SET
    nama = '$nama',
    nim = $nim,
    signature = '$signature'
    WHERE id = $id_peserta
  ");

  if ($update) {
    echo "<script>alert('Data peserta berhasil diperbarui'); window.location='edit_acara_kuliah.php?id=$id_acara';</script>";
    exit;
  } else {
    echo "<script>alert('Gagal memperbarui data peserta!');</script>";
  }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Peserta</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      --success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      --warning: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
      --danger: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
      --card-bg: rgba(255, 255, 255, 0.95);
      --shadow-lg: 0 8px 25px rgba(0,0,0,0.15);
      --radius: 16px;
    }

    body {
      background: var(--primary);
      background-attachment: fixed;
      font-family: 'Inter', sans-serif;
      color: #1a202c;
      min-height: 100vh;
      padding: 2rem 0;
    }

    body::before {
      content: '';
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: 
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%);
      z-index: -1;
    }

    .card-glass {
      background: var(--card-bg);
      border-radius: var(--radius);
      box-shadow: var(--shadow-lg);
      backdrop-filter: blur(20px);
      padding: 2rem;
      margin-bottom: 2rem;
      max-width: 650px;
      margin-left: auto;
      margin-right: auto;
    }

    h3 {
      font-weight: 700;
    }

    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102,126,234,0.25);
    }

    canvas {
      border: 2px dashed #ccc;
      border-radius: 8px;
      width: 100%;
      max-width: 500px;
      height: 150px;
      background: #fff;
    }

    .btn-modern {
      border: none;
      border-radius: 12px;
      padding: 0.6rem 1rem;
      font-weight: 600;
      font-size: 0.9rem;
      color: #fff;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-modern i { font-size: 1rem; }

    .btn-primary { background: var(--primary); }
    .btn-success { background: var(--success); }
    .btn-warning { background: var(--warning); color: #000; }
    .btn-danger  { background: var(--danger); }
    .btn-secondary { background: var(--secondary); }

    .btn-modern:hover {
      transform: translateY(-2px);
      filter: brightness(1.05);
    }
  </style>
</head>
<body>

<div class="container">
  <div class="card-glass">
    <h3 class="mb-4"><i class="fas fa-user-edit me-2"></i>Edit Peserta Kuliah</h3>
    <form method="POST" onsubmit="return handleSubmit()">
      <!-- Nama -->
      <div class="mb-3">
        <label for="nama" class="form-label"><i class="fas fa-user"></i> Nama</label>
        <input type="text" name="nama" id="nama" class="form-control" required 
          value="<?= htmlspecialchars($peserta['nama']) ?>">
      </div>

      <!-- NIM -->
      <div class="mb-3">
        <label for="nim" class="form-label"><i class="fas fa-id-card"></i> NIM</label>
        <input type="number" name="nim" id="nim" class="form-control" required 
          value="<?= htmlspecialchars($peserta['nim']) ?>">
      </div>

      <!-- Signature -->
      <div class="mb-3">
        <label class="form-label"><i class="fas fa-pen-nib"></i> Tanda Tangan</label>
        <canvas id="signatureCanvas"></canvas>
        <button type="button" class="btn btn-warning my-3" onclick="clearCanvas()">
          <i class="fas fa-eraser"></i> Hapus Tanda Tangan
        </button>
        <input type="hidden" name="signature" id="signatureInput">
      </div>

      <!-- Tombol -->
      <div class="d-flex gap-2 flex-wrap">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Simpan
        </button>
        <a href="edit_acara_kuliah.php?id=<?= $id_acara ?>" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Batal
        </a>
      </div>
    </form>
  </div>
</div>


<script>
const canvas = document.getElementById('signatureCanvas');
const ctx = canvas.getContext('2d');
let drawing = false;
let existingSignature = <?= json_encode($peserta['signature']) ?>;

function resizeCanvas() {
  const ratio = window.devicePixelRatio || 1;
  const width = canvas.offsetWidth;
  const height = canvas.offsetHeight;
  canvas.width = width * ratio;
  canvas.height = height * ratio;
  canvas.style.width = width + "px";
  canvas.style.height = height + "px";
  ctx.setTransform(1, 0, 0, 1, 0, 0);
  ctx.scale(ratio, ratio);
}
resizeCanvas();

// tampilkan signature lama jika ada
if (existingSignature && existingSignature !== '') {
  const img = new Image();
  img.onload = function () {
    ctx.drawImage(img, 0, 0, canvas.width / (window.devicePixelRatio || 1), canvas.height / (window.devicePixelRatio || 1));
  }
  img.src = existingSignature;
}

// Mouse events
canvas.addEventListener('mousedown', (e) => {
  drawing = true;
  ctx.beginPath();
  ctx.moveTo(getX(e), getY(e));
});
canvas.addEventListener('mousemove', (e) => {
  if (!drawing) return;
  ctx.lineTo(getX(e), getY(e));
  ctx.stroke();
});
canvas.addEventListener('mouseup', () => drawing = false);
canvas.addEventListener('mouseleave', () => drawing = false);

// Touch events
canvas.addEventListener('touchstart', (e) => {
  e.preventDefault();
  drawing = true;
  const touch = e.touches[0];
  const rect = canvas.getBoundingClientRect();
  ctx.beginPath();
  ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
});
canvas.addEventListener('touchmove', (e) => {
  e.preventDefault();
  if (!drawing) return;
  const touch = e.touches[0];
  const rect = canvas.getBoundingClientRect();
  ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
  ctx.stroke();
});
canvas.addEventListener('touchend', () => drawing = false);

function getX(e) {
  const rect = canvas.getBoundingClientRect();
  return e.clientX - rect.left;
}
function getY(e) {
  const rect = canvas.getBoundingClientRect();
  return e.clientY - rect.top;
}

function clearCanvas() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  existingSignature = '';
}

function handleSubmit() {
  const dataURL = canvas.toDataURL("image/png");
  if (isCanvasBlank()) {
    document.getElementById('signatureInput').value = '';
  } else {
    document.getElementById('signatureInput').value = dataURL;
  }
  return true;
}

function isCanvasBlank() {
  const blank = document.createElement('canvas');
  blank.width = canvas.width;
  blank.height = canvas.height;
  return canvas.toDataURL() === blank.toDataURL();
}
</script>

</body>
</html>

