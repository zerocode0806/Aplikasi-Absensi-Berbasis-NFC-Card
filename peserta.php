<?php 
require 'koneksi.php'; 
$search = trim($_GET['q'] ?? '');
$sql = "SELECT id, uid, nama, alamat, ttd, created_at FROM peserta_main";
if ($search !== '') {
  $like = "%{$search}%";
  $stmt = $koneksi->prepare("$sql WHERE uid LIKE ? OR nama LIKE ? ORDER BY id DESC");
  $stmt->bind_param("ss", $like, $like);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $result = $koneksi->query("$sql ORDER BY id DESC");
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Peserta Terdaftar</title>
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
      --info: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
      --warning: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
      --danger: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
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

    h1 {
      font-weight: 700;
      color: var(--text-primary);
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

    .btn-success { background: var(--success); }
    .btn-primary { background: var(--primary); }
    .btn-info    { background: var(--info); }
    .btn-secondary { background: var(--secondary); }
    .btn-outline-primary { 
      background: transparent; 
      border: 2px solid #667eea; 
      color: #667eea;
    }
    .btn-outline-danger { 
      background: transparent; 
      border: 2px solid #ff416c; 
      color: #ff416c;
    }
    .btn-outline-secondary { 
      background: transparent; 
      border: 2px solid #718096; 
      color: #718096;
    }

    .btn-modern:hover, .btn-outline-primary:hover, .btn-outline-danger:hover, .btn-outline-secondary:hover {
      transform: translateY(-2px);
      filter: brightness(1.05);
    }

    .log { 
      height: 120px; 
      overflow: auto; 
      border: 1px solid #e2e8f0; 
      border-radius: 8px; 
      padding: 8px; 
      background: #f9fafb; 
      font-size: 13px; 
      line-height: 1.3; 
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
      font-size: 14px;
    }

    td {
      padding: 12px 16px;
      border-bottom: 1px solid #eee;
      vertical-align: middle;
      font-size: 14px;
    }

    tr:nth-child(even) {
      background: #f9f9f9;
    }

    tr:hover {
      background: #f1f5ff;
    }

    .ttd-img {
      max-width: 80px;
      max-height: 40px;
      border: 1px solid #e2e8f0;
      border-radius: 4px;
    }

    @media (max-width: 768px) {
      .card-glass { padding: 1.5rem; }
      th, td { padding: 8px 12px; font-size: 12px; }
      .btn span { display: none; }
      .ttd-img { max-width: 60px; max-height: 30px; }
      .log { height: 100px; }
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
  <!-- Header -->
  <div class="card-glass mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <h1 class="h3 mb-0"><i class="fas fa-users me-2"></i>Daftar Peserta</h1>
      </div>
      <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
        <a href="tambah_peserta_main.php" class="btn btn-success btn-modern">
          <i class="fas fa-user-plus"></i> <span>Tambah Manual</span>
        </a>
        <a href="dashboard.php" class="btn btn-secondary btn-modern">
          <i class="fas fa-arrow-left"></i> <span>Kembali</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Search -->
  <div class="card-glass mb-4">
    <form class="d-flex gap-2" method="get">
      <input type="text" name="q" class="form-control" placeholder="Cari nama atau UID..." value="<?=htmlspecialchars($search)?>">
      <button class="btn btn-primary btn-modern">
        <i class="fas fa-search"></i> <span>Cari</span>
      </button>
    </form>
  </div>

  <!-- NFC Scanner -->
  <div class="card-glass mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Scan Kartu NFC</h5>
      <button id="btnScan" class="btn btn-success btn-modern">
        <i class="fas fa-play"></i> <span>Mulai Scan</span>
      </button>
    </div>
    <div class="log" id="log"></div>
  </div>

  <!-- Table -->
  <div class="card-glass">
    <h5 class="mb-3"><i class="fas fa-list me-2"></i>Peserta Terdaftar</h5>
    <div class="table-container">
      <table class="table mb-0">
        <thead>
          <tr>
            <th style="width:50px">#</th>
            <th style="width:120px">UID</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th style="width:100px">TTD</th>
            <th style="width:150px">Terdaftar</th>
            <th style="width:180px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $i=1;
          while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><code><?= htmlspecialchars($row['uid']) ?></code></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['alamat'])) ?></td>
            <td>
              <?php if($row['ttd']): ?>
                <img class="ttd-img" src="<?= htmlspecialchars($row['ttd']) ?>" alt="TTD">
              <?php else: ?>
                <em>Belum ada</em>
              <?php endif; ?>
            </td>
            <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
            <td>
              <div class="d-flex gap-1">
                <a href="edit_peserta_main.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-edit"></i> <span>Edit</span>
                </a>
                <a href="hapus_peserta_main.php?id=<?= $row['id'] ?>" 
                  class="btn btn-outline-danger btn-sm" 
                  onclick="return confirm('Yakin ingin menghapus peserta ini?')">
                  <i class="fas fa-trash"></i> <span>Hapus</span>
                </a>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script>
const logBox = document.getElementById('log');
function log(msg){ 
  logBox.innerHTML += `<div><small>${new Date().toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit',second:'2-digit'})} — ${msg}</small></div>`; 
  logBox.scrollTop = logBox.scrollHeight; 
}

let isProcessing = false;

async function startNFC(){
  if(!('NDEFReader' in window)){ 
    alert('Browser tidak mendukung Web NFC. Gunakan Chrome di Android.'); 
    return; 
  }
  try{
    const reader = new NDEFReader();
    reader.onreading = async (event) => {
      if(isProcessing) return;
      isProcessing = true;
      
      const uid = (event.serialNumber || '').toUpperCase();
      if(!uid){ 
        log('UID tidak terbaca'); 
        isProcessing = false; 
        return; 
      }
      
      log(`UID: <b>${uid}</b> - checking...`);
      
      try{
        // Cek apakah UID sudah terdaftar
        const res = await fetch('check_uid.php', {
          method:'POST',
          headers:{'Content-Type':'application/x-www-form-urlencoded'},
          body: new URLSearchParams({uid})
        });
        const data = await res.json();
        
        if(data.exists){
          log(`Sudah terdaftar: ${data.nama}`);
        } else {
          log(`UID belum terdaftar, buka form...`);
          window.location.href = 'tambah_peserta_main.php?uid=' + encodeURIComponent(uid);
        }
      }catch(e){
        log('Error cek UID, buka form...');
        window.location.href = 'tambah_peserta_main.php?uid=' + encodeURIComponent(uid);
      }
      
      isProcessing = false;
    };
    reader.onreadingerror = () => { isProcessing = false; };
    
    await reader.scan();
    log('NFC aktif - tempelkan kartu');
  }catch(e){
    console.error(e); 
    log('Error: ' + e.message); 
    isProcessing = false;
  }
}
document.getElementById('btnScan').addEventListener('click', startNFC);
</script>
</body>
</html>