<?php
$customCss = "css/faq.css";
$pageTitle = "FAQs | Railwaymanagment";
include 'header.php';
?>

<div class="container my-5">
    <h2 class="text-center mb-4">❓ Frequently Asked Questions</h2>

    <div class="accordion" id="faqAccordion">

        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                    How can I book a train ticket?
                </button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    To book a ticket, go to the <strong>Book Tickets</strong> section from your dashboard, select your preferred train and enter the passenger details. Proceed with payment to confirm the booking.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                    How can I cancel a booked ticket?
                </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Go to the <strong>Cancel Booking</strong> section, enter your booking details, and follow the instructions to cancel your ticket.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                    How can I check train status?
                </button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    You can check train status using the <strong>Train Status Checker</strong> on the user dashboard by entering the train name or number.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                    What payment methods are supported?
                </button>
            </h2>
            <div id="faq4" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    We support online payments using Razorpay, allowing you to pay via UPI, debit/credit card, or net banking.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
                    Where can I see my booked tickets?
                </button>
            </h2>
            <div id="faq5" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    After booking, you will be redirected to the <strong>Ticket Page</strong> which you can save or print. You can also revisit your ticket using the ticket ID.
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>
