<?php
include "../database/database.php";

session_start();

$owner = isset($_SESSION['owner']) ? $_SESSION['owner'] : null;
$admin = isset($_SESSION['admin']) ? $_SESSION['admin'] : null;

$errorMsg = "";
$successMsg = "";



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Adding an establishment</title>

    <?php
    include "../php/head_tag.php";

    if (!$isLoggedIn || $accountRole !== 'owner') {
        header("Location: /bookingapp/page_not_found.php");
    }

        
    if (isset($_POST['submitEstablishment'])) {
        $ownerID = $_SESSION['owner']['OwnerID'];

        $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $floors = (int) $_POST['floors'] ?? 0;

        $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

        $genderInclusivity = mysqli_real_escape_string($conn, $_POST['gender-inclusivity'] ?? '');

        try {
            $sql = "INSERT INTO establishment (OwnerID, Name, Description, NoOfFloors, Type, GenderInclusiveness) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                throw new Exception("Prepared statement error: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, 'ississ', $ownerID, $name, $description, $floors, $type, $genderInclusivity);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
            }

            $estID = mysqli_insert_id($conn);

            $successMsg = "<i class='fa-solid fa-circle-check'></i> Successfully added an establishment.";
            $encryptedEstID = base64_encode($estID);
            header("Location: establishment.php?est=$encryptedEstID");

        } catch (Exception $e) {
            $errorMsg = "<i class='fa-solid fa-circle-xmark'></i> " . $e->getMessage();
            $successMsg = $accountRole . " | " . $ownerID;
        }
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

        .content {
            display: flex;
            flex-direction: column;
            flex-wrap: nowrap;
        }

        .section-container {
            width: 100%;
        }

        nav {
            display: flex !important;
            /* flex-direction: row; */
            justify-content: space-between;
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

        form {
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 100%;
        }

        .btn-group {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }

        .error-msg {
            color: red;
            font-style: italic;
            font-size: 12px;
        }
        
        .form-group {
            margin-right: 10px;
        }


        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 12px;
        }

        button {
            padding: 10px;
            margin: 5px;
            border: none;
            background-color: gold;
            color: maroon;
            border-radius: 5px;
            cursor: pointer;
        }

        button[disabled] {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        .prompt-msgs {
            text-align: left;
        }

        .prompt {
            font-weight: bold;
            font-size: 18px;
        }

        .clearfix::after, .clearfix::before {
            content: "";
            display: table;
            clear: both;
        }

        .form-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: flex-start;
        }

        .form-inline {
            display: flex;
        }

        .form-inline input, .form-inline select, .form-inline textarea {
            padding: 10px;
            border-radius: 10px;
        }

        .form-inline input, .form-inline select {
            margin-right: 5px;
        }

        .mandatory:after {
            content: "*";
            color: red;
        }


        @media screen and (max-width: 1000px) {
            form {
                width: 90%;
                max-width: 500px;
            }

            .form-inline input, .form-inline select {
                width: 100% !important;
            }

            .form-inline {
                display: block;
            }
            nav {
                flex-direction: column !important;
                display: block;

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
            <div class="content">
                <div class="section-container" style="width: 100%">
                    <h2 class="section-header" style="float: left;"><i class="fa-solid fa-building"></i> Adding an establishment</h2>
                </div>

                <div class="section-container">
                    <form method="post" enctype="multipart/form-data" class="clearfix">

                        <input type="hidden" name="owner-id" value="<?php echo $ownerID; ?>">

                        <div class="form-group">
                            <div class="form-inline">    
                                <label for="name" class="mandatory">Name of the establishment:</label>
                                <input type="text" name="name" id="name" placeholder="Enter establishment name..." required>
                            </div>
                            <div class="form-inline">
                                <label for="type" class="mandatory">Type:</label>
                                <select name="type" id="type">
                                    <option value="Dormitory">Dormitory</option>
                                    <option value="Cottage">Cottage</option>
                                    <!-- <option value="Apartment">Apartment</option>
                                    <option value="Boarding House">Boarding House</option> -->
                                </select>
                            </div>                            

                            <div class="form-inline">
                                <label for="floors">No. of floors:</label>
                                <input type="number" name="floors" id="floors" min="1" max="15" value="1" style="text-align: left;">
                            </div>
                        </div>

                        <div class="form-group" style="width: 100%">
                            <div class="form-inline" style="flex-direction: column; align-items: flex-start">
                                <label for="description">Description:</label>
                                <textarea name="description" id="description" cols="50" rows="2" wrap="hard" maxlength="300" style="resize: none; width: 100%;" placeholder="Describe your place that potential tenants may read..."></textarea>
                            </div>

                            <div class="form-inline" style="flex-direction: column; align-items: flex-start">
                                <label for="description">Gender inclusivity:</label>
                                <select name="gender-inclusivity" id="gender-inclusivity" required>
                                    <option value="Males only">Males only</option>
                                    <option value="Females only">Females only</option>
                                    <option value="Coed">Coed</option>
                                </select>
                            </div>
                        </div>

                        <button class="btn btn-primary" name="submitEstablishment" style="float: right"><i class="fa-solid fa-floppy-disk"></i> Add Establishment</button>

                        <div class="prompt-msgs">
                            <p class="prompt error" id="error-message"><?php echo $errorMsg; ?></p>
                            <p class="prompt success" id="success-message"><?php echo $successMsg; ?></p>
                        </div>
                    </form>
                </div>
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

        var priceSlider = document.getElementById("priceRange");
        var priceOutput = document.getElementById("priceOutput");

        priceOutput.addEventListener("DOMContentLoaded", function() {
            formatPriceCurrency(priceSlider.value, 1);
        });

        priceSlider.addEventListener("input", () => {
            if (priceSlider === 0) {
                priceOutput.innerHTML = "Free";
            } else {
                formatPriceCurrency(priceSlider.value);
            }
        });

        function formatPriceCurrency(price) {
            // Format number as currency
            let formattedValue = new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(price);
            priceOutput.innerHTML = formattedValue;
        }
        
        formatPriceCurrency(priceSlider.value);

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