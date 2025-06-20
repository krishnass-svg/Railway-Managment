<?php
$pageTitle = "Route Analytics | Railway Management";
include 'header.php';
include 'dbconfig.php';

// Check admin access
if (!isset($_SESSION['roles']) || !in_array('ADMIN', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

try {
    // Fetch route booking statistics: number of bookings and total revenue per route
    $sql = "
        SELECT 
            t.current AS from_station,
            t.destination AS to_station,
            COUNT(b.id) AS total_bookings,
            SUM(b.total_fare) AS total_revenue
        FROM orrs_bookings b
        INNER JOIN orrs_train t ON b.train_id = t.id
        GROUP BY t.current, t.destination
        ORDER BY total_bookings DESC
        LIMIT 10
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $routes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Failed to fetch route analytics: " . $e->getMessage();
}
?>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Route Analytics Dashboard</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($routes)): ?>
        <table class="table table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>From Station</th>
                    <th>To Station</th>
                    <th>Total Bookings</th>
                    <th>Total Revenue (₹)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($routes as $route): ?>
                    <tr>
                        <td><?= htmlspecialchars($route['from_station']); ?></td>
                        <td><?= htmlspecialchars($route['to_station']); ?></td>
                        <td><?= number_format($route['total_bookings']); ?></td>
                        <td><?= number_format($route['total_revenue'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center">No route analytics data available.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
