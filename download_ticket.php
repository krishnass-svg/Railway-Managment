<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include 'dbconfig.php';

if (!isset($_GET['id'])) {
    die("Booking ID not provided.");
}
$bookingId = $_GET['id'];

// Start capturing HTML output
ob_start();

include 'header.php'; // If this contains session or styles
?>

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 14px;
    }
    .ticket-box {
        border: 1px solid #333;
        padding: 20px;
        margin: 20px;
        width: 100%;
    }
    h2 {
        text-align: center;
        text-decoration: underline;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    th, td {
        padding: 8px 10px;
        border: 1px solid #666;
    }
</style>

<?php
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Booking & train details
    $stmt = $pdo->prepare("
        SELECT b.id, b.pnr, b.user_id, b.train_id, b.travel_date, b.total_fare, b.booking_time,
               t.train_name, t.number, t.source, t.destination
        FROM orrs_bookings b
        JOIN orrs_train t ON b.train_id = t.id
        WHERE b.id = ?
    ");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        die("Booking not found.");
    }

    // Passengers
    $stmt = $pdo->prepare("SELECT name, age, gender, seat_number, coach, fare FROM orrs_passengers WHERE booking_id = ?");
    $stmt->execute([$bookingId]);
    $passengers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Render Ticket
    ?>

    <div class="ticket-box">
        <h2>Journey Now - Train Ticket</h2>

        <p><strong>PNR:</strong> <?= $booking['pnr'] ?><br>
           <strong>Train:</strong> <?= $booking['train_name'] ?> (<?= $booking['number'] ?>)<br>
           <strong>From:</strong> <?= $booking['source'] ?> <strong>To:</strong> <?= $booking['destination'] ?><br>
           <strong>Travel Date:</strong> <?= $booking['travel_date'] ?><br>
           <strong>Booking Time:</strong> <?= $booking['booking_time'] ?><br>
           <strong>Total Fare:</strong> ₹<?= $booking['total_fare'] ?>
        </p>

        <h4>Passenger Details</h4>
        <table>
            <thead>
                <tr>
                    <th>Name</th><th>Age</th><th>Gender</th><th>Coach</th><th>Seat</th><th>Fare</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($passengers as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= $p['age'] ?></td>
                        <td><?= $p['gender'] ?></td>
                        <td><?= $p['coach'] ?></td>
                        <td><?= $p['seat_number'] ?></td>
                        <td>₹<?= $p['fare'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

$html = ob_get_clean();

// Setup Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Force download
$dompdf->stream("ticket_{$bookingId}.pdf", ["Attachment" => true]);
?>
