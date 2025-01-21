<?php
include "../database/database.php";

session_start();


function showToast($message, $type, $icon) {
    echo "<script>";
    echo "showToast($icon, $message, $type);";
    echo "</script>";
}

// Determine the active tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';

// Handle search query
$searchQuery = $_GET['search'] ?? '';

// Pagination setup
$itemsPerPage = 10; // Number of profile cards per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Build the base query for the active tab
switch ($tab) {
    case 'admin':
        $baseQuery = "WHERE u.Role = 'admin' AND u.Status != 'deleted'";
        $innerJoin = "INNER JOIN admin a ON a.UserID = u.UserID";
        $additionalData = ", a.PositionTitle, a.Institution";
        break;

    case 'owner':
        $baseQuery = "WHERE u.Role = 'owner' AND u.Status != 'deleted'";
        $innerJoin = "INNER JOIN establishment_owner o ON o.UserID = u.UserID";
        $additionalData = ", o.PositionTitle, o.Institution";
        break;
            
    case 'tenant':
        $baseQuery = "WHERE u.Role = 'tenant' AND u.Status != 'deleted'";
        $innerJoin = "INNER JOIN tenant t ON t.UserID = u.UserID";
        $additionalData = ", t.UniversityID";
        break;

    case "all":
        $baseQuery = "";
        $innerJoin = "";
        $additionalData = "";
        break;
}

// Add search condition if a search query exists
if (!empty($searchQuery)) {
    $baseQuery .= " AND (p.FirstName LIKE '%$searchQuery%' OR p.MiddleName LIKE '%$searchQuery%' OR p.LastName LIKE '%$searchQuery%' OR p.ExtName LIKE '%$searchQuery%' OR p.Gender LIKE '%$searchQuery%' OR p.HomeAddress LIKE '%$searchQuery%' OR p.ContactNumber LIKE '%$searchQuery%' OR u.Username LIKE '%$searchQuery%' OR u.EmailAddress LIKE '%$searchQuery%')";
}

// Get total count for pagination
$countQuery = "SELECT COUNT(*) AS total FROM user_account u $innerJoin INNER JOIN person p ON p.PersonID = u.PersonID  $baseQuery";
$countResult = mysqli_query($conn, $countQuery);
$totalItems = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

// Fetch paginated data
$dataQuery = "SELECT p.PersonID, p.ProfilePicture, p.FirstName, p.MiddleName, p.LastName, p.ExtName, p.DateOfBirth, p.Gender, p.HomeAddress, p.ContactNumber, u.UserID, u.Username, u.Role, u.IsEmailVerified, u.EmailAddress, u.DateCreated, u.Status $additionalData FROM user_account u $innerJoin INNER JOIN person p ON p.PersonID = u.PersonID $baseQuery LIMIT $offset, $itemsPerPage";
$dataResult = mysqli_query($conn, $dataQuery);

