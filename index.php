<?php
include 'header.php';
?>

<title>Online Railway Reservation System</title>
<style>
    body {
        background: url('img/background.png') no-repeat center center fixed;
        background-size: cover;
        color: white;
    }
    .overlay {
        background-color: rgba(0, 0, 0, 0.6);
        min-height: 100vh;
        padding: 50px 15px;
    }
    .card-bg {
        background-color: rgba(255, 255, 255, 0.1);
        border: none;
        border-radius: 10px;
        padding: 30px;
        color: white;
    }
</style>

<div class="overlay d-flex flex-column justify-content-center align-items-center text-center">
    <div class="container">
        <div class="card-bg mb-4">
            <h1 class="display-5 fw-bold">Journey Now: Online Train Booking Platform</h1>
            <p class="lead">Book your train tickets easily and securely from anywhere.</p>

            <?php if (isset($_SESSION['username'])): 
                // Determine dashboard URL based on roles
                $dashboardUrl = 'userDashboard.php'; // default
                if (isset($_SESSION['roles']) && in_array('ADMIN', $_SESSION['roles'])) {
                    $dashboardUrl = 'adminDashboard.php';
                }
            ?>
                <p class="text-success mt-3">✅ You are logged in.</p>
                <a href="<?php echo $dashboardUrl; ?>" class="btn btn-success mt-2">Journey Now</a>
                <a href="logout.php" class="btn btn-warning mt-2 ms-2">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary btn-lg me-2">Login</a>
                <a href="registration.php" class="btn btn-outline-light btn-lg">Register</a>
            <?php endif; ?>
        </div>

        <div class="card-bg text-align-center">
            <h3>About Us</h3>
            <p>
                Our Online Railway Reservation System helps travelers reserve train tickets online quickly and easily. 
                With secure login, real-time availability, and instant booking features, we ensure a smooth experience for passengers.
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php' ?>
