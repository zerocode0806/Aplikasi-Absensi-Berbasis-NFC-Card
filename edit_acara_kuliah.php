<?php
include 'koneksi.php';

session_start();
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

// Ambil ID acara
$id_acara = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id_acara) {
  $_SESSION['error'] = 'ID acara tidak ditemukan.';
  header('Location: dashboard.php');
  exit;
}

// Ambil data acara
$q_acara = mysqli_query($koneksi, "SELECT * FROM acara WHERE id = $id_acara");
$acara = mysqli_fetch_assoc($q_acara);
if (!$acara) {
  $_SESSION['error'] = 'Acara tidak ditemukan.';
  header('Location: dashboard.php');
  exit;
}

// Update data acara jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama_acara = mysqli_real_escape_string($koneksi, $_POST['nama_acara']);
  $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
  $tempat = mysqli_real_escape_string($koneksi, $_POST['tempat']);

  $update = mysqli_query($koneksi, "UPDATE acara SET 
    nama_acara = '$nama_acara',
    tanggal = '$tanggal',
    tempat = '$tempat'
    WHERE id = $id_acara
  ");

  if ($update) {
    $_SESSION['success'] = 'Data acara berhasil diperbarui.';
    header("Location: edit_acara.php?id=" . $id_acara);
    exit;
  } else {
    $_SESSION['error'] = 'Gagal memperbarui data acara.';
  }
}

// Ambil data peserta
$peserta = mysqli_query($koneksi, "SELECT * FROM peserta WHERE id_acara = $id_acara ORDER BY waktu_isi DESC");
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

    /* Mobile responsive improvements */
    @media (max-width: 768px) {
      th, td { 
        white-space: nowrap;
        padding: 8px 12px; /* Reduced padding on mobile */
      }
      
      .btn span { display: none; }
      
      /* Even thinner borders on mobile */
      td {
        border-bottom: 0.5px solid #f5f5f5; /* Thinner border on mobile */
      }
      
      .table img {
        max-width: 80px; /* Smaller images on mobile */
        border: 0.5px solid #e8e8e8; /* Thinner image border on mobile */
      }
      
      .card-glass {
        padding: 1rem; /* Reduced padding on mobile */
      }
    }

    @media (max-width: 576px) {
      /* Extra small screens - even thinner */
      td {
        border-bottom: 0.25px solid #f8f8f8; /* Very thin border */
        padding: 6px 8px; /* Even smaller padding */
      }
      
      .table img {
        border: none; /* Remove image border on very small screens */
      }
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

  <!-- Form Edit Acara -->
  <div class="card-glass">
    <h3 class="mb-4"><i class="fas fa-calendar-edit me-2"></i>Edit Acara</h3>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Nama Acara</label>
        <input type="text" name="nama_acara" class="form-control" required value="<?= htmlspecialchars($acara['nama_acara']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Tanggal</label>
        <input type="date" name="tanggal" class="form-control" required value="<?= htmlspecialchars($acara['tanggal']) ?>">
      </div>
      <div class="mb-4">
        <label class="form-label">Tempat</label>
        <input type="text" name="tempat" class="form-control" required value="<?= htmlspecialchars($acara['tempat']) ?>">
      </div>
      <button type="submit" class="btn btn-primary btn-modern">
        <i class="fas fa-save"></i> <span>Simpan</span>
      </button>
      <a href="dashboard.php" class="btn btn-secondary btn-modern">
        <i class="fas fa-arrow-left"></i> <span>Kembali</span>
      </a>
    </form>
  </div>

  <!-- Data Peserta -->
  <div class="card-glass">
    <h3 class="mb-3"><i class="fas fa-users me-2"></i> Data Peserta</h3>
    <div class="table-container">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>NIM</th>
            <th>Tanda Tangan</th>
            <th>Waktu Absen</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; while ($row=mysqli_fetch_assoc($peserta)) : ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['nim']) ?></td>
            <td>
              <?php if (!empty($row['signature'])): ?>
                <img src="<?= $row['signature'] ?>" alt="Tanda Tangan">
              <?php else: ?>
                <em>Belum ada</em>
              <?php endif; ?>
            </td>
            <td><?= date('d-m-Y H:i', strtotime($row['waktu_isi'])) ?></td>
            <td>
              <a href="edit_peserta_kuliah.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-edit"></i> <span>Edit</span>
              </a>
              <a href="hapus_peserta.php?id=<?= $row['id'] ?>" 
                  class="btn btn-outline-danger btn-sm" 
                  onclick="return confirm('Yakin ingin menghapus peserta ini?')">
                  <i class="fas fa-trash"></i> <span>Hapus</span>
                </a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

