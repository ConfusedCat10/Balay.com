<?php
include "../database/database.php";

session_start();


function showToast($message, $type, $icon) {
    echo "<script>";
    echo "showToast($icon, $message, $type);";
    echo "</script>";
}


// Pagination setup
$itemsPerPage = 5; // Number of items
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Today's reservation reports</title>

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

        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: maroon;
            color: white;
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

    <?php
    $sql = "SELECT p.ProfilePicture, p.FirstName, p.MiddleName, p.LastName, p.ExtName, p.Gender, u.Username, u.UserID, r.RoomName, r.RoomType, e.EstablishmentID, e.Name AS EstablishmentName, e.Type AS EstablishmentType, r.PaymentRate, rs.ResidencyID, rs.DateOfEntry, rs.DateOfExit, rs.Status AS ResidencyStatus, rs.Remark, rs.CreatedAt AS BookingDate, rs.UpdateAt AS BookingUpdate FROM residency rs INNER JOIN tenant t ON t.TenantID = rs.TenantID INNER JOIN rooms r ON r.RoomID = rs.RoomID INNER JOIN establishment e ON e.EstablishmentID = r.EstablishmentID INNER JOIN user_account u ON u.UserID = t.UserID INNER JOIN person p ON p.PersonID = u.PersonID ORDER BY rs.UpdateAt DESC LIMIT $offset, $itemsPerPage";
    $result = mysqli_query($conn, $sql);

    echo mysqli_error($conn);
    $rowCount = mysqli_num_rows($result);

    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) AS total FROM residency";
    $countResult = mysqli_query($conn, $countQuery);
    $totalItems = mysqli_fetch_assoc($countResult)['total'];
    $totalPages = ceil($totalItems / $itemsPerPage);

    ?>

    <div class="container clearfix">
        <h2>Reports</h2>
        <p>Results: <?php echo $rowCount; ?></p>
        <?php 
            if ($rowCount > 0) {
               ?>
        <div class="table-container" style="margin-top: 10px;">
            <table id="residencyTable">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Dormitory / Room</th>
                        <th>Booking date</th>
                        <th>Last update</th>
                        <th>Residency</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($result)) { 
                            
                            $profilePicture = $row['ProfilePicture'];
                            $gender = $row['Gender'];

                            $profilePicture = isset($profilePicture) ? $profilePicture : "/bookingapp/user/$gender-no-face.jpg";

                            $firstName = $row['FirstName'];
                            $lastName = $row['LastName'];
                            $extName = $row['ExtName'];

                            $fullName = "$lastName, $firstName $extName";

                            $username = $row['Username'];
                            $userID = $row['UserID'];

                            $estName = $row['EstablishmentName'];
                            $estID = $row['EstablishmentID'];
                            $encryptedEstID = base64_encode($estID);

                            $roomName = $row['RoomName'];
                            $roomType = $row['RoomType'];
                            $paymentRate = $row['PaymentRate'];

                            $residencyID = $row['ResidencyID'];
                            $residencyStatus = $row['ResidencyStatus'];
                            $remark = $row['Remark'];

                            $bookingDate = $row['BookingDate'];
                            $bookingDate = isset($bookingDate) ? date('F d, Y h:i:s a', strtotime($bookingDate)) : "";

                            $bookingUpdate = $row['BookingUpdate'];
                            $bookingUpdate = isset($bookingUpdate) ? date('F d, Y h:i:s a', strtotime($bookingUpdate)) : "";

                            $dateOfEntry = $row['DateOfEntry'];
                            $dateOfEntry = isset($dateOfEntry) ? date('F d, Y', strtotime($dateOfEntry)) : "";

                            $dateOfExit = $row['DateOfExit'];
                            $dateOfExit = isset($dateOfExit) ? date('F d, Y', strtotime($dateOfExit)) : "";



                    ?>
                    <tr>
                        <td style="display: flex; ">
                            <img src="<?php echo $profilePicture; ?>" alt="<?php echo $username; ?>" style="width: 90px; height: 90px; object-fit: cover; border-radius: 100%; border: 2px solid maroon;">
                            <div class="tenant-details" style="align-items: flex-start; gap: 2px; display: flex; flex-direction: column; justify-content: center; text-align: left; margin-left: 20px">
                                <h6><a href="/bookingapp/user/profile.php?id=<?php echo $username; ?>"><?php echo $fullName; ?></a></h6>
                                <p>@<?php echo $username; ?></p>
                            </div>
                        </td>
                        <td>
                            <h6><a href="/bookingapp/establishment/establishment.php?est=<?php echo $encryptedEstID; ?>"><?php echo $estName; ?></a></h6>
                            <p><?php echo "$roomName | $roomType | " .  number_format($paymentRate, 2); ?></p>
                        </td>
                        <td>
                            <?php echo $bookingDate; ?>
                        </td>
                        <td>
                            <?php echo $bookingUpdate; ?>
                        </td>
                        <td>
                            <?php echo $dateOfEntry; ?>
                            <?php
                            if ($residencyStatus === 'currently residing') {
                                echo "- present";
                            } else if ($residencyStatus === 'residency ended') {
                                echo "- $dateOfExit";
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo "$residencyStatus <br><hr> $remark"; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php } ?>
        
        <?php if ($result) { ?>
            <!-- Pagination -->
            <div class="pagination">
                <?php
                for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" <?php if ($currentPage === $i) { echo "class='active'"; } ?>>
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php } ?>
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