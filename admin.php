<?php
// Connect to database
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'deletion';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit;
}

// Optional status filter
$statusFilter = '';
if (isset($_GET['status']) && in_array($_GET['status'], ['Pending', 'Successful', 'Cancelled'])) {
  $status = $conn->real_escape_string($_GET['status']);
  $statusFilter = "WHERE status = '$status'";
}

// Toast message handling
$toast = '';
$toastType = '';
if (isset($_GET['success'])) {
  $toast = "✅ Status updated successfully!";
  $toastType = "bg-success";
} elseif (isset($_GET['cancelled'])) {
  $toast = "❎ Deletion request was cancelled.";
  $toastType = "bg-danger";
} elseif (isset($_GET['error'])) {
  $toast = "⚠️ Something went wrong.";
  $toastType = "bg-warning";
}

// Fetch data
$sql = "SELECT * FROM account_deletion_requests $statusFilter ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Deletion Requests</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fb;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .sidebar {
      height: 100vh;
      background: #0b1e63;
      color: #fff;
      padding: 2rem 1rem;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
    }
    .sidebar h5 {
      font-weight: 700;
      margin-bottom: 2rem;
      text-align: center;
    }
    .sidebar .nav-link {
      color: #ffffffd9;
      font-weight: 500;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      transition: 0.3s;
    }
    .sidebar .nav-link:hover {
      background: rgba(255, 255, 255, 0.15);
      color: #fff;
    }
    .dashboard-header {
      background-color: #ffffff;
      padding: 1rem 2rem;
      border-bottom: 1px solid #dee2e6;
      box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .dashboard-header h4 {
      font-weight: 600;
    }
    .stat-card {
      background: #fff;
      border-left: 5px solid #0b1e63;
      border-radius: 16px;
      padding: 1.5rem;
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
      transition: transform 0.2s ease-in-out;
    }
    .stat-card:hover {
      transform: translateY(-2px);
    }
    .stat-card h6 {
      font-size: 14px;
      color: #6c757d;
      margin-bottom: 0.5rem;
      text-transform: uppercase;
    }
    .stat-card h4 {
      font-size: 24px;
      margin: 0;
      font-weight: bold;
    }
    .card-header {
      font-weight: 600;
      background-color: #ffffff;
    }
    .table-hover tbody tr:hover {
      background-color: #f1f3f5;
    }
    .btn-success {
      border-radius: 20px;
      padding: 6px 16px;
      font-size: 14px;
    }
    .toast {
      min-width: 250px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .form-select {
      border-radius: 12px;
      padding: 0.5rem 1rem;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar d-flex flex-column">
        <h5>PSME Admin</h5>
        <a href="admin.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="admin.php?status=Pending" class="nav-link"><i class="bi bi-person-check"></i> Requests</a>
        <a href="#" class="nav-link"><i class="bi bi-gear"></i> Settings</a>
        <a href="logout" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
      </div>

      <!-- Main Content -->
      <div class="col-md-10">
        <div class="dashboard-header">
          <h4>Account Deletion Dashboard</h4>
        </div>

        <div class="container py-4">
          <!-- Stats -->
          <div class="row mb-4">
            <div class="col-md-4">
              <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                  <h6>Total Requests</h6>
                  <h4><?= $conn->query("SELECT COUNT(*) AS total FROM account_deletion_requests")->fetch_assoc()['total']; ?></h4>
                </div>
                <i class="bi bi-clipboard-data text-secondary fs-3"></i>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                  <h6>Pending</h6>
                  <h4><?= $conn->query("SELECT COUNT(*) AS total FROM account_deletion_requests WHERE status = 'Pending'")->fetch_assoc()['total']; ?></h4>
                </div>
                <i class="bi bi-clock-history text-warning fs-3"></i>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                  <h6>Deleted</h6>
                  <h4><?= $conn->query("SELECT COUNT(*) AS total FROM account_deletion_requests WHERE status = 'Successful'")->fetch_assoc()['total']; ?></h4>
                </div>
                <i class="bi bi-check-circle text-success fs-3"></i>
              </div>
            </div>
          </div>

          <!-- Filter Dropdown -->
          <form method="get" class="mb-3">
            <div class="d-flex align-items-center gap-2">
              <select name="status" class="form-select w-auto" onchange="this.form.submit()">
                <option value="">Show All</option>
                <option value="Pending" <?= (isset($_GET['status']) && $_GET['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                <option value="Successful" <?= (isset($_GET['status']) && $_GET['status'] == 'Successful') ? 'selected' : '' ?>>Successful</option>
                <option value="Cancelled" <?= (isset($_GET['status']) && $_GET['status'] == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
              </select>
            </div>
          </form>

          <!-- Toast Notification -->
          <?php if ($toast): ?>
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 1100">
              <div id="statusToast" class="toast show text-white <?= $toastType ?> border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                  <div class="toast-body">
                    <?= $toast ?>
                  </div>
                  <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <!-- Table -->
          <div class="card">
            <div class="card-header bg-white border-bottom">
              <strong>Recent Deletion Requests</strong>
            </div>
            <div class="card-body table-responsive">
              <table class="table table-striped table-hover align-middle">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result->num_rows > 0): $i = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= $i++ ?></td>
                      <td><?= htmlspecialchars($row['email']) ?></td>
                      <td><?= htmlspecialchars($row['name']) ?></td>
                      <td>
                        <?php if ($row['status'] == 'Pending'): ?>
                          <span class="badge bg-warning text-dark">Pending</span>
                        <?php elseif ($row['status'] == 'Successful'): ?>
                          <span class="badge bg-success">Deleted</span>
                        <?php elseif ($row['status'] == 'Cancelled'): ?>
                          <span class="badge bg-secondary">Cancelled</span>
                        <?php else: ?>
                          <span class="badge bg-light text-dark">Unknown</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($row['status'] == 'Pending'): ?>
                          <form method="post" action="update_status.php">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                          </form>
                        <?php else: ?>
                          <span class="text-muted">—</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endwhile; else: ?>
                    <tr>
                      <td colspan="5" class="text-center">No records found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const toastEl = document.getElementById('statusToast');
    if (toastEl) {
      const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
      toast.show();
    }
  </script>
</body>
</html>