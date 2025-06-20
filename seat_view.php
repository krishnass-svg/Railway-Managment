<?php
$pageTitle = "Seat View | Railwaymanagment";
include 'header.php';
include 'dbconfig.php';

if (!isset($_GET['id'])) {
    die("Train ID not provided.");
}

$trainId = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Example: Assume total 60 seats, 20 per coach (A, B, C)
    $totalSeats = 60;
    $seatsPerCoach = 20;

    // Fetch booked seats
    $stmt = $pdo->prepare("SELECT assigned_seats FROM orrs_bookings WHERE train_id = ?");
    $stmt->execute([$trainId]);
    $bookedSeats = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $seats = explode(",", $row['assigned_seats']);
        foreach ($seats as $s) {
            $bookedSeats[] = trim($s);
        }
    }

    // Generate all seats
    $allSeats = [];
    foreach (['A', 'B', 'C'] as $coach) {
        for ($i = 1; $i <= $seatsPerCoach; $i++) {
            $allSeats[] = $coach . $i;
        }
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<style>
    .seat-box {
        width: 60px;
        height: 40px;
        margin: 5px;
        text-align: center;
        line-height: 40px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        display: inline-block;
    }
    .booked { background-color: #dc3545; }  /* red */
    .available { background-color: #28a745; }  /* green */
</style>

<div class="container my-5">
    <h2 class="text-center mb-4">Seat View for Train ID <?= htmlspecialchars($trainId) ?></h2>

    <div class="d-flex flex-wrap justify-content-center">
        <?php foreach ($allSeats as $seat): ?>
            <div class="seat-box <?= in_array($seat, $bookedSeats) ? 'booked' : 'available' ?>">
                <?= $seat ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
