<?php
include 'koneksi.php';

session_start();
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

// Inisialisasi variabel pencarian agar tidak undefined
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$where = '';
if (!empty($q)) {
  // Escape input agar aman dari SQL Injection
  $safe_q = mysqli_real_escape_string($koneksi, $q);
  $where = "
    WHERE 
      a.nama_acara LIKE '%$safe_q%' 
      OR a.tempat LIKE '%$safe_q%'
      OR a.tanggal LIKE '%$safe_q%'
  ";
}

$query = "
  SELECT 
    a.id,
    a.nama_acara AS kasir,
    a.tanggal,
    a.tempat AS total,
    a.jenis,                      -- ✅ ambil kolom jenis
    COUNT(p.id) AS jumlah_item
  FROM acara a
  LEFT JOIN peserta p ON a.id = p.id_acara
  $where
  GROUP BY a.id
  ORDER BY a.tanggal DESC
";

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Aplikasi Absen</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
   :root {
    --primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --primary-solid: #667eea;
    --secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --warning: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    --danger: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    --dark: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --light-bg: #f8fafc;
    --card-bg: rgba(255, 255, 255, 0.95);
    --text-primary: #1a202c;
    --text-secondary: #718096;
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.15);
    --shadow-lg: 0 8px 25px rgba(0,0,0,0.15);
    --border-radius: 16px;
    --border-radius-lg: 24px;
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    background: var(--primary);
    background-attachment: fixed;
    color: var(--text-primary);
    font-family: 'Inter', 'Segoe UI', sans-serif;
    line-height: 1.6;
    min-height: 100vh;
    position: relative;
  }

  body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
      radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.4) 0%, transparent 50%),
      radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.4) 0%, transparent 50%),
      radial-gradient(circle at 40% 40%, rgba(120, 198, 121, 0.4) 0%, transparent 50%);
    z-index: -1;
    animation: float 20s ease-in-out infinite;
  }

  @keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-10px) rotate(1deg); }
    66% { transform: translateY(5px) rotate(-1deg); }
  }

  /* Custom Navbar with integrated search */
  .navbar-custom {
    background: var(--card-bg);
    backdrop-filter: blur(20px);
    border-radius: var(--border-radius-lg);
    margin: 1.5rem;
    padding: 1.5rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .navbar-brand {
    font-size: 1.75rem;
    font-weight: 700;
    background: var(--primary);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .navbar-brand i {
    background: var(--primary);
    border-radius: 12px;
    padding: 0.5rem;
    color: white;
    font-size: 1.25rem;
  }

  /* Search integration in navbar */
  .search-navbar {
    flex: 1;
    max-width: 400px;
    margin: 0 2rem;
  }

  .search-navbar input {
    width: 100%;
    border-radius: var(--border-radius);
    border: 2px solid rgba(0,0,0,0.1);
    background-color: #f8fafc;
    color: var(--text-primary);
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    transition: all 0.3s ease;
  }

  .search-navbar input::placeholder {
    color: var(--text-secondary);
  }

  .search-navbar input:focus {
    outline: none;
    border-color: var(--primary-solid);
    background-color: #fff;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
  }

  .search-navbar button {
    border-radius: var(--border-radius);
    border: none;
    background: var(--primary);
    color: #fff;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-sm);
    margin-left: 0.5rem;
  }

  .search-navbar button:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  .btn-modern {
    border: none;
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    box-shadow: var(--shadow-sm);
  }

  .btn-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s;
  }

  .btn-modern:hover::before {
    left: 100%;
  }

  .btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
  }

  .btn-success {
    background: var(--success);
    color: white;
  }

  .btn-success:hover {
    color: white;
    filter: brightness(1.1);
  }

  .btn-secondary {
    background: var(--secondary);
    color: white;
  }

  .btn-secondary:hover {
    color: white;
    filter: brightness(1.1);
  }

  .btn-danger {
    background: var(--danger);
    color: white;
  }

  .btn-danger:hover {
    color: white;
    filter: brightness(1.1);
  }

  .btn-primary {
    background: var(--primary);
    color: white;
    border: none;
  }

  .btn-primary:hover {
    color: white;
    filter: brightness(1.1);
  }

  .btn-warning {
    background: var(--warning);
    color: white;
    border: none;
  }

  .btn-warning:hover {
    color: white;
    filter: brightness(1.1);
  }

  /* Search Section - removed as it's now integrated in navbar */

  /* Main Content */
  .main-content {
    margin: 0 1.5rem 2rem 1.5rem;
  }

  .purchase-card {
    background: var(--card-bg);
    backdrop-filter: blur(20px);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-lg);
    padding: 2rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
  }

  .purchase-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary);
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .purchase-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
    border-color: rgba(102, 126, 234, 0.3);
  }

  .purchase-card:hover::before {
    opacity: 1;
  }

  .badge-pill {
    border-radius: 50px;
    font-size: 0.8rem;
    padding: 0.5rem 1rem;
    background: var(--primary);
    color: #fff;
    font-weight: 600;
    box-shadow: var(--shadow-sm);
  }

  .text-green {
    color: #10b981;
    font-weight: 600;
  }

  .fw-bold {
    font-weight: 700;
  }

  h3, h1 {
    color: var(--text-primary);
    font-weight: 700;
    margin-bottom: 1rem;
  }

  h5 {
    color: var(--text-primary);
    font-weight: 600;
    line-height: 1.4;
  }

  .text-muted {
    color: var(--text-secondary) !important;
  }

  small {
    font-size: 0.875rem;
  }

  /* Alert styling */
  .alert {
    background: var(--card-bg);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 193, 7, 0.3);
    border-radius: var(--border-radius-lg);
    color: var(--text-primary);
    box-shadow: var(--shadow-md);
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .navbar-custom {
      margin: 1rem;
      padding: 1rem;
    }

    .search-navbar {
      margin: 1rem 0;
      max-width: 100%;
      order: 3;
      width: 100%;
    }

    .search-navbar .d-flex {
      flex-direction: column;
      gap: 0.75rem;
    }

    .search-navbar input {
      width: 100%;
    }

    .search-navbar button {
      width: 100%;
      margin-left: 0;
    }

    .main-content {
      margin: 0 1rem 1.5rem 1rem;
    }

    .purchase-card {
      padding: 1.5rem;
    }

    .navbar-brand {
      font-size: 1.5rem;
    }

    .btn-modern {
      font-size: 0.85rem;
      padding: 0.625rem 1rem;
    }

    /* Mobile navbar buttons - evenly distributed */
    .navbar-nav {
      flex-direction: row !important;
      justify-content: space-evenly;
      flex-wrap: wrap;
      gap: 0.25rem;
      margin-top: 1rem;
    }

    .navbar-nav .nav-item {
      margin-bottom: 0;
      flex: 0 1 auto;
    }

    .navbar-nav .btn-modern {
      white-space: nowrap;
      font-size: 0.9rem;
      padding: 0.75rem 1rem;
    }
  }

  @media (max-width: 576px) {
    .purchase-card {
      font-size: 14px;
      padding: 1.25rem;
    }
    
    .navbar-custom {
      border-radius: var(--border-radius);
    }
    
    .purchase-card {
      border-radius: var(--border-radius);
    }

    /* Extra small screens - maintain even distribution */
    .navbar-nav {
      justify-content: space-evenly;
    }

    .navbar-nav .btn-modern {
      font-size: 0.85rem;
      padding: 0.6rem 0.8rem;
    }

    .navbar-nav .btn-modern span {
      display: none;
    }
  }

  /* Card Animation */
  .purchase-card {
    animation: slideInUp 0.6s ease-out backwards;
  }

  .purchase-card:nth-child(1) { animation-delay: 0.1s; }
  .purchase-card:nth-child(2) { animation-delay: 0.2s; }
  .purchase-card:nth-child(3) { animation-delay: 0.3s; }
  .purchase-card:nth-child(4) { animation-delay: 0.4s; }
  .purchase-card:nth-child(5) { animation-delay: 0.5s; }
  .purchase-card:nth-child(6) { animation-delay: 0.6s; }

  @keyframes slideInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Navbar Toggle Styling */
  .navbar-toggler {
    border: none;
    padding: 0.5rem;
    border-radius: 8px;
    background: var(--primary);
    color: white;
  }

  .navbar-toggler:focus {
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.2);
  }

  /* Desktop navbar styling - keep horizontal */
  @media (min-width: 992px) {
    .navbar-nav {
      flex-direction: row;
      align-items: center;
      gap: 0.5rem;
    }
    
    .navbar-nav .nav-item {
      margin-bottom: 0;
    }
  }
