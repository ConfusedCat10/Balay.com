<?php
include '../../database/database.php';

$residency = [];

$purpose = '';

if (isset($_GET['id']) || $_GET['id'] !== '') {
    $resID = base64_decode($_GET['id']);

    $purpose = isset($_GET['p']) ? $_GET['p'] : '';

    $sql = "SELECT CONCAT(UPPER(p.LastName), ', ', p.FirstName, ' ', p.MiddleName, ' ', p.ExtName) AS TenantName,
    r.RoomID, r.RoomName, r.RoomType, r.PaymentOptions, r.PaymentStructure, r.GenderInclusiveness, r.FloorLocation, r.PaymentRate, e.EstablishmentID, e.Name AS EstablishmentName, e.Type AS EstablishmentType, res.ResidencyID, res.DateOfEntry, res.CreatedAt AS BookingDate, res.Status FROM residency res
    INNER JOIN tenant t ON t.TenantID = res.TenantID
    INNER JOIN user_account u ON u.UserID = t.UserID
    INNER JOIN person p ON p.PersonID = u.PersonID
    INNER JOIN rooms r ON r.RoomID = res.RoomID
    INNER JOIN establishment e ON e.EstablishmentID = r.EstablishmentID
    WHERE res.ResidencyID = $resID";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $residency = mysqli_fetch_assoc($result);
    }
} else {
    header("Location: /bookingapp/page_not_found.php");
}

