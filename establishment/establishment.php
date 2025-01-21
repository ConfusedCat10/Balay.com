<?php
// header('Content-Type: application/json');
include "../database/database.php";

session_start();

$estID = 0;
// $currentEstablishment = null;

// echo isset($_POST['roomID']) ? $_POST['roomID'] : 0;

$editRoomError = '';

$establishment = null;

$roomPage = isset($_GET['roomPage']) ? $_GET['roomPage'] : 1;


if (isset($_GET['est']) && $_GET['est'] !== null) {
    $estID = base64_decode($_GET['est']);

    $sql = "SELECT * FROM establishment WHERE EstablishmentID = $estID AND Status != 'removed'";
    $result = mysqli_query($conn, $sql);

    echo mysqli_error($conn);

    if (mysqli_num_rows($result) > 0) {
        $establishment = mysqli_fetch_assoc($result);
    } else {
        header("Location: /bookingapp/page-not-found.php");
    }
    
} else {
    header("Location: /bookingapp/page-not-found.php");
}

if ($establishment['Status'] === 'removed') {
    header("Location: /bookingapp/index.php");
}


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
    $emptyStar = '';
    $stars = '';

    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rating ? $filledStar : $emptyStar;
    }

    return $stars;
}

// Replace bad words with asterisks
function censorBadWords($string) {
    $badWords = ['shit', 'fuck', 'damn', 'whore', 'asshole', 'dick', 'pussy'];
    foreach ($badWords as $word) {
        $censoredWord = str_repeat('*', strlen($word));
        $string = str_ireplace($word, $censoredWord, $string);
    }
    return $string;
}

// Reviews
$rating = '';
$totalReviews = 0;
$totalScore = 0;
$maxScoreReview = 80; // Number of categories is 8. Adjust this. 8 * 10 points

$staffRating = 0;
$facilitiesRating = 0;
$cleanlinessRating = 0;
$comfortRating = 0;
$signalRating = 0;
$moneyValueRating = 0;
$locationRating = 0;
$securityRating = 0;

$reviewSql = "SELECT COUNT(rv.ReviewID) AS TotalReview, AVG(rv.StaffScore) AS StaffScore, AVG(rv.FacilitiesScore) AS FacilitiesScore, AVG(rv.CleanlinessScore) AS CleanlinessScore, AVG(rv.ComfortScore) AS ComfortScore, AVG(rv.SignalScore) AS SignalScore, AVG(rv.LocationScore) AS LocationScore, AVG(rv.MoneyValueScore) AS MoneyValueScore, AVG(rv.SecurityScore) AS SecurityScore, (AVG(rv.StaffScore + rv.FacilitiesScore + rv.CleanlinessScore + rv.ComfortScore + rv.SignalScore + rv.LocationScore + rv.MoneyValueScore + rv.SecurityScore) / 8) AS OverallScore FROM rooms r JOIN reviews rv ON r.RoomID = rv.RoomID WHERE r.EstablishmentID = $estID";
$reviewResult = mysqli_query($conn, $reviewSql);

if (mysqli_num_rows($reviewResult) > 0) {
    while ($row = mysqli_fetch_assoc($reviewResult)) {
        $totalReviews = $row['TotalReview'];
        $totalScore = $row['OverallScore'];

        $staffRating = $row['StaffScore'];
        $facilitiesRating = $row['FacilitiesScore'];
        $cleanlinessRating = $row['CleanlinessScore'];
        $comfortRating = $row['ComfortScore'];
        $signalRating = $row['SignalScore'];
        $moneyValueRating = $row['MoneyValueScore'];
        $locationRating = $row['LocationScore'];
        $securityRating = $row['SecurityScore'];
    }
}

// Calculate average score
$averageScore = $totalReviews > 0 ? $totalScore / ($totalReviews * 8) : 0;

// Convert average score to a 5-star rating system
$starRating = round(($averageScore / 10) * 5);

$remark = getRemark($averageScore);

// Fetch establishments to the map

// Get coordinates

$currentEstablishment = array();

$sql = "SELECT g.GeoTagID, g.Latitude, g.Longitude, e.Name FROM geo_tags g INNER JOIN establishment e ON e.EstablishmentID = g.EstablishmentID WHERE e.EstablishmentID = $estID";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    $currentEstablishment = array(
        'geoTagID' => $row['GeoTagID'],
        'latitude' => $row['Latitude'] ?? '8.001389',
        'longitude' => $row['Longitude'] ?? '124.265278',
        'name' => $row['Name']
    );
    // echo json_encode($data);
} else {
    // Default to MSU Coordinates
    $data = array(
        'geoTagID' => '',
        'name' => '',
        'latitude' => '8.001389',
        'longitude' => '124.265278'
    );
    // echo json_encode($data);
}

$establishments = array();

$sql = "SELECT m.GeoTagID, e.Name, m.Latitude, m.Longitude FROM establishment e INNER JOIN geo_tags m ON e.EstablishmentID = m.EstablishmentID WHERE e.Status != 'removed'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    $establishments = [
        'geoTagID' => $row['GeoTagID'],
        'latitude' => $row['Latitude'],
        'longitude' => $row['Longitude'],
        'name' => $row['Name']
    ];

}

$addRoomError = "";

function numberToOrdinal($number) {
    if (!is_numeric($number)) {
        throw new InvalidArgumentException("Input must be a number.");
    }

    if ($number < 0) {
        return '-' . numberToOrdinal(-$number);
    }

    $suffixes = ['th', 'st', 'nd', 'rd'];

    $lastDigit = $number % 10;
    $lastTwoDigits = $number % 100;

    if ($lastTwoDigits >= 11 && $lastTwoDigits <= 13) {
        return $number . 'th';
    }

    if ($lastDigit === 1) {
        return $number . 'st';
    }

    if ($lastDigit === 2) {
        return $number . 'nd';
    }

    if ($lastDigit === 3) {
        return $number . 'rd';
    }

    return $number . 'th';
}

function checkAmenity($amenityID, $conn, $estID) {
    $sql = "SELECT * FROM establishment_features WHERE FeatureID = $amenityID AND EstablishmentID = $estID";
    $result = mysqli_query($conn, $sql);


    return mysqli_num_rows($result) > 0;
}

$houseRulesError = "";

// Save House Rules
if (isset($_POST['updateHouseRules'])) {
    $houseRules = mysqli_real_escape_string($conn, $_POST['house-rules'] ?? null);

    try {
        $sql = "UPDATE establishment SET HouseRules = ? WHERE EstablishmentID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception("Prepared statement error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'si', $houseRules, $estID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
        }

        $encryptedEstID = base64_encode($estID);
        header("Location: establishment.php?est=$encryptedEstID#houserules");
    } catch (Exception $e) {
        $houseRulesError = $e->getMessage();
    }
}

// Reset House Rules
if (isset($_POST['resetHouseRules'])) {
    $houseRules = mysqli_real_escape_string($conn, null);
    try {
        $sql = "UPDATE establishment SET HouseRules = ? WHERE EstablishmentID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception("Prepared statement error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'si', $houseRules, $estID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
        }

        $encryptedEstID = base64_encode($estID);
        header("Location: establishment.php?est=$encryptedEstID");
    } catch (Exception $e) {
        $houseRulesError = $e->getMessage();
    }
}

