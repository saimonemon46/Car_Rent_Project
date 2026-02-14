
<?php
session_start();
include('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Car Rental - Your Journey, Our Cars</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 20px;
            text-align: center;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
        }
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.95;
        }
        
        .hero-cta {
            display: inline-block;
            background-color: #ff6b6b;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .hero-cta:hover {
            background-color: #ff5252;
        }
        
        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Featured Section */
        .featured-section {
            padding: 60px 20px;
            background-color: white;
            margin-top: -30px;
            position: relative;
            z-index: 1;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-header h2 {
            font-size: 36px;
            color: #333;
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }
        
        .section-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background-color: #667eea;
            border-radius: 2px;
        }
        
        .section-header p {
            color: #666;
            font-size: 16px;
            margin-top: 20px;
        }
        
        /* Cars Grid */
        .cars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .car-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        
        .car-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .car-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: linear-gradient(135deg, #e0e0e0 0%, #f5f5f5 100%);
        }
        
        .car-info {
            padding: 20px;
        }
        
        .car-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        
        .car-brand {
            color: #667eea;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .car-specs {
            list-style: none;
            margin-bottom: 15px;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            padding: 12px 0;
        }
        
        .car-specs li {
            font-size: 13px;
            color: #666;
            padding: 5px 0;
        }
        
        .car-price {
            font-size: 24px;
            color: #ff6b6b;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .car-price span {
            font-size: 14px;
            color: #999;
        }
        
        .car-button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: opacity 0.3s;
        }
        
        .car-button:hover {
            opacity: 0.9;
        }
        
        /* Info Section */
        .info-section {
            padding: 60px 20px;
            background-color: white;
            margin-top: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .info-card {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .info-card-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }
        
        .info-card h3 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .info-card p {
            color: #666;
            font-size: 14px;
        }
        
        /* Testimonials Section */
        .testimonials-section {
            padding: 60px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .testimonials-section h2 {
            font-size: 36px;
            margin-bottom: 40px;
        }
        
        .testimonial {
            max-width: 500px;
            margin: 0 auto;
            padding: 30px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }
        
        .testimonial p {
            font-size: 16px;
            margin-bottom: 15px;
            font-style: italic;
        }
        
        .testimonial-author {
            font-weight: bold;
            color: #ffd700;
        }
        
        .search-link {
            display: inline-block;
            margin-top: 30px;
            background-color: #ff6b6b;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .search-link:hover {
            background-color: #ff5252;
        }
        
        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .welcome-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .welcome-message {
            font-size: 18px;
            font-weight: bold;
        }
        
        .welcome-menu {
            display: flex;
            gap: 20px;
            list-style: none;
        }
        
        .welcome-menu li {
            margin: 0;
        }
        
        .welcome-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 14px;
        }
        
        .welcome-menu a:hover {
            background-color: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<!-- Hero Section -->
<section class="hero">
    <div>
        <h1>🚗 Premium Car Rental Service</h1>
        <p>Find the perfect car for your next adventure!</p>
        <a href="search.php" class="hero-cta">Explore Our Fleet</a>
    </div>
</section>

<?php //if (!empty($_SESSION['login'])): ?>
<!-- Welcome Section
<section class="welcome-section">
    <div class="welcome-content">
        <div class="welcome-message">
            <?php
            // $session_email = $_SESSION['login'];
            // $sql = "SELECT FullName FROM tblusers WHERE EmailId = :email";
            // $query = $conn->prepare($sql);
            // $query->bindParam(':email', $session_email, PDO::PARAM_STR);
            // $query->execute();
            // $user = $query->fetch(PDO::FETCH_ASSOC);
            
            // if ($user) {
            //     echo "👋 Welcome back, " . htmlentities($user['FullName']) . "!";
            // } else {
            //     echo "👋 Welcome back!";
            // }
            ?>
        </div>
        <ul class="welcome-menu">
            <li><a href="profile.php">📋 My Profile</a></li>
            <li><a href="my_booking.php">📅 My Bookings</a></li>
            <li><a href="change_password.php">🔐 Password</a></li>
        </ul>
    </div>
</section> -->
<?php endif; ?>

<!-- Featured Cars Section -->
<section class="featured-section">
    <div class="container">
        <div class="section-header">
            <h2>Our Popular Cars</h2>
            <p>Browse our selection of high-quality vehicles to suit your needs and preferences</p>
        </div>
        
        <div class="cars-grid">
        <?php $sql = "SELECT tblvehicles.VehiclesTitle,tblbrands.BrandName,tblvehicles.PricePerDay,tblvehicles.FuelType,tblvehicles.ModelYear,tblvehicles.id,tblvehicles.SeatingCapacity,tblvehicles.VehiclesOverview,tblvehicles.Vimage1 from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand limit 9";
        $query = $conn->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        if ($query->rowCount() > 0) {   
            foreach($results as $result) {?>
                <div class="car-card">
                    <div>
                        <?php if(!empty($result['Vimage1'])): ?>
                            <img src="admin/img/vehicleimages/<?php echo htmlentities($result['Vimage1']); ?>" alt="<?php echo htmlentities($result['VehiclesTitle']); ?>" class="car-image">
                        <?php else: ?>
                            <div class="car-image">No Image</div>
                        <?php endif; ?>
                    </div>
                    <div class="car-info">
                        <div class="car-title"><?php echo htmlentities($result['VehiclesTitle']); ?></div>
                        <div class="car-brand"><?php echo htmlentities($result['BrandName']); ?></div>
                        
                        <ul class="car-specs">
                            <li>⛽ Fuel: <?php echo htmlentities($result['FuelType']);?></li>
                            <li>📅 Year: <?php echo htmlentities($result['ModelYear']);?></li>
                            <li>👥 Seats: <?php echo htmlentities($result['SeatingCapacity']);?></li>
                        </ul>
                        
                        <div class="car-price">$<?php echo htmlentities($result['PricePerDay']);?> <span>/Day</span></div>
                        
                        <a href="vehicles_details.php?vid=<?php echo htmlentities($result['id']);?>" class="car-button">View Details</a>
                    </div>
                </div>
            <?php }
        } ?>
        </div>
        
        <div style="text-align: center;">
            <a href="search.php" class="search-link">Search All Vehicles</a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="info-section">
    <div class="container">
        <div class="section-header">
            <h2>Why Choose Us</h2>
            <p>Experience the best car rental service with our professional team</p>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-icon">🚗</div>
                <h3>Wide Selection</h3>
                <p>Choose from our diverse fleet of premium vehicles for any occasion</p>
            </div>
            
            <div class="info-card">
                <div class="info-card-icon">💰</div>
                <h3>Best Prices</h3>
                <p>Competitive rates with transparent pricing and no hidden charges</p>
            </div>
            
            <div class="info-card">
                <div class="info-card-icon">🛡️</div>
                <h3>Safe & Secure</h3>
                <p>All vehicles are fully insured and regularly maintained for your safety</p>
            </div>
            
            <div class="info-card">
                <div class="info-card-icon">⏰</div>
                <h3>24/7 Support</h3>
                <p>Round-the-clock customer support whenever you need assistance</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section">
    <div class="container">
        <h2>What Our Customers Say</h2>
        <div class="testimonial">
            <p>"Amazing service! The cars are in excellent condition and the staff is very professional. Highly recommended!"</p>
            <div class="testimonial-author">- John Doe</div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>    
<?php //include('includes/login.php'); ?>
<?php //include('includes/register.php'); ?>
</body>
</html>