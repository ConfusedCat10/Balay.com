<?php
// Fetch all establishments
$sql = "SELECT e.Name, g.Latitude, g.Longitude FROM geo_tags g INNER JOIN establishment e ON e.EstablishmentID = g.EstablishmentIDWHERE g.EstablishmentID = $estID";
$result = mysqli_query($conn, $sql);

$establishments = [];

while ($row = mysqli_fetch_assoc($result)) {
    $establishments[] = $row;
}

echo json_encode($establishments);
?>