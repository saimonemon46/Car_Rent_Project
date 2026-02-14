
<?php
include('includes/config.php');
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental</title>
</head>
<body>

<?php include('includes/header.php'); ?>



<section>
    <div>
        <h1>Welcome to Car Rental</h1>
        <p>Find the perfect car for your next adventure!</p>
    </div>
</section>

<section>
    <div>
        <h1>Find the Best Car for You</h1>
        <p>Browse our selection of high-quality vehicles to suit your needs and preferences.</p>
        
    </div>

    <div>
        <h2>Our Popular Cars</h2>
        <?php $sql = "SELECT tblvehicles.VehiclesTitle,tblbrands.BrandName,tblvehicles.PricePerDay,tblvehicles.FuelType,tblvehicles.ModelYear,tblvehicles.id,tblvehicles.SeatingCapacity,tblvehicles.VehiclesOverview,tblvehicles.Vimage1 from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand limit 9";
        $query = $conn->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        if ($query->rowCount() > 0) {   
            foreach($results as $result)
                {?>
                    <div style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; text-align: center;">
                        <div style="margin-bottom: 15px;">
                            <?php if(!empty($result['Vimage1'])): ?>
                                <img src="admin/img/vehicleimages/<?php echo htmlentities($result['Vimage1']); ?>" alt="<?php echo htmlentities($result['VehiclesTitle']); ?>" style="max-width: 100%; height: 200px; object-fit: cover; border-radius: 5px;">
                            <?php else: ?>
                                <div style="height: 200px; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 5px;">No Image</div>
                            <?php endif; ?>
                        </div>
                        <h5><?php echo htmlentities($result['VehiclesTitle']); ?></h5>
                        <p style="color: #666; margin: 8px 0;"><?php echo htmlentities($result['BrandName']); ?></p>
                        <ul style="list-style: none; padding: 0; font-size: 14px; color: #555;">
                            <li>⛽ <?php echo htmlentities($result['FuelType']);?></li>
                            <li>📅 <?php echo htmlentities($result['ModelYear']);?></li>
                            <li>👥 <?php echo htmlentities($result['SeatingCapacity']);?> Seats</li>
                        </ul>
                        <p style="font-size: 18px; color: #27ae60; font-weight: bold; margin: 12px 0;">$<?php echo htmlentities($result['PricePerDay']);?> /Day</p>
                        <a href="vehicles_details.php?vid=<?php echo htmlentities($result['id']);?>" style="display: inline-block; background-color: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">View Details</a>
                    </div>

                <?php }}?>
    </div>
</section>

<section>
    <div>
        <h2>Our Satisfied Customers</h2>
        From thousands of satisfied customers worldwide, we pride ourselves on excellent service. 
    </div>
</section>

<?php include('includes/footer.php'); ?>    
<?php include('includes/login.php'); ?>
<?php include('includes/register.php'); ?>
</body>
</html>