<?php
include 'dbconfig.php';
include 'header.php';

$razorpayKey = "rzp_test_ek75cF7ikf5j8R";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Booking ID not provided.");
}

$bookingId = (int)$_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT b.id AS booking_id, b.passenger_name, b.passenger_email, b.seats, b.total_fare, 
               t.name AS train_name, t.route, t.current, t.destination, t.time
        FROM orrs_bookings b
        JOIN orrs_train t ON b.train_id = t.id
        WHERE b.id = ?
    ");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        die("Booking not found.");
    }

    $amountInPaise = (int)($booking['total_fare'] * 100);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Payment | Online Railway Reservation</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Confirm Payment</h4>
        </div>
        <div class="card-body">
          <dl class="row">
            <dt class="col-sm-5">Passenger Name:</dt>
            <dd class="col-sm-7"><?= htmlspecialchars($booking['passenger_name']) ?></dd>

            <dt class="col-sm-5">Email:</dt>
            <dd class="col-sm-7"><?= htmlspecialchars($booking['passenger_email']) ?></dd>

            <dt class="col-sm-5">Train:</dt>
            <dd class="col-sm-7"><?= htmlspecialchars($booking['train_name']) ?> (<?= htmlspecialchars($booking['route']) ?>)</dd>

            <dt class="col-sm-5">Seats:</dt>
            <dd class="col-sm-7"><?= $booking['seats'] ?></dd>

            <dt class="col-sm-5">Total Fare:</dt>
            <dd class="col-sm-7 fw-bold">₹<?= number_format($booking['total_fare'], 2) ?></dd>
          </dl>
          <button id="payButton" class="btn btn-success w-100">Pay Now</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('payButton').onclick = function(e){
    var options = {
        "key": "<?= $razorpayKey ?>",
        "amount": "<?= $amountInPaise ?>",
        "currency": "INR",
        "name": "Online Railway Reservation",
        "description": "Train Booking Payment",
        "handler": function (response){
            window.location.href = "pay_success.php?payment_id=" + response.razorpay_payment_id + "&booking_id=<?= $bookingId ?>";
        },
        "prefill": {
            "name": "<?= htmlspecialchars($booking['passenger_name']) ?>",
            "email": "<?= htmlspecialchars($booking['passenger_email']) ?>"
        },
        "theme": {
            "color": "#198754"
        }
    };
    var rzp1 = new Razorpay(options);
    rzp1.open();
    e.preventDefault();
}
</script>

<!-- Bootstrap 5 JS Bundle (Popper + Bootstrap JS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include 'footer.php';
?>
