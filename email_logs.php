<?php
$conn = new mysqli('localhost', 'root', '', 'deletion');
$result = $conn->query("SELECT * FROM email_logs ORDER BY sent_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Email Logs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <h3>Email Logs</h3>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Recipient</th>
        <th>Subject</th>
        <th>Status</th>
        <th>Error</th>
        <th>Sent At</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['recipient_email']) ?></td>
          <td><?= htmlspecialchars($row['subject']) ?></td>
          <td><?= $row['status'] == 'Sent' ? '<span class="badge bg-success">Sent</span>' : '<span class="badge bg-danger">Failed</span>' ?></td>
          <td><?= htmlspecialchars($row['error']) ?></td>
          <td><?= $row['sent_at'] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>