if (isset($_POST['payBooking'])) {
    $residencyID = $residency['ResidencyID'];
    $purpose = mysqli_real_escape_string($conn, $purpose ?? '');

    try {
        $sql = "INSERT INTO payments (ResidencyID, Amount, Purpose, ReferenceNumber) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "idss", $residencyID, $amount, $purpose, $refID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_stmt_error($stmt));
        }

        $paymentID = mysqli_insert_id($conn);
        $encryptedPaymentID = base64_encode($paymentID);

        header("Location: booking_receipt.php?pay=$encryptedPaymentID");
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

// If cash is selected
if (isset($_POST['ok-button'])) {
    $residencyID = $residency['ResidencyID'];
    try {
        $sql = "UPDATE residency SET Status='reserved' WHERE ResidencyID = $residencyID";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>

    <link rel="shortcut icon" href="/bookingapp/favicon.ico" type="image/x-icon">


    <script defer src="/bookingapp/assets/fontawesome/js/brands.js"></script>
    <script defer src="/bookingapp/assets/fontawesome/js/solid.js"></script>
    <script defer src="/bookingapp/assets/fontawesome/js/regular.js"></script>
    <script defer src="/bookingapp/assets/fontawesome/js/fontawesome.js"></script>

    <script defer src="/bookingapp/js/script.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .receipt-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }
        .receipt-header {
            text-align: center;
            margin: 20px;
        }
        .receipt-details {
            line-height: 1.8;
            display: grid;
            gap: 1rem;
        }
        .receipt-footer {
            display: flex;
            justify-content: space-between;
            text-align: center;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-primary {
            background-color: maroon;
            color: white;
        }

        .btn-secondary {
            border-color: 1px solid maroon;
            color: black;
        }

        .btn-primary:hover {
            background-color: #ffd700;
            color: black;
        }

        .btn-secondary:hover {
            background-color: white;
            color: maroon;
            border: 1px solid maroon;
        }

        .btn-group {
            width: 30%;
            float: right;
            justify-content: flex-end;
            align-items: center;
            margin-top: 30px;
            gap: 10px;
        }

        .clearfix:after, .clearfix:before {
            content: '';
            display: table;
            clear: both;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 80%;
            height: auto;
            margin: auto;
            margin-top: 40px;
        }


        .modal-content button {
            padding: 10px;
            font-weight: 400px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            background-color: maroon;
            color: white;
            border: none;
        }

        .modal-content button:hover {
            background-color: gold;
            color: black;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .form-inline {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-items: center;
            gap: 20px;
            align-content: center;
            margin-top: 20px;
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

        @media print {  
            .no-print {
                display: none;
            }

            .print-only {
                display: block;
            }
        }

        @media screen {
            .print-only {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="receipt-header">
        <a href="/bookingapp/index.php"><img src="/bookingapp/assets/site-logo/logo-text-black.png" style="width: 50%;">
        </a>
        <h4 class="no-print">Booking Confirmation for</h4>
        <h4 class="print-only">BOOKING RECEIPT</h4>
        <h1><?php echo $residency['RoomName']; ?></h1>
        <h3 style="color: grey;"><?php echo $residency['EstablishmentName']; ?></h3>
        <p style="font-size: 18px;"><?php echo date("l | F d, Y", strtotime($residency['DateOfEntry'])); ?></p>
        <p style="font-size: 18px;">Status: <?php echo $residency['Status']; ?></p>
    </div>
    <hr>
    <?php

        // Assuming these variables are fetched from the database
        $tenantName = $residency['TenantName'];
        $roomId = $residency['RoomID'];
        $roomName = $residency['RoomName'];
        $roomType = $residency['RoomType'];
        $paymentOptions = $residency['PaymentOptions'];
        $paymentStructure = $residency['PaymentStructure'];
        $paymentRate = $residency['PaymentRate'];
        $floorLocation = $residency['FloorLocation'];
        $genderInclusiveness = $residency['GenderInclusiveness'];
        $establishmentId = $residency['EstablishmentID'];
        $establishmentType = $residency['EstablishmentType'];
        $establishmentName = $residency['EstablishmentName'];
        $dateOfEntry = date("F d, Y", strtotime($residency['DateOfEntry']));

        $residencyID = $residency['ResidencyID'];

        $referenceNumber = $residencyID . date("miyhis", strtotime($residency['BookingDate'])) . date("miyhis", strtotime($residency['DateOfEntry']));
    ?>
    <div class="receipt-details" style="grid-template-columns: repeat(1, 1fr)">
        <?php
        echo "<p><strong>Reference Number:</strong><br> $referenceNumber</p>";
        ?>
    </div>
    <div class="receipt-details" style="grid-template-columns: repeat(3, 1fr)">
        <?php
        echo "<p><strong>Tenant:</strong><br> $tenantName</p>";
        echo "<p><strong>Residency ID:</strong><br> $residencyID</p>";
        ?>
    </div>
    <div class="receipt-details" style="grid-template-columns: repeat(3, 1fr)">
        <?php
        
        echo "<p><strong>Establishment ID:</strong><br> $establishmentId</p>";
        echo "<p><strong>Establishment Name:</strong><br> $establishmentName</p>";
        echo "<p><strong>Establishment Type:</strong><br> $establishmentType</p>";

        ?>
    </div>
    <div class="receipt-details" style="grid-template-columns: repeat(3, 1fr)">
        <?php
        echo "<p><strong>Room ID:</strong><br> $roomId</p>";
        echo "<p><strong>Room Name:</strong><br> $roomName</p>";
        echo "<p><strong>Floor Location:</strong><br> $floorLocation</p>";
        echo "<p><strong>Payment rate:</strong><br>PHP $paymentRate</p>";
        echo "<p><strong>Payment structure:</strong><br> $paymentOptions</p>";
        echo "<p><strong>Payment schedule:</strong><br> $paymentStructure</p>";
        ?>
    </div>
    <div class="receipt-footer no-print">
        <button class="btn btn-secondary" onclick="history.back()"><i class="fa-solid fa-backward"></i> Go Back</button>
        <!-- <button class="btn btn-primary" name="payBooking"><i class="fa-solid fa-receipt"></i> Proceed to Receipt</button> -->
        <button class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Receipt</button>
        <!-- <button onclick="exportAsPDF()">Download PDF</button> -->
    </div>
</div>

<?php
$estID = $residency['EstablishmentID'];
$paySql = "SELECT pc.ChannelID, pc.ChannelName, pc.ChannelLogo, pc.Type AS ChannelType, epc.EPCID, epc.AccountName, epc.AccountNumber, epc.Notes FROM payment_channel pc
        INNER JOIN establishment_payment_channel epc ON epc.PaymentChannel = pc.ChannelID
        WHERE epc.EstablishmentID = $estID AND epc.IsHidden = 0";
$payResult = mysqli_query($conn, $paySql);

?>

<div class="modal" id="enterAmountModal">
    <div class="modal-content clearfix">
        <span class="close" onclick="closeModal('enterAmountModal')">&times;</span>
        <h1>Pay booking</h1>
        <form action="" method="post">
            <fieldset>
                <img src="" alt="" id="channel-logo" style="float: right; height: 50px; width: auto; margin: 50px">
                <div class="form-inline" style="display: flex; justify-content: flex-start;">
                    <div class="form-group">
                        <label for="payment-mode" class="mandatory">Payment mode:</label>
                        <?php if (mysqli_num_rows($payResult) > 0) {
                         ?>
                        <select name="payment-mode" id="payment-mode"  required>
                            <option value="" selected disabled>Select a payment mode...</option>
                            <?php
                            while ($row = mysqli_fetch_assoc($payResult)) {
                                $payChID = $row['ChannelID'];
                                $encryptedPayChID = base64_encode($payChID);

                                $epcid = $row['EPCID'];

                                $channelName = $row['ChannelName'] ?? '';
                                $channelLogo = $row['ChannelLogo']  ?? '';
                                $type = $row['ChannelType'] ?? '';

                                $accountName = $row['AccountName'] ?? '';
                                $accountNumber = $row['AccountNumber'] ?? '';
                                $notes = $row['Notes'] ?? '';
                            ?>
                            <option data-logo="<?php echo $channelLogo; ?>" data-channel-name="<?php echo $channelName; ?>" data-account-name="<?php echo $accountName; ?>" data-account-number="<?php echo $accountNumber; ?>" data-notes="<?php echo $notes; ?>" value="<?php echo $epcid; ?>"><?php echo "$channelName &middot; $accountName"; ?></option> 
                            <?php } ?>  
                        </select>
                        <?php 
                            } else {
                            echo "<h3>No available payment method!</h3>";
                        } ?>
                    </div>
                </div>
                <div class="form-inline" id="amount-form-group" style="display: flex; justify-content: flex-start;">
                    <div class="form-group">
                        <label for="amount">Bank/e-wallet:</label>
                        <input type="text" id="bank-field" readonly>
                    </div>

                    <div class="form-group">
                        <label for="amount">Amount:</label>
                        <input type="number" id="amount-field" placeholder="Enter amount in Philippine currency." onblur="formatPriceCurrency()">
                    </div>

                    <div class="form-group">
                        <label for="">Account number:</label>
                        <input type="text" maxlength="11" placeholder="Enter account number" >
                    </div>

                    <div class="form-group">
                        <label for="">Account name:</label>
                        <input type="text" placeholder="Enter account name">
                    </div>
                </div>

                <div class="form-inline" id="instruction-form" style="display: flex; justify-content: center;">
                    <div id="instruction" style="border: 1px solid maroon; color: black; padding: 10px 19px; border-radius: 10px;">
                        <h3>Payment instruction:</h3>
                        <p>To pay in cash, please proceed to the Housing Management Division, located in Rajah Indapatra Hall, 2nd Street, MSU Main Campus, Marawi City.</p>
                    </div>
                </div>
            </fieldset>
            <div class="btn-group" id="bank-btn">
                <button type="button" class="btn btn-secondary" id="pay-button" name="pay-later" onclick="redirect('/bookingapp/tenant/residency.php?tab=pending')">Pay later</button>
                <!-- <button type="submit" class="btn btn-primary" id="pay-button" name="pay-button">Pay now</button> -->

            </div>
            <div class="btn-group" id="cash-btn">
                <button type="button" class="btn-primary" id="pay-button" name="ok-button">OK</button>
            </div>
        </form>
    </div>
</div>

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

    function openModal(id) {
        document.getElementById(id).style.display = 'block';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = "none";
    }

    const instructionForm = document.getElementById('instruction-form');
    const amountForm = document.getElementById('amount-form-group');
    const paymentMode = document.getElementById('payment-mode');
    const amountField = document.getElementById('amount-field');
    const bankField = document.getElementById('bank-field');
    const cashBtn = document.getElementById('cash-btn');
    const bankBtn = document.getElementById('bank-btn');

    document.addEventListener('DOMContentLoaded', function() {
        instructionForm.style.display = "none";
        amountForm.style.display = "none";
        cashBtn.style.display = "none";
        bankBtn.style.display = "none";
    });

    paymentMode.onchange = function() {
        if (paymentMode.value === 1) {
            instructionForm.style.display = "block";
            amountForm.style.display = "none";
            cashBtn.style.display = "flex";
            bankBtn.style.display = "none";
            bankField.value = paymentMode.getAttribute('data-channel-name');
        } else {
            instructionForm.style.display = "none";
            amountForm.style.display = "flex";
            bankField.value = paymentMode.getAttribute('data-channel-name');
            cashBtn.style.display = "none";
            bankBtn.style.display = "flex";
        }

        togglePaymentChannel(this);
    }

    function formatPriceCurrency() {
        // Get the input value
        let inputValue = document.getElementById("amount-field").value;

        // Check if the entered value is a valid number
        let number = parseFloat(inputValue);
        if (isNaN(number) || !isFinite(number)) {
            // If not a valid number, set the default value to 1
            number = 0;
        }

        // Parse the input value to a number and format it as Philippine peso
        let formattedMoney = number.toLocaleString('en-PH', {
            style: "currency",
            currency: "PHP"
        });

        // Update the input field with the formatted currency
        document.getElementById("amount-field").value = formattedMoney;
    }

    function togglePaymentChannel(selectID) {
        const selectInput = document.getElementById(selectID);
        
        const epcid = selectInput.value;
        const logo = selectInput.getAttribute('data-logo');
        const channelName = selectInput.getAttribute('data-channel-name');
        const accountName = selectInput.getAttribute('data-account-name');
        const accountNumber = selectInput.getAttribute('data-account-number');
        
        const notes = selectInput.getAttribute('data-notes');

        document.getElementById('channel-logo').src = logo;
        document.getElementById('channel-logo').alt = channelName;
    }

</script>

</body>
</html>
