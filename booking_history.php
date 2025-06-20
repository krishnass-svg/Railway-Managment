<?php
include 'dbconfig.php';
include 'header.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Get user email from DB
$stmtEmail = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmtEmail->execute([$userId]);
$user = $stmtEmail->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<div class='container mt-5 alert alert-danger'>User not found.</div>";
    include 'footer.php';
    exit();
}

$userEmail = $user['email'];

// Get booking history with payment status
$sql = "
    SELECT b.id, b.passenger_name, b.seats, b.total_fare, b.booked_at, 
           b.payment_status, 
           t.number AS train_number, t.name AS train_name, t.current, t.destination, t.time
    FROM orrs_bookings b
    JOIN orrs_train t ON b.train_id = t.id
    WHERE b.passenger_email = ?
    ORDER BY b.booked_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->execute([$userEmail]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2 class="mb-4">Your Booking History</h2>

    <?php if (count($bookings) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Booking ID</th>
                        <th>Train No.</th>
                        <th>Train Name</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Departure</th>
                        <th>Passenger</th>
                        <th>Seats</th>
                        <th>Total Fare</th>
                        <th>Booked On</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['train_number']) ?></td>
                            <td><?= htmlspecialchars($row['train_name']) ?></td>
                            <td><?= htmlspecialchars($row['current']) ?></td>
                            <td><?= htmlspecialchars($row['destination']) ?></td>
                            <td><?= date("h:i A", strtotime($row['time'])) ?></td>
                            <td><?= htmlspecialchars($row['passenger_name']) ?></td>
                            <td><?= $row['seats'] ?></td>
                            <td>₹<?= number_format($row['total_fare'], 2) ?></td>
                            <td><?= date("d M Y, h:i A", strtotime($row['booked_at'])) ?></td>
                            <td>
                                <?php if ($row['payment_status'] === 'PAID'): ?>
                                    <span class="badge bg-success">PAID</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">UNPAID</span>
                                <?php endif; ?>
                            </td>
                            <td>
    <?php if ($row['payment_status'] === 'PAID'): ?>
        <a href="ticket.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">View</a>
    <?php else: ?>
        --
    <?php endif; ?>
</td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">You have not booked any tickets yet.</div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
