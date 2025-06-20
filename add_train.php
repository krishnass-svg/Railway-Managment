<?php
$pageTitle = "Add Train | Railwaymanagment";
include 'header.php';
include 'dbconfig.php';

$addTrainMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number = trim($_POST['number']);
    $name = trim($_POST['name']);
    $route = trim($_POST['route']);
    $current = trim($_POST['current']);
    $destination = trim($_POST['destination']);
    $time = $_POST['time'];
    $fare = $_POST['fare'];

    if (!$number || !$name || !$route || !$current || !$destination || !$time || !$fare) {
        $addTrainMsg = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO orrs_train (number, name, route, current, destination, time, fare) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$number, $name, $route, $current, $destination, $time, $fare]);
        $addTrainMsg = "Train added successfully!";
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Add New Train</h2>
    <?php if ($addTrainMsg): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($addTrainMsg); ?></div>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="number" class="form-control mb-2" placeholder="Train Number" required>
        <input type="text" name="name" class="form-control mb-2" placeholder="Train Name" required>
        <input type="text" name="route" class="form-control mb-2" placeholder="Route" required>
        <input type="text" name="current" class="form-control mb-2" placeholder="Current Station" required>
        <input type="text" name="destination" class="form-control mb-2" placeholder="Destination Station" required>
        <input type="time" name="time" class="form-control mb-2" required>
        <input type="number" name="fare" class="form-control mb-2" placeholder="Fare (₹)" min="0" step="0.01" required>
        <button type="submit" class="btn btn-success">Add Train</button>
    </form>
</div>

<?php include 'footer.php'; ?>
