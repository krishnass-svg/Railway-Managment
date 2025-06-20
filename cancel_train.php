<?php
// cancel_train.php
include 'dbconfig.php';
include 'send_cancel_alerts.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trainId = $_POST['train_id'];
    $cancelDate = $_POST['cancel_date'];
    $reason = $_POST['reason'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insert into cancellation table
        $stmt = $pdo->prepare("INSERT INTO orrs_cancelled_trains (train_id, cancel_date, reason) VALUES (?, ?, ?)");
        $stmt->execute([$trainId, $cancelDate, $reason]);

        // Send email notifications
        sendCancellationEmails($trainId, $cancelDate, $reason);

        echo "<script>alert('Train cancelled and users notified!'); window.location.href='admin_dashboard.php';</script>";
    } catch (PDOException $e) {
        die("Error cancelling train: " . $e->getMessage());
    }
} else {
    die("Invalid request");
}
?>
