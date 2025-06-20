<?php
include 'header.php';
include 'dbconfig.php'; // Assumes $conn is a PDO object
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Train Schedule</h2>

    <form method="GET" class="row mb-4">
        <div class="col-md-10">
            <input type="text" name="search" class="form-control" placeholder="Search by train name, number, or destination" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Train Number</th>
                    <th>Name</th>
                    <th>Route</th>
                    <th>Current</th>
                    <th>Destination</th>
                    <th>Day</th>
                    <th>Arrival</th>
                    <th>Departure</th>
                    <th>Fare</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
                    $search = '%' . trim($_GET['search']) . '%';
                    $sql = "SELECT t.number, t.name, t.route, t.current, t.destination, t.fare,
                                   s.day, s.arrival_time, s.departure_time
                            FROM orrs_train_schedule s
                            JOIN orrs_train t ON s.train_id = t.id
                            WHERE t.name LIKE ? OR t.number LIKE ? OR t.destination LIKE ?
                            ORDER BY t.name, s.day";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$search, $search, $search]);
                } else {
                    $sql = "SELECT t.number, t.name, t.route, t.current, t.destination, t.fare,
                                   s.day, s.arrival_time, s.departure_time
                            FROM orrs_train_schedule s
                            JOIN orrs_train t ON s.train_id = t.id
                            ORDER BY t.name, s.day";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                }

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($results) > 0) {
                    foreach ($results as $row) {
                        echo "<tr>
                                <td>{$row['number']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['route']}</td>
                                <td>{$row['current']}</td>
                                <td>{$row['destination']}</td>
                                <td>{$row['day']}</td>
                                <td>{$row['arrival_time']}</td>
                                <td>{$row['departure_time']}</td>
                                <td>₹{$row['fare']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>No train schedules found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
