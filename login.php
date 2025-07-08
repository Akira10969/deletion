<?php
session_start();

$error = '';
$showProgress = false;

$encoded = 'cHNtZTIwMjQ='; // base64 for 'psme2024'
$realPassword = base64_decode($encoded);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $password = $_POST['password'];
  if ($password === $realPassword) {
    $_SESSION['admin_logged_in'] = true;
    $showProgress = true;
  } else {
    $error = "Incorrect password.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f6fb;
    }
    .header-img {
      height: 360px;
      object-fit: cover;
      border-bottom-left-radius: 60px;
      border-bottom-right-radius: 60px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    .form-container {
      margin-top: -100px;
      background: white;
      border-radius: 20px;
      padding: 2.5rem;
      box-shadow: 0 15px 35px rgba(0,0,0,0.15);
      border: 1px solid #e0e0e0;
      animation: fadeInUp 0.8s ease-out;
    }
    .form-wrapper {
      max-width: 460px;
      width: 100%;
    }
    .form-label {
      font-weight: 600;
      color: #344767;
    }
    .form-control:focus {
      border-color: #0b1e63;
      box-shadow: 0 0 0 0.2rem rgba(11, 30, 99, 0.25);
    }
    .btn-primary {
      background-color: #0b1e63;
      border: none;
    }
    .btn-primary:hover {
      background-color: #0a1851;
    }
    .alert {
      background-color: rgba(220, 53, 69, 0.95);
      border: none;
      color: white;
      font-weight: 500;
      border-radius: 10px;
      margin-top: 10px;
    }
    .progress {
      height: 22px;
      border-radius: 30px;
      background-color: rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }
    .progress-bar {
      font-weight: bold;
      font-size: 14px;
      line-height: 22px;
    }
    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(30px); }
      100% { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body class="bg-white">
  <div class="w-100 overflow-hidden">
    <img src="img/Header.png" alt="Header" class="w-100 header-img" />
  </div>
  <div class="container d-flex justify-content-center">
    <div class="form-container form-wrapper text-center">
      <div class="mb-3">
        <img src="img/psmeinc.png" alt="PSME Logo" width="60" height="60">
      </div>
      <?php if (!$showProgress): ?>
        <h4 class="fw-bold mb-4">ADMIN LOGIN</h4>
        <form method="POST">
          <div class="mb-3 text-start">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control rounded-3" placeholder="Enter password" required>
          </div>
          <button type="submit" class="btn w-100 text-white" style="background-color:#0b1e63;">Login</button>
          <?php if (!empty($error)): ?>
            <div class="alert mt-3"><?= $error ?></div>
          <?php endif; ?>
        </form>
      <?php else: ?>
        <h5 class="mb-3">Validating access, please wait...</h5>
        <div class="progress">
          <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 0%;">0%</div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($showProgress): ?>
  <script>
    const progressBar = document.getElementById('progressBar');
    let width = 0;
    const interval = setInterval(() => {
      if (width >= 100) {
        clearInterval(interval);
        window.location.href = 'admin';
      } else {
        width += 4;
        progressBar.style.width = width + '%';
        progressBar.innerText = width + '%';
      }
    }, 90);
  </script>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>