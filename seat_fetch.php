<?php
include 'dbconfig.php';

if (!isset($_GET['train_id']) || !isset($_GET['coach']) || !isset($_GET['journey_date'])) {
    echo json_encode([]);
    exit;
}

$trainId = $_GET['train_id'];
$coach = $_GET['coach'];
$journey_date = $_GET['journey_date'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $stmt = $pdo->prepare("SELECT seat_number FROM orrs_seats WHERE train_id = ? AND coach = ? AND journey_date = ?");
    $stmt->execute([$trainId, $coach, $journey_date]);
    $booked = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($booked);
} catch (Exception $e) {
    echo json_encode([]);
}
