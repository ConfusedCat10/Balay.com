<?php
include "../database/database.php";

session_start();

function showToast($message, $type, $icon) {
    echo "<script>";
    echo "showToast($icon, $message, $type);";
    echo "</script>";
}

// Pagination setup
$itemsPerPage = 10; // Number of profile cards per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

$tenantID = $_SESSION['tenant']['TenantID'];

// Get total count for pagination
$countQuery = "SELECT COUNT(*) AS total FROM payments pay
            INNER JOIN residency res ON res.ResidencyID = pay.ResidencyID
            INNER JOIN tenant t ON t.TenantID = res.TenantID 
            INNER JOIN rooms r ON r.RoomID = res.RoomID
            INNER JOIN establishment e ON e.EstablishmentID = r.EstablishmentID
            WHERE t.TenantID = $tenantID";
$countResult = mysqli_query($conn, $countQuery);
$totalItems = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

// Fetch paginated data
$dataQuery = "SELECT pay.PaymentID, pay.Purpose, pay.Amount, pay.PaymentDate, pay.ReferenceNumber, r.RoomName, e.Name AS EstablishmentName FROM payments pay
INNER JOIN residency res ON res.ResidencyID = pay.ResidencyID 
INNER JOIN tenant t ON t.TenantID = res.TenantID 
INNER JOIN rooms r ON r.RoomID = res.RoomID
INNER JOIN establishment e ON e.EstablishmentID = r.EstablishmentID
WHERE t.TenantID = $tenantID ORDER BY pay.PaymentDate DESC LIMIT $offset, $itemsPerPage";
$dataResult = mysqli_query($conn, $dataQuery);

echo mysqli_error($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment History</title>

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
            flex-direction: row;
            gap: 2px;
            justify-content: space-between;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            /* gap: 20px; */
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            height: auto;
            border-bottom: 1px solid #ccc;
            margin: 20px;
        }

        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .amount {
            background-color: rgba(0, 0, 0, 0.1);
            padding: 3px;
            border-radius: 10px;
            font-size: 20px;
        }

        .payment-body {
            display: flex;
            justify-content: space-between;
            flex-direction: row;
            width: 100%;
            color: grey;
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

        .

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
        <h1>Payment history</h1>
        <p style="color: grey;">Results found: <?php echo $totalItems; ?></p>
        
        <?php if (mysqli_num_rows($dataResult) > 0) { 
            while ($row = mysqli_fetch_assoc($dataResult)) {
                    $paymentID = $row['PaymentID'];
                    $encryptedPaymentID = base64_encode($paymentID);

                    $amount = "<i class='fa-solid fa-peso-sign'></i> " . number_format($row['Amount'] ?? 0, 2);
                    
                    $paymentDate = date('F d, Y | h:i:s a', strtotime($row['PaymentDate']));
                    $referenceNumber = $row['ReferenceNumber'] ?? 'N/A';

                    $roomName = $row['RoomName'];
                    $establishmentName = $row['EstablishmentName'];

                    $purpose = $row['Purpose'] ?? '';
            ?>
            <div class="card-container">
                <div class="payment-body">
                    <p>Reference: <?php echo $referenceNumber; ?></p>
                </div>
                <div class="payment-header">
                    <h5><a href="/bookingapp/establishment/room/booking_receipt.php?pay=<?php echo $encryptedPaymentID; ?>"><?php echo "$establishmentName ($roomName)"; ?></a></h5>
                    <p class="amount"><?php echo $amount; ?></p>
                </div>
                <div class="payment-body">
                    <p><?php echo $purpose; ?></p>
                    <p><?php echo $paymentDate; ?></p>
                </div>
            </div>

            <?php if ($dataResult) { ?>
                <!-- Pagination -->
                <div id="pagination" class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?tab=<?= htmlspecialchars($tab) ?>&page=<?= $i ?>" class="<?= $i === $currentPage ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php } ?>
        <?php } 
        } else {
         echo "<h3 style='text-align: center; margin-top: 50px'>No records found.</h3>";   
        }?>
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