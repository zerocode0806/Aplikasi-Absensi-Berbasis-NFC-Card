<?php
include 'koneksi.php';

$id_acara = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id_acara) {
  echo "<script>alert('ID acara tidak ditemukan'); window.location='index.php';</script>";
  exit;
}

$cookie_name = 'absen_' . $id_acara;
$alreadyFilled = isset($_COOKIE[$cookie_name]);

// Jika POST (submit AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
  $nim = (int) $_POST['nim']; // pastikan integer
  $signature = mysqli_real_escape_string($koneksi, $_POST['signature']);

  $query = "INSERT INTO peserta (id_acara, nama, nim, waktu_isi, signature) VALUES (
    '$id_acara', '$nama', '$nim', NOW(), '$signature'
  )";
  mysqli_query($koneksi, $query);

  // Set cookie selama 30 hari agar tidak bisa isi lagi
  setcookie($cookie_name, 'sudah', time() + (86400 * 30), "/");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Form Absensi Peserta</title>
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
      inset: 0;
      background: 
        radial-gradient(circle at 20% 80%, rgba(120,119,198,0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255,119,198,0.3) 0%, transparent 50%);
      z-index: -1;
    }

    .card-glass {
      background: var(--card-bg);
      border-radius: var(--radius);
      box-shadow: var(--shadow-lg);
      backdrop-filter: blur(20px);
      padding: 2rem;
      max-width: 650px;
      margin: auto;
    }

    h3 { font-weight: 700; }

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
    .btn-primary   { background: var(--primary); }
    .btn-success   { background: var(--success); }
    .btn-warning   { background: var(--warning); color:#000; }
    .btn-danger    { background: var(--danger); }
    .btn-secondary { background: var(--secondary); }
    .btn-modern:hover {
      transform: translateY(-2px);
      filter: brightness(1.05);
    }

    .thank-you {
      text-align: center;
      margin-top: 30px;
      padding: 20px;
      border-radius: 12px;
      font-weight: 600;
      background: var(--success);
      color: #fff;
      box-shadow: var(--shadow-lg);
    }
  </style>
</head>
<body>

<div class="container">
  <div class="card-glass">
    <h3 class="mb-4 text-center"><i class="fas fa-user-edit me-2"></i>Form Absensi Kuliah</h3>

    <?php if ($alreadyFilled): ?>
      <!-- ✅ Jika sudah mengisi -->
      <div id="thankYouMessage" class="thank-you">
        <i class="fas fa-check-circle me-2"></i>
        Anda sudah mengisi absen kuliah.
      </div>
    <?php else: ?>
      <!-- ✅ Form tampil jika belum mengisi -->
      <form id="absenForm" method="POST" onsubmit="return handleSubmit(event)">
        <div class="mb-3">
          <label class="form-label"><i class="fas fa-user"></i> Nama</label>
          <input type="text" name="nama" id="nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fas fa-id-card"></i> NIM</label>
          <input type="number" name="nim" id="nim" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fas fa-pen-nib"></i> Tanda Tangan</label>
          <canvas id="signatureCanvas"></canvas>
          <button type="button" class="btn-modern btn-warning my-2" onclick="clearCanvas()">
            <i class="fas fa-eraser"></i> <span>Hapus Tanda Tangan</span>
          </button>
          <input type="hidden" name="signature" id="signatureInput">
        </div>
        <button type="submit" class="btn-modern btn-primary w-100">
          <i class="fas fa-paper-plane"></i> <span>Kirim Absensi</span>
        </button>
      </form>

      <!-- ✅ Pesan sukses muncul setelah submit -->
      <div id="thankYouMessage" class="thank-you d-none">
        <i class="fas fa-check-circle me-2"></i>
        Terima kasih! Absensi Anda berhasil direkam.
      </div>
    <?php endif; ?>
  </div>
</div>


<script>
const canvas = document.getElementById('signatureCanvas');
const ctx = canvas?.getContext('2d');
let drawing = false;

function resizeCanvas() {
  if (!canvas) return;
  const ratio = window.devicePixelRatio || 1;
  const width = canvas.offsetWidth;
  const height = canvas.offsetHeight;
  canvas.width = width * ratio;
  canvas.height = height * ratio;
  ctx.setTransform(1, 0, 0, 1, 0, 0);
  ctx.scale(ratio, ratio);
}
resizeCanvas();

canvas?.addEventListener('mousedown', () => { drawing = true; ctx.beginPath(); });
canvas?.addEventListener('mouseup', () => drawing = false);
canvas?.addEventListener('mouseleave', () => drawing = false);
canvas?.addEventListener('mousemove', draw);

canvas?.addEventListener('touchstart', (e) => {
  e.preventDefault();
  drawing = true;
  const touch = e.touches[0];
  const rect = canvas.getBoundingClientRect();
  ctx.beginPath();
  ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
});
canvas?.addEventListener('touchmove', (e) => {
  e.preventDefault();
  if (!drawing) return;
  const touch = e.touches[0];
  const rect = canvas.getBoundingClientRect();
  ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
  ctx.stroke();
});
canvas?.addEventListener('touchend', () => drawing = false);

function draw(e) {
  if (!drawing) return;
  const rect = canvas.getBoundingClientRect();
  ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
  ctx.stroke();
}

function clearCanvas() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.beginPath();
}

function handleSubmit(e) {
  e.preventDefault();
  const dataURL = canvas.toDataURL("image/png");
  document.getElementById('signatureInput').value = dataURL;

  const formData = new FormData(document.getElementById('absenForm'));
  fetch(window.location.href, {
    method: "POST",
    body: formData
  })
  .then(() => {
    document.getElementById('absenForm').classList.add('d-none');
    document.getElementById('thankYouMessage').classList.remove('d-none');
    document.getElementById('thankYouMessage').innerHTML =
      '<i class="fas fa-check-circle me-2"></i>Terima kasih! Absensi Anda berhasil direkam.';
    document.cookie = "<?= $cookie_name ?>=sudah; path=/; max-age=" + (86400 * 30);
  })
  .catch(() => alert("Gagal menyimpan data."));
}
</script>

</body>
</html>
