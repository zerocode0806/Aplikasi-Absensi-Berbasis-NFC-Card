<?php
include 'koneksi.php';
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

$users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id_user ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen User</title>
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
      --danger: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
      --light-bg: #f8fafc;
      --card-bg: rgba(255, 255, 255, 0.95);
      --text-primary: #1a202c;
      --text-secondary: #718096;
      --shadow-lg: 0 8px 25px rgba(0,0,0,0.15);
      --radius: 16px;
    }

    body {
      background: var(--primary);
      background-attachment: fixed;
      font-family: 'Inter', sans-serif;
      color: var(--text-primary);
      min-height: 100vh;
      padding: 2rem 0;
    }

    body::before {
      content: '';
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: 
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.4) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.4) 0%, transparent 50%);
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
      color: var(--text-primary);
    }

    .btn-modern {
      border: none;
      border-radius: 12px;
      padding: 0.6rem 1.2rem;
      font-weight: 600;
      font-size: 0.9rem;
      color: #fff;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-modern i { font-size: 1rem; }

    .btn-success { background: var(--success); }
    .btn-primary { background: var(--primary); }
    .btn-secondary { background: var(--secondary); }
    .btn-danger { background: var(--danger); }

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

    @media (max-width: 768px) {
      th, td { white-space: nowrap; }
      .btn span { display: none; }
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
      
      .log { 
        height: 100px; 
        font-size: 13px; 
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

  <!-- Header -->
  <div class="card-glass mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <h3><i class="fas fa-users me-2"></i>Manajemen User</h3>
      </div>
      <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
        <button class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
          <i class="fas fa-user-plus"></i> <span>Tambah User</span>
        </button>
        <a href="dashboard.php" class="btn btn-secondary btn-modern">
          <i class="fas fa-arrow-left"></i> <span>Kembali</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="card-glass">
    <div class="table-container">
      <table class="table mb-0">
        <thead>
          <tr>
            <th style="width: 50px;">No</th>
            <th>Username</th>
            <th>Password</th>
            <th>Level</th>
            <th style="width: 180px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($row = mysqli_fetch_assoc($users)) : ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><code>************</code></td>
            <td>
              <span class="badge bg-<?= $row['level'] === 'admin' ? 'danger' : 'secondary' ?>">
                <?= $row['level'] ?>
              </span>
            </td>
            <td>
              <button class="btn btn-primary btn-sm btn-modern" data-bs-toggle="modal" data-bs-target="#modalEditUser<?= $row['id_user'] ?>">
                <i class="fas fa-edit"></i> <span>Edit</span>
              </button>
              <button class="btn btn-danger btn-sm btn-modern" data-bs-toggle="modal" data-bs-target="#modalHapusUser<?= $row['id_user'] ?>">
                <i class="fas fa-trash"></i> <span>Hapus</span>
              </button>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- Modal: Tambah User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1">
  <div class="modal-dialog">
    <form action="tambah_user.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah User Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input required type="text" class="form-control" name="username">
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input required type="password" class="form-control" name="password">
        </div>
        <input type="hidden" name="level" value="operator">
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary btn-modern">Simpan</button>
        <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Edit & Hapus User -->
<?php
mysqli_data_seek($users, 0);
while ($row = mysqli_fetch_assoc($users)) :
?>
  <!-- Edit -->
  <div class="modal fade" id="modalEditUser<?= $row['id_user'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <form action="edit_user.php" method="POST" class="modal-content">
        <input type="hidden" name="id_user" value="<?= $row['id_user'] ?>">
        <div class="modal-header">
          <h5 class="modal-title">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($row['username']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Password <small>(Kosongkan jika tidak ingin diubah)</small></label>
            <input type="password" name="password" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary btn-modern">Simpan</button>
          <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Hapus -->
  <div class="modal fade" id="modalHapusUser<?= $row['id_user'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <form action="hapus_user.php" method="GET" class="modal-content">
        <input type="hidden" name="id" value="<?= $row['id_user'] ?>">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Yakin ingin menghapus user <strong><?= htmlspecialchars($row['username']) ?></strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger btn-modern">Ya, Hapus</button>
          <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
<?php endwhile; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

