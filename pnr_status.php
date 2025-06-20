<?php
include 'header.php';
include 'dbconfig.php';

$pnrData = null;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pnr = trim($_POST['pnr']);
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("
            SELECT b.id, b.passenger_name, b.passenger_email, b.seats, b.total_fare, b.payment_status, b.booked_at,
                   t.name AS train_name, t.number AS train_number, t.current, t.destination, t.time
            FROM orrs_bookings b
            JOIN orrs_train t ON b.train_id = t.id
            WHERE b.id = ?
        ");
        $stmt->execute([$pnr]);
        $pnrData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pnrData) $error = "PNR not found.";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.card-status {
  max-width: 600px;
  margin: auto;
  border-radius: 0.5rem;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.card-status .card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>

<div class="container my-5">
  <h2 class="mb-4 text-center">Check PNR Status</h2>

  <form method="POST" class="mb-4 mx-auto" style="max-width: 400px;">
    <div class="input-group">
      <input type="text" name="pnr" class="form-control" placeholder="Enter PNR / Booking ID"
             required pattern="\d{1,10}" maxlength="10"
             title="Enter a valid numeric PNR (max 10 digits)">
      <button class="btn btn-primary">Check</button>
    </div>
  </form>

  <?php if ($error): ?>
    <div class="alert alert-danger mx-auto" style="max-width: 600px;">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php elseif ($pnrData): ?>
    <?php
      $status = strtolower($pnrData['payment_status']);
      $badgeClass = match($status) {
        'confirmed', 'success' => 'bg-success',
        'pending' => 'bg-warning text-dark',
        'cancelled', 'failed' => 'bg-danger',
        default => 'bg-secondary'
      };
    ?>
    <div class="card card-status mb-5">
      <div class="card-header <?= $badgeClass ?>">
        <h5 class="mb-0">PNR: <?= htmlspecialchars($pnrData['id']) ?></h5>
        <span class="badge rounded-pill text-white"><?= strtoupper(htmlspecialchars($pnrData['payment_status'])) ?></span>
      </div>
      <div class="card-body">
        <p><strong>Passenger:</strong> <?= htmlspecialchars($pnrData['passenger_name']) ?> &bull; <a href="mailto:<?= htmlspecialchars($pnrData['passenger_email']) ?>"><?= htmlspecialchars($pnrData['passenger_email']) ?></a></p>
        <p><strong>Train:</strong> <?= htmlspecialchars($pnrData['train_name']) ?> (No. <?= htmlspecialchars($pnrData['train_number']) ?>)</p>
        <p><strong>Route:</strong> <?= htmlspecialchars($pnrData['current']) ?> → <?= htmlspecialchars($pnrData['destination']) ?></p>
        <p><strong>Departure:</strong> <?= htmlspecialchars($pnrData['time']) ?></p>
        <p><strong>Seats:</strong> <?= htmlspecialchars($pnrData['seats']) ?></p>
        <p><strong>Total Fare:</strong> ₹<?= number_format($pnrData['total_fare'], 2) ?></p>
        <p><small class="text-muted"><strong>Booked on:</strong> <?= htmlspecialchars($pnrData['booked_at']) ?></small></p>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
