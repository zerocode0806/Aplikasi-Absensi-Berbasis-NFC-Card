<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama_acara = mysqli_real_escape_string($koneksi, $_POST['nama_acara']);
  $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
  $tempat = mysqli_real_escape_string($koneksi, $_POST['tempat']);
  $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']); // ✅ ambil jenis acara

  $insert = mysqli_query($koneksi, "INSERT INTO acara (nama_acara, tanggal, tempat, jenis) 
                                    VALUES ('$nama_acara', '$tanggal', '$tempat', '$jenis')");

  if ($insert) {
    $_SESSION['success'] = "Acara berhasil ditambahkan!";
    header('Location: dashboard.php');
    exit;
  } else {
    $_SESSION['error'] = "Gagal menambahkan acara.";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Acara</title>
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
    }

    h3 {
      font-weight: 700;
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

    .table-container {
      overflow-x: auto;
      border-radius: var(--radius);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: var(--radius);
      overflow: hidden;
    }

    th {
      background: #667eea;
      color: white;
      padding: 12px 16px;
      text-align: left;
    }

    td {
      padding: 12px 16px;
      border-bottom: 1px solid #eee;
      vertical-align: middle;
    }

    tr:nth-child(even) { background: #f9f9f9; }
    tr:hover { background: #f1f5ff; }

    .table img {
      max-width: 100px;
      border-radius: 6px;
      border: 1px solid #ddd;
    }

    @media (max-width: 768px) {
      th, td { white-space: nowrap; }
      .btn span { display: none; }
    }
  </style>
</head>
<body>

<div class="container">

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success card-glass">
      <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['success'] ?>
      <?php unset($_SESSION['success']); ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger card-glass">
      <i class="fas fa-exclamation-circle me-2"></i> <?= $_SESSION['error'] ?>
      <?php unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>

  <!-- Form Tambah Acara -->
  <div class="card-glass">
    <h3 class="mb-4"><i class="fas fa-calendar-edit me-2"></i>Tambah Acara</h3>
    <form method="POST">
      <div class="mb-3">
        <label for="nama_acara" class="form-label">Nama Acara</label>
        <input type="text" name="nama_acara" id="nama_acara" class="form-control" required placeholder="Masukkan nama acara">
      </div>

      <div class="mb-3">
        <label for="tanggal" class="form-label">Tanggal</label>
        <input type="date" name="tanggal" id="tanggal" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="tempat" class="form-label">Tempat</label>
        <input type="text" name="tempat" id="tempat" class="form-control" required placeholder="Masukkan lokasi acara">
      </div>

      <div class="mb-3">
        <label for="jenis" class="form-label">Jenis Acara</label>
        <select name="jenis" id="jenis" class="form-select" required>
          <option value="">-- Pilih Jenis Acara --</option>
          <option value="rutinan">Rutinan</option>
          <option value="kuliah">Kuliah</option>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> <span>Simpan</span>
        </button>
        <a href="dashboard.php" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> <span>Kembali</span>
        </a>
      </div>
    </form>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>