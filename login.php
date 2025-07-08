<?php
session_start();

$error = '';
$showProgress = false;

// Obfuscated password (this is still just for minimal disguise)
$encoded = 'cHNtZTIwMjQ='; // base64 of "psme2024"
$realPassword = base64_decode($encoded); // becomes 'psme2024'

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
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      background: linear-gradient(135deg, #0b1e63, #ffd700);
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
      overflow: hidden;
    }

    .blur-overlay {
      position: absolute;
      top: 0;
      left: 0;
      height: 100vh;
      width: 100vw;
      backdrop-filter: blur(8px);
      background: rgba(255, 255, 255, 0.1);
      z-index: 1;
    }

    .centered {
      position: relative;
      z-index: 2;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-box {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 16px;
      padding: 40px;
      max-width: 400px;
      width: 100%;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      color: #1a1a1a;
      animation: fadeIn 1s ease;
    }

    .login-box h4 {
      text-align: center;
      font-weight: 600;
      margin-bottom: 25px;
      color: #0b1e63;
    }

    .login-box input {
      background-color: #fff;
      border: 1px solid #ccc;
      color: #1a1a1a;
    }

    .login-box input::placeholder {
      color: #888;
    }

    .icon-lock {
      font-size: 3rem;
      display: block;
      text-align: center;
      margin-bottom: 15px;
      color: #0b1e63;
    }

    .btn-primary {
      background-color: #0b1e63;
      border: none;
      font-weight: 500;
    }

    .btn-primary:hover {
      background-color: #081a4c;
    }

    .alert {
      background-color: rgba(220, 53, 69, 0.95);
      border: none;
      color: white;
    }

    #progress-section {
      display: none;
      text-align: center;
      color: #0b1e63;
    }

    .progress {
      height: 25px;
      border-radius: 30px;
      background-color: rgba(0, 0, 0, 0.1);
    }

    .progress-bar {
      font-weight: 600;
      line-height: 25px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <div class="blur-overlay"></div>

  <div class="centered">
    <div class="login-box">
      <?php if (!$showProgress): ?>
        <div class="icon-lock">
          <i class="bi bi-lock-fill"></i>
        </div>
        <form method="POST">
          <h4>Admin Portal</h4>
          <input type="password" name="password" class="form-control mb-3" placeholder="Enter password" required />
          <button type="submit" class="btn btn-primary w-100">Login</button>

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger mt-3 mb-0"><?= $error ?></div>
          <?php endif; ?>
        </form>
      <?php else: ?>
        <div id="progress-section">
          <h5 class="mb-3">Validating access, please wait...</h5>
          <div class="progress">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 0%;">0%</div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($showProgress): ?>
    <script>
      const progressSection = document.getElementById('progress-section');
      const progressBar = document.getElementById('progressBar');
      progressSection.style.display = 'block';

      let width = 0;
      const interval = setInterval(() => {
        if (width >= 100) {
          clearInterval(interval);
          window.location.href = 'admin';
        } else {
          width += 5;
          progressBar.style.width = width + '%';
          progressBar.innerText = width + '%';
        }
      }, 80);
    </script>
  <?php endif; ?>
</body>
</html>
