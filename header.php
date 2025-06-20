<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
$pageTitle = isset($pageTitle) ? $pageTitle : "Home | Railwaymanagment";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="icon" type="img/x-icon" href="img/train.png">
    <link rel="stylesheet" href="css/app.css" />
    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <?php if (isset($customLoginCss)): ?>
    <link rel="stylesheet" href="<?= $customLoginCss ?>">
    <?php endif; ?>

    <?php if (isset($customCss)): ?>
    <link rel="stylesheet" href="<?= $customCss ?>">
    <?php endif; ?>

</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
        <a href="index.php"> <img src="img/train.png" style=" width: 50px;" alt="Logo"> </a>
            <a class="navbar-brand" href="index.php">Journey Now</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= isset($pageTitle) && strpos($pageTitle, 'Home') !== false ? 'active' : ''; ?>" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                    <?php
                    $dashboardHref = "#";
                // Allow access if logged in
                   if ($isLoggedIn) {
                   if (isset($_SESSION['roles'])) {
                   if (in_array('ADMIN', $_SESSION['roles'])) {
                      $dashboardHref = 'adminDashboard.php';
                      $dashboardAttributes = ''; // No modal trigger for logged-in users
                   } elseif (in_array('USER', $_SESSION['roles'])) {
                        $dashboardHref = 'userDashboard.php';
                        $dashboardAttributes = ''; // No modal trigger for logged-in users
                   }
                }
            }
                    ?>
                    <a class="nav-link <?= isset($pageTitle) && strpos($pageTitle, 'Dashboard') !== false ? 'active' : ''; ?>" href="<?= $dashboardHref ?>">
                    Reservation</a>
                    </li>
                    <?php
                    // Show "Booking" dropdown only if NOT admin
                    if (!isset($_SESSION['roles']) || !in_array('ADMIN', $_SESSION['roles'])):
                    ?>
                    <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdownMenu" role="button" data-bs-toggle="dropdown">
                    Booking
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu">
                    <li><a class="dropdown-item" href="pnr_status.php">PNR Status</a></li>
                    <li><a class="dropdown-item" href="booking_history.php">My Bookings</a></li>
                    </ul>
                    </li>
                    <?php endif; ?>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isset($pageTitle) && strpos($pageTitle, 'About') !== false ? 'active' : ''; ?>" href="aboutUs.php">About Us</a>
                    </li>
                <?php if (!$isLoggedIn): ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($pageTitle, 'Login') !== false ? 'active' : ''; ?>" href="login.php"><b>Login</b></a>
                </li>
                <?php endif; ?>
                    <?php if (!isset($_SESSION['roles']) || !in_array('ADMIN', $_SESSION['roles'])): ?>
                        <?php
                        $username = isset($_SESSION['displayName']) ? $_SESSION['displayName'] : 'Guest';
                        $initial = strtoupper(substr($username, 0, 1));

                        ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="avatar-circle me-2" style="width: 32px; height: 32px; border-radius: 50%; background-color: #6c757d; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                    <?= $initial; ?>
                                </div>
                                <span><?= htmlspecialchars($username); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile Settings</a></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
    </nav>
</body>
