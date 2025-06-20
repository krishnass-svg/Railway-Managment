<?php
include 'header.php';
include 'dbconfig.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo "<div class='alert alert-danger'>You must be logged in to view or cancel bookings.</div>";
    exit;
}

$loggedInEmail = $_SESSION['email'];

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$error = '';
$success = '';
$bookings = [];

try {
    // ✅ Fix: Use orrs_bookings instead of bookings
    if (isset($_GET['cancel_id'])) {
        $cancelId = intval($_GET['cancel_id']);

        // Verify the booking belongs to this user
        $stmt = $pdo->prepare("SELECT passenger_email FROM orrs_bookings WHERE id = ?");
        $stmt->execute([$cancelId]);
        $booking = $stmt->fetch();

        if (!$booking) {
            $error = "Booking not found.";
        } elseif ($booking['passenger_email'] !== $loggedInEmail) {
            $error = "You are not authorized to cancel this booking.";
        } else {
            $delStmt = $pdo->prepare("DELETE FROM orrs_bookings WHERE id = ?");
            if ($delStmt->execute([$cancelId])) {
                $success = "Booking cancelled successfully.";
            } else {
                $error = "Failed to cancel booking.";
            }
        }
    }

    // ✅ Fix: Fetch bookings from correct table
    $stmt = $pdo->prepare("
        SELECT b.id, b.passenger_name, b.seats, b.total_fare, b.train_id,
               t.number AS train_number, t.name AS train_name,
               t.current, t.destination, t.time
        FROM orrs_bookings b
        JOIN orrs_train t ON b.train_id = t.id
        WHERE b.passenger_email = ?
    ");
    $stmt->execute([$loggedInEmail]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<title>Cancel Booking</title>
<div class="container mt-5">
    <h2>My Bookings</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($bookings): ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Passenger Name</th>
                    <th>Train</th>
                    <th>Route</th>
                    <th>Departure Time</th>
                    <th>Seats</th>
                    <th>Total Fare</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['id']) ?></td>
                        <td><?= htmlspecialchars($b['passenger_name']) ?></td>
                        <td><?= htmlspecialchars($b['train_number'] . ' - ' . $b['train_name']) ?></td>
                        <td><?= htmlspecialchars($b['current'] . ' to ' . $b['destination']) ?></td>
                        <td><?= htmlspecialchars($b['time']) ?></td>
                        <td><?= htmlspecialchars($b['seats']) ?></td>
                        <td>₹<?= number_format($b['total_fare'], 2) ?></td>
                        <td>
                            <a href="?cancel_id=<?= $b['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to cancel booking #<?= $b['id'] ?>?');">
                                Cancel
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
