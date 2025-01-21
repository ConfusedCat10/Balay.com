<?php
include "../database/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $establishmentID = filter_input(INPUT_POST, 'establishmentID', FILTER_VALIDATE_INT);
    $photoIndex = filter_input(INPUT_POST, 'photoIndex', FILTER_VALIDATE_INT);

    $sql = "UPDATE establishment_photos SET Photo{$photoIndex} = NULL, Description{$photoIndex} = NULL WHERE EstablishmentID = {$establishmentID}";
        $query = mysqli_query($conn, $sql);

    if (!$query) {
        echo json_encode(['success' => false, 'message' => 'Failed to delete photo description: ' . mysqli_error($conn)]);
    } else {
        echo json_encode(['success' => true, 'message' => 'Photo description has been successfully deleted.']);
    }
}