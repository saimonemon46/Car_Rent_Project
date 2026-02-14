<?php
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
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        
        .search-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .search-filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        
        .filter-group input,
        .filter-group select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .search-button {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            align-self: flex-end;
        }
        
        .search-button:hover {
            background-color: #2980b9;
        }
        
        .results-info {
            margin-bottom: 20px;
            color: #666;
            font-size: 14px;
        }
        
        .vehicles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .vehicle-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .vehicle-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .vehicle-info {
            padding: 15px;
        }
        
        .vehicle-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .vehicle-brand {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .vehicle-details {
            list-style: none;
            margin-bottom: 15px;
            font-size: 13px;
            color: #555;
        }
        
        .vehicle-details li {
            padding: 3px 0;
        }
        
        .vehicle-price {
            font-size: 20px;
            color: #27ae60;
            font-weight: bold;
            margin-bottom: 12px;
        }
        
        .vehicle-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            flex: 1;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            color: #666;
        }
        
        .no-results p {
            margin-bottom: 20px;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #3498db;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<section style="padding: 30px 20px; max-width: 1200px; margin: 0 auto;">
    <a href="index.php" class="back-link">← Back to Home</a>
    
    <h1 style="margin-bottom: 20px;">Search Vehicles</h1>
    
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
</section>

<?php include('includes/footer.php'); ?>
<?php include('includes/login.php'); ?>
<?php include('includes/register.php'); ?>

</body>
</html>
