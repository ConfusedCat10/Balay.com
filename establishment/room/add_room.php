<?php
include '../../database/database.php';

if (isset($_POST['submit-add-room'])) {
    $estID = $_POST['establishment-id'] ?? '0';
    $photo = $_FILES['room-photo']['name'];

    $roomName = mysqli_real_escape_string($conn, $_POST['room-name'] ?? '');
    $roomType = mysqli_real_escape_string($conn, $_POST['room-type'] ?? '');

    $paymentRate = $_POST['price'] ?? 0;

    $paymentRules = mysqli_real_escape_string($conn, $_POST['payment-rules'] ?? '');
    $paymentOptions = mysqli_real_escape_string($conn, $_POST['payment-options'] ?? '');
    $paymentStructure = mysqli_real_escape_string($conn, $_POST['payment-structure'] ?? '');

    $floorLevel = $_POST['floor-level'] ?? 1;

    $availability = $_POST['availability'] ?? '';

    $gender = $_POST['gender'] ?? '';

    $maxOccupant = 1;
    switch ($roomType) {
        case "Single occupancy":
        case "Suite":
        case "One-bedroom apartment":
            $maxOccupant = 1;
            break;
        
        case "Double occupancy":
        case "Suite double":
        case "Two-bedroom apartment":
            $maxOccupant = 2;
            break;

        case "Triple occupancy":
        case "Suite triple":
        case "Three-bedroom apartment":
            $maxOccupant = 3;
            break;
        
        case "Quad apartment":
        case "Studio apartment":
        case "Luxury suite":
            $maxOccupant = 4;
            break;
        
        default:
            $maxOccupant = 1;
    }

    try {
        // Photo
        $allowedExtensions = ["jpg", "jpeg", "png", "gif"];
        $fileExtension = strtolower(pathinfo($photo, PATHINFO_EXTENSION));

        $fileName = $roomName . '_' . $estID . '_' . $roomType . '.' . $fileExtension;

        $newFilePath = "pictures/" . $fileName;

        $tempName = $_FILES['room-photo']['tmp_name'];
        $size = $_FILES['room-photo']['size'];

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("Invalid file type for the photo. Only JPG, JPEG< PNG, and GIF are accepted.");
        }

        if (!getimagesize($tempName)) {
            throw new Exception("The file uploaded is not an image type.");
        }

        if ($size > 5242880) {
            throw new Exception("Photo file is too large. The file size should be lower than 5 MB.");
        }

        if (!move_uploaded_file($tempName, $newFilePath)) {
            throw new Exception("Sorry, there's something wrong in uploading the photo.");
        }

        $sql = "INSERT INTO rooms (RoomName, RoomType, PaymentRate, PaymentOptions, PaymentStructure, Availability, EstablishmentID, FloorLocation, PaymentRules, Photo, GenderInclusiveness, MaxOccupancy) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception("Prepared statement error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'ssdsssiisssi', $roomName, $roomType, $paymentRate, $paymentOptions, $paymentStructure, $availability, $estID, $floorLevel, $paymentRules, $newFilePath, $gender, $maxOccupant);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Statement execution error: " . mysqli_stmt_error($stmt));
        }

        $encryptEstID = base64_encode($estID);
        header("Location: /bookingapp/establishment/establishment.php?est=$encryptEstID#availability");

    } catch (Exception $e) {
        $addRoomError = $e->getMessage();
    }
}
?>