<?php
session_start();
include('includes/config.php');

if(isset($_GET['vid'])) {
    $vid = $_GET['vid'];
    
    $sql = "SELECT tblvehicles.*, tblbrands.BrandName FROM tblvehicles 
            JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand 
            WHERE tblvehicles.id = :vid";
    $query = $conn->prepare($sql);
    $query->bindParam(':vid', $vid, PDO::PARAM_INT);
    $query->execute();
    $vehicle = $query->fetch(PDO::FETCH_ASSOC);
    
    if(!$vehicle) {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlentities($vehicle['VehiclesTitle']); ?> - Car Rental</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .page-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .page-banner h1 {
            font-size: 36px;
            margin-bottom: 8px;
        }

        .page-banner p {
            font-size: 16px;
            opacity: 0.9;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #764ba2;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: start;
        }

        @media (max-width: 768px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .page-banner h1 {
                font-size: 26px;
            }
        }

        .image-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .image-card img {
            width: 100%;
            height: 320px;
            object-fit: cover;
            display: block;
        }

        .no-image {
            width: 100%;
            height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e0e0e0, #f5f5f5);
            font-size: 60px;
        }

        .info-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .info-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
        }

        .info-card-header h2 {
            font-size: 22px;
            margin-bottom: 4px;
        }

        .info-card-header p {
            font-size: 14px;
            opacity: 0.85;
        }

        .info-card-body {
            padding: 25px;
        }

        .specs-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .specs-table tr {
            border-bottom: 1px solid #f0f0f0;
        }

        .specs-table tr:last-child {
            border-bottom: none;
        }

        .specs-table th {
            text-align: left;
            padding: 12px 10px;
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 40%;
        }

        .specs-table td {
            padding: 12px 10px;
            font-size: 15px;
            color: #333;
        }

        .price-display {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .price-display .label {
            font-size: 14px;
            opacity: 0.9;
        }

        .price-display .amount {
            font-size: 28px;
            font-weight: bold;
        }

        .price-display .per-day {
            font-size: 13px;
            opacity: 0.8;
        }

        .button-group {
            display: flex;
            gap: 12px;
        }

        .btn {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 107, 107, 0.4);
        }

        .btn-secondary {
            background-color: #f0f0f0;
            color: #333;
        }

        .btn-secondary:hover {
            background-color: #e0e0e0;
        }

        .overview-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 25px;
            margin-top: 30px;
        }

        .overview-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
            display: inline-block;
        }

        .overview-card p {
            color: #666;
            line-height: 1.8;
            font-size: 15px;
        }

            /* Gallery Styles */
    .gallery-section {
        margin-top: 30px;
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .gallery-item {
        border-radius: 8px;
        overflow: hidden;
        height: 150px;
        cursor: pointer;
        border: 2px solid #f0f0f0;
        transition: transform 0.3s;
    }

    .gallery-item:hover {
        transform: scale(1.03);
        border-color: #667eea;
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<!-- Page Banner -->
<section class="page-banner">
    <h1><?php echo htmlentities($vehicle['VehiclesTitle']); ?></h1>
    <p><?php echo htmlentities($vehicle['BrandName']); ?></p>
</section>

<!-- Main Content -->
<div class="container">
    <a href="search.php" class="back-link">← Back to Search</a>

    <div class="detail-grid">
        <!-- Vehicle Image -->
        <div class="image-card">
            <?php if(!empty($vehicle['Vimage1'])): ?>
                <img src="admin/img/vehicleimages/<?php echo htmlentities($vehicle['Vimage1']); ?>" 
                     alt="<?php echo htmlentities($vehicle['VehiclesTitle']); ?>">
            <?php else: ?>
                <div class="no-image">🚗</div>
            <?php endif; ?>
        </div><div class="gallery-section">
    <h3>📸 Photo Gallery</h3>
    <div class="gallery-grid">
        <?php 
        // Array of image column names from your database
        $image_fields = ['Vimage1', 'Vimage2', 'Vimage3', 'Vimage4', 'Vimage5'];
        
        foreach($image_fields as $field) {
            // Check if the field is not empty and not null
            if(!empty($vehicle[$field])) {
                ?>
                <div class="gallery-item">
                    <img src="admin/img/vehicleimages/<?php echo htmlentities($vehicle[$field]); ?>" 
                         alt="Vehicle Image" 
                         onclick="window.open(this.src, '_blank')">
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>

        <!-- Vehicle Info -->
        <div class="info-card">
            <div class="info-card-header">
                <h2><?php echo htmlentities($vehicle['VehiclesTitle']); ?></h2>
                <p><?php echo htmlentities($vehicle['BrandName']); ?></p>
            </div>
            <div class="info-card-body">
                <table class="specs-table">
                    <tr>
                        <th>🏷️ Brand</th>
                        <td><?php echo htmlentities($vehicle['BrandName']); ?></td>
                    </tr>
                    <tr>
                        <th>📅 Model Year</th>
                        <td><?php echo htmlentities($vehicle['ModelYear']); ?></td>
                    </tr>
                    <tr>
                        <th>⛽ Fuel Type</th>
                        <td><?php echo htmlentities($vehicle['FuelType']); ?></td>
                    </tr>
                    <tr>
                        <th>👥 Seating</th>
                        <td><?php echo htmlentities($vehicle['SeatingCapacity']); ?> Seats</td>
                    </tr>
                    <tr>
                        <th>⚙️ Transmission</th>
                        <td><?php echo htmlentities($vehicle['Transmission'] ?? 'N/A'); ?></td>
                    </tr>
                </table>

                <div class="price-display">
                    <span class="label">Rental Price</span>
                    <div style="text-align: right;">
                        <div class="amount">$<?php echo htmlentities($vehicle['PricePerDay']); ?></div>
                        <div class="per-day">per day</div>
                    </div>
                </div>

                <div class="button-group">
                    <?php if(!empty($_SESSION['login'])): ?>
                        <a href="booking.php?vid=<?php echo htmlentities($vehicle['id']); ?>" class="btn btn-primary">🚗 Book Now</a>
                    <?php else: ?>
                        <a href="includes/login.php" class="btn btn-primary">🔐 Login to Book</a>
                    <?php endif; ?>
                    <a href="search.php" class="btn btn-secondary">← Go Back</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview -->
    <?php if(!empty($vehicle['VehiclesOverview'])): ?>
    <div class="overview-card">
        <h3>📋 Vehicle Overview</h3>
        <p><?php echo nl2br(htmlentities($vehicle['VehiclesOverview'])); ?></p>
    </div>
    <?php endif; ?>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>