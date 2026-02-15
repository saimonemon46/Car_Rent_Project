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

// Collect all available images
$image_fields = ['Vimage1', 'Vimage2', 'Vimage3', 'Vimage4', 'Vimage5'];
$gallery_images = [];
foreach($image_fields as $field) {
    if(!empty($vehicle[$field])) {
        $gallery_images[] = $vehicle[$field];
    }
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

        /* ===================== */
        /* UNIFIED GALLERY CARD  */
        /* ===================== */
        .gallery-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        /* Main hero image */
        .main-image-wrap {
            position: relative;
            width: 100%;
            height: 320px;
            background: #1a1a2e;
            overflow: hidden;
        }

        .main-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: opacity 0.4s ease;
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

        /* Navigation arrows on main image */
        .main-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            z-index: 5;
        }
        .main-nav:hover { background: rgba(102,126,234,0.85); }
        .main-nav.prev { left: 12px; }
        .main-nav.next { right: 12px; }

        /* Image counter badge */
        .img-counter {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background: rgba(0,0,0,0.55);
            color: #fff;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        /* Expand / lightbox button */
        .expand-btn {
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: rgba(0,0,0,0.55);
            color: #fff;
            border: none;
            font-size: 15px;
            padding: 4px 10px;
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .expand-btn:hover { background: rgba(102,126,234,0.85); }

        /* Thumbnail strip (horizontal scroll) */
        .thumb-strip {
            display: flex;
            gap: 8px;
            padding: 10px 12px;
            overflow-x: auto;
            background: #f4f4f8;
            scrollbar-width: thin;
            scrollbar-color: #667eea #e0e0e0;
        }

        .thumb-strip::-webkit-scrollbar {
            height: 5px;
        }
        .thumb-strip::-webkit-scrollbar-track { background: #e0e0e0; border-radius: 4px; }
        .thumb-strip::-webkit-scrollbar-thumb { background: #667eea; border-radius: 4px; }

        .thumb {
            flex: 0 0 80px;
            height: 58px;
            border-radius: 6px;
            overflow: hidden;
            cursor: pointer;
            border: 2.5px solid transparent;
            transition: border-color 0.2s, transform 0.2s, opacity 0.2s;
            opacity: 0.65;
        }
        .thumb:hover { opacity: 0.9; transform: scale(1.04); }
        .thumb.active {
            border-color: #667eea;
            opacity: 1;
        }
        .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .thumb-strip-empty {
            padding: 10px 14px;
            font-size: 13px;
            color: #999;
            background: #f4f4f8;
        }

        /* ===================== */
        /* INFO CARD             */
        /* ===================== */
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

        .price-display .label { font-size: 14px; opacity: 0.9; }
        .price-display .amount { font-size: 28px; font-weight: bold; }
        .price-display .per-day { font-size: 13px; opacity: 0.8; }

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

        .btn-secondary:hover { background-color: #e0e0e0; }

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

        /* ===================== */
        /* LIGHTBOX              */
        /* ===================== */
        .lightbox-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.92);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .lightbox-overlay.open { display: flex; }

        .lightbox-img-wrap {
            position: relative;
            max-width: 90vw;
            max-height: 78vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lightbox-img-wrap img {
            max-width: 90vw;
            max-height: 78vh;
            border-radius: 8px;
            object-fit: contain;
            box-shadow: 0 8px 40px rgba(0,0,0,0.6);
        }

        .lightbox-close {
            position: absolute;
            top: -44px;
            right: 0;
            background: none;
            color: #fff;
            border: none;
            font-size: 30px;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        .lightbox-close:hover { opacity: 1; }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.15);
            color: white;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            font-size: 22px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .lightbox-nav:hover { background: rgba(102,126,234,0.75); }
        .lightbox-nav.prev { left: -56px; }
        .lightbox-nav.next { right: -56px; }

        /* Lightbox thumbnail strip */
        .lightbox-thumbs {
            display: flex;
            gap: 8px;
            margin-top: 16px;
            max-width: 90vw;
            overflow-x: auto;
            padding-bottom: 4px;
            scrollbar-width: thin;
            scrollbar-color: #667eea #333;
        }
        .lightbox-thumbs::-webkit-scrollbar { height: 4px; }
        .lightbox-thumbs::-webkit-scrollbar-track { background: #333; }
        .lightbox-thumbs::-webkit-scrollbar-thumb { background: #667eea; border-radius: 4px; }

        .lightbox-thumb {
            flex: 0 0 64px;
            height: 46px;
            border-radius: 5px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            opacity: 0.55;
            transition: all 0.2s;
        }
        .lightbox-thumb:hover { opacity: 0.85; }
        .lightbox-thumb.active { border-color: #667eea; opacity: 1; }
        .lightbox-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }

        .lightbox-counter {
            color: #aaa;
            font-size: 13px;
            margin-top: 10px;
        }

        @media (max-width: 600px) {
            .lightbox-nav.prev { left: -36px; }
            .lightbox-nav.next { right: -36px; }
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

        <!-- Unified Gallery Card -->
        <div class="gallery-card">
            <?php if(!empty($gallery_images)): ?>

                <!-- Main hero viewer -->
                <div class="main-image-wrap">
                    <img id="mainImg"
                         src="admin/img/vehicleimages/<?php echo htmlentities($gallery_images[0]); ?>"
                         alt="<?php echo htmlentities($vehicle['VehiclesTitle']); ?>">

                    <?php if(count($gallery_images) > 1): ?>
                    <button class="main-nav prev" onclick="shiftGallery(-1)">&#8249;</button>
                    <button class="main-nav next" onclick="shiftGallery(1)">&#8250;</button>
                    <?php endif; ?>

                    <span class="img-counter" id="imgCounter">1 / <?php echo count($gallery_images); ?></span>
                    <button class="expand-btn" onclick="openLightbox(currentIdx)" title="View fullscreen">⛶ Expand</button>
                </div>

                <!-- Scrollable thumbnail strip -->
                <?php if(count($gallery_images) > 1): ?>
                <div class="thumb-strip" id="thumbStrip">
                    <?php foreach($gallery_images as $i => $img): ?>
                    <div class="thumb <?php echo $i === 0 ? 'active' : ''; ?>"
                         id="thumb-<?php echo $i; ?>"
                         onclick="goToSlide(<?php echo $i; ?>)">
                        <img src="admin/img/vehicleimages/<?php echo htmlentities($img); ?>"
                             alt="Photo <?php echo $i + 1; ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="no-image">🚗</div>
                <div class="thumb-strip-empty">No photos available</div>
            <?php endif; ?>
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

<!-- Lightbox -->
<div class="lightbox-overlay" id="lightbox" onclick="closeLightboxOnBg(event)">
    <div class="lightbox-img-wrap">
        <button class="lightbox-close" onclick="closeLightbox()">✕</button>

        <?php if(count($gallery_images) > 1): ?>
        <button class="lightbox-nav prev" onclick="lbShift(-1)">&#8249;</button>
        <button class="lightbox-nav next" onclick="lbShift(1)">&#8250;</button>
        <?php endif; ?>

        <img id="lbImg" src="" alt="Vehicle Photo">
    </div>

    <?php if(count($gallery_images) > 1): ?>
    <div class="lightbox-thumbs" id="lbThumbs">
        <?php foreach($gallery_images as $i => $img): ?>
        <div class="lightbox-thumb" id="lb-thumb-<?php echo $i; ?>" onclick="goToLbSlide(<?php echo $i; ?>)">
            <img src="admin/img/vehicleimages/<?php echo htmlentities($img); ?>" alt="Photo <?php echo $i + 1; ?>">
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="lightbox-counter" id="lbCounter"></div>
</div>

<!-- Gallery & Lightbox JavaScript -->
<script>
    // PHP images array passed to JS
    const images = <?php echo json_encode(array_map(function($img) {
        return 'admin/img/vehicleimages/' . htmlspecialchars($img, ENT_QUOTES);
    }, $gallery_images)); ?>;

    const total = images.length;
    let currentIdx = 0;
    let lbIdx = 0;

    /* ---- Main Gallery ---- */
    function goToSlide(idx) {
        if(total === 0) return;
        idx = (idx + total) % total;
        currentIdx = idx;

        // Swap main image with fade
        const mainImg = document.getElementById('mainImg');
        if(mainImg) {
            mainImg.style.opacity = '0';
            setTimeout(() => {
                mainImg.src = images[idx];
                mainImg.style.opacity = '1';
            }, 180);
        }

        // Update counter
        const counter = document.getElementById('imgCounter');
        if(counter) counter.textContent = (idx + 1) + ' / ' + total;

        // Update thumbnails
        document.querySelectorAll('.thumb').forEach((t, i) => {
            t.classList.toggle('active', i === idx);
        });

        // Scroll active thumb into view
        const activeThumb = document.getElementById('thumb-' + idx);
        if(activeThumb) {
            activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }

    function shiftGallery(dir) {
        goToSlide(currentIdx + dir);
    }

    /* ---- Lightbox ---- */
    function openLightbox(idx) {
        lbIdx = idx;
        document.getElementById('lightbox').classList.add('open');
        document.body.style.overflow = 'hidden';
        updateLightbox();
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('open');
        document.body.style.overflow = '';
    }

    function closeLightboxOnBg(e) {
        // Only close if clicking the dark overlay itself
        if(e.target === document.getElementById('lightbox')) {
            closeLightbox();
        }
    }

    function goToLbSlide(idx) {
        lbIdx = (idx + total) % total;
        updateLightbox();
    }

    function lbShift(dir) {
        lbIdx = (lbIdx + dir + total) % total;
        updateLightbox();
    }

    function updateLightbox() {
        const lbImg = document.getElementById('lbImg');
        if(lbImg) lbImg.src = images[lbIdx];

        const lbCounter = document.getElementById('lbCounter');
        if(lbCounter) lbCounter.textContent = (lbIdx + 1) + ' of ' + total + ' photos';

        document.querySelectorAll('.lightbox-thumb').forEach((t, i) => {
            t.classList.toggle('active', i === lbIdx);
        });

        // Scroll active lb thumb into view
        const activeLbThumb = document.getElementById('lb-thumb-' + lbIdx);
        if(activeLbThumb) {
            activeLbThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }

    /* ---- Keyboard Navigation ---- */
    document.addEventListener('keydown', function(e) {
        const lbOpen = document.getElementById('lightbox').classList.contains('open');
        if(lbOpen) {
            if(e.key === 'ArrowLeft')  lbShift(-1);
            if(e.key === 'ArrowRight') lbShift(1);
            if(e.key === 'Escape')     closeLightbox();
        } else {
            if(e.key === 'ArrowLeft')  shiftGallery(-1);
            if(e.key === 'ArrowRight') shiftGallery(1);
        }
    });

    /* ---- Touch/Swipe support ---- */
    let touchStartX = 0;
    document.getElementById('lightbox') && document.getElementById('lightbox').addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].clientX;
    });
    document.getElementById('lightbox') && document.getElementById('lightbox').addEventListener('touchend', e => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if(Math.abs(diff) > 50) lbShift(diff > 0 ? 1 : -1);
    });

    const mainWrap = document.querySelector('.main-image-wrap');
    mainWrap && mainWrap.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].clientX;
    });
    mainWrap && mainWrap.addEventListener('touchend', e => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if(Math.abs(diff) > 50) shiftGallery(diff > 0 ? 1 : -1);
    });
</script>

<?php include('includes/footer.php'); ?>
</body>
</html>