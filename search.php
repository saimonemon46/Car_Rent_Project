<?php
session_start();
include('includes/config.php');

// Initialize search variables
$search_brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$search_fuel = isset($_GET['fuel']) ? $_GET['fuel'] : '';
$search_price_min = isset($_GET['price_min']) ? intval($_GET['price_min']) : 0;
$search_price_max = isset($_GET['price_max']) ? intval($_GET['price_max']) : 99999;
$search_title = isset($_GET['title']) ? $_GET['title'] : '';

// Build the base query
$where_clauses = array("1=1");

if(!empty($search_title)) {
    $where_clauses[] = "tblvehicles.VehiclesTitle LIKE :title";
}

if(!empty($search_brand)) {
    $where_clauses[] = "tblbrands.id = :brand";
}

if(!empty($search_fuel)) {
    $where_clauses[] = "tblvehicles.FuelType = :fuel";
}

if($search_price_max > 0) {
    $where_clauses[] = "tblvehicles.PricePerDay >= :price_min AND tblvehicles.PricePerDay <= :price_max";
}

$where_sql = implode(' AND ', $where_clauses);

// Get all brands for filter dropdown
$brand_sql = "SELECT * FROM tblbrands ORDER BY BrandName";
$brand_query = $conn->prepare($brand_sql);
$brand_query->execute();
$brands = $brand_query->fetchAll(PDO::FETCH_ASSOC);

// Get vehicle results
$vehicle_sql = "SELECT tblvehicles.*, tblbrands.BrandName 
                FROM tblvehicles 
                JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand 
                WHERE $where_sql 
                ORDER BY tblvehicles.id DESC";

$vehicle_query = $conn->prepare($vehicle_sql);

// Bind parameters
if(!empty($search_title)) {
    $vehicle_query->bindValue(':title', '%' . $search_title . '%', PDO::PARAM_STR);
}
if(!empty($search_brand)) {
    $vehicle_query->bindValue(':brand', $search_brand, PDO::PARAM_INT);
}
if(!empty($search_fuel)) {
    $vehicle_query->bindValue(':fuel', $search_fuel, PDO::PARAM_STR);
}
if($search_price_max > 0) {
    $vehicle_query->bindValue(':price_min', $search_price_min, PDO::PARAM_INT);
    $vehicle_query->bindValue(':price_max', $search_price_max, PDO::PARAM_INT);
}

