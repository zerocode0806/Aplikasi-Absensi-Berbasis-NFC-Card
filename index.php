<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = mysqli_real_escape_string($koneksi, $_POST['username']);
  $password = mysqli_real_escape_string($koneksi, $_POST['password']);

  $result = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
  $user = mysqli_fetch_assoc($result);

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user;
    header("Location: dashboard.php");
    exit;
  } else {
    $error = "Username atau password salah!";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --primary-solid: #667eea;
      --card-bg: rgba(255, 255, 255, 0.9);
      --text-primary: #1a202c;
      --text-secondary: #718096;
      --shadow-lg: 0 8px 25px rgba(0,0,0,0.15);
      --border-radius-lg: 24px;
    }

    body {
      background: var(--primary);
      background-attachment: fixed;
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      color: var(--text-primary);
    }

    body::before {
      content: '';
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: 
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.4) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.4) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(120, 198, 121, 0.4) 0%, transparent 50%);
      z-index: -1;
      animation: float 20s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-8px); }
    }

    .card-form {
      background: var(--card-bg);
      backdrop-filter: blur(20px);
      border-radius: var(--border-radius-lg);
      padding: 2rem;
      box-shadow: var(--shadow-lg);
      width: 100%;
      max-width: 420px;
      border: 1px solid rgba(255,255,255,0.3);
      animation: slideInUp 0.6s ease-out;
    }

    @keyframes slideInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .card-form h3 {
      font-weight: 700;
      margin-bottom: 1.5rem;
      background: var(--primary);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .form-control {
      border-radius: 12px;
      border: 2px solid rgba(0,0,0,0.05);
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
    }
    .form-control:focus {
      border-color: var(--primary-solid);
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .btn-modern {
      border: none;
      border-radius: 12px;
      font-weight: 600;
      padding: 0.75rem 1.25rem;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      position: relative;
      overflow: hidden;
      color: #fff;
      width: 100%;
      justify-content: center;
      background: var(--primary);
      box-shadow: var(--shadow-lg);
    }
    .btn-modern:hover {
      filter: brightness(1.1);
      transform: translateY(-2px);
    }

    .alert {
      border-radius: 12px;
    }
  </style>
</head>
<body>

<div class="card-form">
  <h3><i class="fas fa-sign-in-alt me-2"></i> Login</h3>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label"><i class="fas fa-user me-1"></i> Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label"><i class="fas fa-lock me-1"></i> Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn-modern">
      <i class="fas fa-sign-in-alt"></i> Login
    </button>
  </form>
</div>

</body>
</html>
