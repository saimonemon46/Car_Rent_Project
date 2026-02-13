<header>
  <div>
    <a href="index.php">
      <img src="assets/images/logo.png" alt="Car Rental Logo" />
    </a>
    <?php
    // Fetch company contact info from the database
    $sql = "SELECT EmailId, ContactNo FROM tblcontactusinfo";
    $query = $conn->prepare($sql);
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    // loop through the results and display the contact info
    foreach ($result as $row) {
      $email = $row['EmailId'];
      $contactInfo = $row['ContactNo'];
    }
    ?>
  </div>

  <div>
    <p>
      Mail Us: <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
    </p>
    <p>Help Line: <?php echo $contactInfo; ?></p>
  </div>

  <?php
  if (empty($_SESSION['login'])) {
    ?>
    <div>
      <a href="includes/login.php">SignUp</a>
    </div>
    <?php
  } else {
    ?>
    <p>Welcome, <?php echo $_SESSION['login']; ?>!</p>
    <?php
  }
  ?>

  <hr />

  <nav>
    <?php if (!empty($_SESSION['login'])) { ?>
    <div>
      <?php
      $session_email = $_SESSION['login'];
      $sql = "SELECT FullName FROM tblusers WHERE EmailId = :email";
      $query = $conn->prepare($sql);
      $query->bindParam(':email', $session_email, PDO::PARAM_STR);
      $query->execute();
      $results = $query->fetch(PDO::FETCH_ASSOC);
      if ($query->rowCount() > 0) {
        foreach ($results as $result) {
          echo htmlentities($result['FullName']);
        }
      }
      ?>
      <!-- dropdown menu for user options -->
      <div>
        <ul>
          <!-- <?php if ($_SESSION['login']) { ?> -->
          <li><a href="profile.php">My Profile</a></li>
          <li><a href="change_password.php">Change Password</a></li>
          <li><a href="my_booking.php">My Booking</a></li>
          <li><a href="logout.php">Sign Out</a></li>
          <?php } ?>
        </ul>
      </div>



    </div>
    <?php } ?>
  </nav>

    <!-- Search option -->
    <div>
    <form action="search.php" method="post">
        <input
        type="text"
        name="searchdata"
        placeholder="Search..."
        required
        />
        <button type="submit" name="search">Search</button>
    </form>
    </div>
    <div>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="car_listing.php">Car Listing</a></li>
            <li><a href="contact_us.php">Contact Us</a></li>
        </ul>
    </div>


  hhhhh
</header>