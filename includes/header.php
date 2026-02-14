<header style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0; margin: 0; position: sticky; top: 0; z-index: 1000; box-shadow: 0 8px 16px rgba(0,0,0,0.2); border-bottom: 3px solid rgba(255,255,255,0.1);">
  
  <!-- Top Info Bar -->
  <div style="background-color: rgba(0,0,0,0.1); padding: 10px 20px;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; font-size: 14px; flex-wrap: wrap; gap: 20px;">
      <?php
      // Fetch company contact info from the database
      $sql = "SELECT EmailId, ContactNo FROM tblcontactusinfo";
      $query = $conn->prepare($sql);
      $query->execute();
      $result = $query->fetchAll(PDO::FETCH_ASSOC);
      // loop through the results and display the contact info
      foreach ($result as $row) {
          $company_email = $row['EmailId'];
          $contactInfo = $row['ContactNo'];
      }
      ?>
      <div style="display: flex; gap: 30px; align-items: center;">
        <span>📧 <a href="mailto:<?php echo htmlentities($company_email); ?>"style="color: white; text-decoration: none;"><?php echo htmlentities($company_email); ?></a></span>
        <span>📞 <?php echo htmlentities($contactInfo); ?></span>
      </div>
      
      <div>
        <?php
        if (empty($_SESSION['login'])) {
          ?>
          <a href="includes/login.php" style="color: white; text-decoration: none; padding: 5px 15px; border: 1px solid white; border-radius: 5px; transition: 0.3s;">Sign In</a>
          <?php
        } else {
          ?>
          <span style="color: #ffd700; font-weight: bold;">Welcome, <?php echo htmlentities($_SESSION['login']); ?></span>
          <?php
        }
        ?>
      </div>
    </div>
  </div>
  
  <!-- Main Navigation Bar -->
  <nav style="padding: 15px 20px;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      
      <!-- Logo -->
      <div style="display: flex; align-items: center;">
        <a href="index.php" style="display: flex; align-items: center; text-decoration: none; color: white; font-weight: bold; font-size: 24px;">
          <?php if(file_exists('assets/images/logo.png')): ?>
            <img src="assets/images/logo.png" alt="Car Rental Logo" style="height: 40px; margin-right: 10px;" />
          <?php else: ?>
            <span style="font-size: 28px; margin-right: 8px;">🚗</span>
          <?php endif; ?>
          <span>CarHire</span>
        </a>
      </div>
      
      <!-- Navigation Links -->
      <div style="display: flex; gap: 30px; align-items: center;">
        <a href="index.php" style="color: white; text-decoration: none; transition: 0.3s; padding: 5px 10px; border-radius: 5px;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'" onmouseout="this.style.backgroundColor='transparent'">🏠 Home</a>
        <a href="search.php" style="color: white; text-decoration: none; transition: 0.3s; padding: 5px 10px; border-radius: 5px;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'" onmouseout="this.style.backgroundColor='transparent'">🔍 Browse Cars</a>
        <a href="contact_us.php" style="color: white; text-decoration: none; transition: 0.3s; padding: 5px 10px; border-radius: 5px;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'" onmouseout="this.style.backgroundColor='transparent'">📞 Contact</a>
      </div>
      
      <!-- Search Bar -->
      <form action="search.php" method="GET" style="display: flex; gap: 5px;">
        <input type="text" name="title" placeholder="Search cars..." style="padding: 8px 15px; border: none; border-radius: 20px; width: 200px; font-size: 14px;" />
        <button type="submit" style="padding: 8px 20px; background-color: #ff6b6b; color: white; border: none; border-radius: 20px; cursor: pointer; font-weight: bold; transition: 0.3s;" onmouseover="this.style.backgroundColor='#ff5252'" onmouseout="this.style.backgroundColor='#ff6b6b'">Search</button>
      </form>
      
      <!-- User Menu -->
      <?php if (!empty($_SESSION['login'])) { ?>
      <div style="position: relative;">
        <button style="background-color: rgba(255,255,255,0.2); color: white; border: 1px solid white; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold;" onclick="toggleUserMenu()">👤 Menu ▼</button>
        <div id="userMenu" style="position: absolute; top: 100%; right: 0; background: white; color: #333; min-width: 200px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); display: none; z-index: 100; margin-top: 5px;">
          <?php
          $session_email = $_SESSION['login'];
          $sql = "SELECT FullName FROM tblusers WHERE EmailId = :email";
          $query = $conn->prepare($sql);
          $query->bindParam(':email', $session_email, PDO::PARAM_STR);
          $query->execute();
          $results = $query->fetch(PDO::FETCH_ASSOC);
          if ($query->rowCount() > 0) {
            ?>
            <div style="padding: 15px; border-bottom: 1px solid #eee; font-weight: bold; color: #667eea;">
              <?php echo htmlentities($results['FullName']); ?>
            </div>
            <?php
          }
          ?>
          <a href="profile.php" style="display: block; padding: 12px 15px; text-decoration: none; color: #333; transition: 0.3s;" onmouseover="this.style.backgroundColor='#f0f0f0'" onmouseout="this.style.backgroundColor='white'">👤 My Profile</a>
          <a href="change_password.php" style="display: block; padding: 12px 15px; text-decoration: none; color: #333; transition: 0.3s;" onmouseover="this.style.backgroundColor='#f0f0f0'" onmouseout="this.style.backgroundColor='white'">🔐 Change Password</a>
          <a href="my_booking.php" style="display: block; padding: 12px 15px; text-decoration: none; color: #333; transition: 0.3s;" onmouseover="this.style.backgroundColor='#f0f0f0'" onmouseout="this.style.backgroundColor='white'">📅 My Bookings</a>
          <a href="includes/logout.php" style="display: block; padding: 12px 15px; text-decoration: none; color: #ff6b6b; font-weight: bold; transition: 0.3s; border-top: 1px solid #eee;" onmouseover="this.style.backgroundColor='#fff0f0'" onmouseout="this.style.backgroundColor='white'">🚪 Sign Out</a>
        </div>
      </div>
      <?php } ?>
    </div>
  </nav>

  <script>
    function toggleUserMenu() {
      var menu = document.getElementById('userMenu');
      menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
      var menu = document.getElementById('userMenu');
      var button = event.target.closest('button');
      if (!button && menu) {
        menu.style.display = 'none';
      }
    });
  </script>

</header>