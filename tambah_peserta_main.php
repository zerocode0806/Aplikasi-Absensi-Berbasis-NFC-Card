<?php 
require 'koneksi.php'; 
$prefillUid = isset($_GET['uid']) ? strtoupper($_GET['uid']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Peserta (Main)</title>
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
    <h3 class="mb-4"><i class="fas fa-user-plus me-2"></i>Tambah Peserta (Main)</h3>

    <form method="post" action="tambah_peserta_save.php" onsubmit="return beforeSubmit()">
      <div class="mb-3">
        <label class="form-label"><i class="fas fa-id-card"></i> UID Kartu</label>
        <input type="text" name="uid" class="form-control" value="<?=htmlspecialchars($prefillUid)?>" <?= $prefillUid ? 'readonly' : '' ?> required>
      </div>

      <div class="mb-3">
        <label class="form-label"><i class="fas fa-user"></i> Nama</label>
        <input type="text" name="nama" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label"><i class="fas fa-map-marker-alt"></i> Alamat</label>
        <textarea name="alamat" class="form-control" rows="3"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label"><i class="fas fa-pen-nib"></i> Tanda Tangan</label>
        <canvas id="pad"></canvas>
        <div class="mt-2">
          <button type="button" class="btn-modern btn-warning" onclick="clearPad()">
            <i class="fas fa-eraser"></i> <span>Bersihkan</span>
          </button>
        </div>
        <input type="hidden" name="ttd" id="ttd">
      </div>

      <div class="d-flex gap-2 flex-wrap">
        <button class="btn-modern btn-primary" type="submit">
          <i class="fas fa-save"></i> <span>Simpan</span>
        </button>
        <button type="button" class="btn-modern btn-secondary" onclick="history.back()">
          <i class="fas fa-arrow-left"></i> <span>Kembali</span>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
const canvas = document.getElementById('pad');
const ctx = canvas.getContext('2d');
let drawing = false, last = null;

function resizeCanvas(){
  const dpr = window.devicePixelRatio || 1;
  const rect = canvas.getBoundingClientRect();
  canvas.width = rect.width * dpr;
  canvas.height = rect.height * dpr;
  ctx.scale(dpr, dpr);
  ctx.lineWidth = 2; ctx.lineCap='round'; ctx.strokeStyle='#000';
}
resizeCanvas(); window.addEventListener('resize', resizeCanvas);

function pos(e){
  if(e.touches){ const t=e.touches[0]; return {x: t.clientX - canvas.getBoundingClientRect().left, y: t.clientY - canvas.getBoundingClientRect().top}; }
  return {x: e.offsetX, y: e.offsetY};
}
function start(e){ drawing=true; last=pos(e); }
function move(e){ if(!drawing) return; const p=pos(e); ctx.beginPath(); ctx.moveTo(last.x,last.y); ctx.lineTo(p.x,p.y); ctx.stroke(); last=p; }
function end(){ drawing=false; }

canvas.addEventListener('mousedown', start);
canvas.addEventListener('mousemove', move);
canvas.addEventListener('mouseup', end);
canvas.addEventListener('mouseleave', end);
canvas.addEventListener('touchstart', e=>{e.preventDefault(); start(e);});
canvas.addEventListener('touchmove', e=>{e.preventDefault(); move(e);});
canvas.addEventListener('touchend', e=>{e.preventDefault(); end();});

function clearPad(){ ctx.clearRect(0,0,canvas.width,canvas.height); }
function beforeSubmit(){
  document.getElementById('ttd').value = canvas.toDataURL('image/png'); 
  return true;
}
</script>
</body>
</html>
