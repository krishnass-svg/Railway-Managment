<?php
include 'header.php';
include 'dbconfig.php';

if (!isset($_GET['id'])) {
    die("Booking ID not provided.");
}
$bookingId = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch booking + train details
    $stmt = $pdo->prepare("
        SELECT b.*, t.name AS train_name, t.number AS train_number,
               t.current AS from_station, t.destination AS to_station, t.time AS departure_time
        FROM orrs_bookings b
        JOIN orrs_train t ON b.train_id = t.id
        WHERE b.id = ?
    ");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch();

    if (!$booking) {
        die("Booking not found.");
    }

    // Fetch passengers
    $stmt2 = $pdo->prepare("SELECT * FROM orrs_passengers WHERE booking_id = ?");
    $stmt2->execute([$bookingId]);
    $passengers = $stmt2->fetchAll();

} catch (PDOException $e) {
    die("Error fetching ticket: " . $e->getMessage());
}
?>

<style>
body {
    background: #f0f2f5;
}
#ticket-box {
    background: #fff;
    margin: auto;
    max-width: 800px;
    padding: 30px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
@media print {
    .no-print {
        display: none;
    }
}
.footer-text {
    font-size: 13px;
    color: #666;
}

</style>

<div id="ticket-box" class="container my-5">
    <div class="text-center mb-4">
        <img src="img/train.png" alt="Journey Now Logo" style="height: 60px; margin-bottom: 10px;">
        <h4 class="text-muted">Journey Now</h4>
        <h2 class="fw-bold">Ticket Confirmation</h2>
    </div>

    <div class="mb-4">
        <h5>Train: <?= htmlspecialchars($booking['train_name']) ?> (<?= htmlspecialchars($booking['train_number']) ?>)</h5>
        <p><strong>From:</strong> <?= htmlspecialchars($booking['from_station']) ?></p>
        <p><strong>To:</strong> <?= htmlspecialchars($booking['to_station']) ?></p>
        <p><strong>Departure Time:</strong> <?= htmlspecialchars($booking['departure_time']) ?></p>
        <p><strong>Journey Date:</strong> <?= htmlspecialchars($booking['travel_date']) ?></p>
        <p><strong>Coach:</strong> <?= htmlspecialchars($booking['coach'] ?? 'N/A') ?></p>
        <p><strong>Seats:</strong> <?= htmlspecialchars($booking['seat_numbers']) ?></p>
        <p><strong>Total Fare:</strong> ₹<?= number_format($booking['total_fare'], 2) ?></p>
    </div>

    <div>
        <h5>Passenger Details</h5>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Fare</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($passengers as $index => $p): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['age']) ?></td>
                        <td><?= htmlspecialchars($p['email']) ?></td>
                        <td><?= htmlspecialchars($p['phone']) ?></td>
                        <td>₹<?= number_format($p['fare'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center no-print mt-4">
        <p>© 2025 Journey Now | All Rights Reserved</p>
    </div>

</div>

<!-- html2canvas for automatic image download -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<?php if (isset($_GET['auto']) && $_GET['auto'] == 1): ?>
<script>
window.onload = function () {
    setTimeout(() => {
        const ticket = document.getElementById("ticket-box");
        html2canvas(ticket, {
            scale: 2,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            const link = document.createElement("a");
            link.download = "Train_Ticket_<?= $bookingId ?>.png";
            link.href = canvas.toDataURL("image/png");
            link.click();
        });
    }, 1000);
};

</script>
<?php endif; ?>

<?php include 'footer.php'; ?>

