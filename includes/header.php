<header>
    <div>
        <a href="index.php">
            <img src="assets/images/logo.png" alt="Car Rental Logo">
        </a>
        <?php
        // Fetch company contact info from the database 
        $sql = "SELECT EmailId, ContactNo FROM tblcontactusinfo";
        $query = $conn->prepare($sql);
        $query->execute();  
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        // loop through the results and display the contact info
        foreach($result as $row) {
            $email = $row['EmailId'];
            $contactInfo = $row['ContactNo'];
        }
        ?>
    </div>
    <div>
        <p>Mail Us: <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
        <p>Help Line: <?php echo $contactInfo; ?></p>
    </div>


    <?php 
    if(empty($_SESSION['login']))
        {?>
            <div>
                <a href="includes/login.php">SignUp</a> 
            </div>
        <?php } else { ?>
        <p>Welcome, <?php echo $_SESSION['login']; ?>!  </p>
        <?php } ?>


</header>