$vehicle_query->execute();
$vehicles = $vehicle_query->fetchAll(PDO::FETCH_ASSOC);
$total_results = count($vehicles);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Vehicles - Car Rental</title>
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
        
        .search-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            margin-bottom: 30px;
            text-align: center;
            border-bottom: 3px solid rgba(255,255,255,0.1);
        }
        
        .search-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
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
        
        .search-container {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .search-filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }
        
        .filter-group input,
        .filter-group select {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        
        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .search-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            align-self: flex-end;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .search-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .results-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f0f7ff;
            border-left: 4px solid #667eea;
            border-radius: 6px;
            color: #333;
            font-size: 14px;
        }
        
        .results-info strong {
            color: #667eea;
        }
        
        .vehicles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        
        .vehicle-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .vehicle-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .vehicle-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: linear-gradient(135deg, #e0e0e0 0%, #f5f5f5 100%);
        }
        
        .vehicle-info {
            padding: 20px;
        }
        
        .vehicle-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        
        .vehicle-brand {
            color: #667eea;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        
        .vehicle-details {
            list-style: none;
            margin-bottom: 15px;
            font-size: 13px;
            color: #666;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        
        .vehicle-details li {
            padding: 5px 0;
        }
        
        .vehicle-price {
            font-size: 22px;
            color: #ff6b6b;
            font-weight: bold;
            margin-bottom: 12px;
        }
        
        .vehicle-price span {
            font-size: 13px;
            color: #999;
        }
        
        .vehicle-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            flex: 1;
            padding: 12px;
            text-align: center;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .no-results {
            text-align: center;
            padding: 60px;
            background-color: white;
            border-radius: 12px;
            color: #666;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .no-results h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .no-results p {
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .clear-filters-btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .clear-filters-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
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
        
        .search-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<!-- Search Header -->
<div class="search-header">
    <h1>🔍 Search Vehicles</h1>
    <p>Find your perfect car with advanced filters</p>
</div>

<div class="container">
    <a href="index.php" class="back-link">← Back to Home</a>
    
    <div class="search-container">
        <form method="GET" action="search.php">
            <div class="search-filters">
                <div class="filter-group">
                    <label for="title">Vehicle Name</label>
                    <input type="text" id="title" name="title" placeholder="e.g., BMW, Tesla" value="<?php echo htmlentities($search_title); ?>">
                </div>
                
                <div class="filter-group">
                    <label for="brand">Brand</label>
                    <select id="brand" name="brand">
                        <option value="">All Brands</option>
                        <?php foreach($brands as $brand): ?>
                            <option value="<?php echo htmlentities($brand['id']); ?>" <?php echo ($search_brand == $brand['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlentities($brand['BrandName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="fuel">Fuel Type</label>
                    <select id="fuel" name="fuel">
                        <option value="">All Types</option>
                        <option value="Petrol" <?php echo ($search_fuel == 'Petrol') ? 'selected' : ''; ?>>Petrol</option>
                        <option value="Diesel" <?php echo ($search_fuel == 'Diesel') ? 'selected' : ''; ?>>Diesel</option>
                        <option value="CNG" <?php echo ($search_fuel == 'CNG') ? 'selected' : ''; ?>>CNG</option>
                        <option value="Electric" <?php echo ($search_fuel == 'Electric') ? 'selected' : ''; ?>>Electric</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="price_min">Min Price ($/Day)</label>
                    <input type="number" id="price_min" name="price_min" placeholder="0" value="<?php echo htmlentities($search_price_min); ?>">
                </div>
                
                <div class="filter-group">
                    <label for="price_max">Max Price ($/Day)</label>
                    <input type="number" id="price_max" name="price_max" placeholder="10000" value="<?php echo htmlentities($search_price_max); ?>">
                </div>
            </div>
            
            <button type="submit" class="search-button">Search Vehicles</button>
        </form>
    </div>
    
    <div class="results-info">
        <strong>Found <?php echo $total_results; ?> vehicle(s)</strong>
        <?php if(!empty($search_title)): ?>
            matching "<?php echo htmlentities($search_title); ?>"
        <?php endif; ?>
    </div>
    
    <?php if($total_results > 0): ?>
        <div class="vehicles-grid">
            <?php foreach($vehicles as $vehicle): ?>
                <div class="vehicle-card">
                    <div>
                        <?php if(!empty($vehicle['Vimage1'])): ?>
                            <img src="admin/img/vehicleimages/<?php echo htmlentities($vehicle['Vimage1']); ?>" 
                                 alt="<?php echo htmlentities($vehicle['VehiclesTitle']); ?>" 
                                 class="vehicle-image">
                        <?php else: ?>
                            <div class="vehicle-image" style="background-color: #e0e0e0; color: #999;">No Image Available</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="vehicle-info">
                        <div class="vehicle-title"><?php echo htmlentities($vehicle['VehiclesTitle']); ?></div>
                        <div class="vehicle-brand"><?php echo htmlentities($vehicle['BrandName']); ?></div>
                        
                        <ul class="vehicle-details">
                            <li>⛽ Fuel: <?php echo htmlentities($vehicle['FuelType']); ?></li>
                            <li>📅 Year: <?php echo htmlentities($vehicle['ModelYear']); ?></li>
                            <li>👥 Seats: <?php echo htmlentities($vehicle['SeatingCapacity']); ?></li>
                        </ul>
                        
                        <div class="vehicle-price">$<?php echo htmlentities($vehicle['PricePerDay']); ?>/Day</div>
                        
                        <div class="vehicle-actions">
                            <a href="vehicles_details.php?vid=<?php echo htmlentities($vehicle['id']); ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-results">
            <p>No vehicles found matching your search criteria.</p>
            <a href="search.php" class="btn btn-primary" style="display: inline-block; color: white; text-decoration: none;">Clear Filters</a>
        </div>
    <?php endif; ?>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
