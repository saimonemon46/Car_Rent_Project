<?php
// This sidebar is only displayed to logged-in users
if (empty($_SESSION['login'])) {
    return;
}
?>

<style>
    .sidebar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        width: 280px;
        padding: 0;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        position: sticky;
        top: 100px;
        height: fit-content;
    }
    
    .sidebar-header {
        padding: 25px;
        border-bottom: 2px solid rgba(255,255,255,0.1);
        text-align: center;
    }
    
    .sidebar-header h3 {
        margin: 0 0 8px 0;
        font-size: 18px;
        font-weight: bold;
    }
    
    .sidebar-header p {
        margin: 0;
        font-size: 13px;
        opacity: 0.9;
    }
    
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .sidebar-menu li {
        margin: 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .sidebar-menu li:last-child {
        border-bottom: none;
    }
    
    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        color: white;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 14px;
    }
    
    .sidebar-menu a:hover {
        background-color: rgba(255,255,255,0.1);
        padding-left: 25px;
    }
    
    .sidebar-menu i {
        margin-right: 12px;
        font-size: 16px;
        width: 20px;
    }
    
    .sidebar-section {
        padding: 15px 20px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255,255,255,0.6);
        font-weight: bold;
        margin-top: 10px;
    }
    
    .sidebar-footer {
        padding: 15px 20px;
        border-top: 2px solid rgba(255,255,255,0.1);
        margin-top: auto;
    }
    
    .sidebar-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .stat-item {
        text-align: center;
        padding: 10px;
        background-color: rgba(255,255,255,0.1);
        border-radius: 6px;
        font-size: 12px;
    }
    
    .stat-value {
        font-size: 16px;
        font-weight: bold;
        color: #ffd700;
    }
    
    .sidebar-logout {
        background-color: rgba(255, 107, 107, 0.2);
        border-radius: 6px;
    }
    
    .sidebar-logout:hover {
        background-color: rgba(255, 107, 107, 0.4);
    }
    
    @media (max-width: 1024px) {
        .sidebar {
            width: 100%;
            position: static;
            margin-bottom: 30px;
        }
    }
</style>

<aside class="sidebar">
    <!-- User Profile Section -->
    <div class="sidebar-header">
        <?php
        $session_email = $_SESSION['login'];
        $sql = "SELECT FullName, ContactNo, Address FROM tblusers WHERE EmailId = :email";
        $query = $conn->prepare($sql);
        $query->bindParam(':email', $session_email, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($user):
        ?>
            <h3>👤 <?php echo htmlentities(explode(' ', $user['FullName'])[0]); ?></h3>
            <p><?php echo htmlentities($user['ContactNo'] ?? 'No phone'); ?></p>
        <?php endif; ?>
    </div>
    
    <!-- Main Menu -->
    <ul class="sidebar-menu">
        <div class="sidebar-section">Account</div>
        <li><a href="profile.php">👤 My Profile</a></li>
        <li><a href="my_booking.php">📅 My Bookings</a></li>
        <li><a href="change_password.php">🔐 Change Password</a></li>
        
        <div class="sidebar-section">Browse</div>
        <li><a href="search.php">🔍 Search Cars</a></li>
        <li><a href="car_listing.php">🚗 All Vehicles</a></li>
        
        <div class="sidebar-section">Help</div>
        <li><a href="contact_us.php">📞 Contact Us</a></li>
        <li><a href="#">❓ FAQ</a></li>
    </ul>
    
    <!-- Stats Section -->
    <div class="sidebar-footer">
        <?php
        // Get user booking count
        $count_sql = "SELECT COUNT(*) as booking_count FROM tblbooking WHERE userEmail = :email";
        $count_query = $conn->prepare($count_sql);
        $count_query->bindParam(':email', $session_email, PDO::PARAM_STR);
        $count_query->execute();
        $stats = $count_query->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="sidebar-stats">
            <div class="stat-item">
                <div class="stat-value"><?php echo $stats['booking_count']; ?></div>
                <div>Bookings</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">⭐</div>
                <div>Rating</div>
            </div>
        </div>
        <a href="logout.php" style="display: block; background-color: rgba(255, 107, 107, 0.2); color: white; padding: 12px; text-align: center; border-radius: 6px; text-decoration: none; font-weight: bold; transition: 0.3s;" onmouseover="this.style.backgroundColor='rgba(255, 107, 107, 0.4)'" onmouseout="this.style.backgroundColor='rgba(255, 107, 107, 0.2)'">🚪 Sign Out</a>
    </div>
</aside>
