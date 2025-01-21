<?php
include '../database/database.php';

session_start();

if (isset($_POST['toggle-payment-channel'])) {
    $epcID = $_POST['epcid'];
    $estID = $_POST['estid'];
    $action = $_POST['action'] ?? 0;

    try {
        $sql = "UPDATE establishment_payment_channel SET IsHidden = ? WHERE EPCID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'ii', $action, $epcID);

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