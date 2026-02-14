<?php
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
</head>
<body>

<?php include('includes/header.php'); ?>

<section>
    <div>
        <h1><?php echo htmlentities($vehicle['VehiclesTitle']); ?></h1>
        <p><?php echo htmlentities($vehicle['BrandName']); ?></p>
    </div>
</section>

<section>
    <div>
        <div>
            <?php if(!empty($vehicle['Vimage1'])): ?>
                <img src="admin/img/vehicleimages/<?php echo htmlentities($vehicle['Vimage1']); ?>" alt="<?php echo htmlentities($vehicle['VehiclesTitle']); ?>" style="max-width: 100%;">
            <?php else: ?>
                <p>No image available</p>
            <?php endif; ?>
        </div>

        <div>
            <h2>Vehicle Details</h2>
            <table>
                <tr>
                    <th>Brand</th>
                    <td><?php echo htmlentities($vehicle['BrandName']); ?></td>
                </tr>
                <tr>
                    <th>Model Year</th>
                    <td><?php echo htmlentities($vehicle['ModelYear']); ?></td>
                </tr>
                <tr>
                    <th>Fuel Type</th>
                    <td><?php echo htmlentities($vehicle['FuelType']); ?></td>
                </tr>
                <tr>
                    <th>Seating Capacity</th>
                    <td><?php echo htmlentities($vehicle['SeatingCapacity']); ?> Seats</td>
                </tr>
                <tr>
                    <th>Transmission</th>
                    <td><?php echo htmlentities($vehicle['Transmission'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Price Per Day</th>
                    <td><strong>$<?php echo htmlentities($vehicle['PricePerDay']); ?></strong></td>
                </tr>
            </table>

            <h2>Overview</h2>
            <p><?php echo htmlentities($vehicle['VehiclesOverview']); ?></p>

            <div>
                <a href="search.php">Back to Search</a>
                <button onclick="alert('Booking feature coming soon!')">Book Now</button>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>
<?php include('includes/login.php'); ?>
<?php include('includes/register.php'); ?>

</body>
</html>
