<?php
$pageTitle = "Cancelled Trains | Journey Now";
include 'header.php';
include 'dbconfig.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get list of cancelled trains (future only)
    $stmt = $pdo->prepare("
        SELECT c.cancel_date, c.reason, t.name, t.number, t.route
        FROM orrs_cancelled_trains c
        JOIN orrs_train t ON c.train_id = t.id
        WHERE c.cancel_date >= CURDATE()
        ORDER BY c.cancel_date ASC
    ");
    $stmt->execute();
    $cancelledTrains = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<div class="container my-5">
    <h2 class="mb-4 text-center">Upcoming Cancelled Trains</h2>

    <?php if (empty($cancelledTrains)): ?>
        <div class="alert alert-success text-center">No upcoming train cancellations.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-danger">
                <tr>
                    <th>Date</th>
                    <th>Train Name</th>
                    <th>Train Number</th>
                    <th>Route</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cancelledTrains as $train): ?>
                    <tr>
                        <td><?= htmlspecialchars($train['cancel_date']) ?></td>
                        <td><?= htmlspecialchars($train['name']) ?></td>
                        <td><?= htmlspecialchars($train['number']) ?></td>
                        <td><?= htmlspecialchars($train['route']) ?></td>
                        <td><?= htmlspecialchars($train['reason']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
