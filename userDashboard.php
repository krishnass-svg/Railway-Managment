<?php
$customCss = "css/dashboard.css";
$pageTitle = "User Dashboard | Railwaymanagment";
include 'header.php';
include 'dbconfig.php';

if (!isset($_SESSION['roles']) || !in_array('USER', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

// Handle Train Status Checker form submission
$trainStatusMessage = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_status'])) {
    $query = trim($_POST['train_query']);

    $stmt = $conn->prepare("SELECT name, number FROM orrs_train WHERE number = :query OR name LIKE :like_query LIMIT 1");
    $stmt->execute([
        ':query' => $query,
        ':like_query' => "%$query%"
    ]);
    $train = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($train) {
        $statuses = ['On Time', 'Delayed by 10 mins', 'Delayed by 30 mins', 'Cancelled', 'On Time'];
        $status = $statuses[array_rand($statuses)];

        $trainStatusMessage = "Train <strong>" . htmlspecialchars($train['name']) . " (" . htmlspecialchars($train['number']) . ")</strong> is currently: <strong>$status</strong>.";
    } else {
        $trainStatusMessage = "No train found matching <strong>" . htmlspecialchars($query) . "</strong>.";
    }
}
?>

<div class="container dashboard-container">
    <h2 class="text-center dashboard-title">Book Your Tickets Now</h2>

    <!-- Action Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">🚆 Train Schedule</h5>
                    <p class="card-text">View up-to-date train schedules and timings.</p>
                    <a href="view_train_schedule.php" class="btn btn-primary mt-auto">View Schedule</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">🎟️ Book Tickets</h5>
                    <p class="card-text">Reserve your train tickets in a few clicks.</p>
                    <a href="available.php" class="btn btn-success mt-auto">Book Now</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">❌ Cancel Booking</h5>
                    <p class="card-text">Changed your plans? Cancel your reservation easily.</p>
                    <a href="cancel_booking.php" class="btn btn-danger mt-auto">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Train Status Checker -->
    <!--<section class="my-5 px-3">
        <h4 class="text-center text-light mb-4">🚦 Quick Train Status Checker</h4>
        <form method="POST" class="row justify-content-center g-2">
            <div class="col-md-6">
                <input type="text" name="train_query" class="form-control" placeholder="Enter Train Number or Name" required>
            </div>
            <div class="col-auto">
                <button type="submit" name="check_status" class="btn btn-primary">Check Status</button>
            </div>
        </form>

        <?php if ($trainStatusMessage): ?>
            <div class="alert alert-info mt-3 mx-auto" style="max-width: 600px;">
                <?php echo $trainStatusMessage; ?>
            </div>
        <?php endif; ?>
    </section>-->
</div>

<!-- Most Booked Train Ticket Routes Section -->
<section class="mt-6 px-3">
    <h4 class="fw-bold text-center mb-3 text-light">🔥 Most Booked Train Ticket Routes</h4>

    <?php
    $sql = "SELECT 
                t.current AS from_station,
                t.destination AS to_station,
                COUNT(*) AS bookings
            FROM orrs_bookings b
            JOIN orrs_train t ON b.train_id = t.id
            GROUP BY t.current, t.destination
            ORDER BY bookings DESC
            LIMIT 8";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $routes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if (!empty($routes)): ?>
        <div class="mx-auto" style="max-width: 800px;">
            <table class="table align-middle table-borderless">
                <thead>
                    <tr>
                        <th class="fw-bold fs-5">Route</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($routes as $route): ?>
                        <tr class="border-bottom">
                            <td class="py-3 ps-2 fs-6">
                                <?php echo htmlspecialchars($route['from_station']); ?> 
                                to 
                                <?php echo htmlspecialchars($route['to_station']); ?>
                            </td>
                            <td class="text-end pe-2">
                                <a href="available.php?from=<?php echo urlencode($route['from_station']); ?>&to=<?php echo urlencode($route['to_station']); ?>"
                                   class="btn btn-sm fw-semibold px-4 py-2"
                                   style="background-color: #ffe5e5; color: #000; border-radius: 25px;">
                                   Search Train
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-center text-muted">No popular routes found.</p>
    <?php endif; ?>
</section>

<!-- Help Section -->
<section class="mt-5 py-5 bg-light rounded shadow-sm">
    <div class="container text-center">
        <h4 class="mb-3 fw-bold text-primary">Need Help?</h4>
        <p class="text-muted mb-4">Find answers to your questions or get in touch with our support team.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="faq.php" class="btn btn-outline-primary">📚 FAQs</a>
            <a href="contactUs.php" class="btn btn-outline-secondary">📞 Contact Support</a>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
