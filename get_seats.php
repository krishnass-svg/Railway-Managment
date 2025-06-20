<?php
include 'dbconfig.php';

$train_id = $_GET['train_id'] ?? null;
$coach = $_GET['coach'] ?? null;
$travel_date = $_GET['date'] ?? null;

if (!$train_id || !$coach || !$travel_date) {
    echo json_encode(['error' => 'Missing data']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $stmt = $pdo->prepare("SELECT seat_number FROM orrs_seats WHERE train_id = ? AND coach = ? AND journey_date = ?");
    $stmt->execute([$train_id, $coach, $travel_date]);
    $booked = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(['booked' => $booked]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'DB error']);
}
?>