echo mysqli_error($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Accounts</title>

    <?php
    include "../php/head_tag.php";
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
            background-color: #ffd700;
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

    <div class="container clearfix">
        <div class="card overflow-hidden">
            <div class="row no-gutters row-bordered row-border-light" style="flex-wrap: nowrap">
                <div class="tab-container col-md-3">
                    <div class="tabs list-group list-group-flush">
                        <button class="list-group-item list-group-item-action tab-button <?= $tab === 'all' ? 'active' : '' ?>" onclick="loadTab('all')">All Users</button>
                        <button class="list-group-item list-group-item-action tab-button <?= $tab === 'admin' ? 'active' : '' ?>" onclick="loadTab('admin')">Admins</button>
                        <button class="list-group-item list-group-item-action tab-button <?= $tab === 'tenant' ? 'active' : '' ?>" onclick="loadTab('tenant')">Tenants</button>
                        <button class="list-group-item list-group-item-action tab-button <?= $tab === 'owner' ? 'active' : '' ?>" onclick="loadTab('owner')">Owners</button>
                    </div>
                </div>

                <!-- Search and Pagination -->
                <div class="card-body search-pagination pb-2">
                    <div class="tab-content">
                        <?php
                        if ($tab === 'owner') { ?>
                        <button type="button" class="btn btn-primary" onclick="redirect('/bookingapp/create_account/create.php?role=owner')" style="float: right; margin-left: 10px; align-items: center">Create an owner account</button>
                        <?php } ?>

                        <?php if ($tab === 'admin') { ?>
                        <button type="button" class="btn btn-primary" onclick="redirect('/bookingapp/create_account/create.php?role=admin')" style="float: right; margin-left: 10px; align-items: center">Create an admin account</button>
                        <?php } ?>
                        <!-- Search Form -->
                        <form id="search-form" method="GET">
                            <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
                            <input type="hidden" name="page" value="<?= htmlspecialchars($currentPage) ?>">
                            <input type="text" name="search" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Search people and accounts..." />
                            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>



                        <!-- Profile Cards Container -->
                        <div class="card-container" style="align-items: flex-start;">
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

                                        $thisMiddleInitials = "";

                                        if (!empty($thisMiddleName)) {
                                            $thisMiddleInitials = $thisMiddleName[0] . '.';
                                        }

                                        $thisFullName = $thisFirstName . ' ' . $thisMiddleInitials . ' ' . $thisLastName . ' ' . $thisExtName;
                                        
                                        $thisDateOfBirth = htmlspecialchars($row['DateOfBirth']);
                                        $thisGender = htmlspecialchars($row['Gender']);

                                        $thisHomeAddress = htmlspecialchars($row['HomeAddress']);
                                        $thisContactNumber = htmlspecialchars($row['ContactNumber']);
                                        
                                        $thisUserID = htmlspecialchars($row['UserID']);
                                        $thisUsername = htmlspecialchars($row['Username']);
                                        $thisRole = htmlspecialchars($row['Role']);
                                        $thisIsEmailVerified = htmlspecialchars($row['IsEmailVerified']);
                                        $thisEmailAddress = htmlspecialchars($row['EmailAddress']);
                                        $thisDateCreated = htmlspecialchars($row['DateCreated']);
                                        $thisUserStatus = htmlspecialchars($row['Status']);

                                        if ($tab !== 'all') {
                                            if ($thisRole === 'admin' || $thisRole === 'owner') {
                                                $thisPositionTitle = htmlspecialchars($row['PositionTitle']);
                                                $thisInstitution = htmlspecialchars($row['Institution']);
                                            } else if ($thisRole === 'tenant') {
                                                $thisUniversityID = htmlspecialchars($row['UniversityID']);
                                            }
                                        }

                                        // Calculate age if date of birth is available
                                        $thisAge = !empty($thisDateOfBirth) ? date_diff(date_create($thisDateOfBirth), date_create('today'))->y : '';

                                        // Show default profile picture based on the gender when profile picture is not available
                                        if (empty($thisProfilePicture) || !$thisProfilePicture) {
                                            $thisProfilePicture = "/bookingapp/user/$thisGender-no-face.jpg";
                                        }
                            ?>
                                        <div class="profile-card">  
                                            <div class="profile-header" onclick="toggleDetails(this)">
                                                <img src="<?php echo $thisProfilePicture; ?>" alt="<?php echo $thisLastName; ?>" class="profile-pic">
                                                <div class="profile-info">
                                                    <h3 class="profile-name"><?php echo $thisFullName; ?></h3>
                                                    <p class="profile-role">
                                                        <?php
                                                        if ($tab === 'all') {
                                                            echo "$thisRole<br>";
                                                        } else {
                                                            if ($thisRole === 'owner' || $thisRole === 'admin') {
                                                                echo "$thisPositionTitle<br>$thisInstitution";
                                                            } else if ($thisRole === 'tenant') {
                                                                echo "ID Number: $thisUniversityID";
                                                            }
                                                        }
                                                        ?>
                                                    </p>
                                                </div>
                                                <span class="toggle-icon"><i class="fa-solid fa-caret-down"></i></span>
                                            </div>

                                            <div class="profile-details hidden">
                                                <p><strong>Gender:</strong> <?php echo $thisGender; ?></p>
                                                <p><strong>Age:</strong> <?php echo $thisAge; ?></p>
                                                <p><strong>Address:</strong> <?php echo $thisHomeAddress; ?></p>
                                                <p><strong>Contact:</strong> <?php echo $thisContactNumber; ?></p>
                                                <p><strong>Username:</strong> <?php echo $thisUsername; ?></p>
                                                <p><strong>Email:</strong> <?php echo $thisEmailAddress; ?></p>
                                                <!-- <p><strong>Status:</strong> <?php echo $thisUserStatus; ?></p> -->
                                                <p><strong>Date created:</strong> <?php echo date('d F Y', strtotime($thisDateCreated)); ?></p>
                                            </div>

                                            <div class="profile-actions">
                                                <button class="btn" onclick="redirect('/bookingapp/user/profile.php?id=<?php echo $thisUsername; ?>')"><i class="fa-solid fa-eye"></i> View Profile</button>
                                                <!-- <button class="btn btn-secondary"><i class="fa-solid fa-edit"></i> Edit</button> -->
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
                        
                            
                        <?php if ($dataResult) { ?>
                            <!-- Pagination -->
                            <div class="pagination">
                                <?php
                                for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <a href="?tab=<?= htmlspecialchars($tab) ?>&page=<?= $i ?>&search=<?= htmlspecialchars($searchQuery) ?>" <?php if ($currentPage === $i) { echo "class='active'"; } ?>>
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