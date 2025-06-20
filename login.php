<?php
$error = null;
$pageTitle = "Login | Railwaymanagment";
$customLoginCss = "css/login.css";
include 'header.php';
include 'dbconfig.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, password, first_name, last_name, email FROM users WHERE email = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                $_SESSION['displayName'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];

                $roleStmt = $conn->prepare("SELECT r.name 
                                            FROM roles r 
                                            INNER JOIN users_roles ur ON r.id = ur.role_id 
                                            WHERE ur.user_id = ?");
                $roleStmt->execute([$user['id']]);
                $roles = $roleStmt->fetchAll(PDO::FETCH_COLUMN);
                $_SESSION['roles'] = $roles;

                if (in_array('ADMIN', $roles)) {
                    header("Location: adminDashboard.php");
                    exit();
                } elseif (in_array('USER', $roles)) {
                    header("Location: userDashboard.php");
                    exit();
                } else {
                    $_SESSION['login_error'] = "No valid role assigned. Contact administrator.";
                    header("Location: login.php");
                    exit();
                }
            } else {
                $_SESSION['login_error'] = "Invalid password.";
            }
        } else {
            $_SESSION['login_error'] = "No user found with this email address.";
        }
    }
}

if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card bg-light text-dark p-4 shadow">
                <h3 class="text-center mb-4">
                    <i class="fas fa-sign-in-alt me-2 text-primary"></i>Login
                </h3>
                <form action="login.php" method="post">
                    <div class="mb-3 input-group">
                        <span class="input-group-text bg-primary text-white">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your email" required autofocus>
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text bg-primary text-white">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-arrow-right-to-bracket me-1"></i>Login
                        </button>
                    </div>
                    <div class="text-center">
                        <p class="mb-0">
                            <i class="fas fa-user-plus me-1 text-muted"></i>
                            Don't have an account?
                            <a href="registration.php" class="text-primary">Register here</a>
                        </p>
                    </div>
                </form>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger mt-2"><?= $error ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
