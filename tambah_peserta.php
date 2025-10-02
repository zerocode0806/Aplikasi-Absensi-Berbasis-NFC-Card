<?php
include 'koneksi.php';

session_start();
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

// Ambil ID acara dari URL
$id_acara = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id_acara) {
  echo "<script>alert('ID acara tidak ditemukan'); window.location='index.php';</script>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
  $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
  $signature = mysqli_real_escape_string($koneksi, $_POST['signature']);

  // Simpan data ke tabel peserta
  $query = "INSERT INTO peserta (id_acara, nama, alamat, waktu_isi, signature) VALUES (
    '$id_acara', '$nama', '$alamat', NOW(), '$signature'
  )";

  if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Peserta berhasil ditambahkan'); window.location='absen.php?id=$id_acara';</script>";
  } else {
    echo "<script>alert('Gagal menyimpan data!');</script>";
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
    <h3 class="mb-4"><i class="fas fa-user-plus me-2"></i>Tambah Peserta</h3>
    <form method="POST" onsubmit="return handleSubmit()">
      <div class="mb-3">
        <label for="nama" class="form-label"><i class="fas fa-user"></i> Nama</label>
        <input type="text" name="nama" id="nama" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="alamat" class="form-label"><i class="fas fa-map-marker-alt"></i> Alamat</label>
        <textarea name="alamat" id="alamat" class="form-control" rows="3" required></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label"><i class="fas fa-pen-nib"></i> Tanda Tangan</label>
        <canvas id="signatureCanvas"></canvas>
        <button type="button" class="btn-modern btn-warning my-3" onclick="clearCanvas()">
          <i class="fas fa-eraser"></i> <span>Hapus Tanda Tangan</span>
        </button>
        <input type="hidden" name="signature" id="signatureInput">
      </div>

      <div class="d-flex gap-2 flex-wrap">
        <button type="submit" class="btn-modern btn-primary">
          <i class="fas fa-save"></i> <span>Simpan</span>
        </button>
        <a href="absen.php?id=<?= $id_acara ?>" class="btn-modern btn-secondary">
          <i class="fas fa-arrow-left"></i> <span>Batal</span>
        </a>
      </div>
    </form>
  </div>
</div>

<script>
const canvas = document.getElementById('signatureCanvas');
const ctx = canvas.getContext('2d');
let drawing = false;

// Resize canvas
function resizeCanvas() {
  const ratio = window.devicePixelRatio || 1;
  const width = canvas.offsetWidth;
  const height = canvas.offsetHeight;
  canvas.width = width * ratio;
  canvas.height = height * ratio;
  ctx.setTransform(1, 0, 0, 1, 0, 0);
  ctx.scale(ratio, ratio);
}
resizeCanvas();

// Mouse
canvas.addEventListener('mousedown', () => { drawing = true; ctx.beginPath(); });
canvas.addEventListener('mouseup', () => drawing = false);
canvas.addEventListener('mouseleave', () => drawing = false);
canvas.addEventListener('mousemove', draw);

// Touch
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

// Draw
function draw(e) {
  if (!drawing) return;
  const rect = canvas.getBoundingClientRect();
  ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
  ctx.stroke();
}

// Clear
function clearCanvas() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.beginPath();
}

// Save
function handleSubmit() {
  document.getElementById('signatureInput').value = canvas.toDataURL("image/png");
  return true;
}
</script>

</body>
</html>

