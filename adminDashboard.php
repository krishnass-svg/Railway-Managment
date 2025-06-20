<?php
$customCss = "css/dashboard.css"; 
$pageTitle = "Admin Dashboard | Railway Management";
include 'header.php';
include 'dbconfig.php';

// Check for admin access
if (!isset($_SESSION['roles']) || !in_array('ADMIN', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

// Fetch total booking revenue
$total_revenue = 0;
try {
    $sql = "SELECT SUM(total_fare) AS total_revenue FROM orrs_bookings";
    $stmt = $conn->query($sql);
    $result = $stmt->fetch();
    $total_revenue = $result['total_revenue'] ?? 0;
} catch (PDOException $e) {
    $total_revenue = 0;
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Welcome to Admin Dashboard</h2>

    <!-- Section 1: Total Revenue -->
    <div class="card mb-4 p-3 shadow-sm">
        <div class="card-body d-flex flex-column justify-content-center">
            <h4>Total Booked Train Revenue</h4>
            <p class="fs-4">₹ <?php echo number_format($total_revenue, 2); ?></p>
        </div>  
    </div>

       <!-- Sections 2 to 6: Cards in a responsive grid -->
    <div class="row g-4 mb-5">

        <!-- Section 1: Cancel Train -->
        <div class="col-md-4">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body d-flex flex-column justify-content-center">
                   <h5 class="card-title">Cancel Train</h5>
                   <p class="card-text">Cancel a train for a specific date and notify users.</p>
                   <a href="cancel_train_form.php" class="btn btn-danger mt-auto">Cancel a Train</a>
                </div>
            </div>
        </div>

        <!-- Section 2: Manage Users -->
        <div class="col-md-4">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">Manage Users</h5>
                    <p class="card-text">View and manage registered users.</p>
                    <a href="manage_users.php" class="btn btn-warning mt-auto">Manage Users</a>
                </div>
            </div>
        </div>

        <!-- Section 3: Route Analytics -->
        <div class="col-md-4">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">Top Routes</h5>
                    <p class="card-text">See which routes are booked the most.</p>
                    <a href="route_analytics.php" class="btn btn-info mt-auto">View Top Routes</a>
                </div>
            </div>
        </div>

        <!-- Section 4: Feedbacks -->
        <div class="col-md-4">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">Feedbacks</h5>
                    <p class="card-text">See which routes are booked the most.</p>
                    <a href="admin_feedbacks.php" class="btn btn-info mt-auto">View Feedbacks</a>
                </div>
            </div>
        </div>

    </div>
</div>



<?php include 'footer.php'; ?>
