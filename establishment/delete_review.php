<?php
include "../database/database.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reviewID = $_POST['review-id'];
    $estID = base64_encode($_POST['est-id']);
    try {
        $sql = "DELETE FROM reviews WHERE ReviewID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "i", $reviewID);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_stmt_error($stmt));
        }

        header("Location: establishment.php?est=$estID#reviews");
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
?>