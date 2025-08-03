<?php

include "../../database/database.php";

// server-script.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true); // Decode the JSON data
    $residencyID = $data['id']; // Get the ID from the data
    $stat = $data['stat']; // Get the ID from the data
    $tab = $data['tab'];

    $remark = $data['rmrk'];

    // $status = 'cancelled';

    // echo "Stat: $stat"; 
    // switch ($stat) {
    //     case 1:
    //         $status = 'pending';
    //         break;
    //     case 2:
    //         $status = 'confirmed';
    //         break;
    //     case 3:
    //         $status = 'cancelled';
    //         break;
    //     case 4:
    //         $status = 'currently residing';
    //         break;
    //     case 5:
    //         $status = 'reserved';
    //         break;
    //     case 6:
    //         $status = 'deleted';
    //         break;
    //     case 7:
    //         $status = 'rejected';
    //         break;
    //     case 8:
    //         $status = 'residency ended';
    //         break;
    //     // default:
    //     //     $status = 'cancelled';
    //     //     break;
    // }

    // echo json_encode("Status: $status | Code: $stat"); 
    try {
        // Use a prepared statement to safely query the database with the ID
        $sql = $stat === 'residency ended' ? 
            "UPDATE residency SET Status = ?, Remark = ?, DateOfExit = CURDATE() WHERE ResidencyID = ?" : 
            "UPDATE residency SET Status = ?, Remark = ?, DateOfExit = NULL WHERE ResidencyID = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'ssi', $stat, $remark, $residencyID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_stmt_error($stmt));
        }

        $st = str_replace(' ', '+', $status);
        header("Location: index.php?res_st=$st");
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    }

    $stmt->close();
    $conn->close();
}
?>
