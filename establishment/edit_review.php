<?php
include "../database/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomID = $_POST['reviewed-room'];
    $reviewID = $_POST['reviewID'];
    $estID = $_POST['estID'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment'] ?? 'No comment');

    // Scores
    $staffScore = (int)$_POST['staff-rate'] ?? 0;
    $facilitiesScore = (int)$_POST['facilities-rate'] ?? 0;
    $cleanlinessScore = (int)$_POST['cleanliness-rate'] ?? 0;
    $comfortScore = (int)$_POST['comfort-rate'] ?? 0;
    $moneyValueScore = (int)$_POST['money-value-rate'] ?? 0;
    $locationScore = (int)$_POST['location-rate'] ?? 0;
    $signalScore = (int)$_POST['signal-rate'] ?? 0;
    $securityScore = (int)$_POST['security-rate'] ?? 0;

    try {
        $sql = "UPDATE reviews SET RoomID = ?, StaffScore = ?, FacilitiesScore = ?, CleanlinessScore = ?, ComfortScore = ?, MoneyValueScore = ?, LocationScore = ?, SignalScore = ?, SecurityScore = ?, Comments = ? WHERE ReviewID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception("Prepared statement error:" . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "iiiiiiiiisi", $roomID, $staffScore, $facilitiesScore, $cleanlinessScore, $comfortScore, $moneyValueScore, $locationScore, $signalScore, $securityScore, $comment, $reviewID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Statement execution error: ".  mysqli_stmt_error($stmt));
        }

        $encryptedEstID = base64_encode($estID);
        header("Location: /bookingapp/establishment/establishment.php?est=$encryptedEstID#reviews");

    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

?>