<?php
$pageTitle = "Contact Us | Railwaymanagment";
include 'header.php';
include 'dbconfig.php';

$name = $email = $phone = $message = "";
$successMsg = $errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email   = htmlspecialchars(trim($_POST['email'] ?? ''));
    $phone   = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    // Validate fields
    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        $errorMsg = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email address.";
    } elseif (!preg_match('/^[6-9]\d{9}$/', $phone)) {
        $errorMsg = "Invalid phone number. Enter a valid 10-digit Indian number.";
    } else {
        $stmt = $conn->prepare("INSERT INTO feedback (name, email, phone, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $message]);
        $successMsg = "Thank you! Your feedback has been submitted. Our support team will get back to you shortly.";
    }
}
?>

<!-- Font Awesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
.contact-wrapper {
    background: linear-gradient(135deg, #5b6be0, #2bc4ad);
    border-radius: 15px;
    padding: 40px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    color: #ffffff;
}
.contact-wrapper h2 {
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 30px;
}
.contact-wrapper label {
    color: #f0f0f0;
}
.contact-wrapper .input-group-text {
    background-color: rgba(255, 255, 255, 0.85);
    border: none;
    color: #444;
    font-size: 1.1rem;
}
.contact-wrapper input,
.contact-wrapper textarea {
    border: none;
    box-shadow: none;
}
.contact-wrapper .form-control:focus {
    border-color: #fff;
    box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
}
.contact-wrapper .btn-primary {
    background-color: #ffffff;
    color: #2b2b2b;
    border: none;
    font-weight: 600;
    transition: 0.3s ease;
}
.contact-wrapper .btn-primary:hover {
    background-color: #f4f4f4;
    color: #2bc4ad;
}
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 contact-wrapper">
            <h2 class="text-center"><i class="fas fa-headset me-2"></i>Contact Us</h2>
            
            <?php if ($successMsg): ?>
                <div class="alert alert-success text-center"><?= $successMsg ?></div>
            <?php elseif ($errorMsg): ?>
                <div class="alert alert-danger text-center"><?= $errorMsg ?></div>
            <?php endif; ?>

            <p class="text-center mb-4">Facing issues with booking or payment? Fill out the form below or reach out to our 24x7 Support Team. We typically respond within 24 hours.</p>

            <form method="post" action="">
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                </div>
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                </div>
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="text" class="form-control" name="phone" placeholder="Phone Number (10 digits)" required pattern="[6-9]\d{9}">
                </div>
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="fas fa-comment-dots"></i></span>
                    <textarea class="form-control" name="message" rows="4" placeholder="Your Message" required></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-5"><i class="fas fa-paper-plane me-2"></i>Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
