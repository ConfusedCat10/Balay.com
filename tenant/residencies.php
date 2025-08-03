<?php
include "../database/database.php";

session_start();


function showToast($message, $type, $icon) {
    echo "<script>";
    echo "showToast($icon, $message, $type);";
    echo "</script>";
}



// Determine the active tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'currently%20residing';
$tab = str_replace("%20", " ", $tab);

// echo $tab;

// Handle search query
$searchQuery = $_GET['search'] ?? '';

// Pagination setup
$itemsPerPage = 10; // Number of profile cards per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

$tenantID = isset($_SESSION['tenant']) ? $_SESSION['tenant']['TenantID'] : '0';

// echo $tenantID;

// Get total count for pagination
$countQuery = "SELECT COUNT(*) AS total FROM residency res 
            INNER JOIN tenant t ON t.TenantID = res.TenantID 
            INNER JOIN rooms r ON r.RoomID = res.RoomID
            INNER JOIN establishment e ON e.EstablishmentID = r.EstablishmentID
            WHERE res.Status = '$tab'";
$countResult = mysqli_query($conn, $countQuery);
$totalItems = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

// Fetch paginated data
$dataQuery = "SELECT res.ResidencyID, r.RoomID, r.RoomName, r.RoomType, e.EstablishmentID,
e.Name AS EstablishmentName, e.Type AS EstablishmentType,
ep.Photo1, res.DateOfEntry, res.DateOfExit, res.CreatedAt AS BookingDate,
res.Status AS ResidencyStatus, res.Remark FROM residency res 
INNER JOIN tenant t ON t.TenantID = res.TenantID 
INNER JOIN rooms r ON r.RoomID = res.RoomID
INNER JOIN establishment e ON e.EstablishmentID = r.EstablishmentID
INNER JOIN establishment_photos ep ON ep.EstablishmentID = e.EstablishmentID
WHERE res.Status = '$tab' AND t.TenantID = $tenantID ORDER BY res.DateOfEntry LIMIT $offset, $itemsPerPage";
$dataResult = mysqli_query($conn, $dataQuery);

