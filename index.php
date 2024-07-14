<?php 
include 'db_connection.php';
include 'navbar.php';

$sql = "SELECT * FROM specials";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Promotions - Kape-Kada Coffee Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-image: url('bg.svg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed; 
            background-position: center;
            margin-top: 120px;
        }
        .navbar {
            font-family: 'Merriweather', serif;
        }
        .promotions-section {
            padding: 60px 0;
        }
        .promotion-item {
            background-color: #FFF;
            padding: 30px;
            margin-bottom: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .promotion-item:hover {
            transform: scale(1.1); 
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); 
        }
        .promotion-title {
            color: #1e1e1e;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .promotion-price {
            color: #cc0000;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .promotion-description {
            color: #555;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .promotion-duration {
            color: #777;
            font-size: 14px;
            font-style: italic;
        }
        h1.text-center {
            color: white; 
            font-size: 36px; 
            font-weight: bold; 
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); 
            margin-bottom: 30px; 
            letter-spacing: 1px; 
            text-transform: uppercase; 
        }
        .banner { 
            margin: 30px 0; 
        }
        
        .slider-container {
            display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
            -ms-flex-align: center;
                align-items: center;
        gap: 10px;
        -webkit-border-radius: var(--border-radius-md);
                border-radius: var(--border-radius-md);
        overflow: auto hidden;
        -webkit-scroll-snap-type: inline mandatory;
            -ms-scroll-snap-type: inline mandatory;
                scroll-snap-type: inline mandatory;
        overscroll-behavior-inline: contain;
        }
        
        .slider-item {
            position: relative;
            min-width: 100%;
            max-height: 450px;
            aspect-ratio: 1 / 1;
            -webkit-border-radius: var(--border-radius-md);
                    border-radius: var(--border-radius-md);
            overflow: hidden;
            scroll-snap-align: start;
        }
        
        .slider-item .banner-img {
            width: 100%;
            height: 100%;
            -o-object-fit: cover;
                object-fit: cover;
            -o-object-position: right;
                object-position: right;
                border-radius: 5px;
        }
        
        .banner-content {
            background: hsla(0, 0%, 100%, 0.8);
            position: absolute;
            bottom: 25px;
            left: 25px;
            right: 25px;
            padding: 20px 25px;
            -webkit-border-radius: var(--border-radius-md);
                    border-radius: var(--border-radius-md);
        }

        .banner-btn {
            background: var(--malibec);
            color: var(--white);
            width: -webkit-max-content;
            width: -moz-max-content;
            width: max-content;
            font-size: var(--fs-11);
            font-weight: var(--weight-600);
            text-transform: uppercase;
            padding: 4px 10px;
            -webkit-border-radius: var(--border-radius-sm);
                    border-radius: var(--border-radius-sm);
            -webkit-transition: var(--transition-timing);
            -o-transition: var(--transition-timing);
            transition: var(--transition-timing);
            z-index: 1;
        }

        .banner-btn:hover { background: var(--eerie-black); }.about-section {
            background-color: #F9F5F1; 
            padding: 60px 0;
        }

        .about-heading {
            text-align: center;
            margin-bottom: 40px;
            color: #6B4F4E; 
            font-family: 'Merriweather', serif;
            font-size: 32px;
        }

        .about-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            color: #6B4F4E; 
            font-family: 'Montserrat', sans-serif;
            font-size: 18px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
<div class="banner">
    <div class="container">
        <div class="slider-container has-scrollbar">
            <div class="slider-item">
                <img src="banner.svg" alt="bg1 banner" class="banner-img">
            </div>
            <div class="slider-item">
                <img src="banner2.svg" alt="bg2 banner" class="banner-img">
            </div>
        </div>
    </div>
</div>
<div class="container promotions-section">
    <h1 class="text-center">This Season's Specials</h1>
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
    ?>
    <div class="promotion-item" onclick="window.location.href='specials_details.php?id=<?php echo $row['specials_id']; ?>';">
        <h2 class="promotion-title"><?php echo $row['name']; ?></h2>
        <p class="promotion-description"><?php echo $row['description']; ?></p>
        <p class="promotion-duration">Duration: <?php echo $row['start_date']; ?> to <?php echo $row['end_date']; ?></p>
    </div>
    <?php
    }
    ?>
</div>
<div class="about-section">
    <div class="container">
        <h2 class="about-heading">About Us</h2>
        <div class="about-content">
            <p>Welcome to Kape-Kada Coffee Shop, your go-to place for delicious coffee and cozy ambiance. At Kape-Kada, we believe in creating a warm and inviting space where friends can gather, conversations flow, and memories are made over a perfect cup of coffee.</p>
            <p>Our journey began with a passion for crafting exceptional coffee experiences. From carefully sourced beans to expertly brewed blends, each cup is a testament to our commitment to quality and flavor. But Kape-Kada is more than just a coffee shop; it's a community hub where people come together to unwind, connect, and savor the simple joys of life.</p>
            <p>Whether you're seeking a peaceful moment alone or catching up with friends, we invite you to join us at Kape-Kada and experience the magic of great coffee and genuine hospitality.</p>
        </div>
    </div>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
} else {
    echo "No specials available at the moment.";
}
?>