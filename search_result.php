<?php
include "database/database.php";

session_start();

$owner = isset($_SESSION['owner']) ? $_SESSION['owner'] : null;
$admin = isset($_SESSION['owner']) ? $_SESSION['owner'] : null;

$establishments = null;



// Assign remark based on average score
function getRemark($average) {
    if ($average >= 9) return 'Luxury';
    if ($average >= 8) return 'First Class';
    if ($average >= 7) return 'Comfort';
    if ($average >= 6) return 'Standard';
    if ($average >= 5) return 'Tourist';
    if ($average == 0) return 'No rating';
    
    return 'Poor';
}

function generateStars($rating) {
    $filledStar = '&#9733';
    $emptyStar = '&#9734';
    $stars = '';

    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rating ? $filledStar : $emptyStar;
    }

    return $stars;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Browse dormitories and cottages</title>
    
    <?php
    include "php/head_tag.php";
    
    $filter = isset($_GET['filter']) ? true : false;
    
    $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $amenities = isset($_GET['amenities']) ? explode(',', $_GET['amenities']) : [];

    $priceRange = 0;
    $rating = 0;
    $remark = 0;

    if (isset($_GET['check-price-range']) && $_GET['check-price-range'] === 'on') {
        $priceRange = isset($_GET['priceRange']) ? $_GET['priceRange'] : 0;
    } else {
        empty($priceRange);
    }

    if (isset($_GET['check-rating']) && $_GET['check-rating'] === 'on') {
        $rating = isset($_GET['rating']) ? $_GET['rating'] : 0;

        $rating = isset($_GET['rating']) ? intval($_GET['rating']) : 0;
    
        $rating = 2 * $rating;
    } else {
        empty($rating);
    }

    if (isset($_GET['check-review']) && $_GET['check-review'] === 'on') {
        $remark = isset($_GET['remark']) ? $_GET['remark'] : 0;
    } else {
        empty($remark);
    }

    $roomType = isset($_GET['roomType']) ? $_GET['roomType'] : '';
    $establishmentType = isset($_GET['establishmentType']) ? $_GET['establishmentType'] : '';
    $genderType = isset($_GET['genderType']) ? $_GET['genderType'] : '';
    $orderType = isset($_GET['order-type']) ? $_GET['order-type'] : '';
    $maxCapacity = isset($_GET['maxCapacity']) ? intval($_GET['maxCapacity']) : 4;

    $params = array();

    $sql = "SELECT 
            e.EstablishmentID,
            e.Name AS EstablishmentName,
            e.Type AS EstablishmentType,
            e.GenderInclusiveness, 
            e.Description,
            gt.Latitude, 
            gt.Longitude,
            ep.Photo1 AS Thumbnail,
            f.Name AS FeatureName,
            f.Icon AS AmenityIcon,
            MIN(r.PaymentRate) AS CheapestRoomPrice,
            MAX(r.PaymentRate) AS ExpensiveRoomPrice,
            GROUP_CONCAT(DISTINCT f.Name, '|', f.Icon) AS Features,
            COUNT(DISTINCT re.ReviewID) AS NumberOfReviews,
            IFNULL(AVG((re.StaffScore + re.FacilitiesScore + re.CleanlinessScore + re.ComfortScore + re.MoneyValueScore + re.LocationScore + re.SignalScore + re.SecurityScore) / 8), 0) AS AverageRating,
            COUNT(DISTINCT res.ResidencyID) AS NumberOfBookings,
            r.MaxOccupancy - COUNT(DISTINCT res.ResidencyID) AS VacantRooms,
            SUM(r.MaxOccupancy) - COUNT(res.ResidencyID) AS TotalVacantSpaces
        FROM 
            establishment e
        LEFT JOIN 
            geo_tags gt ON e.EstablishmentID = gt.EstablishmentID
        LEFT JOIN 
            establishment_photos ep ON e.EstablishmentID = ep.EstablishmentID
        LEFT JOIN 
            rooms r ON e.EstablishmentID = r.EstablishmentID
        LEFT JOIN 
            establishment_features ef ON e.EstablishmentID = ef.EstablishmentID
        LEFT JOIN 
            features f ON ef.FeatureID = f.FeatureID
        LEFT JOIN 
            reviews re ON r.RoomID = re.RoomID
        LEFT JOIN 
            residency res ON r.RoomID = res.RoomID
        WHERE 
            e.Status != 'removed'
    ";

    // Filtering
    if (!empty($searchQuery)) {
        $sql .= " AND (e.Name LIKE '%$searchQuery%' OR f.Name LIKE '%$searchQuery%')";
        
    }
    
    if ($priceRange > 0) {
        $sql .= " AND r.PaymentRate >= $priceRange";
        
    }
    
    if ($rating > 0) {
        $sql .= " AND AverageRating >= $rating";
        
    }

    if ($remark > 0) {
        $sql .= " AND AverageRating >= $remark";
        
    }
    
    if (!empty($amenities)) {
        $amenityIds = implode(',', $amenities);
        $sql .= " AND f.Icon IN ('$amenityIds')";
        
    }
    
    if (!empty($roomType)) {
        $sql .= " AND r.RoomType = '$roomType'";
        
    }
    
    if (!empty($establishmentType)) {
        $sql .= " AND e.Type = '$establishmentType'";
        
    }
    
    if (!empty($genderType)) {
        $sql .= " AND e.GenderInclusiveness = '$genderType'";
        
    }
    
    if ($maxCapacity > 0) {
        $sql .= " AND r.MaxOccupancy = $maxCapacity";
        
    }
    
    
    $sql .= " GROUP BY e.EstablishmentID ";

    // Sorting
    switch ($orderType) {
        case 'cheapest':
            $sql .= " ORDER BY MIN(r.PaymentRate) ASC";
            break;
        case 'highest_price':
            $sql .= " ORDER BY MAX(r.PaymentRate) DESC";
            break;
        case 'most_booked':
            $sql .= " ORDER BY COUNT(DISTINCT res.ResidencyID) DESC";
            break;
        case 'most_vacant':
            $sql .= " ORDER BY (r.MaxOccupancy - COUNT(DISTINCT res.ResidencyID)) DESC";
            break;
        default:
            $sql .= " ORDER BY EstablishmentName";
            break;
    }


    // Pagination
    $itemsPerPage = 10;
    $offset = ($page - 1) * $itemsPerPage;
    $sql .= " LIMIT $offset, $itemsPerPage";

    // echo count($params);
    // echo $sql;

    $establishmentResult = mysqli_query($conn, $sql);

    $rowCount = 0;


    if ($establishmentResult) {
        $rowCount = mysqli_num_rows($establishmentResult);
    } else {
        echo mysqli_error($conn);
    }

    if (!$filter || empty($searchQuery)) {
        $countSql = "SELECT COUNT(DISTINCT e.EstablishmentID) AS totalItems FROM establishment e 
                LEFT JOIN rooms r ON e.EstablishmentID = r.EstablishmentID 
                LEFT JOIN reviews re ON r.RoomID = re.RoomID 
                WHERE e.Status != 'removed'";

        // echo $countSql;

        $countResult = mysqli_query($conn, $countSql);
        $totalItems = mysqli_fetch_assoc($countResult)['totalItems'];
        $totalPages = ceil($totalItems / $itemsPerPage);
    }
    ?>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>

      <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

     <!-- <link rel="stylesheet" href="/bookingapp/assets/leaflet/leaflet.css">
     <script src="/bookingapp/assets/leaflet/leaflet.js"></script> -->

    <style>
        #previewMap {
            width: 100%;
            height: 500px;
        }

        #viewMap {
            height: 300px;
            border: 1px solid black;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-inline {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-items: center;
            gap: 20px;
            align-content: center;
        }

        .form-group input,
        .form-group select {
            padding: 10px;
            border-radius: 5px;
            border: none;
            border-bottom: 1px solid rgba(0,0,0,0.5);
            width: 300px;
        }


        /* Pagination links */
        .pagination {
            margin-top: 40px;
            float: right;
        }

        .pagination a {
            color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color .3s;
        }
        
        /* Pagination links */
        .pagination {
            margin-top: 40px;
            float: right;
        }

        .pagination a {
            color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color .3s;
        }
        
        /* Style the active/current link */
        .pagination a.active {
            background-color: maroon;
            color: white;
        }

        #viewMapModal .modal-content {
            width: 80%; height: 90%; 
        }

        .main-content {
            height: auto;
        }

        /* Selects */
        .sort-container {
            float: left;
        }

        .sort-select {
            padding: 5px;
            border-radius: 20px;
            margin: 5px;
        }

        .card-dropdown {
            position: relative;
            display: inline-block;
        }

        .card-dropdown-menu {
            display: none;
            position: absolute;
            bottom: 100%;
            right: 0;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            z-index: 200;
            min-width: 150px;
            padding: 10px 0;
            overflow: hidden;
        }

        .card-dropdown-menu a {
            padding: 10px 15px;
            display: block;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }

        .card-dropdown-menu a:hover {
            background-color: #f0f0f0;
        }

        .card-dropdown:hover .card-dropdown-menu {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }

        .room-card-image img {
            height: 270px;
            object-fit: cover;
        }

        .room-card.list-view .room-card-image img {
            width: 100%;
            height: 270px;
            object-fit: cover;
        }

        .filter-group {
            padding: 10px;
        }

        .filter-options {
            padding-top: 10px;
        }

        .filter-label {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media screen and (max-width: 1000px) {
            #viewMapModal .modal-content {
                width: 90%;
                height: 95%;
            }

            .search-map {
                display: inline-flex;
            }
        }
    </style>