// Putting a marker
if (isset($_POST['save-location'])) {
    $latitude = $_POST['latitude'] ?? 7.99740;
    $longitude = $_POST['longitude'] ?? 124.26044; // Default MSU Location

    try {
        $sql = "INSERT INTO geo_tags (EstablishmentID, Latitude, Longitude) VALUES ($estID, $latitude, $longitude) ON DUPLICATE KEY
        UPDATE Latitude = $latitude, Longitude = $longitude";
        $query = mysqli_query($conn, $sql);

        $encryptedEstID = base64_encode($estID);
        header("Location: establishment.php?est=$encryptedEstID");
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

$resID = isset($_POST['residency-id']) ? $_POST['residency-id'] : '';

if (isset($_POST['approve-reservation'])) {
    toggleReservation($conn, $resID, 'confirmed', $estID);
}

if (isset($_POST['reject-reservation'])) {
    toggleReservation($conn, $resID, 'rejected', $estID);
}

if (isset($_POST['cancel-reservation'])) {
    toggleReservation($conn, $resID, 'cancelled', $estID);
}

if (isset($_POST['end-residency'])) {
    toggleReservation($conn, $resID, 'residency ended', $estID);
}

if (isset($_POST['renew-residency'])) {
    toggleReservation($conn, $resID, 'currently residing', $estID);
}

function toggleReservation($conn, $residencyID, $action, $estID) {
    try {
        $sql = "UPDATE residency SET Status = ? WHERE ResidencyID = ?";

        switch ($action) {
            case 'residency ended':
                $sql = "UPDATE residency SET DateOfExit = CURRENT_TIMESTAMP(), Status = ? WHERE ResidencyID = ?";
                break;
            
            case 'currently residing':
                $sql = "UPDATE residency SET DateOfExit = NULL, Status = ? WHERE ResidencyID = ?";
                break;
            
            default: {
                $sql = "UPDATE residency SET Status = ? WHERE ResidencyID = ?";
            }
        }

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "si", $action, $residencyID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_stmt_error($stmt));
        }

        $rtf = isset($_GET['rtf']) ? $_GET['rtf'] : '';
        $res_st = isset($_GET['res_st']) ? $_GET['res_st'] : '';
        $res_st = urldecode($res_st);
        $res_st = str_replace("+", " ", $res_st);

        $encryptedEstID = base64_encode($estID);
        header("Location: establishment.php?est=$encryptedEstID&rtf=$rtf&res_st=$res_st#tenants");
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $establishment['Name']; ?></title>

    <?php include "../php/head_tag.php";

    $isUserAdmin = false;

    if (isset($_SESSION['admin'])) {
        $isUserAdmin = true;
    }
    
    $isUserOwner = false;

    if (isset($_SESSION['owner']) && $establishment['OwnerID'] === $_SESSION['owner']['OwnerID']) {
        $isUserOwner = true;
    }

    $isUserTenant = false;

    if (isset($_SESSION['tenant'])) {
        $isUserTenant = true;
    }

    $isUserResident = false;
    $tenantID = $_SESSION['tenant']['TenantID'] ?? 0;

    $residency = array();
    // $residencyID = 0;

    $sql = "SELECT rs.ResidencyID, rs.DateOfEntry, rs.DateOfExit, rs.Status, rs.CreatedAt, r.RoomID, e.Type AS EstablishmentType FROM residency rs INNER JOIN rooms r ON r.RoomID = rs.RoomID INNER JOIN establishment e ON e.EstablishmentID = r.EstablishmentID WHERE rs.TenantID = $tenantID AND r.EstablishmentID = $estID";
    $result = mysqli_query($conn, $sql);

    echo mysqli_error($conn);

    if (mysqli_num_rows($result) > 0) {
        $residency = mysqli_fetch_assoc($result);

        $isUserResident = true;
    }


    // echo "Same type = $sameResidencyEstType";
    ?>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin="" >

      <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

    <style>
        

        #map-preview {
            height: 300px;
            border: 1px solid black;
            width: 100%;
            z-index: 1;
        }

        .map-editing {
            display: none;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .map-editing .btn-group {
            float: right;
        }

        .modal-content {
            width: 80%;
            height: 80%;
            margin: auto;
            margin-top: 40px;
        }

        .search-map {
            width: 100%;
        }
    
        .search-map button {
            width: auto;
        }

        .header-container {

            .search-map {
                width: 100%;
            }
            padding-block: 0rem 5rem;
        }

        .container {
            display: block;
            padding: 30px;
            max-width: var(--max-width);
            margin: auto;
            height: auto;
        }

        .page-nav {
            margin: auto;
            /* max-width: var(--max-width); */
            border-bottom: 1px solid black;
            z-index: 100;
        }

        .page-nav button {
            padding: 10px 24px;
            float: left;
            color: black;
            background-color: white;
            border: 1px solid rgba(0,0,0,0.1);
            outline: none;
            cursor: pointer;
        }

        .page-nav:after {
            content: "";
            clear: both;
            display: table;
        }

        .page-nav button:not(:last-child) {
            border-right: none;
        }

        .page-nav button:hover {
            background-color: #ccc;
        }

        .sticky-nav {
            position: fixed;
            top: 0;
            width: 100%;
            margin: auto;
        }

        .sticky-nav + .container {
            padding-top: 102px;
        }

        .btn {
            padding: 10px;
            cursor: pointer;
        }

        .special-btn {
            border: none;
            outline: none;
            color: maroon;
            background: transparent;
        }

        .btn-primary {
            background-color: maroon;
            color: white;
        }

        .btn-primary:hover {
            background-color: #ffd700;
            color: black;
        }

        .btn-secondary {
            background-color: #ffd700 !important;
            color: black !important;
        }

        .btn-secondary:hover {
            background-color: grey !important;
            color: white !important;
        }

        .special-btn:hover {
            outline: 2px solid maroon;
            background: transparent;
            color: maroon;
        }

        .acc-row {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        /* Image Gallery */
        .gallery-container {
            position: relative;
            box-shadow: var(--box-shadow);
            border-radius: 20px;
            width: 100%;
        }

        /* Hide the images by default */
        .image-item {
            display: none;
        }

        .image-item img {
            height: 360px;
            object-fit: cover;
            object-position: center;
        }

        .cursor {
            cursor: pointer;
        }

        .prev-image, .next-image {
            cursor: pointer;
            position: absolute;
            top: 40%;
            width: auto;
            padding: 16px;
            margin-top: -50px;
            color: white;
            font-weight: bold;
            font-size: 20px;
            border-radius: 0 3px 3px 0;
            user-select: none;
            -webkit-user-select: none;
        }

        .next-image {
            right: 0;
            border-radius: 3px 0 0 3px;
        }

        .prev-image:hover, .next-image:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .numbertext {
            color: #f2f2f2;
            font-size: 12px;
            padding: 8px 12px;
            position: absolute;
            top: 0;
        }

        .gallery-caption-container {
            text-align: center;
            color: black;
            padding: 2px 16px;
        }

        .image-row:after {
            content: "";
            display: table;
            clear: both;
        }

        .image-column {
            float: left;
            width: 16.66%;
        }

        .image-column img {
            height: 100px;
            object-fit: cover;
        }

        .demo {
            opacity: 0.6;
        }

        .selected-image, .demo:hover {
            opacity: 1;
        }

        .rating-col {
            text-align: left;
            padding: 10px;
        }

        .rating-col span.score   {
            text-align: center;
            background-color: maroon;
            color: white;
            padding: 10px;
            border-radius: 15px;
        }

        .score {
            text-align: center;
            background-color: maroon;
            color: white;
            padding: 10px;
            border-radius: 15px;
        }

        .chip-group {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
            gap: 1rem;
        }

        .clearfix:after, .clearfix:before {
            content: "";
            display: table;
            clear: both;
        }

        .chip {
            display: inline-block;
            padding: 0 25px;
            height: 50px;
            font-size: 16px;
            line-height: 50px;
            border-radius: 10px;
            background-color: #f1f1f1;
            cursor: pointer;
        }

        .chip.added:hover {
            cursor: pointer;
            background-color: #ffd700;
            color: black;
        }

        .chip:hover {
            border: 1px solid maroon;
        }

        .chip.added {
            background-color: maroon;
            color: white;
        }

        .container-section {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            padding-bottom: 50px;
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

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: flex-start;
            align-items: center;
            margin-top: 30px;
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

        .room-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        /* Availability status */
        .room-availability {
            background-color: #00552b;
            color: white;
            font-size: 10px;
            padding: 5px;
            border-radius: 25px;
            float: right;
        }

        /* Table */
        table {
            padding: 10px;
            border: 1px solid rgba(0, 0, 0, 0.5);
            border-radius: 25px;
        }

        .thead {
            padding-right: 10px;
            font-weight: bold;
        }

        td, tr {
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding: 5px;
        }

        td ul li {
            font-size: 12px;
        }

        /* Category progress bars */

        .category {
            padding: 10px;
            border: 1px solid rgba(0,0,0,0.4);
            border-radius: 10px;
        }

        .category-ratings {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 20px;
        }

        .category-label {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }

        .category-progress {
            width: 100%;
            background-color: grey;
            border-radius: 25px;
        }

        .progress-bar {
            width: 0;
            height: 10px;
            background-color: #ffd700;
            border-radius: 25px;
        }

        .sort-container {
            float: right;
            font-size: 12px;
        }

        .sort-container select {
            padding: 5px 25px;
            margin-left: 5px;
            border-radius: 10px;
        }

        /* Review */
        .review-container {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            gap: 1rem;
            align-items: flex-start;
            justify-content: flex-start;
            white-space: nowrap;
            overflow: auto;
        }

        .review-card {
            max-width: 300px;
            min-height: 200px;
            height: auto;
            cursor: pointer;
        }

        .review-card:hover {
            outline: 1px solid maroon;
        }

        .reviewer-name strong {
            font-size: 12px;
        }

        .reviewer-name {
            line-height: 1rem;
        }

        .reviewer-profile-header {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding-bottom: 10px;
        }

        .review-content {
            font-size: 12px;
            text-align: left;
            padding: 10px;
        }

        .text-container {
            display: -webkit-box;
            /* -webkit-line-clamp: 4; */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: normal;
            position: relative;
            max-width: 400px;
            height: 72px;
        }

        .read-more {
            color: blue;
            cursor: pointer;
            position: relative;
            background-color: white;
        }

        .reviewer-name {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .expanded. .review-content {
            display: block;
            -webkit-line-clamp: unset;
        }

        .hidden {
            display: none;
        }
        
        .clearfix:after, .clearfix:before {
            content: "";
            display: table;
            clear: both;
        }

        .centered-block {
            position: absolute;
            top: 32%;
            left: 50%;
            transform: translate(-50%, 50%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .centered-block p {
            color: white;
            font-weight: bold;
        }

        .profile-card {
            width: 100%;
            max-width: 350px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .profile-header {
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .profile-pic {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
            border: 2px solid #007bff;
        }

        .profile-info {
            flex-grow: 1;
        }

        .profile-name {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .profile-role {
            font-size: 0.9rem;
            color: #555;
            margin: 0 !important;
        }

        .profile-details {
            padding: 10px;
            height: auto;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 15px;
            margin-top: 20px;
        }

        .profile-details p {
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .toggle-icon {
            font-size: 1.5rem;
            font-weight: bold;
            color: maroon;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.2s;
        }

        .profile-details.open {
            max-height: 500px;
            padding: 15px;
        }

        .profile-header .toggle-icon.open {
            transform: translateY(-50%) rotate(45deg);
        }

        .profile-actions {
            padding: 15px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: #fff;
            background-color: maroon;
            font-size: 0.9rem;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn:hover {
            background-color: #ffd700;
            color: black;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            align-items: c  enter;
        }

        .action-btns {
            margin-left: 20px;
            gap: 5px;
            color: grey;
            cursor: pointer;
        }

        .action-btns .remove:hover {
            color: red;
        }

        .action-btns .edit:hover {
            color: #ffd700;
        }

        fieldset {
            padding: 10px;
            height: 100%;
            border-radius: 10px;
        }

        .form-inline {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-items: center;
            gap: 20px;
            align-content: center;
            margin: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group input,
        .form-group select {
            padding: 10px;
            border-radius: 5px;
            border: none;
            border-bottom: 1px solid rgba(0,0,0,0.5);
            width: 300px;
        }

        .mandatory:after {
            content: "*";
            color: red;
        }

        .option-btns {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        
        .option-btn {
            border: 1px solid grey;
            padding: 3px;
            border-radius: 3px;
            cursor: pointer;
        }

        .option-btn.add:hover {
            background-color: maroon;
            color: white;
        }

        .option-btn.edit:hover {
            background-color: #ffd700;
            color: black;
        }

        .stats {
            text-align: center;
        }
        
        .stats h5 {
            margin-bottom: 5px;
        }

        /* Pagination links */
        .pagination {
            margin-top: 40px;
            margin-bottom: 40px;
            float: right;
            width: 100%;
            display: flex;
            flex-wrap: wrap;
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

        .room-card-image img {
            height: 317px !important;
        }

        @media (max-width: 1000px) {
            .acc-row {
                display: block;
            }
            .accommodation-title {
                margin-bottom: 10px;
                
            }
            .accommodation-header-btn-group {
                display: flex;
                flex-direction: row-reverse;
                justify-content: flex-end;
                flex-wrap: nowrap;
                gap: 2px;
            }
            .review-container {
                display: block;
            }
            .review-card {
                max-width: 100%;
            }

            .form-group input, .form-group select {
                width: 80%;
            }

            .form-group {
                margin-bottom: 10px;
                width: 100%;
            }

            .form-inline {
                display: flex;
                margin: auto;
                justify-content: center;
            }

            .modal-content {
                width: 100% !important;
                height: 70% !important;
                overflow: auto;
            }

            .form-inline {
                justify-content: flex-start !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <?php include "../php/header.php"; ?>        
        <!-- Search section -->
        <?php include "../php/search_section.php"; ?>
    </header>


    <!-- Breadcrumb -->
    <!-- <ul class="breadcrumb" style="margin-bottom: 20px;">
        <li><a href="#">Mindanao State University Main Campus</a></li>
        <li><a href="#">2nd Street</a></li>
        <li><a href="#">Near College of Information and Computing Sciences</a></li>
        <li>Rajah Indapatra Hall</li>
    </ul> -->

    <div class="page-nav" style="width: 100%;" id="sticky-nav">
        <button style="width: 20%" onclick="redirect('#overview');">Overview</button>
        <button style="width: 20%" onclick="redirect('#amenities');">Amenities</button>
        <button style="width: 20%" onclick="redirect('#availability');">Availability</button>
        <button style="width: 20%" onclick="redirect('#houserules');">House rules</button>
        <button style="width: 20%" onclick="redirect('#reviews');">Reviews</button>
    </div>

    <?php
    $location = null;
    $longitude = null;
    $latitude = null;

    $sql = "SELECT * FROM geo_tags WHERE EstablishmentID = $estID";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $location = mysqli_fetch_assoc($result);

        $longitude = $location['Longitude'];
        $latitude = $location['Latitude'];
    }

    // Backend Section
    function getLocationDetails($lat, $lng) {
        $url = "https://nominatim.openstreetmap.org/ui/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Balay.com'); // Add a user agent
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    $locationDetails = getLocationDetails($latitude, $longitude);

    $locationDescription = "Location details are not available.";
    $address = array();
    if (!empty($locationDetails)) {
        $address = $locationDetails['address'] ?? [];
        $locationDescription = "Located on " . ($address['road'] ?? 'an unnamed road') .
                       ", near " . ($address['suburb'] ?? 'an unnamed neighborhood') .
                       " in " . ($address['city'] ?? $address['town'] ?? $address['village'] ?? 'a locality') .
                       ". It is part of the " . ($address['state'] ?? 'region') .
                       ", " . ($address['country'] ?? 'country') . ".";
    }
    ?>

    <?php
    // Fetch all establishments
    $sql = "SELECT e.EstablishmentID, e.Name, g.Latitude, g.Longitude FROM geo_tags g INNER JOIN establishment e ON e.EstablishmentID = g.EstablishmentID";
    $result = mysqli_query($conn, $sql);

    $establishments = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $establishments[] = $row;
    }

    // echo json_encode($establishments);
    ?>

    <div class="container">

        <!-- Overview -->
        <div class="container-section" id="overview">
            <!-- Header -->
            <div class="acc-row accommodation-header">
                <div class="accommodation-title" style="width: 50%;">
                    <p class="section-subheader" style="text-transform: uppercase"><?php echo $establishment['Type']; ?></p>
                    <h2 class="section-header"><?php echo $establishment['Name']; ?></h2>
                    <p style="font-size: 14px;">
                        <i class="fa-solid fa-location-pin"></i> <span id="est-address">
                        </span> (<a style="color: blue; cursor: pointer" onclick="openModal('viewMapModal');">Show map</a>) <br>
                        <i class="fa-solid fa-venus-mars"></i> <?php echo $establishment['GenderInclusiveness'] ?> &middot;
                        <i class="fa-solid fa-stairs"></i>
                        <?php  echo $establishment['NoOfFloors'] . ' floors'; ?>
                    </p>
                </div>
                <?php if (!$isUserOwner) { ?>
                    <div class="accommodation-header-btn-group">
                        <!-- <button type="button" class="btn special-btn" title="Add to favorites"><i class="fa-solid fa-heart"></i></button> -->
                        <!-- <button type="button" class="btn special-btn" title="Share"><i class="fa-solid fa-share-nodes"></i></button> -->
                        <!-- <button class="btn" onclick="openModal('viewMapModal');"><i class="fa-solid fa-location-pin"></i> See on Map</button> -->
                    </div>
                <?php } else { ?>
                    <div class="accommodation-header-btn-group">
                        <button type="button" class="btn btn-primary" onclick="redirect('edit.php?est=<?php echo base64_encode($estID); ?>')"><i class="fa-solid fa-edit"></i> Edit establishment</button>
                        <button type="button" class="btn btn-secondary" onclick="redirect('#tenants')"><i class="fa-solid fa-users"></i> See tenants</button>
                    </div>
                <?php } ?>
                
            </div>

            <?php if ($isUserOwner) { ?>
            <button type="button" class="btn btn-primary" onclick="openModal('establishmentPhotosModal')"><i class="fa-solid fa-camera"></i> Manage photos</button>

            <button class="btn btn-secondary" id="edit-location-btn" onclick="allowMapEditing(); redirect('#map-preview')"><i class="fa-solid fa-location"></i> Change establishment's location</button>

            <?php } ?>

            <!-- Gallery -->
            <div class="acc-row">
                <div class="main-content" style=" justify-content: flex-start; height: auto; justify-items: flex-start;">

                <?php
                $images = [];
                $descriptions = [];
                $noPhotos = true;
                $sql = "SELECT * FROM establishment_photos WHERE EstablishmentID = $estID";
                $estPhotoResult = mysqli_query($conn, $sql);

                if (mysqli_num_rows($estPhotoResult) > 0) {
                    $noPhotos = false;

                    while ($row = mysqli_fetch_assoc($estPhotoResult)) {
                        for ($i = 1; $i <= 6; $i++) {
                            $images[] = isset($row['Photo' . $i]) && $row['Photo' . $i] !== '' ? "/bookingapp/establishment/" . $row['Photo' . $i] : "/bookingapp/assets/images/msu-facade.jpg";
                            $descriptions[] = isset($row['Description' . $i]) && $row['Description' . $i] !== '' ? $row['Description' . $i] : "No description";
                        }
                    }
                    mysqli_free_result($estPhotoResult); // Corrected variable name
                }

                ?>

                <?php if (!$noPhotos) { ?>
                    <div class="gallery-container">
                        <!-- Display uploaded photos -->
                        <?php foreach ($images as $key => $image) { ?>
                            <div class="image-item">
                                <div class="numbertext"><?php echo ($key + 1) . " / " . count($images); ?></div>
                                <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($descriptions[$key]); ?>" style="width: 100%; object-fit: cover">
                                <div class="gallery-caption-container">
                                    <p id="imageCaption"><?php echo $descriptions[$key]; ?></p>
                                </div>
                            </div>  
                        <?php } ?>

                        <a class="prev-image" onclick="plusSlides(-1)">&laquo;</a>
                        <a class="next-image" onclick="plusSlides(1)">&raquo;</a>

                        <div class="image-row">
                            <?php foreach ($images as $key => $image) { ?>
                                <div class="image-column">
                                    <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($descriptions[$key]); ?>" class="demo cursor" style="width: 100%" onclick="currentSlide(<?php echo ($key + 1); ?>)">
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                <?php } else { ?>

                        <div class="gallery-container" style="background-color: rgba(0, 0, 0, 0.2); width: 100%; height: 350px">
                            <div style="position: absolute;">
                            <div class="image-item" style="display: block">
                                    <img src="/bookingapp/assets/images/msu-facade.jpg" alt="MSU Facade" style="border-radius: 10px;">
                                    <div class="centered-block">
                                        <p><i class="fa-solid fa-image"></i> No images yet.</p>
                                        <?php if ($isUserOwner) { ?>
                                            <button class="btn btn-secondary" onclick="openModal('establishmentPhotosModal')"><i class="fa-solid fa-image"></i> Upload Images</button>
                                        <?php } ?>
                                    </div>
                            </div>
                            </div>
                        </div>

                    <?php } ?>    

                    <div class="panel container-section clearfix" style="margin-top: 20px; width: 100%; margin-left: 0; padding: 10px;">
                        <div class="map-editing">
                            <h3>Just point to where the establishment is located</h3>
                        </div>
                        <div id="map-preview"></div>
                        <div class="map-editing">
                            <form action="" method="post">
                                <div class="form-inline" style="font-size: 12px;">
                                    <div class="form-group">
                                        <label for="latitude">Latitude:</label>
                                        <input type="text" name="latitude" id="latitude" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="longitude">Longitude:</label>
                                        <input type="text" name="longitude" id="longitude" readonly>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-primary" name="save-location"><i class="fa-solid fa-floppy-disk"></i> Save location</button>
                                    <button type="button" class="btn btn-secondary" onclick="discardMapEditing()"><i class="fa-solid fa-xmark"></i> Discard</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    
                    <div class="panel container-section" style="margin-top: 20px; width: 100%; margin-left: 0; padding: 10px;">
                        <div class="section-head">
                                <div class="head-title">
                                    <h2>Description</h2>
                                </div>
                            </div>
                        <div class="accommodation-details main-content" style="margin-top: 30px; justify-items: left; width: 100%">
                        
                            <p style="text-align: left;">
                                <?php
                                $description = $establishment['Description'];
                                $description = str_replace('\\', '', $description);
                                $description = str_replace('\"', '', $description);
                                echo $description;
                                ?>
                            </p>

                            <div class="auto-description">
                                <?php echo $locationDescription; ?>
                            </div>
                        </div>
                    </div>

                    

                    <!-- Amenities -->
                    <div class="container-section" id="amenities" style="width: 100%; border: none;">
                        <?php if ($isUserOwner) { ?>
                                <button class="btn btn-primary" id="add-feature" onclick="openModal('addEstablishmentAmenityModal')" style="float: right; margin-left: 10px;"><i class="fa-solid fa-plus"></i> Manage amenities</button>
                            <?php } ?>
                        <div class="section-head">
                            <div class="head-title">
                                <h2>Amenities</h2>
                                <p>Most popular features and facilities</p>
                            </div>
                        </div>

                        <?php
                        $sql = "SELECT f.Name, f.Icon FROM establishment_features e INNER JOIN features f ON f.FeatureID = e.FeatureID WHERE EstablishmentID = $estID ORDER BY f.Icon";
                        $result = mysqli_query($conn, $sql);

                        ?>

                        <div class="chip-group clearfix">
                            <?php
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) { ?>
                            <span class="chip">
                                <i class="fa-solid fa-<?php echo $row['Icon']; ?>"></i> <?php echo $row['Name']; ?>
                            </span>                
                            <?php }
                            } else {
                                echo "<h3>No amenities featured yet.</h3>";
                            } ?>
                        </div>
                    </div>
                </div>

                <div class="sidebar">

                
                <?php
                    // Get the owner
                    $owner = null;
                    $sql = "SELECT p.ProfilePicture, CONCAT(p.FirstName, ' ', p.MiddleName, ' ', p.LastName, ' ', p.ExtName) AS FullName, o.PositionTitle, o.Institution, u.Username, p.HomeAddress, p.Gender, p.ContactNumber, u.EmailAddress FROM establishment_owner o INNER JOIN user_account u ON u.UserID = o.UserID INNER JOIN person p ON p.PersonID = u.PersonID WHERE o.OwnerID = " . $establishment['OwnerID'];
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        $owner = mysqli_fetch_assoc($result);

                        $profilePicture = $owner['ProfilePicture'];
                        $gender = $owner['Gender'];

                        if (empty($profilePicture)) {
                            $profilePicture = "/bookingapp/user/$gender-no-face.jpg";
                        }
                    }
                    ?>
                    
                    <div style="display: flex; flex-wrap: wrap; flex-direction: column; justify-content: flex-start; align-items: center; padding: 20px; margin-top: 0;">
                        <h3 style="margin-bottom: 5px">Owner</h3>
                        <div class="profile-card">  
                            <div class="profile-header">
                                <img src="<?php echo $profilePicture; ?>" alt="<?php echo $owner['FullName']; ?>" class="profile-pic">
                                <div class="profile-info">
                                    <h3 class="profile-name"><?php echo $owner['FullName']; ?></h3>
                                    <p class="profile-role">
                                        <?php
                                            echo '@' . $owner['Username'] . '<br>' .$owner['PositionTitle'] . '<br>' . $owner['Institution'];
                                        ?>
                                    </p>
                                </div>
                            </div>

                            <div class="profile-details">
                                <p><strong><i class="fa-solid fa-location-pin"></i></strong> <?php echo $owner['HomeAddress']; ?></p>
                                <p><strong><i class="fa-solid fa-phone"></i></strong> <?php echo $owner['ContactNumber']; ?></p>
                                <p><strong><i class="fa-solid fa-envelope"></i></strong> <?php echo $owner['EmailAddress']; ?></p>
                            </div>

                            <div class="profile-actions">
                                <button class="btn" onclick="redirect('/bookingapp/user/profile.php?id=<?php echo $owner['Username']; ?>')"><i class="fa-solid fa-eye"></i> View Profile</button>
                            </div>                    
                        </div>
                    </div>
        

                    <div class="panel" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="rating-col">
                            <span style="font-weight: bold;"><?php echo $remark; ?></span> <br>
                            <span><?php echo generateStars($starRating); ?></span><br>
                            <span style="font-size: 10px"><?php echo $totalReviews; ?> reviews</span>
                        </div>
                        <div class="rating-col">
                            <span class="score"><?php echo number_format($averageScore, 1); ?></span>
                        </div>
                    </div>

                    <div class="panel" style="display: flex; justify-content: space-between; align-items: center; padding: 20px">
                        <h3>Price</h3>
                        <p>
                            <?php
                            $paymentRate = 0;
                            $sql = "SELECT AVG(r.PaymentRate) AS PaymentRate FROM rooms r WHERE r.EstablishmentID = $estID AND r.Availability != 'Deleted' GROUP BY r.EstablishmentID";
                            $result = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                $row = mysqli_fetch_assoc($result);

                                $paymentRate = $row['PaymentRate'];
                            }

                            echo "₱" . number_format($paymentRate, '2', '.', ',');

                            ?>
                        </p>
                    </div>
<!--
                    <?php
                    $paySql = "SELECT epc.EPCID, pc.ChannelID, pc.ChannelName, epc.AccountNumber, epc.AccountName, epc.Notes, pc.ChannelLogo, epc.IsHidden FROM establishment_payment_channel epc INNER JOIN payment_channel pc ON pc.ChannelID = epc.PaymentChannel WHERE epc.EstablishmentID = $estID";

                    if (!$isUserOwner) {
                        $paySql .= " AND IsHidden = 0";
                    }

                    $payResult = mysqli_query($conn, $paySql);

                    if ($payResult && mysqli_num_rows($payResult) > 0) {
                    ?>

                    <div class="panel" style="padding: 20px">
                        <h3 style="margin-bottom: 20px;">Payment Channels:</h3>
                        <?php
                        while ($payChannel = mysqli_fetch_assoc($payResult)) {
                            $payID = $payChannel['EPCID'];
                            $channelName = $payChannel['ChannelName'];
                            $channelID = $payChannel['ChannelID'];
                            $channelLogo = $payChannel['ChannelLogo'];
                            $accountName = $payChannel['AccountName'];
                            $accountNumber = $payChannel['AccountNumber'];
                            $notes = $payChannel['Notes'];

                            $isHidden = $payChannel['IsHidden'];

                            $notes = str_replace("\\", "", $notes);
                        ?>
                        <div class="card" style="margin-left: 3px; border-bottom: 1px solid #ccc; padding: 10px;">
                            <div class="pay-header" style="display: flex; justify-content: space-between; align-items: center">
                                <div>
                                    <h4><?php echo $channelName; ?></h4>
                                    <div style="color: grey; font-size: 15px">
                                        <p><strong><?php echo $accountName; ?></strong></p>
                                        <p><?php echo $accountNumber; ?></p>
                                        <p><?php echo $notes; ?></p>
                                    </div>
                                    <?php if ($isUserOwner) {
                                    $notes = str_replace("'", "\\'", $notes);    
                                    ?>
                                    <div class="btn-group" style="margin-top: 10px;">
                                        <button type="button" style="padding: 3px; background-color: yellow; color: black; cursor: pointer;" onclick="editPaymentChannel(<?php echo $payID; ?>, <?php echo $channelID; ?>, '<?php echo $accountName; ?>', '<?php echo $accountNumber; ?>', '<?php echo $notes; ?>')"><i class="fa-solid fa-edit"></i> Edit</button>
                                        <button type="button" style="padding: 3px; background-color: red; color: white; cursor: pointer;" onclick="togglePaymentChannel(<?php echo $payID; ?>, <?php echo $isHidden; ?>, '<?php echo $channelName; ?>')">
                                        <?php
                                        echo $isHidden ? "<i class='fa-solid fa-eye'></i> Show" : "<i class='fa-solid fa-eye-slash'></i> Hide";
                                        ?>
                                        </button>
                                    </div>
                                    <?php } ?>
                                </div>
                                <img src="<?php echo $channelLogo; ?>" alt="<?php echo $channelName; ?>" style="height: 30px; width: auto;">
                            </div>
                        </div>
                        <?php } ?>
                        
                        
                        <?php if ($isUserOwner) { ?>
                            <div style="margin: 20px;">
                                <button type="button" class="btn btn-primary" onclick="openModal('addPaymentChannelModal')" style=" width: 100%">Add a Payment Channel</button>
                            </div>
                        <?php } ?>
                    </div>

                    <?php } else {
                        if ($isUserOwner) { ?>
                        <div class="panel" style="display: flex; justify-content: center; align-items: center; padding: 20px">
                            <button type="button" class="btn btn-primary" onclick="openModal('addPaymentChannelModal')">Add a Payment Channel</button>
                        </div>
                    <?php }
                    } ?> -->

                    <div class="panel" style="display: flex; justify-content: space-around; align-items: center; padding: 20px">
                        <div class="stats">
                            <h5>Rooms</h5>
                            <span>
                            <?php
                            $rooms = 0;
                            $sql = "SELECT COUNT(r.RoomID) AS NoOfRooms FROM rooms r WHERE r.EstablishmentID = $estID AND r.Availability != 'Deleted'";
                            $result = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                $row = mysqli_fetch_assoc($result);

                                $rooms = $row['NoOfRooms'];
                            }
                            echo $rooms;
                            ?>
                            </span>
                        </div>
                        <div class="stats">
                            <h5>Tenants</h5>
                            <span>
                                <?php
                                $tenants = 0;
                                $sql = "SELECT COUNT(rs.ResidencyID) AS NoOfTenants FROM residency rs INNER JOIN rooms r ON r.RoomID = rs.RoomID WHERE r.EstablishmentID = $estID AND rs.Status = 'currently residing' AND r.Availability != 'Deleted'";
                                $result = mysqli_query($conn, $sql);

                                if (mysqli_num_rows($result) > 0) {
                                    $row = mysqli_fetch_assoc($result);

                                    $tenants = $row['NoOfTenants'];
                                }
                                echo $tenants;
                                ?>
                            </span>
                        </div>
                        <div class="stats">
                            <h5>Vacancies</h5>
                            <span>
                            <?php
                                $vacancies = 0;
                                $maxOccupancy = 1;
                                $sql = "SELECT SUM(r.MaxOccupancy) AS TotalAvailableSpaces FROM rooms r WHERE r.EstablishmentID = $estID AND r.Availability = 'available'";
                                $result = mysqli_query($conn, $sql);

                                if (mysqli_num_rows($result) > 0) {
                                    $row = mysqli_fetch_assoc($result);

                                    $maxOccupancy = $row['TotalAvailableSpaces'] ?? 0;

                                    $vacancies = $maxOccupancy - $tenants;

                                    $vacancies = $vacancies < 0 ? 0 : $vacancies;
                                }
                                echo $vacancies;
                            ?>
                            </span>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <!-- Availability and Prices -->
        <div class="container-section clearfix" id="availability">            
            
            <h2>Availability and prices</h2>

            <?php

            $itemsPerPage = 6;
            $offset = ($roomPage - 1) * $itemsPerPage;

            $sql = "SELECT * FROM rooms WHERE EstablishmentID = $estID AND Availability != 'Deleted' ORDER BY FloorLocation, RoomName LIMIT $offset, $itemsPerPage";

            $roomResult = mysqli_query($conn, $sql);
            $noOfRooms = mysqli_num_rows($roomResult);

            $countSql = "SELECT COUNT(*) AS totalItems FROM rooms WHERE EstablishmentID = $estID AND Availability != 'Deleted'";
            $countResult = mysqli_query($conn, $countSql);
            $totalItems = mysqli_fetch_assoc($countResult)['totalItems'];
            $totalPages = ceil($totalItems / $itemsPerPage);
            

            $paymentRate = 0;
            ?>

            <?php if ($noOfRooms > 0) { ?>                

            <div class="list-grid-switcher" style="align-items: center; gap: 5px;">
                <label for="">View:</label>
                <button class="toggle-btn list-mode" title="Select to toggle list view mode." id="toggleListView" onclick="setListView();"><i class="fa-solid fa-list"></i> List</button>
                <button class="toggle-btn grid-mode toggle-active" title="Select to toggle grid view mode." id="toggleGridView" onclick="setGridView();"><i class="class fa-solid fa-grip"></i> Grid</button>

                <?php if ($isUserOwner) { ?>
                    <button class="btn btn-primary" id="add-feature" onclick="openModal('addRoomModal')" style="float: right; margin-left: 10px;"><i class="fa-solid fa-plus"></i> Add a room</button>
                <?php } ?>
                
            </div>



            <div class="room-grid">
                <?php while ($row = mysqli_fetch_assoc($roomResult)) {
                    $roomID = $row['RoomID'] ?? '';
                    $roomName = $row['RoomName'] ?? '';
                    $roomType = $row['RoomType'] ?? '';
                    $paymentRate = $row['PaymentRate'] ?? 0;
                    $paymentOptions = $row['PaymentOptions'] ?? '';
                    $paymentStructure = $row['PaymentStructure'] ?? '';
                    $availability = $row['Availability'] ?? '';
                    $floorLocation = $row['FloorLocation'] ?? '';
                    $paymentRules = $row['PaymentRules'] ?? '';
                    $photo = "/bookingapp/establishment/room/" . $row['Photo'] ?? '/bookingapp/assets/images/room_sample.jpg';
                    $maxOccupancy = (int)$row['MaxOccupancy'];

                    $genderInclusiveness = $row['GenderInclusiveness'] ?? '';


                    // Reviews
                    $roomRating = "";
                    $roomTotalReviews = 0;
                    $roomTotalScore = 0;
                    $roomMaxScoreReview = 70; // Number of categories is 7. Adjust this. 7 * 10 points

                    $roomStaffRating = 0;
                    $roomFacilitiesRating = 0;
                    $roomCleanlinessRating = 0;
                    $roomComfortRating = 0;
                    $roomSignalRating = 0;
                    $roomMoneyValueRating = 0;
                    $roomLocationRating = 0;
                    $roomSecurityRating = 0;

                    $roomReviewSql = "SELECT COUNT(rv.ReviewID) AS TotalReview, (AVG(rv.StaffScore + rv.FacilitiesScore + rv.CleanlinessScore + rv.ComfortScore + rv.SignalScore + rv.LocationScore + rv.MoneyValueScore + rv.SecurityScore) / 8) AS OverallScore FROM reviews rv WHERE rv.RoomID = $roomID";
                    $roomReviewResult = mysqli_query($conn, $roomReviewSql);

                    if (mysqli_num_rows($roomReviewResult) > 0) {
                        $roomReview = mysqli_fetch_assoc($roomReviewResult);

                        $roomTotalReviews = $roomReview['TotalReview'];
                        $roomTotalScore = $roomReview['OverallScore'];
                    }

                    // Calculate average score
                    $roomAverageScore = $roomTotalReviews > 0 ? $roomTotalScore / ($roomTotalReviews * 8) : 0;

                    // Convert average score to a 5-star rating system
                    $roomStarRating = round(($roomAverageScore / 10) * 5);

                    $roomRemark = getRemark($roomAverageScore);

                    // Check if user is tenant of this room
                    $isUserRoomTenant = false;
                    $tenancy = array();
                    $tenantSql = "SELECT rs.ResidencyID, rs.DateOfEntry, rs.DateOfExit, rs.Status, rs.CreatedAt, r.RoomID FROM residency rs INNER JOIN rooms r ON r.RoomID = rs.RoomID WHERE rs.TenantID = $tenantID AND r.RoomID = $roomID";
                    $tenantResult = mysqli_query($conn, $tenantSql);

                    echo mysqli_error($conn);

                    if (mysqli_num_rows($tenantResult) > 0) {
                        $tenancy = mysqli_fetch_assoc($tenantResult);
                        $isUserRoomTenant = true;
                    }
                ?>
                    <div class="room-card">
                        <div class="room-card-image">
                            <img src="<?php echo $photo; ?>" alt="<?php echo $roomName; ?>" />
                            <div class="room-card-icons">
                                <?php
                                if ($isUserTenant) {
                                ?>
                                <span title="Add to favorites"><i class="fa-solid fa-thumbs-up"></i></span>
                                <?php } ?>
                                <span title="View Photos"><i class="fa-solid fa-photo-film"></i></span>
                                
                            </div>
                            <div class="room-review" style="align-items: center; background-color: rgba(0,0,0,0.4); padding: 5px;">
                                <?php echo $roomRemark; ?> <span class="room-score"><?php echo number_format($roomAverageScore, 1); ?></span><br>
                                <?php echo number_format($roomTotalReviews); ?> reviews
                            </div>
                        </div>
                        <div class="room-card-details clearfix" style="position: relative">
                            <span class="room-availability" style="font-size: 14px;">
                                <?php
                                
                                $residencySql = "SELECT COUNT(rs.ResidencyID) AS NoOfTenants FROM residency rs WHERE rs.RoomID = $roomID AND rs.Status != 'deleted'";
                                $residencyResult = mysqli_query($conn, $residencySql);

                                $residents = 0;

                                if (mysqli_num_rows($residencyResult) > 0) {
                                    $residents = mysqli_fetch_assoc($residencyResult)['NoOfTenants'];
                                }

                                $noSpaceAvailable = false;
                                if ($maxOccupancy <= $residents || $maxOccupancy <= 0) {
                                    echo "Fully occupied";
                                    $noSpaceAvailable = true;
                                } else {
                                    echo $availability;
                                }
                                ?>

                            </span>
                            
                            <span style="color: maroon; position: absolute; top: 0.5rem; left: 1rem; padding: 5px; border-radius: 5px; margin: 10px 10px 0 0; font-size: 14px; background: grey; color: white; text-transform: lowercase">
                                ₱   
                                <?php
                                echo number_format($paymentRate, 2) . ' ' . $paymentStructure . ' ' . $paymentOptions;
                                ?>
                            </span>
                            <h4 style="margin-top: 3rem"><?php echo $roomName; ?></h4>
                            <p>
                                <span>
                                    <i class="fa-solid fa-people-roof"></i> <?php echo $roomType; ?>
                                    &nbsp;
                                    <i class="fa-solid fa-users"></i>
                                    <?php

                                    echo $residents;
                                    ?>
                                    tenants &middot;

                                    <?php
                                    $availableSpace = 0;
                                    
                                    $availableSpace = $maxOccupancy - $residents;
                                    
                                    if ($availableSpace === 1) {
                                        echo $availableSpace . " vacancy";
                                    } else {
                                        echo $availableSpace . " vacancies";
                                    }
                                    ?>
                                
                                </span>
                                
                                <span><i class="fa-solid fa-stairs"></i> <?php echo numberToOrdinal($floorLocation); ?> Floor</span>                             &nbsp;&middot;&nbsp;
                                <span><i class="fa-solid fa-venus-mars"></i> <?php echo $genderInclusiveness; ?></span>
                            </p>

                            <?php
                            
                            $amenitySql = "SELECT f.Icon, f.Name, f.FeatureID FROM room_features rf INNER JOIN features f ON f.FeatureID = rf.FeatureID WHERE rf.RoomID = $roomID ORDER BY f.Name";
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

                            <?php if ($isUserOwner) { ?>
                            <p class="option-btns">
                                <button class="option-btn room-amenity-btn add" onclick="addRoomAmenity('<?php echo base64_encode($estID); ?>', <?php echo $roomID; ?>)"><i class="fa-solid fa-plus"></i> Add amenity</button>
                            </p>
                            <?php } ?>

                            <?php if (!empty($paymentRules)) { ?>
                            <h6>Payment rules</h6>
                            <p>
                                <?php echo $paymentRules; ?>
                            </p>
                            <?php } ?>

                            <h6>Rating</h6>
                            <p>
                                <span class="room-stars" style="color: #ffd700; font-size: 18px">
                                <?php echo generateStars($roomStarRating); ?>                                </span>
                            </p>
                            
                            <?php if ($isUserOwner) { ?>
                                <button class="btn btn-primary" onclick="editRoom('editRoomModal', <?php echo $roomID; ?>, '<?php echo $roomName; ?>',  '<?php echo $roomType; ?>',  <?php echo $paymentRate; ?>,  '<?php echo $paymentRules; ?>',  '<?php echo $paymentOptions; ?>',  '<?php echo $paymentStructure; ?>',  <?php echo $floorLocation; ?>,  '<?php echo $availability; ?>',  '<?php echo $genderInclusiveness; ?>',  '<?php echo $photo; ?>')"><i class="fa-solid fa-edit"></i> Edit</button>
                                <button class="btn btn-secondary" onclick="deleteRoom(<?php echo $roomID; ?>, '<?php echo $roomName; ?>', 'deleteRoomModal')"><i class="fa-solid fa-trash"></i> Delete</button>
                            <?php } ?>

                            <?php if ($isUserOwner || $isUserAdmin) {
                            ?>
                            <button style="margin-top: 10px; position: absolute; right: 1rem; bottom: 1rem; padding: 10px; background-color: #00552b; color: white; border: 1px solid black; cursor: pointer;" onclick="redirect('?est=<?php echo base64_encode($estID); ?>&rtf=<?php echo $roomID; ?>&res_st=currently+residing#tenants')"><i class="fa-solid fa-users"></i> See tenants</button>
                            <?php
                            } ?>

                            <?php if ($isUserTenant) {
                                if ($loggedIn) {
                                    // $noSpaceAvailable = true;
                                    if ($availability === 'Available' && !$noSpaceAvailable && !$isUserRoomTenant) {
                                         ?>
                                        <button class="book-now-btn btn" onclick="bookRoom('<?php echo $roomID; ?>', '<?php echo $paymentRate; ?>', '<?php echo $roomName; ?>', '<?php echo $establishment['Name']; ?>')" style="margin-top: 10px; position: absolute; right: 1rem; bottom: 1rem;">Book Now</button>
                                    <?php 
                                    }
                                    
                                    if ($isUserRoomTenant) {
                                    
                                    ?>

                                    <button style="margin-top: 10px; position: absolute; right: 1rem; bottom: 1rem; padding: 10px; color: black;border: 1px solid black" disabled>You are <?php echo $tenancy['Status']; ?> here!</button>

                                    <?php
                                    }
                                    
                                    ?>
                                    
                            <?php } else { ?>
                                    <button class="book-now-btn btn" onclick="redirect('/bookingapp/login.php?rdr=est-book-room&est=<?php echo base64_encode($estID); ?>')" style="margin-top: 10px; position: absolute; right: 1rem; bottom: 1rem;">Sign in to book</button>
                            <?php }
                            } ?>
                            
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    
                    <?php if ($roomResult && mysqli_num_rows($roomResult) > 0) { ?>
                    <!-- Pagination -->
                    <div id="pagination" class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?est=<?php echo base64_encode($estID); ?>&roomPage=<?= $i ?>#availability" <?php echo $roomPage === $i ? 'class="page-active"' : ''; ?>>
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php } ?>
                

                    <?php } else { ?>
                <div class='room-grid' style="display: flex; justify-content: center; align-items: center; flex-direction: column; gap: 10px;">
                    <h3 style='text-align: center'>There are no rooms.</h3>
                    <?php if ($isUserOwner) { ?>
                        <button class="btn btn-primary" id="add-feature" onclick="openModal('addRoomModal')" style="float: right; margin-left: 10px;"><i class="fa-solid fa-plus"></i> Add a room</button>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <!-- House Rules -->
        <div class="container-section" id="houserules">
            <?php if ($isUserOwner) { ?>
                <button class="btn btn-primary" id="add-feature" onclick="openModal('addHouseRulesModal')" style="float: right; margin-left: 10px;"><i class="fa-solid fa-plus"></i> Edit house rules</button>
            <?php } ?>            
            <h2>House rules</h2>
            <p><?php echo $establishment['Name']; ?></p>

            <div id="houseRules" style="margin-top: 20px; font-size: 20px">
                <?php
                    if (!empty($establishment['HouseRules'])) {
                        echo "<p>" . str_replace("\\r\\n", "<br>", $establishment['HouseRules']) . "</p>";
                    } else {
                        echo "<h3 style='text-align: center'>No house rules yet!</h3>";
                    }
                ?>
            </div>
        </div>

        <?php if ($isUserOwner || $isUserAdmin) {
            // Fetch paginated data
            $tenantRoom = isset($_GET['rtf']) ? $_GET['rtf'] : '';
            $residencyStatus = isset($_GET['res_st']) ? $_GET['res_st'] : "currently residing";
            $residencyStatus = str_replace("+", " ", $residencyStatus);

            $roomFilterQuery = isset($_GET['rtf']) ? " AND r.RoomID = $tenantRoom" : "";

            $dataQuery = "SELECT p.PersonID, p.ProfilePicture, p.FirstName, p.MiddleName, p.LastName, p.ExtName, p.Gender, p.ContactNumber, p.HomeAddress, t.UniversityID, u.UserID, res.ResidencyID, res.Status AS ResidencyStatus, res.DateOfEntry, res.DateOfExit, r.RoomName, res.CreatedAt AS DateReserved, res.UpdateAt AS LastUpdate, u.Username FROM residency res INNER JOIN tenant t ON t.TenantID = res.TenantID INNER JOIN user_account u ON U.UserID = t.UserID INNER JOIN person p ON p.PersonID = u.PersonID INNER JOIN rooms r ON r.RoomID = res.RoomID WHERE r.EstablishmentID = $estID AND res.Status = '$residencyStatus' AND r.RoomID LIKE '%$tenantRoom%' GROUP BY p.PersonID  ORDER BY res.Status, res.DateOfEntry, p.LastName, p.FirstName, p.MiddleName, p.ExtName";
            $dataResult = mysqli_query($conn, $dataQuery);

            echo mysqli_error($conn);

            $roomSql = "SELECT RoomID, RoomName FROM rooms WHERE EstablishmentID = $estID";
            $roomResult = mysqli_query($conn, $roomSql);
 ?>
            <div class="container-section" id="tenants" style="padding-bottom: 50px; ">
                <form action="establishment.php?est=<?php echo base64_encode($estID); ?>" id="filter-tenant-form" method="get" onsubmit="addHashToUrl('tenants');">
                    <button type="submit" class="btn btn-primary" id="filter_tenant" style="float: right; padding: 10px; margin-left: 10px;"><i class="fa-solid fa-filter"></i> Filter tenants</button>

                    <input type="hidden" name="est" value="<?php echo base64_encode($estID); ?>">
                    
                    
                    <select name="rtf" id="room-tenant-filter" style="float: right; padding: 10px; margin-left: 10px;">
                        <?php
                        if (mysqli_num_rows($roomResult) > 0) {
                            echo "<option value='' ";
                            if (!isset($_GET['rtf'])) {
                                echo "selected";
                            }
                            echo ">All rooms</option>";
                            while ($rooms = mysqli_fetch_assoc($roomResult)) {
                                $roomID = $rooms['RoomID'];
                                $roomName = $rooms['RoomName'];
                                echo "<option value='$roomID' ";
                                if (isset($_GET['rtf']) && $_GET['rtf'] === $roomID) {
                                    echo "selected";
                                }
                                echo ">$roomName</option>";
                            }
                        }
                        ?>
                    </select>

                    <select name="res_st" id="room-status-filter" style="float: right; padding: 10px; margin-left: 10px;">
                        <option value="currently residing" <?php if (isset($_GET['res_st']) && $_GET['res_st'] === 'currently residing') { echo 'selected'; } ?>>Current tenants</option>
                        <option value="pending" <?php if (isset($_GET['res_st']) && $_GET['res_st'] === 'pending') { echo 'selected'; } ?>>Pending reservations</option>
                        <option value="confirmed" <?php if (isset($_GET['res_st']) &&  $_GET['res_st'] === 'confirmed') { echo 'selected'; } ?>>Confirmed reservations</option>
                        <option value="residency ended" <?php if (isset($_GET['res_st']) && $_GET['res_st'] === 'residency ended') { echo 'selected'; } ?>>Former tenants</option>
                        <option value="cancelled" <?php if (isset($_GET['res_st']) && $_GET['res_st'] === 'cancelled') { echo 'selected'; } ?>>Cancelled reservations</option>
                        <option value="rejected" <?php if (isset($_GET['res_st']) && $_GET['res_st'] === 'rejected') { echo 'selected'; } ?>>Rejected reservations</option>
                    </select>
                </form>
                <h2>Tenants</h2>

                <div class="card-container">
                <?php                     
                if ($dataResult && mysqli_num_rows($dataResult) > 0) {
                    while ($row = mysqli_fetch_assoc($dataResult)) {
                        // Extract data from the current row
                        $thisPersonID = $row['PersonID'];
                        $thisProfilePicture = htmlspecialchars($row['ProfilePicture']);
                        $thisFirstName = htmlspecialchars($row['FirstName']);
                        $thisMiddleName = htmlspecialchars($row['MiddleName']);
                        $thisLastName = htmlspecialchars($row['LastName']);
                        $thisExtName = htmlspecialchars($row['ExtName']);

                        $residencyID = $row['ResidencyID'];

                        if (!empty($middleName)) {
                            $thisMiddleInitials = $thisMiddleName[0] . '.';
                        }

                        $thisFullName = $thisFirstName . ' ' . $thisMiddleInitials . ' ' . $thisLastName . ' ' . $thisExtName;
                        
                        $thisGender = htmlspecialchars($row['Gender']);

                        $thisHomeAddress = htmlspecialchars($row['HomeAddress']);
                        $thisContactNumber = htmlspecialchars($row['ContactNumber']);

                        $thsiUniversityID = htmlspecialchars($row['UniversityID']);

                        $dateOfEntry = isset($row['DateOfEntry']) ? date('F d, Y', strtotime($row['DateOfEntry'])) : '';
                        $dateOfExit = isset($row['DateOfExit']) ? date('F d, Y', strtotime($row['DateOfExit'])) : '';

                        // Show default profile picture based on the gender when profile picture is not available
                        if (empty($thisProfilePicture) || !$thisProfilePicture) {
                            $thisProfilePicture = "/bookingapp/user/$thisGender-no-face.jpg";
                        }
            ?>
                        <div class="profile-card">  
                            <div class="profile-header" onclick="toggleDetails(this)">
                                <img src="<?php echo $thisProfilePicture; ?>" alt="<?php echo $thisLastName; ?>" class="profile-pic">
                                <div class="profile-info">
                                    <h3 class="profile-name"><a href="/bookingapp/user/profile.php?id=<?php echo $row['Username']; ?>" style="color: maroon;"><?php echo $thisFullName; ?></a></h3>
                                    <p class="profile-role">
                                        <?php echo $roomName; ?> <br>
                                        <?php echo date("F d, Y", strtotime($row['DateOfEntry'])); ?>
                                        <?php if (isset($dateOfExit) && !empty($dateOfExit)) {
                                            echo " - " . $dateOfExit;
                                        } ?> <br>
                                        Status: <?php echo $row['ResidencyStatus']; ?>
                                    </p>
                                </div>
                                <!-- <span class="toggle-icon"><i class="fa-solid fa-caret-down"></i></span> -->
                            </div>

                            <div class="profile-details hidden">
                                <p><strong>ID Number:</strong> <?php echo $thsiUniversityID; ?></p>
                                <p><strong>Home Address:</strong> <?php echo $thisHomeAddress; ?></p>
                                <p><strong>Contact:</strong> <?php echo $thisContactNumber; ?></p>
                            </div>

                            
                        </div>
            
                        
            <?php
                    }
            ?>
            <?php
                } else {
                    echo "<p>No tenants found for this tab.</p>";
            }
            ?>
                </div>
            </div>
        <?php } ?>

        <!-- Reviews -->
        <div class="container-section" id="reviews">
            <?php if (!$isUserOwner) {
                    if ($loggedIn) {
                        if ($isUserResident) {?>
                        <button class="btn btn-primary" id="add-feature" onclick="openModal('writeReviewModal')" style="float: right; margin-left: 10px;"><i class="fa-solid fa-edit"></i> Write a review</button>
                        <?php }
                    } else { ?>
                    <button class="btn btn-primary" id="add-feature" onclick="redirect('/bookingapp/login.php?rdr=est-write-review&est=<?php echo base64_encode($estID); ?>')" style="float: right; margin-left: 10px;"><i class="fa-solid fa-edit"></i> Sign in to write a review</button>
            <?php }
            } ?>
            <h2>Reviews</h2>
            
            <div class="acc-row" style="justify-content: flex-start; align-items: center; gap: 10px;">
                <span class="score"><?php echo number_format($averageScore, 1); ?></span>
                <span class="rating"><?php echo $remark; ?></span> &middot;
                <span><?php echo number_format($totalReviews); ?> reviews</span>
                <?php if ($totalReviews > 0) { ?>
                <span style="float: right; font-size: 12px;"><a href="#reviewers">Read all reviews</a></span>
                <?php } ?>
            </div>

            <h4 class="acc-row">Categories</h4>

            <div class="acc-row category-ratings">
                <div class="category">
                    <label for="" class="category-label">
                        <span><i class="fa-solid fa-users"></i> Staff</span>
                        <span><?php echo number_format($staffRating, '1'); ?></span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar" style="width: <?php echo $staffRating * 10; ?>px"></div>
                    </div>
                </div>

                <div class="category">
                    <label for="" class="category-label">
                        <span><i class="fa-solid fa-building"></i> Facilities & amenities</span>
                        <span><?php echo number_format($facilitiesRating, '1'); ?></span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar" style="width: <?php echo $facilitiesRating * 10; ?>px"></div>
                    </div>
                </div>

                <div class="category">
                    <label for="" class="category-label">
                        <span><i class="fa-solid fa-broom"></i> Cleanliness</span>
                        <span><?php echo number_format($cleanlinessRating, '1'); ?></span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar" style="width: <?php echo $cleanlinessRating * 10; ?>px"></div>
                    </div>
                </div>

                <div class="category">
                    <label for="" class="category-label">
                        <span><i class="fa-solid fa-heart"></i> Comfort</span>
                        <span><?php echo number_format($comfortRating, '1'); ?></span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar" style="width: <?php echo $comfortRating * 10; ?>px"></div>
                    </div>
                </div>

                <div class="category">
                    <label for="" class="category-label">
                        <span><i class="fa-solid fa-wifi"></i> Signal</span>
                        <span><?php echo number_format($signalRating, '1'); ?></span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar" style="width: <?php echo $signalRating * 10; ?>px"></div>
                    </div>
                </div>

                <div class="category">
                    <label for="" class="category-label">
                        <span><i class="fa-solid fa-peso-sign"></i> Value for money</span>
                        <span><?php echo number_format($moneyValueRating, '1'); ?></span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar" style="width: <?php echo $moneyValueRating * 10; ?>px"></div>
                    </div>
                </div>

                <div class="category">
                    <label for="" class="category-label">
                        <span><i class="fa-solid fa-location-pin"></i> Location</span>
                        <span><?php echo number_format($locationRating, '1'); ?></span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar" style="width: <?php echo $locationRating * 10; ?>px"></div>
                    </div>
                </div>

                <div class="category">
                    <label for="" class="category-label">
                        <span><i class="fa-solid fa-shield"></i> Security</span>
                        <span><?php echo number_format($securityRating, '1'); ?></span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar" style="width: <?php echo $securityRating * 10; ?>px"></div>
                    </div>
                </div>
            </div>

            <?php
            $sql = "SELECT rv.ReviewID, p.ProfilePicture, p.FirstName, p.MiddleName, p.LastName, p.ExtName, p.Gender, u.Username, r.RoomName, rv.RoomID, rv.Comments, rv.UpdatedAt, rv.StaffScore, rv.FacilitiesScore, rv.CleanlinessScore, rv.ComfortScore, rv.MoneyValueScore, rv.LocationScore, rv.SignalScore, rv.SecurityScore, rv.TenantID FROM reviews rv INNER JOIN tenant t ON t.TenantID = rv.TenantID INNER JOIN user_account u ON u.UserID = t.UserID INNER JOIN person p ON p.PersonID = u.PersonID INNER JOIN rooms r ON r.RoomID = rv.RoomID WHERE r.EstablishmentID = $estID ORDER BY rv.UpdatedAt DESC";
            $result = mysqli_query($conn, $sql);

            echo mysqli_error($conn);

            if (mysqli_num_rows($result) > 0) {
                
            ?>

            <h4 class="acc-row">Guests who loved staying here</h4>
            <!-- <div class="sort-container">
                <label for="">Sort reviews by: </label>
                <select name="" id="">
                    <option value="most-relevant">Most relevant</option>
                    <option value="newest-first">Newest first</option>
                    <option value="oldest-first">Oldest first</option>
                    <option value="highest-score">Highest score</option>
                    <option value="lowest-score">Lowest score</option>
                </select>
            </div> -->

            <div class="acc-row review-container" id="reviewers">
               <?php
                $i = 0;
               while ($row = mysqli_fetch_assoc($result)) {
                    $reviewID = $row['ReviewID'];
                    $encryptedReviewID = base64_encode($reviewID);

                    $reviewerTenantID = $row['TenantID'];

                    $reviewerProfilePicture = $row['ProfilePicture'];

                    $reviewerGender = $row['Gender'] ?? 'male';
                    
                    if (empty($reviewerProfilePicture)) {
                        $reviewerProfilePicture = "/bookingapp/user/$reviewerGender-no-face.jpg";
                    }

                    $reviewerFirstName = $row['FirstName'];
                    $reviewerMiddleName = $row['MiddleName'];
                    $reviewerLastName = $row['LastName'];
                    $reviewerExtName = $row['ExtName'];

                    if (!empty($reviewerMiddleName)) {
                        $reviewMiddleName = $reviewerMiddleName[0] . '.';
                    }

                    $reviewerFullName = $reviewerFirstName . ' ' . $reviewerMiddleName . ' ' . $reviewerLastName . ' ' . $reviewerExtName;

                    $reviewerUsername = '@' . $row['Username'];

                    $comment = !empty($row['Comments']) ? $row['Comments'] : '<span style="color: grey; font-style: italic">No comment</span>';
                    $comment = str_replace('\\r\\n', "<br>", $comment);
                    $comment = censorBadWords($comment);

                    // Scores by category
                    $staff = $row['StaffScore'] ?? 0;
                    $facilities = $row['FacilitiesScore'] ?? 0;
                    $cleanliness = $row['CleanlinessScore'] ?? 0;
                    $comfort = $row['ComfortScore'] ?? 0;
                    $moneyValue = $row['MoneyValueScore'] ?? 0;
                    $location = $row['LocationScore'] ?? 0;
                    $signal = $row['SignalScore'] ?? 0;
                    $security = $row['SecurityScore'] ?? 0;

                    $reviewerScore = ($staff + $facilities + $cleanliness + $comfort + $moneyValue + $location + $signal + $security) / 8;
                    $reviewerStarRating = round(($reviewerScore / 10) * 5);

                    $roomName = $row['RoomName'];
                ?>
                     <div class="panel review-card clearfix" title="Click to see reviews from guests.">
                    <div class="reviewer-profile-header">
                        <div class="reviewer-profile-pic">
                            <img src="<?php echo $reviewerProfilePicture; ?>" alt="<?php echo $reviewerUsername; ?>" style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover;">
                        </div>
                        <div class="reviewer-name">
                            <strong><?php echo $reviewerFullName; ?></strong><br>
                            <span style="font-size: 10px;">
                                <?php echo $reviewerUsername . "<br>" . $roomName; ?>
                            </span>
                        </div>
                    </div>
                    <div class="review-rating">
                    <hr>
                            <span style="color: grey; padding: 5px; border-radius: 10px; font-size: 12px;"><?php echo number_format($reviewerScore, 1) . ' &middot; ' . getRemark($reviewerScore) . ' &middot; ' . generateStars($reviewerStarRating); ?></span>
                    </div>
                    <div class="review-content">
                        <p class="text-container" id="text-content-<?php echo $i; ?>"><?php echo str_replace('\\', '', $comment); ?></p>
                        <span class="read-more" id="read-more-<?php echo $i; ?>" onclick="toggleReadMore('<?php echo $i; ?>')">Read More</span>
                    </div>

                    <?php
                    if ($isLoggedIn && $tenantID === $reviewerTenantID) {
                        ?>
                        <div class="btn-group" style="float: right">
                            <button style="background-color: #FFD700; color: black;  cursor: pointer; padding: 5px; border-radius: 10px;" onclick="editReview(<?php echo $reviewID; ?>, <?php echo $roomID; ?>, '<?php echo str_replace('\'', '', $comment); ?>', <?php echo $staff; ?>, <?php echo $facilities; ?>, <?php echo $cleanliness; ?>, <?php echo $comfort; ?>, <?php echo $moneyValue; ?>, <?php echo $location; ?>, <?php echo $signal; ?>, <?php echo $security; ?>)"><i class="fa-solid fa-edit"></i> Edit</button>
                            <button style="background-grey: grey; color: black; cursor: pointer; padding: 5px; border-radius: 10px" onclick="deleteReview(<?php echo $reviewID; ?>)"><i class="fa-solid fa-trash"></i> Delete</button>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <?php $i++;
               } ?>
            </div>

            <!-- <button class="btn">Read all reviews</button> -->

            <?php } else {
                echo "<h3 style='text-align: center'>No reviewers</h3>";
            } ?>
        </div>



     
    </div>

    <!-- Modals -->
    <?php // include "../modal/view_map_modal.php"; ?>

    <?php include "../modal/add_room.php"; ?>

    <?php include "../modal/edit_room.php"; ?>

    <?php include "../modal/delete_room.php"; ?>

    <?php include "../modal/add_room_amenity.php"; ?>

    <?php include "../modal/add_establishment_amenity.php"; ?>

    <?php // include "../modal/location_modal.php"; ?>

    <?php include "../modal/add_house_rules.php"; ?>

    <?php include "../modal/upload_multiple_photos.php"; ?>

    <?php include "../modal/book_room.php"; ?>

    <?php include "../modal/write_review.php"; ?>

    <?php include "../modal/edit_review.php"; ?>

    <?php include "../modal/delete_review.php"; ?>

    <?php include "../modal/add_payment_channel_modal.php"; ?>

    <?php include "../modal/edit_payment_channel_modal.php"; ?>

    <?php include "../modal/toggle_payment_channel_modal.php"; ?>
    
    
    <!-- Footer -->
    <?php include "../php/footer.php"; ?>

    <div id="toastBox" style="bottom: -755px;"></div>


    <script src="/bookingapp/js/jquery-1.10.2.min.js"></script>
    <script src="/bookingapp/js/bootstrap.bundle.min.js"></script>
    <!-- <script type="module" src="/bookingapp/js/openai.js"></script> -->
    <script src="/bookingapp/js/scrollreveal.js "></script>
    <script src="/bookingapp/js/rayal.js"></script>
    <script type="text/javascript"></script>

    <script>


        function toggleDetails(header) {
            const details = header.nextElementSibling;
            const icon = header.querySelector(".toggle-icon");

            if (details.classList.contains("open")) {
                details.classList.remove("open");
                icon.classList.remove("open");
            } else {
                details.classList.add("open");
                details.classList.add("open");
            }
        }

        // Initialize maps
        var latitude = <?php echo isset($latitude) ? $latitude : 'null'; ?>;
        var longitude = <?php echo isset($longitude) ? $longitude : 'null'; ?>;

        var defaultLatitude = 7.99516864;
        var defaultLongitude = 124.26071524;
        var zoomLevel = 16;

        // Initialize modal map
        const mapPreview = L.map('map-preview').setView([defaultLatitude, defaultLongitude], zoomLevel);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="://www.openstreetmap.org/">OpenStreetMap</a>',
            subdomains: ['a', 'b', 'c']
        }).addTo(mapPreview);

        // Establishment data from PHP
        var establishments = <?php echo json_encode($establishments); ?>;

        // Add markers to the map
        establishments.forEach(function(establishment) {
            var encryptedEstID = btoa(establishment.EstablishmentID);
            if (establishment.Latitude && establishment.Longitude) {
                L.marker([establishment.Latitude, establishment.Longitude])
                    .addTo(mapPreview)
                    .bindPopup(`<a href="/bookingapp/establishment/establishment.php?est=${encryptedEstID}">${establishment.Name}</a>`);
            }
        });

        // Add a marker if latitude and longitude exist
        if (latitude && longitude) {
            const establishmentLocation = [parseFloat(latitude), parseFloat(longitude)];

            mapPreview.setView(establishmentLocation, 16);

            L.marker(establishmentLocation).addTo(mapPreview)
            .bindTooltip("<b><?php echo $establishment['Name']; ?></b>", {
                permanent: true,
                direction: "top"
            });

            document.getElementById('latitude').value = latitude;
            document.getElementById('longitude').value = longitude;

            // Get address
            getEstablishmentAddress(parseFloat(latitude), parseFloat(longitude));

        } else {
            console.log("No saved location found. Defaulting to MSU Main Campus");
        }

        


        // Get map address
        function getEstablishmentAddress(lat, lng) {
            // Reverse Geocode using Nominatim
            var url = 'https://nominatim.openstreetmap.org/reverse';
            var params = {
                lat: lat,
                lon: lng,
                format: 'json',
                addressDetails: 1,
                zoom: 19
            };

            fetch(`${url}?${Object.keys(params).map(key => `${key}=${params[key]}`).join('&')}`)
            .then(response => response.json())
            .then(data => {
                // Get the place name from the response
                var placeName = data.display_name;
                document.getElementById('est-address').innerText = placeName;
            })
            .catch(error => console.error('Error:', error));
        }

        // Add marker for establishment location
        let marker;

        function allowMapEditing() {
            var mapEditing = document.getElementsByClassName("map-editing");

            for (var i = 0; i < mapEditing.length; i++) {
                mapEditing[i].style.display = "block";
            }

            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;

            // Current

            // Add marker on click
            mapPreview.on('click', (e) => {
                if (marker) {
                    mapPreview.removeLayer(marker);
                }
                marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(mapPreview);
                document.getElementById('latitude').value = e.latlng.lat;
                document.getElementById('longitude').value = e.latlng.lng;
            });
        }

        function discardMapEditing() {
            var mapEditing = document.getElementsByClassName("map-editing");

            for (var i = 0; i < mapEditing.length; i++) {
                mapEditing[i].style.display = "none";
            }
        }

        // Generate description using AI-powered API
        // const openAI = require("openai");
        const estName = <?php echo json_encode($establishment['Name']); ?>;

        // openAI(estName, latitude, longitude);

        

        document.getElementById('year').textContent = new Date().getFullYear();

        var promptMessage = "";

        // Stick Page Nav
        window.onscroll = function() {stickPageNav()};

        var header = document.getElementById("sticky-nav");
        var sticky = header.offsetTop;

        function stickPageNav() {
            if (window.pageYOffset > sticky) {
                header.classList.add("sticky-nav");
                // header.style.maxWidth = "0";
            } else {
                header.classList.remove("sticky-nav");
                // header.style.maxWidth = "1200px";
            }
        }


        <?php if (!$noPhotos) { ?>
        // Image Slideshow Gallery
        let slideIndex = 1;
        showSlides(slideIndex);

        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            let i;
            let imageItems = document.getElementsByClassName("image-item");
            let dots = document.getElementsByClassName("demo");
            let captionText = document.getElementById("imageCaption");

            if (n > imageItems.length) {
                slideIndex = 1;
            }

            if (n < 1) {
                slideIndex = imageItems.length;
            }

            for (i = 0; i < imageItems.length; i++) {
                imageItems[i].style.display = "none";
            }

            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" selected-image", "");
            }

            imageItems[slideIndex - 1].style.display = "block";
            dots[slideIndex - 1].className += " selected-image";
            captionText.innerHTML = dots[slideIndex - 1].alt;
        }

        <?php } ?>


        <?php if ($noOfRooms > 0)  {?>
        // Toggle View

        const toggleGridBtn = document.getElementById("toggleGridView");
        const toggleListBtn = document.getElementById("toggleListView");
        const roomGrid = document.querySelector(".room-grid");
        const roomCard = document.getElementsByClassName("room-card");

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

        // Read more functionality
        
        function toggleReadMore(index) {
            var readMoreLink = document.getElementById(`read-more-${index}`);
            var text = document.getElementById(`text-content-${index}`);

            if (text.style.height === '72px') {
                text.style.height = 'auto';
                readMoreLink.textContent = 'Read Less';
                readMoreLink.style.color = "red";
            } else {
                text.style.height = '72px';
                readMoreLink.textContent = 'Read More';
                readMoreLink.style.color = "blue";
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var texts = document.querySelectorAll('.text-container');
            var readMoreLinks = document.querySelectorAll('.read-more');

            texts.forEach((text, index) => {
                var textHeight = text.scrollHeight;

                // text = text.replace("\\'", );

                if (textHeight <= 72) {
                    readMoreLinks[index].style.display = "none";
                }
            });
        });

        // Modal
        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }

        function openModal(id) {
            document.getElementById(id).style.display = "block";
        }

        setTimeout(function(){ mapPreview.invalidateSize()}, 500);

        function formatPriceCurrency(priceInput, priceOutput) {
            // Format number as currency
            let input = document.getElementById(priceInput);
            let output = document.getElementById(priceOutput);
            let formattedValue = new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(input.value);
            output.innerHTML = formattedValue;
        }

        formatPriceCurrency('add-price-input', 'add-price-output');

        // Review ranges
        function rateCategory(inputID, outputID) {
            let input = document.getElementById(inputID);
            let output = document.getElementById(outputID);
            output.innerHTML = input.value;
        }

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

        // Toggle single photo upload
        function displayPhotoPreview(fileInput, photoPreview) {
            let input = document.getElementById(fileInput);
            let preview = document.getElementById(photoPreview);
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function toggleAmenity(amenityID, chip, estID, scope) {
            const action = chip.classList.contains('added') ? 'remove' : 'add';

            fetch('/bookingapp/php/toggle_amenity.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `estID=${estID}&amenityID=${amenityID}&action=${action}&scope=${scope}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        chip.classList.toggle('added');
                    } else {
                        showToast('circle-xmark', data.message, 'error');
                    }
                });
        }

        function toggleRoomAmenity(amenityID, chip, scope) {
            const action = chip.classList.contains('added') ? 'remove' : 'add';

            let roomID = document.getElementById('amenityRoomID').value;

            fetch('/bookingapp/php/toggle_amenity.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `roomID=${roomID}&amenityID=${amenityID}&action=${action}&scope=${scope}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        chip.classList.toggle('added');
                    } else {
                        showToast('circle-xmark', data.message, 'error');
                    }
                });
        }

        function addRoomAmenity(estID, roomID) {
            openModal('addRoomAmenity');
            document.getElementById('amenityRoomID').value = roomID;
        }

        // Edit Room
        function editRoom(modalID, roomID, roomName, roomType, price, rules, options, structure, floorLevel, availability, gender, photo) {
            openModal(modalID);
            document.getElementById('edit-room-id').value = roomID;
            document.getElementById('edit-room-photo-preview').src = photo;
            document.getElementById('edit-old-photo').value = photo;
            document.getElementById('edit-room-photo-preview').alt = roomName;
            document.getElementById('edit-room-name').value = roomName;
            document.getElementById('edit-room-type').value = roomType;
            document.getElementById('edit-room-price').value = price;
            document.getElementById('edit-price-output').value = new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(price);
            document.getElementById('edit-room-payment-rules').value = rules;
            document.getElementById('edit-payment-structure').value = structure;
            document.getElementById('edit-payment-options').value = options;
            document.getElementById('edit-floor-level').value = floorLevel;
            document.getElementById('edit-room-availability').value = availability;
            document.getElementById('edit-room-gender').value = gender;

            // console.log(photo);
        }

        // Upload photo via AJAX
        function uploadPhoto(photoIndex) {
            const inputId = `photo-input-${photoIndex}`;
            const descriptionId = `photo-description-${photoIndex}`;
            const fileInput = document.getElementById(inputId);
            const descriptionInput = document.getElementById(descriptionId);
            const file = fileInput.files[0];

            const formData = new FormData();
            formData.append('photo', file);
            formData.append('description', descriptionInput.value);
            formData.append('photoIndex', photoIndex);
            formData.append('establishmentID', <?php echo json_encode($estID); ?>);

            fetch('upload_photo.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('circle-check', 'Photo uploaded successfully!', 'success');
                    document.getElementById(`photo-preview-${photoIndex}`).src = data.photoPath;
                } else {
                    showToast('circle-xmark', 'Failed to upload photo: ' + data.message, 'error');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Upload photo via AJAX
        function deletePhoto(photoIndex) {
            const inputId = `photo-input-${photoIndex}`;
            const descriptionId = `photo-description-${photoIndex}`;
            // const fileInput = document.getElementById(inputId);
            const descriptionInput = document.getElementById(descriptionId);
            // const file = fileInput.files[0];

            const formData = new FormData();
            formData.append('photoIndex', photoIndex);
            formData.append('establishmentID', <?php echo json_encode($estID); ?>);

            fetch('delete_photo.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('circle-check', 'Photo deleted successfully!', 'success');
                    document.getElementById(`photo-preview-${photoIndex}`).src = "/bookingapp/assets/images/msu-facade.jpg";
                } else {
                    showToast('circle-xmark', 'Failed to delete photo: ' + data.message, 'error');
                }
            })
            .catch(error => console.error('Error:', error));
        }

            function deleteRoom(roomID, roomName, modalID) {
                openModal(modalID);
                document.getElementById('delete-room-id').value = roomID;
                document.getElementById('delete-modal-title').innerHTML = "Deleting " + roomName;
            }

            var now = new Date().toISOString().split('T')[0];

            // Set date input limits (MAX or MIN)
            function setDateLimit(inputID, date, limit) {
                document.getElementById(inputID).setAttribute(limit, date);
            }
            
            function bookRoom(roomID, price, roomName, estName) {
                openModal('bookRoomModal');
                document.getElementById('book-room-id').value = roomID;
                document.getElementById('book-room-name').value = roomName;
                document.getElementById('book-room-establishment').value = estName;
                document.getElementById('book-room-price').value = new Intl.NumberFormat('en-PH', {
                    style: 'currency',
                    currency: 'PHP'
                }).format(price);
                document.getElementById('book-payment-price').value = price;
                document.getElementById('book-date').setAttribute('min', now);
            }

            // Toogle Payment Channels
            function togglePaymentChannels(selectID, paymentDetailsForm, noteFormID) {
                const selectInput = document.getElementById(selectID);
                const paymentDetails = document.getElementById(paymentDetailsForm);
                const noteForm = document.getElementById(noteFormID);

                if (selectID.value === '1') {
                    paymentDetails.style.display = "none";
                    noteForm.style.display = "block";
                } else {
                    paymentDetails.style.display = "flex";
                    noteForm.style.display = "block";
                }
            }

            document.getElementById('non-cash').style.display = 'none';
            document.getElementById('note-form').style.display = 'none';

            document.getElementById('edit-non-cash').style.display = 'none';
            document.getElementById('edit-note-form').style.display = 'none';

            function editPaymentChannel(estPayId, channelID, accountName, accountNumber, notes) {
                openModal('editPaymentChannelModal');
                document.getElementById('estPayChannelID').value = estPayId;
                document.getElementById('edit-payment-channel').value = channelID;
                document.getElementById('edit-account-name').value = accountName;
                document.getElementById('edit-account-number').value = accountNumber;
                document.getElementById('edit-notes').value = notes;
            }

            togglePaymentChannels('edit-payment-channel', 'edit-non-cash', 'edit-note-form');

            function togglePaymentChannel(estPayID, action, channelName) {
                openModal('togglePaymentChannelModal');
                const modalTitle = document.getElementById("payment-channel-modal-title");
                const toggleActionOutput = document.getElementById("payment-toggle-action");
                var toggleAction = '';
                
                if (action === 1) {
                    modalTitle.innerHTML = "Show " + channelName + " account?";
                    toggleAction = 'Are you going to show this <strong>' + channelName + "</strong> account?";
                    action = 0;
                } else if (action === 0) {
                    modalTitle.innerHTML = "Hide " + channelName + " account?";
                    toggleAction = 'Are you going to hide this <strong>' + channelName + "</strong> account";
                    action = 1;
                }

                toggleActionOutput.innerHTML = toggleAction;
                document.getElementById('epcid').value = estPayID;
                document.getElementById('toggle-action').value = action;
            }

            // Edit review
            function editReview(reviewID, roomID, comments, staff, facilities, cleanliness, comfort, moneyValue, location, signal, security) {
                openModal('editReviewModal');

                document.getElementById('reviewID').value = reviewID;
                document.getElementById('reviewed-room-edit').value = roomID;
                document.getElementById('comment-edit').textContent = comments.replaceAll("<br>", "\n");

                document.getElementById('staff-rate-output-edit').textContent = staff;
                document.getElementById('staff-range-edit').value = staff;

                document.getElementById('facilities-rate-output-edit').textContent = facilities;
                document.getElementById('facilities-range-edit').value = facilities;

                document.getElementById('cleanliness-rate-output-edit').textContent = cleanliness;
                document.getElementById('cleanliness-range-edit').value = staff;

                document.getElementById('comfort-rate-output-edit').textContent = comfort;
                document.getElementById('comfort-range-edit').value = comfort;

                document.getElementById('money-value-rate-output-edit').textContent = moneyValue;
                document.getElementById('money-value-range-edit').value = moneyValue;

                document.getElementById('location-rate-output-edit').textContent = staff;
                document.getElementById('location-range-edit').value = staff;

                document.getElementById('signal-rate-output-edit').textContent = signal;
                document.getElementById('signal-range-edit').value = signal;

                document.getElementById('security-rate-output-edit').textContent = staff;
                document.getElementById('security-range-edit').value = staff;

            }

            function deleteReview(reviewID) {
                openModal('deleteReviewModal');
                document.getElementById('delete-review-id').value = reviewID;
                document.getElementById('delete-modal-title').innerHTML = "Deleting Review #" + reviewID;
            }

            
        function addHashToUrl(id) {
            const form = document.getElementById('filter-tenant-form');
            form.action += '#' + id;
        }

        // Tenant Change Status
        function submitChangeTenantStatus(form, event) {
            event.preventDefault();

            var formData = new FormData(form);
            var url = 'establishment.php';

            var params = new URLSearchParams();
            formData.forEach((value, key) => {
                params.append(key, value);
            });
            params.append('est', '<?php echo base64_encode($estID); ?>');
            params.append('rtf', '<?php echo $_GET['rtf'] ?? ''; ?>');
            params.append('res_st', '<?php echo $_GET['res_st'] ?? ''; ?>');

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'establishment.php', true)
        }

        
    </script>
</body>
</html>
