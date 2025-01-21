<?php
include '../../database/database.php';

if (isset($_POST['delete-room'])) {
    $roomID = $_POST['room-id'];
    $estID = $_POST['est-id'];

    try {
        $sql = "UPDATE rooms SET Availability = 'Deleted' WHERE RoomID = $roomID";
        $query = mysqli_query($conn, $sql);

        if (!$query) {
            throw new Exception("Query error: " . mysqli_error($conn));
        }

        $encryptedEstID = base64_encode($estID);
        header("Location: /bookingapp/establishment/establishment.php?est=$encryptedEstID#availability");
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

?>