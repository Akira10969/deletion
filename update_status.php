<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// DB connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'deletion';
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle request
if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    $get = $conn->query("SELECT * FROM account_deletion_requests WHERE id = $id");
    if ($get->num_rows > 0) {
        $user = $get->fetch_assoc();

        // Update status
        $conn->query("UPDATE account_deletion_requests SET status = 'Successful' WHERE id = $id");

        // Send email
        $mail = new PHPMailer(true);
        $status = 'Sent';
        $errorMsg = '';

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'info@psmeinc.org.ph'; // SMTP username
            $mail->Password = 'jxmt bnxg grxg hddd';   // Use an App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('icto@psmeinc.org.ph', 'PSME ICTO');
            $mail->addAddress($user['email'], $user['name']);
            $mail->isHTML(true);
            $mail->Subject = 'Account Deletion Request Approved';
            $mail->Body = "
                <h3>Hi " . htmlspecialchars($user['name']) . ",</h3>
                <p>Your request to delete your account has been <strong>approved</strong>.</p>
                <p>If you have any questions, feel free to contact us.</p>
                <p><em>- PSME ICTO</em></p>
            ";

            $mail->send();
        } catch (Exception $e) {
            $status = 'Failed';
            $errorMsg = $mail->ErrorInfo;
        }

        // Log the email
        $stmt = $conn->prepare("INSERT INTO email_logs (recipient_email, subject, message, status, error) VALUES (?, ?, ?, ?, ?)");
        $subject = $mail->Subject;
        $body = $mail->Body;
        $stmt->bind_param("sssss", $user['email'], $subject, $body, $status, $errorMsg);
        $stmt->execute();
    }
}

header("Location: admin.php?success=1");
exit;