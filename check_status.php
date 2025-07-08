<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];

  $stmt = $conn->prepare("SELECT status, created_at FROM account_deletion_requests WHERE email = ? ORDER BY id DESC LIMIT 1");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $status = $row['status'];

    if ($status === 'Cancelled') {
      // Let them submit a new request
      echo json_encode(['status' => 'new']);
    } else {
      echo json_encode([
        'status' => 'existing',
        'email' => $email,
        'account_status' => $status,
        'created_at' => $row['created_at']
      ]);
    }
  } else {
    // No record found — allow new request
    echo json_encode(['status' => 'new']);
  }

  $stmt->close();
  $conn->close();
}
?>
