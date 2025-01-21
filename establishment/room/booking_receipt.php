<?php
include '../../database/database.php';

$payment = [];

if (isset($_GET['pay']) || $_GET['pay'] !== '') {
    $payID = base64_decode($_GET['pay']);

    $sql = "SELECT CONCAT(UPPER(p.LastName), ', ', p.FirstName, ' ', p.MiddleName, ' ', p.ExtName) AS TenantName,
    r.RoomID, r.RoomName, r.RoomType, r.PaymentOptions, r.PaymentStructure, r.GenderInclusiveness, r.FloorLocation, r.PaymentRate, e.EstablishmentID, e.Name AS EstablishmentName, e.Type AS EstablishmentType, pay.Amount, res.DateOfEntry, pay.PaymentDate, pay.ReferenceNumber FROM payments pay
    INNER JOIN residency res ON res.ResidencyID = pay.ResidencyID
    INNER JOIN tenant t ON t.TenantID = res.TenantID
    INNER JOIN user_account u ON u.UserID = t.UserID
    INNER JOIN person p ON p.PersonID = u.PersonID
    INNER JOIN rooms r ON r.RoomID = res.RoomID
    INNER JOIN establishment e ON e.EstablishmentID = r.EstablishmentID
    WHERE pay.PaymentID = $payID";
    $result = mysqli_query($conn, $sql);

    echo mysqli_error($conn);

    if ($result) {
        $payment = mysqli_fetch_assoc($result);
    }
} else {
    header("Location: /bookingapp/page_not_found.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Receipt</title>

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
            margin-bottom: 20px;
        }
        .receipt-details {
            line-height: 1.8;
            display: grid;
            gap: 1rem;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            background-color: maroon;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #ffd700;
            color: black;
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="receipt-header">
        <a href="/bookingapp/index.php"><img src="/bookingapp/assets/site-logo/logo-text-black.png" style="width: 50%;">
        </a>
        <h4>RECEIPT</h4>
        <h1><?php echo $payment['RoomName']; ?></h1>
        <h3 style="color: grey;"><?php echo $payment['EstablishmentName']; ?></h3>
        <p style="font-size: 12px;"><?php echo date("F d, Y | h:i:s a", strtotime($payment['PaymentDate'])); ?></p>
    </div>
    <hr>
    <?php

        // Assuming these variables are fetched from the database
        $referenceNumber = $payment['ReferenceNumber'];
        $tenantName = $payment['TenantName'];
        $roomId = $payment['RoomID'];
        $roomName = $payment['RoomName'];
        $roomType = $payment['RoomType'];
        $paymentOptions = $payment['PaymentOptions'];
        $paymentStructure = $payment['PaymentStructure'];
        $paymentStructure = str_replace('Per', 'One', $paymentStructure);
        $paymentRate = $payment['PaymentRate'];
        $floorLocation = $payment['FloorLocation'];
        $genderInclusiveness = $payment['GenderInclusiveness'];
        $establishmentId = $payment['EstablishmentID'];
        $establishmentType = $payment['EstablishmentType'];
        $establishmentName = $payment['EstablishmentName'];
        $pricePaid = "PHP " . number_format($payment['Amount'], 2);
        $dateOfEntry = date("F d, Y", strtotime($payment['DateOfEntry']));
        $paymentDate = date("F d, Y | h:i:s a", strtotime($payment['PaymentDate']));
    ?>
    <div class="receipt-details" style="grid-template-columns: repeat(2, 1fr)">
        <?php
        echo "<p><strong>Tenant:</strong><br> $tenantName</p>";
        echo "<p><strong>Reference code:</strong><br> $referenceNumber</p>";
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
        echo "<p><strong>Payment rate:</strong><br> PHP $paymentRate</p>";
        echo "<p><strong>Payment schedule:</strong><br> $paymentOptions</p>";
        echo "<p><strong>Paying for:</strong><br> $paymentStructure</p>";
        ?>
    </div>
    <div class="receipt-details" style="grid-template-columns: repeat(3, 1fr)">
        <?php
        echo "<p><strong>Amount Paid:</strong><br> $pricePaid</p>";
        echo "<p><strong>Payment date:</strong><br> $paymentDate</p>";
        echo "<p><strong>Reserved date:</strong><br> $dateOfEntry</p>";
        ?>
    </div>
    <div class="receipt-footer">
        <button class="btn btn-primary" onclick="window.print()">Print Receipt</button>
        <!-- <button onclick="exportAsPDF()">Download PDF</button> -->
    </div>
</div>

<script>
    function exportAsPDF() {
        window.location.href = 'generate_pdf_receipt.php'; // Redirect to the PHP script to generate the PDF
    }
</script>

</body>
</html>
