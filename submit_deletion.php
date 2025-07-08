<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "deletion";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$name = $_POST['name'];
$email = $_POST['confirmed_email'];
$birthday = $_POST['birthday'];
$prc_number = $_POST['prc_number'];
$reason = $_POST['reason'];

// Prevent duplicate entry
$stmt = $conn->prepare("SELECT id FROM account_deletion_requests WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
  $stmt = $conn->prepare("INSERT INTO account_deletion_requests (name, email, birthday, prc_number, reason) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $name, $email, $birthday, $prc_number, $reason);
  $stmt->execute();
  echo "<script>alert('Request submitted successfully!'); window.location.href='index.html';</script>";
} else {
  echo "<script>alert('You have already submitted a deletion request.'); window.location.href='index.html';</script>";
}

$conn->close();
?>
