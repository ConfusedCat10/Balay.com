<?php
include '../database/database.php';

session_start();

if (isset($_POST['editPaymentChannel'])) {
    $epcID = $_POST['estPayChannelID'];
    // echo "EstID: " . $_POST['editEstID'];
    $estID = $_POST['editEstID'];

    $paymentChannel = mysqli_real_escape_string($conn, $_POST['payment-channel'] ?? 1);
    $accountName = mysqli_real_escape_string($conn, $_POST['account-name'] ?? '');
    $accountNumber = mysqli_real_escape_string($conn, $_POST['account-number'] ?? '');

    $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');

    try {
        $sql = "UPDATE establishment_payment_channel SET PaymentChannel = ?, AccountNumber = ?, AccountName = ?, Notes = ? WHERE EPCID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'isssi', $paymentChannel, $accountNumber, $accountName, $notes, $epcID);

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