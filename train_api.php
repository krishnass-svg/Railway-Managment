<?php
header('Content-Type: application/json');

$pnr = $_GET['pnr'] ?? '';
$train_id = $_GET['train_id'] ?? '';

// Return fake PNR status
if ($pnr) {
    $statusOptions = ['Confirmed', 'Waiting List', 'RAC', 'Cancelled'];
    $randomStatus = $statusOptions[array_rand($statusOptions)];

    echo json_encode([
        'pnr' => $pnr,
        'status' => $randomStatus,
        'coach' => 'S' . rand(1, 10),
        'seat_number' => rand(1, 72),
        'journey_date' => '2025-06-15',
        'from' => 'Dharwad',
        'to' => 'Bangalore',
    ]);
}
// Return fake real-time seat availability
elseif ($train_id) {
    echo json_encode([
        'train_id' => $train_id,
        'seats_available' => rand(0, 20),
        'last_updated' => date('Y-m-d H:i:s'),
    ]);
}
// If no valid request
else {
    echo json_encode(['error' => 'Invalid request. Provide pnr or train_id.']);
}
