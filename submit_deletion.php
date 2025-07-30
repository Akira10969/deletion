<?php
header('Content-Type: application/json');
require_once 'includes/db.php';

$secretKey = "6LfnFpQrAAAAAI4BP3fp-JHHPWJS6TFjwQz8mI36";
$recaptcha = $_POST['g-recaptcha-response'] ?? '';

if (empty($recaptcha)) {
    echo json_encode(['success' => false, 'error' => 'reCAPTCHA is missing.']);
    exit;
}

$verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptcha");
$responseData = json_decode($verifyResponse, true);

if (!$responseData['success']) {
    echo json_encode(['success' => false, 'error' => 'reCAPTCHA verification failed.']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['confirmed_email'] ?? '');
$birthday_input = trim($_POST['birthday'] ?? '');

$birthday_date = DateTime::createFromFormat('m/d/Y', $birthday_input);
if (!$birthday_date) {
    echo json_encode(['success' => false, 'error' => 'Invalid birthday format. Use MM/DD/YYYY.']);
    exit;
}

$birthday = $birthday_date->format('Y-m-d'); // Convert to MySQL-compatible format

$prc = trim($_POST['prc_number'] ?? '');
$reason = trim($_POST['reason'] ?? '');

if (!$name || !$email || !$birthday || !$prc || !$reason) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    exit;
}

// Format check for birthday
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthday)) {
    echo json_encode(['success' => false, 'error' => 'Invalid birthday format. Use YYYY-MM-DD.']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM account_deletion_requests WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'You already submitted a request.']);
    exit;
}
$stmt->close();

// Insert
$stmt = $conn->prepare("INSERT INTO account_deletion_requests (name, email, birthday, prc_number, reason) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $birthday, $prc, $reason);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Request submitted.']);
} else {
    echo json_encode(['success' => false, 'error' => 'Database insert failed: ' . $stmt->error]);
}
?>