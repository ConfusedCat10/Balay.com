<?php
include "../../database/database.php";

session_start();


function showToast($message, $type, $icon) {
    echo "<script>";
    echo "showToast($icon, $message, $type);";
    echo "</script>";
}

// Determine the active tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';
$res_st = isset($_GET['res_st']) ? $_GET['res_st'] : 'currently residing';
$res_st = str_replace("+", " ", $res_st);

// Handle search query
$searchQuery = $_GET['search'] ?? '';;

$searchFilter = '';

if (!empty($searchQuery) || $searchQuery !== '') {
    $searchFilter = " AND (p.FirstName LIKE '%$searchQuery%' OR p.MiddleName LIKE '%$searchQuery%' OR p.LastName LIKE '%$searchQuery%' OR p.ExtName LIKE '%$searchQuery%' OR u.Username LIKE '%$searchQuery%' OR p.Gender LIKE '%$searchQuery%' OR t.UniversityID LIKE '%$searchQuery%' )";
}


// Fetch paginated data
// $dataQuery = "SELECT p.PersonID, p.F";
// $dataResult = mysqli_query($conn, $dataQuery);

echo mysqli_error($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Tenants</title>

    <?php
    include "../../php/head_tag.php";


    $adminID = $admin['AdminID'] ?? null;

    $adminID = $_SESSION['admin']['AdminID'] ?? null;
    
    $isUserOwner = false;

    if (isset($owner)) {
        $isUserOwner = true;
    }

    $ownerID = $owner['OwnerID'] ?? null;

    // echo $ownerID;

    if (!$isLoggedIn || $accountRole !== 'owner') {
        header("Location: /bookingapp/page_not_found.php");
    }
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

        .col-md-3, .col-md-9 {
            padding: 10px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border-radius: 20px;
        }

        #search-form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2px;
        }

        #search-form input[type='text'] {
            width: 100%;
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

        .card-body {
            display: flex;
            flex-direction: column;
            gap: 2px;

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

        .tab-content {
            height: 100%;
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
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 15px;
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
            background-color: white;
            color: black
        }

        .btn:hover {
            background-color: #ffd700;
            color: black;
        }

        .tab-container {
            padding: 20px;
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
        
        /* Add a grey background color on mouse-over */
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }

        .pagination a.active:hover {
            background-color: var(--secondary-color);
            color: black;
        }

        .search-pagination {
            padding: 10px;
        }

        .modal-close {
            text-align: right;
            font-size: 36px;
            cursor: pointer;
        }

        .modal-close:hover {
            color: #FFD700;
        }

        .modal-header {
            display: flex;
            flex-direction: row-reverse;
        }

        .modal-footer {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
        }

        @media (max-width: 1000px) {
            .form-inline input, .form-inline select {
                width: 100% !important;
            }

            .form-inline {
                display: block;
            }
            
            .profile-card {
                max-width: 100%;
            }

            nav {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <?php include "../../php/header.php"; ?>
    </header>

    <?php
        // Get establishments under owner
        $establishments = array();
        $estQuery = "SELECT * FROM establishment e WHERE e.OwnerID = $ownerID AND e.Status != 'removed' ORDER BY e.Name";
        $estResult = mysqli_query($conn, $estQuery);

        echo mysqli_error($conn);

        // echo $ownerID;

        // echo mysqli_num_rows($estResult);    

    ?>

    <div class="container">
        <h1>Residents</h1>
        <div class="card overflow-hidden">
            <div class="row no-gutters row-bordered row-border-light" style="flex-wrap: nowrap">
                <div class="tab-container col-md-3">
                    <div class="tabs list-group list-group-flush">
                        <button class="list-group-item list-group-item-action tab-button <?= $tab === 'all' ? 'active' : '' ?>" onclick="loadTab('all')">All establishments</button>
                        <?php while ($establishments = mysqli_fetch_assoc($estResult)) { 
                            $estID = $establishments['EstablishmentID']; 
                            $estName = $establishments['Name'];
                            $estType = $establishments['Type'];
                            ?>
                            <button class="list-group-item list-group-item-action tab-button <?= $tab === $estID ? 'active' : '' ?>" onclick="loadTab('<?php echo $estID; ?>')"><?php echo $estName; ?></button>
                        <?php } ?>
                    </div>
                </div>

                <?php
                $rooms = array();
                ?>

                <!-- Search and Pagination -->
                <div class="card-body search-pagination pb-2">
                    <div class="tab-content">
                        <!-- Profile Cards Container -->
                        <form id="search-form" method="GET" style="margin-bottom: 20px">
                            <?php $currentPage = 1; ?>
                            <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
                            <input type="text" name="search" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Search people and accounts..." value="<?php echo $searchQuery; ?>" style="width: 60%" />
                            <select name="res_st" style="width: 30%">
                                <option value="currently residing" <?php if ($res_st === 'currently residing') { echo 'selected'; } ?>>Current tenants</option>
                                <option value="residency ended" <?php if ($res_st === 'residency ended') { echo 'selected'; } ?>>Past tenants</option>
                                <option value="confirmed" <?php if ($res_st === 'confirmed') { echo 'selected'; } ?>>Reserved tenants</option>
                                <option value="pending" <?php if ($res_st === 'pending') { echo 'selected'; } ?>>Pending reservations</option>
                                <option value="cancelled" <?php if ($res_st === 'cancelled') { echo 'selected'; } ?>>Cancelled reservations</option>
                                <option value="rejected" <?php if ($res_st === 'rejected') { echo 'selected'; } ?>>Rejected tenants</option>
                            </select>
                            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                              
                            <?php 
                            if ($tab === 'all') {
                                $tenants = array();
                                $tenantSql = "SELECT p.ProfilePicture, p.FirstName, p.MiddleName, p.LastName, p.ExtName, p.Gender, u.Username, t.UniversityID, rs.ResidencyID, rs.DateOfEntry, rs.UpdateAt AS LastUpdate, rs.DateOfExit, rs.Status AS ResidencyStatus, r.RoomName, r.RoomType, rs.CreatedAt AS BookingDate, e.Name AS EstablishmentName, rs.Remark FROM residency rs INNER JOIN tenant t ON t.TenantID = rs.TenantID INNER JOIN rooms r ON r.RoomID = rs.RoomID INNER JOIN user_account u ON u.UserID = t.UserID INNER JOIN person p ON p.PersonID = u.PersonID INNER JOIN establishment e ON e.EstablishmentID = r.EstablishmentID WHERE e.OwnerID = $ownerID AND rs.Status = '$res_st' $searchFilter ORDER BY p.LastName, p.FirstName, p.ExtName, p.MiddleName";
                                $tenantResult = mysqli_query($conn, $tenantSql);
                                
                                if (mysqli_num_rows($tenantResult) > 0) {
                                    // echo "<h4>Current tenants</h4>"; ?>

                                    <div class="card-container" style="display: flex; gap: 12px;align-items: flex-start">       
                            <?php

                                    while ($tenants = mysqli_fetch_assoc($tenantResult)) {
                                    $gender = $tenants['Gender'] ?? 'Male';
                                    $profilePicture = $tenants['ProfilePicture'] ?? "/bookingapp/user/$gender-no-face.jpg";

                                    $firstName = $tenants['FirstName'] ?? '';
                                    $middleName = isset($tenants['MiddleName']) ? $tenants['MiddleName']     : '';
                                    $lastName = $tenants['LastName'] ?? '';
                                    $extName = $tenants['ExtName'] ?? '';

                                    $fullName = $firstName . ' ' . $middleName . ' ' . $lastName;

                                    if (!empty($extName)) {
                                        $fullName .= ' ' . $extName;
                                    }

                                    $username = $tenants['Username'];

                                    $universityID = $tenants['UniversityID'];

                                    $residencyID = $tenants['ResidencyID'];
                                    $residencyStatus = $tenants['ResidencyStatus'];
                                    $remark = $tenants['Remark'];

                                    $remark = str_replace("\'", "'", $remark);

                                    $dateOfEntry = $tenants['DateOfEntry'];
                                    $bookingDate = $tenants['BookingDate'];
                                    $dateOfExit = $tenants['DateOfExit'];
                                    $lastUpdate = $tenants['LastUpdate'];

                                    $dateOfEntry = isset($dateOfEntry) ? date('F d, Y', strtotime($dateOfEntry)) : '';

                                    $dateOfExit = isset($dateOfExit) ? date('F d, Y', strtotime($dateOfExit)) : '';

                                    $bookingDate = isset($dateOfEntry) ? date('F d, Y h:i:s a', strtotime($bookingDate)) : '';

                                    $lastUpdate = isset($lastUpdate) ? date('F d, Y h:i:s a', strtotime($lastUpdate)) : '';

                                    $estName = $tenants['EstablishmentName'];

                                    $roomName = $tenants['RoomName'];
                                    $roomType = $tenants['RoomType'];

                                    $referenceNumber = $residencyID . date("miyhis", strtotime($bookingDate)) . date("miyhis", strtotime($dateOfEntry));

                            ?>

                                <div class="profile-card">  
                                    <div class="profile-header" onclick="toggleDetails(this)">
                                        <img src="<?php echo $profilePicture; ?>" alt="<?php echo $lastName; ?>" class="profile-pic">
                                        <div class="profile-info">
                                            <h3 class="profile-name">
                                                <a href="/bookingapp/user/profile.php?id=<?php echo $username; ?>" target="_blank" style="color: maroon;">
                                                    <?php echo $fullName; ?>
                                                </a>
                                            </h3>
                                            <p class="profile-role">
                                                <?php
                                                    echo "@$username<br>
                                                    $estName";
                                                ?>
                                            </p>
                                        </div>
                                        <span class="toggle-icon"><i class="fa-solid fa-caret-down"></i></span>
                                    </div>

                                    <div class="profile-details hidden">
                                        <p><strong>Reference:</strong> <?php echo $referenceNumber; ?></p>
                                        <p><strong>Start of residency:</strong> <?php echo $dateOfEntry; ?></p>
                                        <p><strong>End of residency:</strong> <?php echo $dateOfExit; ?></p>
                                        <p><strong>Room:</strong> <?php echo "$roomName - $roomType"; ?></p>
                                        <p><strong>Booking date:</strong> <?php echo $bookingDate; ?></p>
                                        <p><strong>Last update:</strong> <?php echo $lastUpdate; ?></p>

                                        <?php if (!empty($remark)) { ?>
                                            <p style="text-align: center; background-color: yellow; color: black; padding: 5px;"><?php echo $remark; ?></p>
                                        <?php } ?>

                                        <div class="profile-actions">
                                            <!-- <form action="update_residency_status.php" method="post"> -->
                                            <?php
                                            switch ($residencyStatus) {
                                                case 'currently residing':
                                                    echo "<button class='btn' onclick='toggleResidencyStatus(\"residency ended\", $residencyID, \"Residency ended by owner\")'><i class='fa-solid fa-door-open'></i> End residency</button>";
                                                    break;
    
                                                case 'pending':
                                                    echo "<button class='btn btn-primary' onclick='toggleResidencyStatus(\"confirmed\", $residencyID, \"Reservation confirmed by owner\")'><i class='fa-solid fa-thumbs-up'></i> Accept</button>";
                                                    echo "<button class='btn btn-secondary' onclick='openRemarkModal()'><i class='fa-solid fa-thumbs-down'></i> Reject</button>";

                                                    include "../../modal/residency_remark_modal.php";

                                                    break;
    
                                                case 'confirmed':
                                                    echo "<button class='btn' onclick='toggleResidencyStatus(\"cancelled\", $residencyID, \"Reservation cancelled by owner\")'><i class='fa-solid fa-ban'></i> Cancel reservation</button>";
                                                    break;
    
                                                case 'residency ended':
                                                    echo "<button class='btn' onclick='toggleResidencyStatus(\"currently residing\", $residencyID, \"Renewed by owner\")'><i class='fa-solid fa-repeat'></i> Renew residency</button>";
                                                    break;
    
                                                case 'rejected':
                                                    echo "<button class='btn' onclick='toggleResidencyStatus(\"currently residing\", $residencyID, \"Reconsidered by owner\")'><i class='fa-solid fa-thumbs-up'></i> Reconsider accepting</button>";
                                                    break;
                                                    
                                            }
                                            ?>
                                            <!-- </form> -->
                                        </div>                    
                                    </div>
                                </div>

                            <?php } ?>
                                </div>
                            <?php
                                }
                            } else {
                                $roomSql = "SELECT * FROM rooms WHERE EstablishmentID = $tab AND Availability != 'Deleted' ORDER BY RoomName";
                                $roomResult = mysqli_query($conn, $roomSql);

                                if (mysqli_num_rows($roomResult) > 0) {
                                    while ($rooms = mysqli_fetch_assoc($roomResult)) {
                                        $roomID = $rooms['RoomID'];
                                        $roomName = $rooms['RoomName'];
                                        $roomType = $rooms['RoomType']; 
                                        $maxOccupancy = $rooms['MaxOccupancy'];
                                        echo "<h5>$roomName</h5>";
                                        echo "<p style='font-size: 12px; color: grey'>$roomType &middot; Good for $maxOccupancy person(s)</p>";
                                        // echo "<hr>";

                                        $tenantSql = "SELECT p.ProfilePicture, p.FirstName, p.MiddleName, p.LastName, p.ExtName, p.Gender, u.Username, t.UniversityID, rs.ResidencyID, rs.DateOfEntry, rs.DateOfExit, rs.Status AS ResidencyStatus, r.RoomName, r.RoomType, rs.CreatedAt AS BookingDate, rs.UpdateAt AS LastUpdate, e.Name AS EstablishmentName, rs.Remark FROM residency rs INNER JOIN tenant t ON t.TenantID = rs.TenantID INNER JOIN rooms r ON r.RoomID = rs.RoomID INNER JOIN user_account u ON u.UserID = t.UserID INNER JOIN person p ON p.PersonID = u.PersonID INNER JOIN establishment e ON e.EstablishmentID = r.EstablishmentID WHERE r.RoomID = $roomID AND rs.Status = '$res_st' $searchFilter ORDER BY p.LastName, p.FirstName, p.ExtName, p.MiddleName";
                                        $tenantResult = mysqli_query($conn, $tenantSql);

                                        if (mysqli_num_rows($tenantResult) > 0) {
                                            echo "<p style='float: right; text-align: right; font-size: 10px; color: grey'>No of tenants: " . mysqli_num_rows($tenantResult) . "</p>";?>
                                            <div class="card-container" style="display: flex; gap: 12px; border-bottom: 1px solid #ccc; margin-bottom: 20px;padding-bottom: 10px; align-items: flex-start">   
                                            <?php
                                            while ($tenants = mysqli_fetch_assoc($tenantResult)) {

                                                $gender = $tenants['Gender'] ?? 'Male';
                                                $profilePicture = $tenants['ProfilePicture'] ?? "/bookingapp/user/$gender-no-face.jpg";

                                                $firstName = $tenants['FirstName'] ?? '';
                                                $middleName = isset($tenants['MiddleName']) ? $tenants['MiddleName'] : '';
                                                $lastName = $tenants['LastName'] ?? '';
                                                $extName = $tenants['ExtName'] ?? '';

                                                $fullName = $firstName . ' ' . $middleName . ' ' . $lastName;

                                                if (!empty($extName)) {
                                                    $fullName .= ' ' . $extName;
                                                }

                                                $username = $tenants['Username'];

                                                $universityID = $tenants['UniversityID'];

                                                $residencyID = $tenants['ResidencyID'];
                                                $residencyStatus = $tenants['ResidencyStatus'];

                                                $remark = $tenants['Remark'];

                                                $dateOfEntry = $tenants['DateOfEntry'];
                                                $bookingDate = $tenants['BookingDate'];
                                                $dateOfExit = $tenants['DateOfExit'];
                                                $lastUpdate = $tenants['LastUpdate'];

                                                $dateOfEntry = isset($dateOfEntry) ? date('F d, Y', strtotime($dateOfEntry)) : '';

                                                $dateOfExit = isset($dateOfExit) ? date('F d, Y', strtotime($dateOfExit)) : '';

                                                $bookingDate = isset($dateOfEntry) ? date('F d, Y h:i:s a', strtotime($bookingDate)) : '';

                                                $lastUpdate = isset($lastUpdate) ? date('F d, Y h:i:s a', strtotime($lastUpdate)) : '';

                                                $estName = $tenants['EstablishmentName'];

                                                $roomName = $tenants['RoomName'];
                                                $roomType = $tenants['RoomType'];

                                                $referenceNumber = $residencyID . date("miyhis", strtotime($bookingDate)) . date("miyhis", strtotime($dateOfEntry));

                                        ?>

                                <div class="profile-card"> 
                                    <div class="profile-header" onclick="toggleDetails(this)">
                                        <img src="<?php echo $profilePicture; ?>" alt="<?php echo $lastName; ?>" class="profile-pic">
                                        <div class="profile-info">
                                            <h3 class="profile-name">
                                                <a href="/bookingapp/user/profile.php?id=<?php echo $username; ?>" target="_blank" style="color: maroon;">
                                                    <?php echo $fullName; ?>
                                                </a>
                                            </h3>
                                            <p class="profile-role">
                                                <?php
                                                    echo "@$username<br>
                                                    $estName";
                                                ?>
                                            </p>
                                        </div>
                                        <span class="toggle-icon"><i class="fa-solid fa-caret-down"></i></span>
                                    </div>

                                    <div class="profile-details hidden">
                                        <p><strong>Reference:</strong> <?php echo $referenceNumber; ?></p>
                                        <p><strong>Start of residency:</strong> <?php echo $dateOfEntry; ?></p>
                                        <p><strong>End of residency:</strong> <?php echo $dateOfExit; ?></p>
                                        <p><strong>Room:</strong> <?php echo "$roomName - $roomType"; ?></p>
                                        <p><strong>Booking date:</strong> <?php echo $bookingDate; ?></p>
                                        <p><strong>Last Update:</strong> <?php echo $lastUpdate; ?></p>

                                        <?php if (!empty($remark)) { ?>
                                            <p style="text-align: center; background-color: yellow; color: black; padding: 5px;"><?php echo $remark; ?></p>
                                        <?php } ?>

                                        <div class="profile-actions">
                                        <?php

                                        switch ($residencyStatus) {
                                            case 'currently residing':
                                                echo "<button class='btn' onclick='toggleResidencyStatus(\"residency ended\", $residencyID)'><i class='fa-solid fa-door-open'></i> End residency</button>";
                                                break;

                                            case 'pending':
                                                echo "<button class='btn btn-primary' onclick='toggleResidencyStatus(\"confirmed\", $residencyID)'><i class='fa-solid fa-thumbs-up'></i> Accept</button>";
                                                echo "<button class='btn btn-secondary' onclick='op'><i class='fa-solid fa-thumbs-down'></i> Reject</button>";
                                                break;

                                            case 'confirmed':
                                                echo "<button class='btn' onclick='toggleResidencyStatus(\"cancelled\", $residencyID)'><i class='fa-solid fa-ban'></i> Cancel reservation</button>";
                                                break;

                                            case 'residency ended':
                                                echo "<button class='btn' onclick='toggleResidencyStatus(\"currently residing\", $residencyID)'><i class='fa-solid fa-repeat'></i> Renew residency</button>";
                                                break;

                                            case 'rejected':
                                                echo "<button class='btn' onclick='toggleResidencyStatus(\"currently residing\", $residencyID)'><i class='fa-solid fa-thumbs-up'></i> Reconsider accepting</button>";
                                                break;
                                                
                                        } ?>
                                        </div>                    
                                    </div>
                                </div>

                                    

                                    <?php }
                                    echo "</div>";
                                        } else {
                                            echo "<p style='text-align: center; font-weight: bold'>Vacant room</p>";
                                        }
                                    }
                                }
                                
                                
                                    // echo "<h4>Current tenants</h4>"; ?>

                                    
                            <?php 
                            } ?>
                    </div>
                </div>
            </div>

        </div>
                                </div>
    </div>

    <!-- Footer -->
    <?php include "../../php/footer.php"; ?>

    

    <!-- <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script> -->
    <script src="/bookingapp/js/jquery-1.10.2.min.js"></script>
    <script src="/bookingapp/js/bootstrap.bundle.min.js"></script>
    <script src="/bookingapp/js/script.js"></script>
    <script src="/bookingapp/js/scrollreveal.js "></script>
    <script src="/bookingapp/js/rayal.js"></script>
    <script type="text/javascript"></script>

    <script>

        // Modal
        function openRemarkModal() {
            document.getElementById('statusRemarkModal').style.display = "flex";
        }

        function closeModal(modalID) {
            document.getElementById(modalID).style.display = "none";
        }

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

        // Set the current year in the footer dynamically
        document.getElementById('year').textContent = new Date().getFullYear();

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


        function loadTab(tab) {
            var res_st = '<?php echo $_GET['res_st'] ?? ''; ?>';
            window.location.href = `?tab=${tab}&res_st=${res_st}`;  
        }

        function toggleResidencyStatus(status, residencyID, remark) {

            if (remark === 'rejected') {
                remark = document.getElementById('status-remark').value;
            }

            const data = { id: residencyID, stat: status, rmrk: remark };


            fetch('update_residency_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type' : 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
            })
            .catch(error => {
                console.error('Error:', error);
                // alert(error);
            });
            status = status.replace(/ /g, "+");
            var currentTab = '<?php echo $tab; ?>';
            redirect("?res_st=" + status + "&tab=" + currentTab + "&rmrk=" + remark);
        }

        // loadTab('current');

        // function performSearch() {
        //     const activeTab = document.querySelector(".tab-button.active")?.dataset.tab || "all";
        //     const searchQuery = document.getElementById("search-input").value || "";
        //     fetchData(activeTab, 1, searchQuery);
        // }

        // function fetchData(tab, page, searchQuery) {
        //     const url = `accounts.php?tab=${tab}&page=${page}&search=${encodeURIComponent(searchQuery)}`;
            
        //     fetch(url)
        //         .then((response) => response.text())
        //         .then((html) => {
        //             // Populate the profile cards and pagination
        //             const parser = new DOMParser();
        //             const doc = parser.parseFormString(html, "text/html");

        //             const cardsContainer = document.getElementById("profile-cards");
        //             const paginationContainer = document.getElementById("pagination");

        //             const newCards = doc.querySelector("#profile-cards");
        //             const newPagination = doc.querySelector("#pagination");

        //             cardsContainer.innerHTML = newCards.innerHTML;
        //             paginationContainer.innerHTML = newPagination.innerHTML;

        //             // Highlight the active tab
        //             document.querySelectorAll(".tab-button").forEach((button) => {
        //                 button.classList.toggle("active", button.dataset.tab === tab);
        //             });
        //         })
        //         .catch((error) => console.error("Error loading data: ", error));
        // }

        // // Handle pagination button clicks dynamically
        // document.getElementById("pagination").addEventListener("click", function (event) {
        //     const target = event.target;
        //     if (target.tagName === "A") {
        //         event.preventDefault();

        //         const tab = document.querySelector(".tab-button.active").dataset.tab;
        //         const page = target.dataset.page;
        //         const searchQuery = document.getElementById("search-input").value || "";

        //         fetchData(tab, page, searchQuery);
        //     }
        // });



    </script>
</body>
</html>