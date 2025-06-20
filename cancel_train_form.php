<?php
$pageTitle = "Cancel Train | Admin Panel";
include 'header.php';
include 'dbconfig.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get train list
    $stmt = $pdo->query("SELECT id, name, number FROM orrs_train");
    $trains = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<div class="container my-5">
    <h2 class="mb-4 text-center">Cancel a Train</h2>

    <form action="cancel_train.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Select Train</label>
            <select name="train_id" class="form-select" required>
                <option value="">-- Choose Train --</option>
                <?php foreach ($trains as $train): ?>
                    <option value="<?= $train['id'] ?>">
                        <?= htmlspecialchars($train['name']) ?> (<?= $train['number'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Cancellation Date</label>
            <input type="date" name="cancel_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Reason for Cancellation</label>
            <textarea name="reason" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-danger">Cancel Train and Notify Users</button>
    </form>
</div>

<?php include 'footer.php'; ?>
