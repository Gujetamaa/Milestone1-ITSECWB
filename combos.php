<?php
include 'navbar.php';
include 'db_connection.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$sql = "SELECT * FROM combo_meals";
if ($filter) {
    $sql .= " WHERE category = '$filter'";
}
$result = mysqli_query($conn, $sql);

$combos = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $combos[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combo Deals - Kape-Kada Coffee Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F5F5DC; 
            color: #6B4F4E; 
            font-family: 'Montserrat', sans-serif;
            background-image: url('bg.svg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed; 
            background-position: center;
        }
        .navbar {
            font-family: 'Merriweather', serif;
        }
        .container {
            margin-top: 120px;
        }
        .card {
            margin-bottom: 20px;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.25rem;
            transition: all 0.3s ease-in-out;
        }
        .card:hover {
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            color: #A52A2A;
            font-size: 1.25rem;
            font-weight: bold;
        }
        .card-text {
            font-size: 1rem;
        }
        .card-subtitle {
            color: #333;
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .card-link {
            color: #6B4F4E;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
        }
        .card-link:hover {
            color: #A52A2A;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4" style="color: white;">Combo Deals</h1>
    <form action="combos.php" method="get">
        <div class="form-group">
            <label for="filter" style="color: white;">Filter by Time of Day:</label>
            <select id="filter" name="filter" class="form-control" onchange="this.form.submit()">
                <option value="">All Combos</option>
                <option value="Morning" <?php if ($filter == 'Morning') echo 'selected'; ?>>Morning</option>
                <option value="Evening" <?php if ($filter == 'Evening') echo 'selected'; ?>>Evening</option>
            </select>
        </div>
    </form>
    <?php foreach ($combos as $combo): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo $combo['name']; ?></h5>
                <h6 class="card-subtitle">₱<?php echo number_format($combo['price'], 2); ?></h6>
                <p class="card-text"><?php echo $combo['description']; ?></p>
                <a href="combo_detail.php?id=<?php echo $combo['id']; ?>" class="card-link">View Combo</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
