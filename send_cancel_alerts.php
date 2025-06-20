<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendCancellationEmails($trainId, $cancelDate, $reason) {
    include 'dbconfig.php';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get train details
        $trainStmt = $pdo->prepare("SELECT name, number FROM orrs_train WHERE id = ?");
        $trainStmt->execute([$trainId]);
        $train = $trainStmt->fetch();

        // Get all bookings for that train and date
        $bookingStmt = $pdo->prepare("
            SELECT passenger_name, passenger_email 
            FROM orrs_bookings 
            WHERE train_id = ? AND travel_date = ?
        ");
        $bookingStmt->execute([$trainId, $cancelDate]);

        $subject = "Train Cancellation Notice: {$train['name']} ({$train['number']})";
        $body = "Dear Passenger,<br><br>We regret to inform you that Train <strong>{$train['name']} ({$train['number']})</strong> scheduled on <strong>{$cancelDate}</strong> has been cancelled.<br><br>Reason: <em>{$reason}</em><br><br>Please contact support for refunds or rescheduling.<br><br>Regards,<br>Railway Reservation Team";

        while ($row = $bookingStmt->fetch(PDO::FETCH_ASSOC)) {
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'kss616996@gmail.com'; // your Gmail
                $mail->Password   = 'NORMAL@432::"HELLO"@#()00';     // app password from Gmail
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('your-email@gmail.com', 'Railway Management');
                $mail->addAddress($row['passenger_email'], $row['passenger_name']);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $body;

                $mail->send();
            } catch (Exception $e) {
                error_log("Email to {$row['passenger_email']} failed: " . $mail->ErrorInfo);
            }
        }
    } catch (PDOException $e) {
        error_log("DB Error: " . $e->getMessage());
    }
}
?>
