<?php
$pageTitle = "BookTrain | Railwaymanagment";
include 'header.php';
include 'dbconfig.php';

// Connect to DB
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Initialize search parameters
$from = "";
$to = "";

// Build dynamic query
$query = "SELECT * FROM orrs_train WHERE 1=1";
$params = [];

// Check if from station is set
if (isset($_GET['from']) && !empty(trim($_GET['from']))) {
    $from = trim($_GET['from']);
    $query .= " AND current LIKE ?";
    array_push($params, "%$from%");
}

// Check if to station is set
if (isset($_GET['to']) && !empty(trim($_GET['to']))) {
    $to = trim($_GET['to']);
    $query .= " AND destination LIKE ?";
    array_push($params, "%$to%");
}

$query .= " ORDER BY name";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
?>

<div class="container my-5">

    <div class="mb-3 text-start">
        <a href="userDashboard.php" class="btn btn-secondary">&larr; Go Back</a>
    </div>

    <h2 class="mb-4 text-center">Available Trains</h2>

    <!-- Search Form -->
    <form method="GET" class="row mb-4 g-2">
        <div class="col-md-5">
            <input type="text" name="from" class="form-control" placeholder="From (Current Station)" value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-md-5">
            <input type="text" name="to" class="form-control" placeholder="To (Destination Station)" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Train Number</th>
                <th>Train Name</th>
                <th>Route</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Dep. Time</th>
                <th>Fare</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($stmt->rowCount() > 0): ?>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['number']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['route']) ?></td>
                        <td><?= htmlspecialchars($row['current']) ?></td>
                        <td><?= htmlspecialchars($row['destination']) ?></td>
                        <td><?= htmlspecialchars($row['time']) ?></td>
                        <td>₹<?= number_format($row['fare']) ?></td>
                        <td>
                            <a href="book_train.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Book</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center">No trains found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
