<?php

include "../database/database.php";

// server-script.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // $data = json_decode(file_get_contents('php://input'), true); // Decode the JSON data
    $residencyID = $_POST['residency-id']; // Get the ID from the data
    $status = isset($_POST['cancelled']) ? "cancelled" : "pending"; // Get the ID from the data\

    $remark = "";

    if ($status === 'cancelled') {
        $remark = "Cancelled by tenant";
    } else {
        $remark = "Reservation made again by tenant";
    }
    
    try {
        // Use a prepared statement to safely query the database with the ID
        $sql = "UPDATE residency SET Status = ?, Remark = ? WHERE ResidencyID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'ssi', $status, $remark, $residencyID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_stmt_error($stmt));
        }

        $st = str_replace(' ', '%20', $status);
        header("Location: residencies.php?tab=$status");
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    }

    $stmt->close();
    $conn->close();
}
?>
