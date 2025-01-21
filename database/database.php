<?php

// Just put comment on the database not being used
// Use this for dummy data
$conn = mysqli_connect("localhost", "root", "", "bookingapp");

// Use this for clean data.
// $conn = mysqli_connect("localhost", "root", "", "bookingapp_empty"); 

// $conn = mysqli_connect("localhost", "root", "", "bookingapp_official");


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>