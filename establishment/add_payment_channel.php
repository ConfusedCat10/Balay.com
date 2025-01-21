<?php
include '../database/database.php';

session_start();

if (isset($_POST['addPaymentChannel'])) {
    $estID = $_POST['estID'];

    $paymentChannel = mysqli_real_escape_string($conn, $_POST['payment-channel'] ?? '');
    $accountName = mysqli_real_escape_string($conn, $_POST['account-name'] ?? '');
    $accountNumber = mysqli_real_escape_string($conn, $_POST['account-number'] ?? '');

    $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');

    try {
        $sql = "INSERT INTO establishment_payment_channel (EstablishmentID, PaymentChannel, AccountNumber, AccountName, Notes) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'iisss', $estID, $paymentChannel, $accountNumber, $accountName, $notes);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_stmt_error($stmt));
        }

        $encryptedEstID = base64_encode($estID);
        header("Location: /bookingapp/establishment/establishment.php?est=$encryptedEstID");
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

?>