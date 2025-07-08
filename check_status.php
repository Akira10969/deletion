<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "deletion";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$email = $_POST['email'] ?? '';

if ($email === '') {
  echo json_encode(['status' => 'invalid']);
  exit;
}

$stmt = $conn->prepare("SELECT * FROM account_deletion_requests WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
    echo json_encode([
    'status' => 'existing',
    'email' => $row['email'],
    'account_status' => $row['status'], // should be 'Pending' or 'Successful'
    'created_at' => $row['created_at']
    ]);
} else {
  echo json_encode(['status' => 'new']);
}

$conn->close();
?>
