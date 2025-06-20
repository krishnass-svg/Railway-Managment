<?php
$customCss = "css/dashboard.css";
$pageTitle = "Book Now | Railway Management";
include 'header.php';
include 'dbconfig.php';

if (!isset($_GET['id'])) die("Train ID not provided.");
$trainId = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT * FROM orrs_train WHERE id = ?");
    $stmt->execute([$trainId]);
    $train = $stmt->fetch() ?: die("Train not found.");
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  #seatContainer .seat-grid {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: 0.5rem;
  }
  #seatContainer .seat {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    text-align: center;
    padding: 0.5rem;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  #seatContainer .seat.booked {
    background: #dc3545;
    color: white;
    cursor: not-allowed;
  }
  #seatContainer .seat.selected {
    background: #28a745;
    color: white;
  }
</style>

<div class="container my-5">
  <h2 class="mb-4 text-center">Book Train: <?= htmlspecialchars($train['name']) ?></h2>

  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card-body">
      <p><strong>Train Number:</strong> <?= htmlspecialchars($train['number']) ?></p>
      <p><strong>Route:</strong> <?= htmlspecialchars($train['route']) ?></p>
      <p><strong>From:</strong> <?= htmlspecialchars($train['current']) ?></p>
      <p><strong>To:</strong> <?= htmlspecialchars($train['destination']) ?></p>
      <p><strong>Departure Time:</strong> <?= htmlspecialchars($train['time']) ?></p>
      <p><strong>Fare:</strong> ₹<?= number_format($train['fare'], 2) ?> (per seat)</p>
    </div>
  </div>

  <form action="booking_process.php" method="POST" id="bookingForm">
    <input type="hidden" name="train_id" value="<?= $train['id'] ?>">

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="travel_date" class="form-label">Travel Date</label>
        <input type="date" name="travel_date" id="travel_date" class="form-control" required
               min="<?= date('Y-m-d') ?>">
      </div>
      <div class="col-md-6">
        <label for="coach" class="form-label">Select Coach Type</label>
        <select name="coach" id="coach" class="form-select" required>
          <option value="">-- Select Coach --</option>
          <option value="A">Sleeper (A)</option>
          <option value="B">AC 3 Tier (B)</option>
          <option value="C">General (C)</option>
        </select>
      </div>
    </div>

    <div id="seatContainer" class="mb-4"></div>

    <div id="passengerContainer" class="mb-4">
      <h5>Passengers</h5>
      <div class="passenger-row row g-3 mb-3">
        <div class="col-md-3"><input name="passenger_name[]" class="form-control" placeholder="Name" required></div>
        <div class="col-md-2"><input name="passenger_age[]" type="number" class="form-control" placeholder="Age" required></div>
        <div class="col-md-3"><input name="passenger_email[]" type="email" class="form-control" placeholder="Email" required></div>
        <div class="col-md-3"><input name="passenger_phone[]" type="tel" class="form-control" placeholder="Phone" required></div>
        <div class="col-md-1 d-flex align-items-center">
          <button type="button" class="btn btn-outline-danger remove-passenger">×</button>
        </div>
      </div>
    </div>

    <button type="button" id="addPassenger" class="btn btn-secondary mb-3">+ Add Passenger</button>
    <input type="hidden" name="selected_seats" id="selectedSeatsInput" required>

    <div class="mb-4">
      <label class="form-label">Estimated Fare:</label>
      <p id="farePreview" class="fs-5">₹0.00</p>
    </div>

    <button type="submit" class="btn btn-success">Proceed to Payment</button>
  </form>
</div>

<script>
const seatContainer = document.getElementById('seatContainer');
const selectedSeatsInput = document.getElementById('selectedSeatsInput');
const baseFare = <?= $train['fare'] ?>;

function updateFarePreview() {
  const ages = Array.from(document.querySelectorAll('input[name="passenger_age[]"]'))
                    .map(i => parseInt(i.value) || 0);
  let total = ages.reduce((sum, age) => {
    if (age < 5) return sum;
    if (age <= 12) return sum + baseFare * 0.5;
    if (age >= 60) return sum + baseFare * 0.6;
    return sum + baseFare;
  }, 0);
  document.getElementById('farePreview').innerText = '₹' + total.toFixed(2);
}

document.getElementById('addPassenger').addEventListener('click', () => {
  const row = document.querySelector('.passenger-row');
  const clone = row.cloneNode(true);
  clone.querySelectorAll('input').forEach(i => i.value = '');
  document.getElementById('passengerContainer').appendChild(clone);
  updateFarePreview();
});

document.getElementById('passengerContainer').addEventListener('click', e => {
  if (e.target.classList.contains('remove-passenger')) {
    e.target.closest('.passenger-row').remove();
    updateFarePreview();
  }
});

document.getElementById('passengerContainer').addEventListener('input', updateFarePreview);

document.getElementById('coach').addEventListener('change', loadSeats);
document.getElementById('travel_date').addEventListener('change', loadSeats);

function loadSeats() {
  const coach = document.getElementById('coach').value;
  const date = document.getElementById('travel_date').value;
  if (!coach || !date) return;

  fetch(`get_seats.php?train_id=<?= $trainId ?>&coach=${coach}&date=${date}`)
    .then(res => res.json())
    .then(data => {
      const booked = data.booked || [];
      let html = '<div class="seat-grid">';
      for (let i = 1; i <= 20; i++) {
        const seatCode = coach + i;
        html += `<div class="seat ${booked.includes(i.toString()) ? 'booked' : ''}"
                       data-seat="${seatCode}">${seatCode}</div>`;
      }
      html += '</div>';
      seatContainer.innerHTML = html;

      document.querySelectorAll('#seatContainer .seat').forEach(div => {
        if (!div.classList.contains('booked')) {
          div.addEventListener('click', () => {
            div.classList.toggle('selected');
            updateSelectedSeats();
          });
        }
      });
    });
}

function updateSelectedSeats() {
  const selected = Array.from(document.querySelectorAll('.seat.selected'))
                          .map(d => d.dataset.seat);
  const passengerCount = document.querySelectorAll('input[name="passenger_name[]"]').length;
  if (selected.length > passengerCount) {
    alert("Selected seats cannot exceed number of passengers.");
    return;
  }
  selectedSeatsInput.value = selected.join(",");
}
</script>

<?php include 'footer.php'; ?>
