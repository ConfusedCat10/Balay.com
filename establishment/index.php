<?php
include "../database/database.php";

session_start();



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

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$orderType = isset($_GET['sort']) ? $_GET['sort'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// // Adding sorting
// switch($orderType) {
//     case 'cheapest':
//         $sql .= " ORDER BY CheapestRoomPrice ASC";
//         break;

//     case 'highest_price':
//         $sql .= " ORDER BY ExpensiveRoomPrice DESC";
//         break;
    
//     case 'top_rated':
//         $sql .= " ORDER BY AverageRating DESC";
//         break;

//     case 'low_rated':
//         $sql .= " ORDER BY AverageRating ASC";
//         break;

//     case 'most_booked':
//         $sql .= " ORDER BY NumberOfBookings DESC";
//         break;

//     case 'most_vacant':
//         $sql .= " ORDER BY TotalVacantSpaces DESC";
//         break;

//     default:
//         $sql .= " ORDER BY AverageRating DESC";
// }

// echo generateStars(3.5);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Establishments</title>

    <?php
    include "../php/head_tag.php";

    if (!$isLoggedIn || $accountRole === 'tenant') {
        header("Location: /bookingapp/page_not_found.php");
    }

    // Get establishments

    $isUserOwner = isset($_SESSION['owner']) ? true : false;
    $ownerID = $accountRole === 'owner' ? $_SESSION['owner']['OwnerID'] : 0;

    if ($accountRole === 'owner') {
        // Get owner information
        $ownerID = $_SESSION['owner']['OwnerID'];

        // Get establishments owned by owner
        $sql = "SELECT * FROM establishment WHERE OwnerID = $ownerID AND Status != 'removed'";


    } else if ($accountRole === 'admin') {
        $sql = "SELECT * FROM establishment WHERE Status != 'removed'";
    }

    if (!empty($search)) {
        $sql .= " AND (Name LIKE '%$search%' OR Type LIKE '%$search%')";
    }
        
    // Pagination
    $itemsPerPage = 10;
    $offset = ($page - 1) * $itemsPerPage;
    $sql .= " LIMIT $offset, $itemsPerPage";


    $establishmentResult = mysqli_query($conn, $sql);


    $countSql = "SELECT COUNT(EstablishmentID) AS totalItems FROM establishment WHERE Status != 'removed'";

    if ($accountRole === 'owner') {
        $countSql .= " AND OwnerID = $ownerID";
    }

    if (!empty($search)) {
        $countSql .= " AND (Name LIKE '%$search%' OR Type LIKE '%$search%')";
    }

    $countResult = mysqli_query($conn, $countSql);
    $totalItems = mysqli_fetch_assoc($countResult)['totalItems'];
    $totalPages = ceil($totalItems / $itemsPerPage);

    ?>
    <!-- <link rel="stylesheet" href="/bookingapp/css/w3.css"> -->

    <link rel="stylesheet" href="/bookingapp/css/profile.css">
    <link href="/bookingapp/css/bootstrap.css" rel="stylesheet">

    <style>
        .form-inline {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 3px;
        }

        .container {
            margin-top: 25px;
            margin-bottom: 25px;
            padding-bottom: 50px;
            display: block;
            height: auto;        
        }

        nav {
            display: flex !important;
        }

        .balay-nav-links a:hover {
            text-decoration: none !important;
        }

        .card {
            padding: 10px;
            width: 100%;
        }
        
        input, select {
            width: 100%;
        }

        .modal-content {
            width: 40%;
            position: relative;
            display: flex;
            margin: auto;
            border-radius: 8px
        }

        .modal-content h3 {
            font-size: 24px;
            font-weight: bold;
        }

        .modal-content .close {
            position: absolute;
            top: 20px;
            right: 25px;
            margin-left: 10px;
        }

        .modal {
            justify-content: center;
        }

        .toggle-password {
            position: absolute;
            right: 20px;
            top: 11px;
            cursor: pointer;
        }

        .edit-btn {
            float: right;
            background-color: #ffd700;
            color: black;
            border: none;
            outline: none;
            border-radius: 5px;
        }

        .edit-btn:hover {
            background-color: maroon;
            color: white;
        }

        #search-form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            width: 100%;
        }

        #search-form input[type='text'] {
            width: 50%;
        }

        #search-form select {
            width: 20%;
            padding: 5px;
        }

        #search-form button {
            border-radius: 5px;
            padding: 10px;
            background-color: maroon;
            color: white;
            border: none;
            outline: none;
        }

        #search-form button:hover {
            background-color: #ffd700;
            color: black;
        }



        #search-form input, #search-form select {
            padding: 10px;
            border-radius: 20px;
        }

        .content {
            display: flex;
            justify-content: flex-start;
            flex-direction: row;
            flex-wrap: wrap;
        }
       
        /* Pagination links */
        .pagination {
            margin-top: 40px;
            float: right;
            width: 100%;
            justify-content: flex-end;
        }

        .pagination a {
            color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color .3s;
        }
        
        /* Style the active/current link */
        .pagination a.page-active {
            background-color: maroon;
            color: white;
        }
        
        /* Add a grey background color on mouse-over */
        .pagination a:hover:not(.page-active) {
            background-color: #ddd;
        }

        .pagination a.page-active:hover {
            background-color: #ffd700;
            color: black;
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
            display: flex;
            align-items: center;
            gap: 10px;
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

        .section-settings {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }


        /* Room Container */
        .room-grid {
            margin-top: 4rem;
            display: grid;
            gap: 2rem;
        }

        .room-list {
            display: block;
            margin: 0;
        }

        .room-card {
            overflow: hidden;
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            
        }

        .room-card-image {
            position: relative;
            isolation: isolate;
        }

        .room-card-image img {
            height: 200px;
            object-fit: cover;
        }

        .room-card-icons {
            position: absolute;
            right: 1rem;
            bottom: 1rem;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 1rem;
            z-index: 1;
        }

        .room-card-icons span {
            display: inline-block;
            padding: 2px 8px;
            font-size: 1rem;
            background-color: var(--white);
            border-radius: 100%;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .room-card-icons span:nth-child(1) {
            color: #f472b6;
        }

        .room-card-icons span:nth-child(2) {
            color: #c084fc;
        }

        .room-card-icons span:nth-child(3) {
            color: #60a5fa;
        }

        .room-price {
            font-size: 12px;
            position: absolute;
            left: 1rem;
            bottom: 1rem;
            z-index: 1;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .room-card-details {
            padding: 1rem;
        }

        .room-card h4 {
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
            font-weight: 500;
            color: var(--dark);
        }

        .room-card p {
            margin-bottom: 0.5rem;
            color: black;
            font-size: 12px;
        }

        .room-card-details p span {
            margin-right: 3px;
        }

        .room-card h5 {
            margin-bottom: 1rem;
            font-size: 1rem;
            font-weight: 500;
            color: var(--light);
        }

        .room-card h5 span {
            font-size: 1.1rem;
            color: var(--dark);
        }

        .book-now-btn {
            background-color: var(--primary-color);
            color: white;
            float: right;
        }

        .book-now-btn:hover {
            background-color: var(--secondary-color);
            color: black;
        }

        .room-score {
            padding: 5px;
            background-color: #ffd700;
            color: black;
        }

        .room-review {
            font-size: 10px;
            position: absolute;
            top: 1rem;
            right: 1rem;
        }

        .room-review:not(span) {
            color: white;
        }

        .room-grid.list-view {
            display: block;
        }

        .room-card.list-view {
            display: grid;
            grid-template-columns: repeat(1, fr);
            margin-bottom: 20px;
        }

        .room-card.list-view .room-card-image img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            z-index: 200;
            min-width: 150px;
            padding: 10px 0;
            overflow: hidden;
        }

        .dropdown-menu a {
            padding: 10px 15px;
            display: block;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }

        .dropdown-menu a:hover {
            background-color: #f0f0f0;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }

        .section-container {
            max-width: 100%;
            margin: 1px;
        }


        @media (max-width: 1000px) {
            .form-inline input, .form-inline select {
                width: 100% !important;
            }

            .form-inline {
                display: block;
            }
            nav {
                flex-direction: column;
            }
        }

        /* Media Queries for Responsive Design */
            @media (width > 576px) {
                .room-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (width > 768px) {
                nav {
                    padding: 2rem 1rem;
                    position: static;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }

                .balay-navbar {
                    padding: 0;
                }

                .balay-nav-menu-btn {
                    display: none;
                }

                .balay-nav-links {
                    padding: 0;
                    width: unset;
                    position: static;
                    transform: none;
                    flex-direction: row;
                    background-color: transparent;
                }

                .nav-btn {
                    display: block;
                }

                .balay--nav-links a::after {
                    position: absolute;
                    content: "";
                    left: 0;
                    bottom: 0;
                    height: 2px;
                    width: 0;
                    background-color: var(--primary-color);
                    transition: 0.3s;
                    transform-origin: left;
                }

                .balay-nav-links a:hover::after {
                    width: 100%;
                }

                .room-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

            }

            @media (width > 1024px) {
                .room-grid {
                    gap: 2rem;
                    grid-template-columns: repeat(3, 1fr);
                }
            }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <?php include "../php/header.php"; ?>
    </header>

    <div class="container clearfix">
        <div class="card overflow-hidden">
            <div class="content">
                <div class="section-container" style="width: 100%">
                    <h2 class="section-header" style="float: left;">Dormitories and Cottages</h2>
                    <?php if ($accountRole === 'owner') { ?>
                        <button class="btn btn-primary" style="float: right" onclick="redirect('add.php')"><i class="fa-solid fa-building"></i> Add an Establishment</button>
                    <?php } ?>
                </div>

                <?php if (mysqli_num_rows($establishmentResult) > 0) { ?> 
                <form id="search-form" class="section-container" method="GET">
                    <input type="hidden" name="page" value="<?php echo isset($page) ? $page : 1; ?>">
                    <input type="text" name="search" placeholder="Search establishments..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" />

                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>

                <div class="section-container section-settings">

                    <div class="list-grid-switcher" style="align-items: center; gap: 10px;">
                        <span>View:</span>
                        <button class="toggle-btn list-mode" title="Select to toggle list view mode." id="toggleListView" onclick="setListView();"><i class="fa-solid fa-list"></i></button>
                        <button class="toggle-btn grid-mode toggle-active" title="Select to toggle grid view mode." id="toggleGridView" onclick="setGridView();"><i class="class fa-solid fa-grip"></i></button>
                    </div>
                </div>

                <div class="section-container room-container">

                    

                    <div class="room-grid">
                        <?php while ($row = mysqli_fetch_assoc($establishmentResult)) {
                                $establishmentID = $row['EstablishmentID'];
                                $encryptedEstID = base64_encode($establishmentID);
                                $thisOwnerID = $row['OwnerID'];

                                $name = $row['Name'];
                                $type = $row['Type'];

                                $genderInclusiveness = $row['GenderInclusiveness'];
                                
                                $description = $row['Description'];
                                $description = str_replace("\\", "", $description);

                                
                                // // Backend Section
                                // function getLocationDetails($lat, $lng) {
                                //     $url = "https://nominatim.openstreetmap.org/ui/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18";

                                //     $ch = curl_init();
                                //     curl_setopt($ch, CURLOPT_URL, $url);
                                //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                //     curl_setopt($ch, CURLOPT_USERAGENT, 'Balay.com'); // Add a user agent
                                //     $response = curl_exec($ch);
                                //     curl_close($ch);

                                //     return json_decode($response, true);
                                // }


                                // Geo-Tag
                                $geoSql = "SELECT * FROM geo_tags WHERE EstablishmentID = $establishmentID";
                                $geoResult = mysqli_query($conn, $geoSql);

                                // $location = "Location not found.";
                                if (mysqli_num_rows($geoResult) > 0) {
                                    $geoTag = mysqli_fetch_assoc($geoResult);

                                    $latitude = $geoTag["Latitude"];
                                    $longitude = $geoTag["Longitude"];
                                    // $location = getLocationDetails($latitude, $longitude);
                                }

                                // Reviews
                                $rating = "";
                                $totalReviews = 0;
                                $totalScore = 0;
                                $maxScoreReview = 80; // Number of categories is 8. Adjust this. 8 * 10 points

                                $reviewSql = "SELECT COUNT(rv.ReviewID) AS TotalReview, AVG(rv.StaffScore) AS StaffScore, AVG(rv.FacilitiesScore) AS FacilitiesScore, AVG(rv.CleanlinessScore) AS CleanlinessScore, AVG(rv.ComfortScore) AS ComfortScore, AVG(rv.SignalScore) AS SignalScore, AVG(rv.LocationScore)  AS LocationScore, (rv.MoneyValueScore) AS MoneyValueScore, (AVG(rv.StaffScore + rv.FacilitiesScore + rv.CleanlinessScore + rv.ComfortScore + rv.SignalScore + rv.LocationScore + rv.MoneyValueScore + rv.SecurityScore) / 8) AS OverallScore FROM rooms r JOIN reviews rv ON r.RoomID = rv.RoomID WHERE r.EstablishmentID = $establishmentID";
                                $reviewResult = mysqli_query($conn, $reviewSql);

                                if (mysqli_num_rows($reviewResult) > 0) {
                                    while ($row = mysqli_fetch_assoc($reviewResult)) {
                                        $totalReviews = $row['TotalReview'];
                                        $totalScore = $row['OverallScore'];
                                    }
                                }
                                
                                // Calculate average score
                                $averageScore = $totalReviews > 0 ? $totalScore / ($totalReviews * 8) : 0;

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
                                    $featuredPhoto = isset($featuredPhoto) && $featuredPhoto !== '' ? $featuredPhoto : "/bookingapp/assets/images/msu-facade.jpg"; 
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
                                    <!-- <div class="room-card-icons">
                                        <span title="Add to favorites"><i class="fa-solid fa-heart"></i></span>
                                        <span title="View Map" onclick="redirect('#')"><i class="fa-solid fa-location-pin"></i></span>
                                        <div class="card-dropdown">
                                            <span title="Show Options"><i class="fa-solid fa-caret-down"></i></span>
                                            <div class="card-dropdown-menu">
                                                <a href="establishment.php?est=<?php echo $encryptedEstID; ?>#availability">Check-in</a>
                                            </div>
                                        </div>
                                    </div> -->
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
                                        <?php echo generateStars(4.5); ?>
                                    </p>
                                    <p>
                                        <!-- <span><i class="fa-solid fa-location-pin"></i> <?php echo $location; ?></span><br> -->
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
                                    <button class="book-now-btn btn" onclick="redirect('establishment.php?est=<?php echo $encryptedEstID; ?>')">
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
                    <div id="pagination" class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?search=<?= htmlspecialchars($search) ?>&page=<?= $i ?>" <?php echo $page === $i ? 'class="page-active"' : ''; ?>>
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php } ?>

                <?php } else {
                    echo "<div class='section-container' style='margin: auto'>";
                    echo "<h3>No establishments yet.</h3>";
                    echo "</div>";
                }?>
            </div>
        </div>
        
    </div>
    
    <div id="toastBox"></div>

    <!-- Footer -->
    <?php include "../php/footer.php"; ?>



    <!-- <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script> -->
    <script src="/bookingapp/js/jquery-1.10.2.min.js"></script>
    <script src="/bookingapp/js/bootstrap.bundle.min.js"></script>
    <script src="/bookingapp/js/script.js"></script>
    <script src="/bookingapp/js/scrollreveal.js "></script>
    <script src="/bookingapp/js/rayal.js"></script>
    <script type="text/javascript"></script>

    <script>

        // Check internet
        const offlinePageUrl = "/bookingapp/no_internet_connection.php";

        // Function to handle internet connection status
        function checkInternetConnection() {
            if (!navigator.onLine) {
                // Save the current page URL to localStorage
                localStorage.setItem("lastVisitedPage", window.location.href);
                // Redirect to the "No Internet Connection" page
                window.location.href = offlinePageUrl;
            }
        }

        // Add event listeners for network status changes
        window.addEventListener("online", () => {
        // Redirect back to the last visited page if it exists
            const lastVisitedPage = localStorage.getItem("lastVisitedPage");
            if (lastVisitedPage && lastVisitedPage !== offlinePageUrl) {
                localStorage.removeItem("lastVisitedPage"); // Clear the stored page
                window.location.href = lastVisitedPage; // Redirect back
            }
        });

        // Set the current year in the footer dynamically
        document.getElementById('year').textContent = new Date().getFullYear();

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

        // Modal functionalities
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

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


    </script>
</body>
</html>