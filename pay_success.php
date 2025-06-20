<?php
include 'header.php';
include 'dbconfig.php';

$razorpayKey = "rzp_test_ek75cF7ikf5j8R";
$razorpaySecret = "fr4FYOf41WzH0rEXzGYICa5oE";

if (!isset($_GET['payment_id'], $_GET['booking_id'])) {
    die("<div class='container'><h3 class='text-danger mt-5'>Invalid payment response.</h3></div>");
}

$paymentId = $_GET['payment_id'];
$bookingId = (int)$_GET['booking_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update booking status
    $stmt = $pdo->prepare("UPDATE orrs_bookings SET payment_status = 'PAID', razorpay_payment_id = ? WHERE id = ?");
    $stmt->execute([$paymentId, $bookingId]);

} catch (PDOException $e) {
    die("<div class='container'><h3 class='text-danger mt-5'>Database error: " . htmlspecialchars($e->getMessage()) . "</h3></div>");
}
?>

<title>Payment Success</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<div class="container my-5">
    <div class="card shadow-sm p-4">
        <h2 class="text-success mb-4">Payment Successful!</h2>
        <p><strong>Payment ID:</strong> <?= htmlspecialchars($paymentId); ?></p>
        <p>You will be redirected to your ticket shortly...</p>
    </div>
</div>

<!-- Redirect after 3 seconds -->
<script>
    setTimeout(() => {
        window.location.href = "ticket.php?id=<?= $bookingId ?>&auto=1";
    }, 3000);
</script>

<?php include 'footer.php'; ?>
