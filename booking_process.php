<?php
include 'dbconfig.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Invalid request method.");

$trainId    = $_POST['train_id'] ?? '';
$travelDate = $_POST['travel_date'] ?? '';
$coach      = $_POST['coach'] ?? '';
$seatStr    = $_POST['selected_seats'] ?? '';

$seats = array_values(array_filter(
    array_map('trim', explode(',', $seatStr)),
    fn($s) => $s !== ''
));

$names  = $_POST['passenger_name']  ?? [];
$ages   = $_POST['passenger_age']   ?? [];
$emails = $_POST['passenger_email'] ?? [];
$phones = $_POST['passenger_phone'] ?? [];

$passengerCount = count($names);
$seatCount      = count($seats);

if ($passengerCount !== $seatCount) {
    die("Number of passengers ($passengerCount) must match number of seats selected ($seatCount).");
}

try {
    $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($seatCount > 0) {
        $placeholders = implode(',', array_fill(0, $seatCount, '?'));
        $seatNumsOnly = array_map(fn($s) => substr($s, 1), $seats);

        $stmt = $pdo->prepare("
            SELECT seat_number
            FROM orrs_seats
            WHERE train_id = ? AND coach = ? AND journey_date = ?
              AND seat_number IN ($placeholders)
        ");
        $stmt->execute(array_merge([$trainId, $coach, $travelDate], $seatNumsOnly));
        $bookedSeats = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        if (!empty($bookedSeats)) {
            die("These seats are already booked: " . implode(', ', $bookedSeats));
        }
    }

    $stmt = $pdo->prepare("SELECT fare FROM orrs_train WHERE id = ?");
    $stmt->execute([$trainId]);
    $baseFare = (float) $stmt->fetchColumn();

    // Initialize correctly
    $fareBreakdown = [];
    $totalFare = 0.0;

    foreach ($ages as $i => $age) {
        $fare = 0.0;
        if ($age >= 5) {
            if ($age <= 12) $fare = $baseFare * 0.5;
            elseif ($age >= 60) $fare = $baseFare * 0.6;
            else $fare = $baseFare;
        }
        $fare = round($fare, 2);
        $fareBreakdown[] = $fare;
        $totalFare += $fare;
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO orrs_bookings
          (train_id, passenger_name, passenger_email, seats, total_fare, seat_numbers, travel_date, coach)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $trainId,
        $names[0],    // first passenger used as booking reference
        $emails[0],
        $seatCount,
        $totalFare,
        implode(',', $seats),
        $travelDate,
        $coach
    ]);
    $bookingId = $pdo->lastInsertId();

    $insSeat = $pdo->prepare("
        INSERT INTO orrs_seats (train_id, coach, seat_number, booking_id, journey_date)
        VALUES (?, ?, ?, ?, ?)
    ");
    foreach ($seatNumsOnly as $sn) {
        $insSeat->execute([$trainId, $coach, $sn, $bookingId, $travelDate]);
    }

    $insPsg = $pdo->prepare("
        INSERT INTO orrs_passengers (booking_id, name, age, email, phone, fare)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    foreach ($names as $i => $name) {
        $insPsg->execute([
            $bookingId,
            $name,
            $ages[$i],
            $emails[$i],
            $phones[$i],
            $fareBreakdown[$i]
        ]);
    }

    $pdo->commit();
    header("Location: payment.php?id=" . $bookingId);
    exit;

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    die("Booking failed: " . $e->getMessage());
}