</head>
<body>
    <!-- Header -->
    <header class="header">
        <?php include "php/header.php"; ?>
        <div class="section-container header-container" id="home">
            <a href="index.php"><img src="/bookingapp/assets/site-logo/logo-text-white.png" style="width: 300px;" alt="Balay.com logo"></a>
            <h1>Your Premier Online Accommodation System
                in <span>Mindanao State University</span>.
            </h1>
        </div>
    </header>

    <!-- Search section -->
   <?php include "php/search_section.php"; ?>
   <!-- End of search section -->

   <?php
        // Fetch all establishments
        $establishments = [];

        // echo json_encode($establishments);
    ?>
   <div class="dashboard-container clearfix" style="padding-bottom: 10px">
   
   
   
        
   <?php

   if ($rowCount > 0) {
     //   while ($row = mysqli_fetch_assoc($result)) { ?>
        <!-- Sidebar Content -->

        
        <div class="sidebar">
            <!-- <div class="panel map-container" id="mapContainer">
                <div id="previewMap"></div>
                <button class="btn" onclick="openModal('viewMapModal');">See Map</button>
            </div> -->

            <!-- <div class="panel" id="no-internet-prompt">
                <h5 style="text-align: center">Can't access the map. Check the internet connection.</h5>
            </div> -->
            <div class="panel filter-container">
                <h3 style="margin: 10px;">Filters</h3>
                <div class="content">
                    <?php include "php/filter_options.php"; ?>
                </div>
            </div>
        </div>


        <!-- Main Content -->
        <div class="main-content">
            <div class="section-container room-container"  style="padding-top: 20px">
                <p class="section-subheader">DORMITORIES & COTTAGES</p>
                <h2 class="section-header">Bolos Kano</h2>

                <div class="sort-container">
                    <label for="sort-select">Sort:</label>
                    <select name="order-type" class="sort-select" id="sort-select">
                        <option value="">Default</option>
                        <option value="cheapest">Cheapest</option>
                        <option value="highest_price">Highest price</option>
                        <option value="most_booked">Most booked</option>
                        <option value="most_vacant">Most vacant</option>
                    </select>
                        <p>Showing <?php echo $rowCount; ?> result(s).</p>
                    <!- -->
                </div>

                <div class="list-grid-switcher">
                    <button class="toggle-btn list-mode" title="Select to toggle list view mode." id="toggleListView" onclick="setListView();"><i class="fa-solid fa-list"></i></button>
                    <button class="toggle-btn grid-mode toggle-active" title="Select to toggle grid view mode." id="toggleGridView" onclick="setGridView();"><i class="class fa-solid fa-grip"></i></button>
                </div>

                <div class="room-grid">
                    <?php 
                    // mysqli_stmt_bind_result($stmt, $establishmentID, $name, $type, $genderInclusiveness, $description,)
                    while ($row = mysqli_fetch_assoc($establishmentResult)) { 
                        $establishments[] = $row;
                        $establishmentID = $row['EstablishmentID'];
                        $encryptedEstID = base64_encode($establishmentID);

                        $name = $row['EstablishmentName'];
                        $type = $row['EstablishmentType'];

                        $genderInclusiveness = $row['GenderInclusiveness'];
                        
                        $description = $row['Description'];
                        $description = str_replace("\\", "", $description);

                        // Geo-Tag
                        $geoSql = "SELECT * FROM geo_tags WHERE EstablishmentID = $establishmentID";
                        $geoResult = mysqli_query($conn, $geoSql);

                        $location = "Not yet on the map!";

                        if (mysqli_num_rows($geoResult) > 0) {
                            $geoTag = mysqli_fetch_assoc($geoResult);
                        }

                        // Reviews
                        $rating = null;
                        $totalReviews = 0;
                        $totalScore = 0;
                        $maxScoreReview = 70; // Number of categories is 7. Adjust this. 7 * 10 points

                        $reviewSql = "SELECT COUNT(rv.ReviewID) AS TotalReview, AVG(rv.StaffScore) AS StaffScore, AVG(rv.FacilitiesScore) AS FacilitiesScore, AVG(rv.CleanlinessScore) AS CleanlinessScore, AVG(rv.ComfortScore) AS ComfortScore, AVG(rv.SignalScore) AS SignalScore, AVG(rv.LocationScore) AS LocationScore, (rv.MoneyValueScore) AS MoneyValueScore, (AVG(rv.StaffScore + rv.FacilitiesScore + rv.CleanlinessScore + rv.ComfortScore + rv.SignalScore + rv.LocationScore + rv.MoneyValueScore) / 7) AS OverallScore FROM rooms r JOIN reviews rv ON r.RoomID = rv.RoomID WHERE r.EstablishmentID = $establishmentID";
                        $reviewResult = mysqli_query($conn, $reviewSql);

                        if (mysqli_num_rows($reviewResult) > 0) {
                            while ($row = mysqli_fetch_assoc($reviewResult)) {
                                $totalReview = $row['TotalReview'];
                                $totalScore = $row['OverallScore'];
                            }
                        }
                        
                        // Calculate average score
                        $averageScore = $totalReviews > 0 ? $totalScore / ($totalReviews * 7) : 0;

                        // Convert average score to a 5-star rating system
                        $starRating = round(($averageScore / 10) * 5);

                        $remark = getRemark($averageScore);

                        // Check if establishment has any photo
                        $featuredPhoto = "/bookingapp/assets/images/msu-facade.jpg";
                        $photoDescription = "Description";

                        $photoSql = "SELECT * FROM establishment_photos WHERE EstablishmentID = $establishmentID";
                        $photoResult = mysqli_query($conn, $photoSql);

                        if (mysqli_num_rows($photoResult) > 0) {
                            $row = mysqli_fetch_assoc($photoResult);
                            
                            $featuredPhoto = $row['Photo1'];
                            $featuredPhoto = isset($featuredPhoto) && $featuredPhoto !== '' ? "/bookingapp/establishment/" . $featuredPhoto : "/bookingapp/assets/images/msu-facade.jpg"; 
                            $photoDescription = $row['Description1'] ?? 'No description';
                        }

                        // Get number of rooms and price average
                        $price = 0;
                        $noOfRooms = 0;
                        $priceSql = "SELECT COUNT(RoomID) AS NoOfRooms, AVG(PaymentRate) AS PaymentRate FROM rooms WHERE EstablishmentID = $establishmentID AND Availability != 'Deleted'";
                        $priceResult = mysqli_query($conn, $priceSql);

                        if (mysqli_num_rows($priceResult) > 0) {
                            $rooms = mysqli_fetch_assoc($priceResult);

                            $price = $rooms['PaymentRate'];
                            $noOfRooms = $rooms['NoOfRooms'];
                        }
                    ?>
                    <div class="room-card">
                        <div class="room-card-image">
                            <img src="<?php echo $featuredPhoto; ?>" alt="<?php echo $photoDescription; ?>" />
                            <div class="room-card-icons">
                                <span title="Add to favorites"><i class="fa-solid fa-heart"></i></span>
                                <span title="View Map" onclick="redirect('#')"><i class="fa-solid fa-location-pin"></i></span>
                                <div class="card-dropdown">
                                    <span title="Show Options"><i class="fa-solid fa-caret-down"></i></span>
                                    <div class="card-dropdown-menu">
                                        <a href="establishment.php?est=<?php echo $encryptedEstID; ?>#availability">Check-in</a>
                                    </div>
                                </div>
                            </div>
                            <div class="room-price">
                                <?php
                                    $formated_number = number_format($price, 2, '.', ',');
                                    
                                    echo '₱' . $formated_number;
                                ?>  
                            </div>
                            <div class="room-review">
                                <?php echo $remark; ?> <span class="room-score"><?php echo number_format($averageScore, 1); ?></span><br>
                                <?php echo number_format($totalReviews); ?> reviews    
                            </div>
                        </div>
                        <div class="room-card-details clearfix">
                            <h4><?php echo $name; ?></h4>
                            <p>
                                <span><i class="fa-solid fa-building"></i> <?php echo $type; ?></span><br>
                                <?php echo generateStars($starRating); ?>
                            </p>
                            <p>
                                <span><i class="fa-solid fa-location-pin"></i> <?php echo $location; ?></span><br>
                                <span><i class="fa-solid fa-venus-mars"></i> <?php echo $genderInclusiveness; ?></span> &middot;
                                <span><i class="fa-solid fa-door-open"></i> <?php echo "$noOfRooms room(s)"; ?></span>
                                
                                <?php
                                
                                $amenitySql = "SELECT f.Icon, f.Name, f.FeatureID FROM establishment_features rf INNER JOIN features f ON f.FeatureID = rf.FeatureID WHERE rf.EstablishmentID = $establishmentID";
                                $amenityResult = mysqli_query($conn, $amenitySql);

                                if (mysqli_num_rows($amenityResult) > 0) { ?>
                                
                                <h6>Amenities</h6>
                                <p>
                                    <?php
                                        while ($amenity = mysqli_fetch_assoc($amenityResult)) {
                                    ?>
                                    <span><i class="fa-solid fa-<?php echo $amenity['Icon']; ?>"></i> <?php echo $amenity['Name']; ?></span>
                                    <?php 
                                    } ?>
                                </p>
                                <?php } ?>
                                <hr>
                                <span style="font-size: 12px; font-style: italic"><?php echo $description; ?></span>
                            </p>
                            <button class="book-now-btn btn" style="float: right; font-size: 15px; padding: 10px" onclick="redirect('establishment/establishment.php?est=<?php echo $encryptedEstID; ?>')">
                                <?php if ($accountRole !== 'tenant') {
                                    echo 'View establishment';
                                } else {
                                    echo 'Book now';
                                } ?>    
                            </button>
                        </div>
                    </div>
                    <?php } ?>
                </div> 
            </div>

            <?php if ($establishmentResult) { ?>
                <!-- Pagination -->
                <?php if (!$filter) { ?>
                        <div id="pagination" class="pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="search_result.php?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                <?php }
                
            } ?>
        </div>

        <?php 
   }  else { ?>
        <div style="padding-bottom: 500px; margin: auto; display: flex; flex-direction: column; justify-content: center; gap: 10px;">
            <h1 style='text-align: center;'>No establishment available found.</h1>
            <button type='button' style="padding: 10px; border-radius: 10px; border: none; outline: none; background-color: maroon; color: white; cursor: pointer;" onclick="redirect('search_result.php')"><i class="fa-solid fa-hourglass"></i> View all establishments</button>
        </div>
   <?php } ?>

   </div>

   
   <!-- Modals -->
    <?php include "modal/view_map_modal.php"; ?>

     <!-- Footer -->
    <?php
    include "php/footer.php";
    ?>

    <div id="toastBox"></div>

    <script type="module" src="/bookingapp/js/script.js"></script>
    <script src="/bookingapp/js/scrollreveal.js "></script>
    <script src="/bookingapp/js/rayal.js"></script>
    <!-- <script src="/bookingapp/assets/leaflet/leaflet.js"></script> -->
    <!-- <script src="/bookingapp/assets/leaflet/leaflet.js"></script> -->
    <!-- <script src="/bookingapp/js/maps.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/ol/6.5.0/ol.js"></script> -->

    <script>

        
        // Set the current year in the footer dynamically
        document.getElementById('year').textContent = new Date().getFullYear();

        <?php if ($rowCount > 0) { ?>
        // Price Range Slider

        var priceInput = document.getElementById("priceRangeInput");

        priceInput.onchange = function() {
            formatPriceCurrency(priceInput.value)
        }

        priceInput.oninput = function() {
            formatPriceCurrency(priceInput.value)
        }
        
        function formatPriceCurrency(price) {
            // Format number as currency
            let formattedValue = new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(price);
            document.getElementById('priceRangeOutput').innerHTML = formattedValue;
            // priceOutput.innerHTML = formattedValue;
        }

        formatPriceCurrency(priceInput.value);

        // Star Rating Functionality
        document.querySelectorAll('.star-rating input').forEach(star => {
            star.addEventListener('change', () => {
                const rating = star.value;
                showToast("star", `You rated this for ${rating} stars.`, "warning");
            });
        });
 
        // Modal
        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }

        function openModal(id) {
            document.getElementById(id).style.display = "block";
        }



        const toggleGridBtn = document.getElementById("toggleGridView");
        const toggleListBtn = document.getElementById("toggleListView");
        const roomGrid = document.querySelector(".room-grid");
        const roomCard = document.getElementsByClassName("room-card");

        // Toggle View
        function setGridView() {
            var i;

            for (i = 0; i < roomCard.length; i++) {
                roomCard[i].classList.remove("list-view");
            }

            roomGrid.classList.remove("list-view");
            toggleGridBtn.classList.add("toggle-active");
            toggleListBtn.classList.remove("toggle-active");
        }
        
        function setListView() {
            var i;
            roomGrid.classList.add("list-view");

            for (i = 0; i < roomCard.length; i++) {
                roomCard[i].classList.add("list-view");
            }

            toggleGridBtn.classList.remove("toggle-active");
            toggleListBtn.classList.add("toggle-active");
        }

        // Set default view
        setListView();

        <?php } ?>


        // Detect if there is an Internet connection. If there is, show the map plugin. Otherwise, hide the map plugin.
               // Check internet
        window.addEventListener('load', function() {
            function updateStatus() {
                if (!navigator.onLine) {
                    window.location.href = "/bookingapp/no_internet_connection.php";
                }
            }

            updateStatus();

            window.addEventListener('online', updateStatus);
            window.addEventListener('offline', updateStatus);

        });
        

        // Function to show the snackbar
        // Toast notification functionalities
        let toastBox = document.getElementById("toastBox");

        function showToast(icon, message, type) {
            let toast = document.createElement('div');
            toast.classList.add('toast');
            toast.innerHTML = "<i class='fa-solid fa-" + icon + "'></i> " + message;
            toastBox.appendChild(toast);

            toast.classList.add(type);

            toast.addEventListener("click", () => {
                toast.remove();
            });

            setTimeout(() => {
                toast.remove();
            }, 5000);
        }
        
        <?php if ($rowCount > 0) { ?>
            const sortSelect = document.getElementById('sort-select');

            sortSelect.addEventListener('change', (e) => {
                const selectedValue = e.target.value;
                window.location.href = `${window.location.pathname}?order-type=${selectedValue}`;
            });
        <?php } ?>

    </script>

</body>
</html>