echo mysqli_error($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Residencies</title>

    <?php
    include "../php/head_tag.php";

    if (!$loggedIn && $accountRole != 'tenant') {
        header("Location: /bookingapp/index.php");
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
            background-color: #6c757d;
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
        <?php include "../php/header.php"; ?>
    </header>

    <div class="container">
        <div class="card overflow-hidden">
            <div class="row no-gutters row-bordered row-border-light" style="flex-wrap: nowrap">
                <div class="tab-container col-md-3">
                    <div class="tabs list-group list-group-flush">
                        <button class="list-group-item list-group-item-action tab-button <?= $tab === 'currently residing' ? 'active' : '' ?>" onclick="loadTab('currently residing')">Current Home</button>
                        <!-- <button class="list-group-item list-group-item-action tab-button <?= $tab === 'reserved' ? 'active' : '' ?>" onclick="loadTab('reserved')">Reservations</button> -->
                        <button class="list-group-item list-group-item-action tab-button <?= $tab === 'residency ended' ? 'active' : '' ?>" onclick="loadTab('residency ended')">Past Homes</button>
                        <button class="list-group-item list-group-item-action tab-button <?= $tab === 'pending' ? 'active' : '' ?>" onclick="loadTab('pending')">Pending confirmation</button>
                        <button class="list-group-item list-group-item-action tab-button <?= $tab === 'cancelled' ? 'active' : '' ?>" onclick="loadTab('cancelled')">Cancelled reservations</button>
                        <button class="list-group-item list-group-item-action tab-button <?= $tab === 'confirmed' ? 'active' : '' ?>" onclick="loadTab('confirmed')">Confirmed reservations</button>
                        <button class="list-group-item list-group-item-action tab-button <?= $tab === 'rejected' ? 'active' : '' ?>" onclick="loadTab('rejected')">Rejected reservations</button>
                    </div>
                </div>

                <!-- Search and Pagination -->
                <div class="card-body search-pagination pb-2">
                    <div class="tab-content">


                        <!-- Profile Cards Container -->
                        <div class="card-container" style="display: flex; gap: 12px; align-items: flex-start">
                            <?php                     
                                if ($dataResult && mysqli_num_rows($dataResult) > 0) {
                                    while ($row = mysqli_fetch_assoc($dataResult)) {
                                        // Extract data from the current row
                                        $residencyID = $row['ResidencyID'];
                                        $encryptedResidencyID = base64_encode($residencyID);
                                        $roomID = $row['RoomID'];
                                        $roomName = $row['RoomName'];
                                        $roomType = $row['RoomType'];
                                        $establishmentID = $row['EstablishmentID'];
                                        $establishmentName = $row['EstablishmentName'];
                                        $establishmentType = $row['EstablishmentType'];
                                        $establishmentPhoto = $row['Photo1'];
                                        $establishmentPhoto = isset($establishmentPhoto) && $establishmentPhoto !== "" ? "/bookingapp/establishment/$establishmentPhoto" : "/bookingapp/assets/images/msu-facade.jpg";

                                        $dateOfEntry = date("F d, Y", strtotime($row['DateOfEntry']));
                                        $dateOfExit = isset($row['DateOfExit']) ? date("F d, Y", strtotime($row['DateOfExit'])) : "";
                                        $bookingDate = date("F d, Y", strtotime($row['BookingDate']));

                                        $residencyStatus = $row['ResidencyStatus'];
                                        $remark = $row['Remark'];

                                        $referenceNumber = $residencyID . date("miyhis", strtotime($bookingDate)) . date("miyhis", strtotime($dateOfEntry));
                    
                            ?>
                                        <div class="profile-card">  
                                            <div class="profile-header" onclick="toggleDetails(this)">
                                                <img src="<?php echo $establishmentPhoto; ?>" alt="<?php echo $establishmentName; ?>" class="profile-pic">
                                                <div class="profile-info">
                                                    <h3 class="profile-name"><a href="/bookingapp/establishment/establishment.php?est=<?php echo base64_encode($establishmentID); ?>"><?php echo $establishmentName; ?></a></h3>
                                                    <p class="profile-role">
                                                        <?php
                                                            echo "$roomName<br>";
                                                        ?>
                                                    </p>
                                                </div>
                                                <span class="toggle-icon"><i class="fa-solid fa-caret-down"></i></span>
                                            </div>

                                            <div class="profile-details hidden">
                                                <p><strong>Room ID:</strong> <?php echo $roomID; ?></p>
                                                <p><strong>Establishment Type:</strong> <?php echo $establishmentType; ?></p>
                                                <p><strong>Room Type:</strong> <?php echo $roomType; ?></p>
                                                <p><strong>Booking date:</strong> <?php echo $bookingDate; ?></p>
                                                <p><strong>Residency started on:</strong> <?php echo $dateOfEntry; ?></p>
                                                <p><strong>Residency ended on:</strong> <?php echo $dateOfExit; ?></p>
                                                <p><strong>Status:</strong> <?php echo $residencyStatus; ?></p>
                                                <p><strong>Reference:</strong> <?php echo $referenceNumber; ?></p>

                                                <?php if (!empty($remark)) { ?>
                                                    <p style="text-align: center; background-color: yellow; color: black; padding: 5px;"><?php echo $remark; ?></p>
                                                <?php } ?>

                                            <div class="profile-actions">
                                                <form action="update_residency_status.php" method="post">
                                                <input type="hidden" name="residency-id" value="<?php echo $residencyID; ?>">
                                                <?php
                                                switch ($tab) {       
                                                    case 'currently residing':
                                                        echo "<button type='button' class='btn' onclick='redirect(\"/bookingapp/establishment/room/confirm_booking.php?id=$encryptedResidencyID\")'><i class='fa-solid fa-receipt'></i> See receipt</button>";
                                                        break;

                                                    case 'pending':
                                                        echo "<button type='submit' name='cancelled' class='btn btn-primary'><i class='fa-solid fa-ban'></i> Cancel reservation</button>";
                                                        break;
        
                                                    case 'confirmed':
                                                        echo "<button type='button' class='btn' style='margin-right: 5px' onclick='redirect(\"/bookingapp/establishment/room/confirm_booking.php?id=$encryptedResidencyID\")'><i class='fa-solid fa-receipt'></i> See receipt</button>";
                                                        echo "<button type='submit' name='cancelled' class='btn'><i class='fa-solid fa-ban'></i> Cancel reservation</button>";
                                                        break;

                                                    case 'cancelled':
                                                        echo "<button type='submit' name='pending'  class='btn'><i class='fa-solid fa-repeat'></i> Reserve again</button>";
                                                        break;
        
                                                    case 'residency ended':
                                                        echo "<button type='submit' name='pending'  class='btn'><i class='fa-solid fa-repeat'></i> Reserve again</button>";
                                                        break;
        
                                                    case 'rejected':
                                                        echo "<button type='submit' name='pending'  class='btn'<i class='fa-solid fa-thumbs-up'></i> Reserve again</button>";
                                                        break;
                                                        
                                                } ?>
                                                </form>
                                            </div>                    
                                        </div>
                                    </div>
                            
                                        
                            <?php
                                    }
                            ?>
                            <?php
                                } else {
                                    echo "<p>No data found for this tab.</p>";
                            }
                            ?>
                        </div>
                        
                            
                    
                        <?php if ($dataResult && mysqli_num_rows($dataResult) > 0) { ?>
                            <!-- Pagination -->
                            <div id="pagination" class="pagination">
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <a href="?tab=<?= htmlspecialchars($tab) ?>&page=<?= $i ?>" class="<?= $i === $currentPage ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
                                </div>
    </div>

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
            window.location.href = `?tab=${tab}`;  
        }

        function toggleResidencyStatus(status, residencyID) {
            const data = { id: residencyID, stat: status };

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
                alert(error);
            });
            status = status.replace(/ /g, "%20");
            // redirect("?tab=" + status);
        }



    </script>
</body>
</html>