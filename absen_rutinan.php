<?php
include 'koneksi.php';
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

// Ambil ID acara
$id_acara = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id_acara) {
  echo "<script>alert('ID acara tidak ditemukan'); window.location='index.php';</script>";
  exit;
}

// Data acara
$acara = mysqli_query($koneksi, "SELECT * FROM acara WHERE id = $id_acara");
$acara_data = mysqli_fetch_assoc($acara);
if (!$acara_data) {
  echo "<script>alert('Acara tidak ditemukan'); window.location='index.php';</script>";
  exit;
}

// Data peserta awal
$peserta = mysqli_query($koneksi, "SELECT * FROM peserta WHERE id_acara = $id_acara ORDER BY waktu_isi DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Absensi Acara dengan NFC</title>
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
      padding: 0.75rem 1.25rem;
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

    .btn-modern:hover {
      transform: translateY(-2px);
      filter: brightness(1.05);
    }

    .log { 
      height: 200px; 
      overflow: auto; 
      border: 1px solid #e2e8f0; 
      border-radius: 8px; 
      padding: 10px; 
      background: #f9fafb; 
      font-size: 14px; 
      line-height: 1.4; 
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

    tr:nth-child(even) {
      background: #f9f9f9;
    }

    tr:hover {
      background: #f1f5ff;
    }

    .table img {
      max-width: 100px;
      border-radius: 6px;
      border: 1px solid #ddd;
    }

    /* Mobile responsive improvements */
    @media (max-width: 768px) {
      th, td { 
        white-space: nowrap;
        padding: 8px 12px;
      }
      
      .btn span { display: none; }
      
      td {
        border-bottom: 0.5px solid #f5f5f5;
      }
      
      .table img {
        max-width: 80px;
        border: 0.5px solid #e8e8e8;
      }
      
      .card-glass {
        padding: 1rem;
      }
      
      .log { 
        height: 120px; 
        font-size: 13px; 
      }
    }

    @media (max-width: 576px) {
      td {
        border-bottom: 0.25px solid #f8f8f8;
        padding: 6px 8px;
      }
      
      .table img {
        border: none;
      }
    }
  </style>
</head>
<body>

<div class="container">

  <!-- Header Acara -->
  <div class="card-glass mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <h3><i class="fas fa-calendar-alt me-2"></i>Absensi Acara: <?= htmlspecialchars($acara_data['nama_acara']) ?></h3>
        <p class="mb-0 text-muted small">
          <strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($acara_data['tanggal'])) ?> |
          <strong>Tempat:</strong> <?= htmlspecialchars($acara_data['tempat']) ?>
        </p>
      </div>
      <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
        <a href="tambah_peserta.php?id=<?= $id_acara ?>" class="btn btn-success btn-modern">
          <i class="fas fa-user-plus"></i> <span>Tambah Peserta</span>
        </a>
        <a href="export_docx.php?id=<?= $id_acara ?>" class="btn btn-primary btn-modern">
          <i class="fas fa-file-word"></i> <span>Download</span>
        </a>
        <a href="#" class="btn btn-info btn-modern" onclick="copyShareLink()">
          <i class="fas fa-share-alt"></i> <span>Bagikan</span>
        </a>
        <a href="dashboard.php" class="btn btn-secondary btn-modern">
          <i class="fas fa-arrow-left"></i> <span>Kembali</span>
        </a>
      </div>
    </div>
  </div>

  <!-- NFC Scanner -->
  <div class="card-glass mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Scan Kartu NFC</h5>
      <button id="btnScan" class="btn btn-primary btn-modern">
        <i class="fas fa-id-card"></i> <span>Mulai Scan NFC</span>
      </button>
    </div>
    <div class="log" id="log"></div>
  </div>

  <!-- Tabel Peserta -->
  <div class="card-glass">
    <h5 class="mb-3"><i class="fas fa-list me-2"></i>Daftar Peserta</h5>
    <div class="table-container">
      <table class="table mb-0">
        <thead>
          <tr>
            <th style="width: 50px;">No</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th style="width: 150px;">Tanda Tangan</th>
            <th style="width: 180px;">Waktu Absen</th>
          </tr>
        </thead>
        <tbody id="pesertaTable">
          <?php $no=1; while($row=mysqli_fetch_assoc($peserta)) : ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['alamat']) ?></td>
            <td>
              <?php if (!empty($row['signature'])): ?>
                <img src="<?= $row['signature'] ?>" alt="Tanda Tangan">
              <?php else: ?>
                <em>Belum ada</em>
              <?php endif; ?>
            </td>
            <td><?= date('d-m-Y H:i', strtotime($row['waktu_isi'])) ?></td>
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
  logBox.innerHTML += `<div>${new Date().toLocaleTimeString()} — ${msg}</div>`;
  logBox.scrollTop = logBox.scrollHeight;
}

