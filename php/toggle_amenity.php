<?php
include "../database/database.php";

$id = isset($_POST['estID']) ? $_POST['estID'] : $_POST['roomID'];
$amenityID = $_POST['amenityID'];
$action = $_POST['action'];
$scope = $_POST['scope'];

if ($scope === 'est') {
    if ($action === 'add') {
        $query = "INSERT INTO establishment_features (EstablishmentID, FeatureID) VALUES ($id, $amenityID)";
    } elseif ($action === 'remove') {
        $query = "DELETE FROM establishment_features WHERE EstablishmentID = $id AND FeatureID = $amenityID";
    }
} elseif ($scope === 'room') {
    if ($action === 'add') {
        $query = "INSERT INTO room_features (RoomID, FeatureID) VALUES ($id, $amenityID)";
    } elseif ($action === 'remove') {
        $query = "DELETE FROM room_features WHERE RoomID = $id AND FeatureID = $amenityID";
    }
}

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

?>