</style>

</head>
<body>

<!-- Navbar with integrated search -->
<nav class="navbar navbar-expand-lg navbar-custom">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      Aplikasi Absen
    </a>
    
    <!-- Search form integrated in navbar -->
    <form class="search-navbar d-none d-lg-flex" method="GET" action="">
      <div class="d-flex">
        <input 
          type="text" 
          name="q" 
          value="<?= htmlspecialchars($q) ?>" 
          class="form-control" 
          placeholder="Cari nama acara, tempat, atau tanggal...">
        <button class="btn btn-primary" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </form>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <i class="fas fa-bars"></i>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Mobile search -->
      <form class="search-navbar d-lg-none mb-3" method="GET" action="">
        <div class="d-flex">
          <input 
            type="text" 
            name="q" 
            value="<?= htmlspecialchars($q) ?>" 
            class="form-control" 
            placeholder="Cari nama acara, tempat, atau tanggal...">
          <button class="btn btn-primary" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </form>
      
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a href="tambah_acara.php" class="btn btn-success btn-modern">
            <i class="fas fa-plus"></i>
            <span class="d-none d-sm-inline">Tambah Acara</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="user.php" class="btn btn-secondary btn-modern">
            <i class="fas fa-users-cog"></i>
            <span class="d-none d-sm-inline">Kelola User</span>
          </a>
          <a href="peserta.php" class="btn btn-secondary btn-modern">
            <i class="fas fa-users"></i>
            <span class="d-none d-sm-inline">Kelola Peserta</span>
          </a>
        </li>
        <li class="nav-item">
          <button type="button" onclick="confirmLogout()" class="btn btn-danger btn-modern">
            <i class="fas fa-sign-out-alt"></i>
            <span class="d-none d-sm-inline">Logout</span>
          </button>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="main-content">
  <div class="row g-4">
    <?php
    while ($row = mysqli_fetch_assoc($result)):
      $tanggal = date('d/m/Y', strtotime($row['tanggal']));
      $formattedTotal = htmlspecialchars($row['total']);
    ?>
    <div class="col-12 col-md-6 col-lg-4">
      <div class="purchase-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="badge bg-info badge-pill">#<?= $row['id'] ?></span>
          <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i><?= $tanggal ?></small>
        </div>

        <h5 class="mb-3 text-truncate"><?= htmlspecialchars($row['kasir']) ?></h5>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex align-items-center gap-2 text-muted small">
            <i class="fas fa-users"></i>
            <?= $row['jumlah_item'] ?> orang
          </div>
          <div class="d-flex align-items-center gap-2 text-green small">
            <i class="fas fa-map-marker-alt"></i>
            <?= $formattedTotal ?>
          </div>
        </div>

        <a href="<?php 
              if ($row['jenis'] === 'kuliah') {
                echo 'absen_kuliah.php?id='.(int)$row['id'];
              } else {
                echo 'absen_rutinan.php?id='.(int)$row['id'];
              }
            ?>" 
          class="btn btn-primary btn-modern w-100 mb-2">
          <i class="fas fa-eye"></i> <span class="d-none d-sm-inline">Lihat Data</span>
        </a>

        <div class="d-flex justify-content-between gap-2">
          <a href="<?php 
                if ($row['jenis'] === 'kuliah') {
                  echo 'edit_acara_kuliah.php?id='.(int)$row['id'];
                } else {
                  echo 'edit_acara.php?id='.(int)$row['id'];
                }
              ?>" 
            class="btn btn-warning btn-modern w-50 d-flex justify-content-center align-items-center gap-2">
            <i class="fas fa-edit"></i>
            <span class="d-none d-sm-inline">Edit</span>
          </a>
          
          <a href="hapus_acara.php?id=<?= $row['id'] ?>" 
            class="btn btn-danger btn-modern w-50 d-flex justify-content-center align-items-center gap-2"
            onclick="return confirm('Yakin ingin menghapus acara ini?');">
            <i class="fas fa-trash"></i>
            <span class="d-none d-sm-inline">Hapus</span>
          </a>
        </div>
      </div>
    </div>

    <?php endwhile; ?>

    <?php if (mysqli_num_rows($result) == 0): ?>
      <div class="col-12">
        <div class="alert alert-warning text-center">
          <i class="fas fa-info-circle me-2"></i> Tidak ada data ditemukan.
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
function confirmLogout() {
  if (confirm("Apakah Anda yakin ingin logout?")) {
    window.location.href = "logout.php";
  }
}
</script>

</body>
</html>