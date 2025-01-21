<?php

include '../../database/database.php';

if (isset($_POST['book-now'])) {
    $tenantID = $_POST['tenantID'] ?? 0;
    $roomID = $_POST['room-id'] ?? 0;
    $price = $_POST['paymentPrice'] ?? 0.0;
    $dateOfEntry = $_POST['date-of-entry'];

    $status = "pending";
    try {

        if (checkResidencyEstType($conn, $tenantiD, $roomID)) {
            throw new Exception("Failed to book. You cannot both with the same establishment type where you are currently residing at.");
        }

        // Add as a resident (assuming the user is really paying)
        $sql = "INSERT INTO residency (TenantID, RoomID, DateOfEntry, Status) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'iiss', $tenantID, $roomID, $dateOfEntry, $status);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_stmt_error($stmt));
        }

        $residencyID = base64_encode(mysqli_insert_id($conn));

        // Add Payment
        $refID = $tenantID . $roomID . $price . $dateOfEntry . date('Y-m-d');

        $refID = base64_encode($refID);

        $p = "Reservation";

        header("Location: confirm_booking.php?id=$residencyID&p=$p");
    } catch (Exception $e) {
        echo  $e->getMessage();
    }
}

function createPayment($conn, $residencyID, $amount, $refID) {
    try {
        $sql = "INSERT INTO payments (ResidencyID, Amount, ReferenceNumber) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ids", $residencyID, $amount, $refID);

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

function checkResidencyEstType($conn, $tenantID, $roomID) {
    $bool = false;
    $sql = "SELECT e.Type FROM residency rs INNER JOIN rooms r ON rs.RoomID = r.RoomID INNER JOIN establishment e ON r.EstablishmentID = e.EstablishmentID WHERE rs.TenantID = $tenantID AND rs.Status = 'currently residing' AND r.RoomID = $roomID";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $thisEstType = $row['Type'];

            if ($thisEstType === $estType) {
                $bool = true;
            }
        }
    }

    return $bool;
}

?>