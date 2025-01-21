<?php
include "../database/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = "est_photos/";
    $establishmentID = filter_input(INPUT_POST, 'establishmentID', FILTER_VALIDATE_INT);
    $photoIndex = filter_input(INPUT_POST, 'photoIndex', FILTER_VALIDATE_INT);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);


    // Ensure the target directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Handle the uploaded file
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['photo'];
        $timestamp = date("mdYHi");
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = "Photo_{$photoIndex}_{$establishmentID}_{$timestamp}.{$fileExtension}";
        $targetFile = $targetDir . $newFileName;

        // Move the file
        if (move_uploaded_file($file['tmp_name'], $targetFile)) { 
            // Save file and description info to the database
            $query = "INSERT INTO establishment_photos (EstablishmentID, Photo{$photoIndex}, Description{$photoIndex}) 
                VALUES ({$establishmentID}, '{$targetFile}', '{$description}')
                ON DUPLICATE KEY UPDATE Photo{$photoIndex}='{$targetFile}', Description{$photoIndex}='{$description}'";
            $result = mysqli_query($conn, $query);
            if ($result) {
                echo json_encode(['success' => true, 'photoPath' => $targetFile]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save photo info: ' . mysqli_error($conn) . ' | Establishment ID: ' . $establishmentID . ' | Photo Index: ' . $photoIndex]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
        }
    } else {
        // If only the description is being updated
        $sql = "UPDATE establishment_photos SET Description{$photoIndex} = '{$description}' WHERE EstablishmentID = {$establishmentID}";
        $query = mysqli_query($conn, $sql);

        if (!$query) {
            echo json_encode(['success' => false, 'message' => 'Failed to update photo description: ' . mysqli_error($conn)]);
        } else {
            echo json_encode(['success' => true, 'message' => 'Photo description has been successfully updated.']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($conn);
?>