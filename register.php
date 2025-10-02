<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = mysqli_real_escape_string($koneksi, $_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $level = 'operator';

  $check = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
  if (mysqli_num_rows($check) > 0) {
    $error = "Username sudah digunakan!";
  } else {
    $query = "INSERT INTO users (username, password, level) VALUES ('$username', '$password', '$level')";
    if (mysqli_query($koneksi, $query)) {
      header("Location: index.php");
      exit;
    } else {
      $error = "Gagal membuat akun!";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #f9f9f9;
      font-family: 'Inter', sans-serif;
    }
    .card-form {
      background: #fff;
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
      max-width: 500px;
      margin: 100px auto;
    }
    .btn-success {
      background-color: #2ed573;
      border: none;
    }
    .btn-success:hover {
      background-color: #24b963;
    }
    .form-control:focus {
      border-color: #2ed573;
      box-shadow: none;
    }
  </style>
</head>
<body>

<div class="card-form">
  <h3 class="mb-4 fw-bold"><i class="fas fa-user-plus me-2"></i>Registrasi</h3>
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label"><i class="fas fa-user"></i> Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label"><i class="fas fa-lock"></i> Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="d-flex justify-content-between align-items-center">
      <button type="submit" class="btn btn-success"><i class="fas fa-user-plus me-1"></i> Daftar</button>
      <a href="index.php">Sudah punya akun?</a>
    </div>
  </form>
</div>

</body>
</html>
