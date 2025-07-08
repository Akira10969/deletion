<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
  $email = $_POST['email'];

  $stmt = $conn->prepare("UPDATE account_deletion_requests SET status = 'Cancelled' WHERE email = ?");
  $stmt->bind_param("s", $email);

  if ($stmt->execute()) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
  }

  $stmt->close();
  $conn->close();
}
?>