let reader; 
let lastScan = 0;
let isScanning = false;

async function startNFC(){
  if(!('NDEFReader' in window)){
    alert('Browser tidak mendukung Web NFC. Gunakan Chrome Android.');
    return;
  }

  try{
    if(!reader){
      reader = new NDEFReader();
      
      // Set event handler sebelum scan untuk response lebih cepat
      reader.onreading = handleNFCReading;
      reader.onreadingerror = (error) => {
        console.warn('NFC read error:', error);
        isScanning = false; // Reset flag saat error
      };
      
      await reader.scan({
        signal: new AbortController().signal // Bisa dibatalkan jika perlu
      });
      
      log('✅ NFC aktif dan siap - tempelkan kartu sekarang!');
    } else {
      log('ℹ️ NFC sudah aktif - tempelkan kartu sekarang!');
    }
  }catch(e){
    console.error('NFC Start Error:', e);
    log('Error: ' + e.message);
    isScanning = false;
  }
}

async function handleNFCReading(event){
  const now = Date.now();
  
  // Hilangkan cooldown sepenuhnya untuk response maksimal
  // Hanya cek jika sedang proses untuk mencegah double processing
  if(isScanning) return;
  
  lastScan = now;
  isScanning = true;

  const uid = (event.serialNumber || '').toUpperCase();
  if(!uid){ 
    log('UID kosong / tidak terbaca'); 
    isScanning = false;
    return; 
  }

  log(`UID terdeteksi: <b>${uid}</b> - Processing...`);

  try{
    // Request dengan timeout sangat cepat dan optimasi header
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 3000); // 3 detik timeout
    
    const res = await fetch('nfc_check.php', {
      method:'POST',
      headers:{
        'Content-Type':'application/x-www-form-urlencoded',
        'Cache-Control': 'no-cache'
      },
      body: new URLSearchParams({uid, id_acara: <?= $id_acara ?>}),
      signal: controller.signal,
      cache: 'no-cache' // Hindari cache
    });
    
    clearTimeout(timeoutId);
    
    if(!res.ok) {
      throw new Error(`HTTP ${res.status}`);
    }
    
    const data = await res.json();

    if(data.status === 'registered'){
      log(`✅ Absensi tersimpan untuk: ${data.nama}`);
      prependRow(data);
    }else if(data.status === 'unregistered'){
      log('❗ UID belum terdaftar. Pindah ke form tambah peserta...');
      window.location.href = 'tambah_peserta_main.php?id=<?= $id_acara ?>&uid=' + encodeURIComponent(uid);
    }else{
      log('⚠️ ' + (data.message || 'Terjadi kesalahan'));
    }
  }catch(e){
    if (e.name === 'AbortError') {
      log('⚠️ Timeout - coba scan ulang');
    } else {
      console.error(e);
      log('⚠️ Error: ' + e.message + ' - coba scan ulang');
    }
  } finally {
    // Reset flag segera tanpa delay
    isScanning = false;
  }
}

document.getElementById('btnScan').addEventListener('click', startNFC);

// Tambah peserta ke tabel langsung
function prependRow(row) {
  const tbody = document.getElementById("pesertaTable");
  let tr = document.createElement("tr");
  tr.innerHTML = `
    <td></td>
    <td>${row.nama}</td>
    <td>${row.alamat}</td>
    <td>${row.signature ? `<img src="${row.signature}" alt="Tanda Tangan">` : `<em>Belum ada</em>`}</td>
    <td>${row.waktu_isi}</td>
  `;
  tbody.prepend(tr);
  renumber();
}

function renumber() {
  const tbody = document.getElementById('pesertaTable');
  [...tbody.querySelectorAll('tr')].forEach((tr, i) => {
    tr.querySelector('td').textContent = i + 1;
  });
}

function copyShareLink() {
  const idAcara = <?= $id_acara ?>;
  const link = `${window.location.origin}/tambah_peserta_public.php?id=${idAcara}`;
  navigator.clipboard.writeText(link).then(() => {
    alert('✅ Link berhasil disalin:\n' + link);
  }).catch(() => {
    alert('❌ Gagal menyalin link!');
  });
}


</script>
</body>
</html>