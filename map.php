<?php
include "database/database.php";

session_start();

$owner = isset($_SESSION['owner']) ? $_SESSION['owner'] : null;
$admin = isset($_SESSION['owner']) ? $_SESSION['owner'] : null;

$establishments = array();



// Assign remark based on average score
function getRemark($average) {
    if ($average >= 9) return 'Luxury';
    if ($average >= 8) return 'First Class';
    if ($average >= 7) return 'Comfort';
    if ($average >= 6) return 'Standard';
    if ($average >= 5) return 'Tourist';
    if ($average == 0) return 'No rating';
    
    return 'Poor';
}

function generateStars($rating) {
    $filledStar = '&#9733';
    $emptyStar = '&#9734';
    $stars = '';

    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rating ? $filledStar : $emptyStar;
    }

    return $stars;
}

$estSql = "SELECT e.EstablishmentID, e.Name, g.Latitude, g.Longitude FROM geo_tags g INNER JOIN establishment e ON e.EstablishmentID = g.EstablishmentID WHERE e.Status != 'removed'";
$estResult = mysqli_query($conn, $estSql);

if (mysqli_num_rows($estResult) > 0) {
    while ($row = mysqli_fetch_assoc($estResult)) {
        $establishments[] = $row;
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search for accommodation</title>
    
    <?php
    include "php/head_tag.php"; ?>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>

      <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

    <style>
        #map {
            height: 80%;
            border: 1px solid black;
        }
    </style>

<body>
    <!-- Header -->
    <header class="header">
        <?php include "php/header.php"; ?>
        <div class="section-container header-container" id="home">
            <a href="index.php"><img src="/bookingapp/assets/site-logo/logo-text-white.png" style="width: 300px;" alt="Balay.com logo"></a>
            <h1>Search the Map
            </h1>
        </div>

        <!-- Search section -->
            <section class="section-container booking-container booking-form">
                <!-- <form method="get" action="/bookingapp/map.php" class="booking-form"> -->
                    <div class="input-group" style="width: 100%">
                        <span><i class="fa-solid fa-magnifying-glass"></i></span>
                        <div class="dropdown">
                            <label for="guest">Search anywhere:</label>
                            <input type="text" name="search" id="placeName" placeholder="Search places" value="<?php echo isset($_GET['search']) ? $_GET['search'] : '' ?>">
                        </div>
                        <span><i class="fa-solid fa-location-pin"></i></span>
                        <div class="dropdown">
                            <label for="guest">Current latitude:</label>
                            <input type="text" name="latitude" id="latitude" placeholder="Current latitude" readonly>
                        </div>
                        <div class="dropdown">
                            <label for="guest">Current longitude:</label>
                            <input type="text" name="longitude" id="longitude" placeholder="Current longitude" readonly>
                        </div>

                        <button class="btn" onclick="searchLocation()"><i class="fa-solid fa-magnifying-glass"></i> SEARCH</button>
                        <!-- <button class="btn" onclick="redirect('/bookingapp/index.php')"><i class="fa-solid fa-building"></i> BROWSE ESTABLISHMENTS</button> -->

                    </div>
                <!-- </form> -->
        </section>
    </header>

   <div class="container" style="display: flex; flex-direction: column; gap: 10px;">
        <div style="display: flex; flex-direction: column; gap: 10px; justify-content: center; align-items: center">
            <h3>Currently navigating at:</h3>
            <span id="currentPlace"></span>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="returnToMSU()"><i class="fa-solid fa-home"></i> Return to Mindanao State University - Main Campus</button>
                <button class="btn btn-secondary" onclick="goToMyLocation()"><i class="fa-solid fa-location"></i> Go to My Location</button>
            </div>
        </div>
        <div id="map"></div>
        <div id="no-internet-prompt">
            <h1>No internet connection.</h1>
            <p>We're sorry that we can't show you the map when there is no Internet connection.</p>
        </div>
   </div>
    
    <!-- Footer -->
    <?php
    include "php/footer.php";
    ?>

    <div id="toastBox"></div>

    <script>


        document.getElementById('year').textContent = new Date().getFullYear();

        // OpenStreetMap using Leaflet JS

        var defaultLatitude = 7.99740;
        var defaultLongitude = 124.26044;
        var zoomLevel = 15;

        var map = L.map('map').setView([defaultLatitude, defaultLongitude], zoomLevel);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
            subdomains: ['a', 'b', 'c']
        }).addTo(map);

        // Establishment data from PHP
        var establishments = <?php echo json_encode($establishments); ?>;

        // Add markers to the map
        establishments.forEach(function(establishment) {
            var encryptedEstID = btoa(establishment.EstablishmentID);
            if (establishment.Latitude && establishment.Longitude) {
               var marker = L.marker([establishment.Latitude, establishment.Longitude]).addTo(map); // Customize popup content as needed

                marker.bindTooltip(`<a href="establishment/establishment.php?est=${encryptedEstID}" target="_blank">${establishment.Name}</a>`, {
                    permanent: true,
                    direction: "top",
                    className: 'leaflet-tooltip-hyperlink' // Custom class to allow HTML
                })
                .bindPopup(`<a href="establishment/establishment.php?est=${encryptedEstID}" target="_blank">Go to the establishment.</a>`);
            }
        });

        window.addEventListener('onload', () => {
            updateCenterCoordinates();
        });

        // Function to update the input fields with the current map center
        function updateCenterCoordinates() {
            const center = map.getCenter();
            const longitude = center.lng;
            const latitude = center.lat;
            document.getElementById('longitude').value = longitude;
            document.getElementById('latitude').value = latitude;

            var placeNameOutput = document.getElementById("currentPlace");

            // Reverse Geocode using Nominatim
            var url = 'https://nominatim.openstreetmap.org/reverse';
            var params = {
                lat: latitude,
                lon: longitude,
                format: 'json',
                addressdetails: 1
            };

            fetch(`${url}?${Object.keys(params).map(key => `${key}=${params[key]}`).join('&')}`)
            .then(response => response.json())
            .then(data => {
                // Get the place name from the response
                var placeName = data.display_name;

                if (placeName !== '' || !placeName) {
                    placeNameOutput.innerText = placeName;
                } else {
                    placeNameOutput.innerText = "[Loading place...]";
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Search Location on the Map
        function searchLocation() {
            const query = document.getElementById("placeName").value;

            // Fetch coordinates from Nominatim
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = data[0].lat;
                        const lon = data[0].lon;

                        // Center map on the search result
                        map.setView([lat, lon], 12);

                        // Update marker position
                        if (marker) {
                            marker.setLatLng([lat, lon]);
                        } else {
                            marker = L.marker([lat, lon]).addTo(map);
                        }

                        // Update coordinate fields
                        updateCenterCoordinates();
                    } else {
                        showToast("ban", "Location not found.", "error");
                    }
                })
                .catch(error => {
                    // console.error("Error fetching location:", error);
                    showToast("circle-xmark", "An error occurred in fetching location. Please try again.", "error");
                });
        }

        // Update the coordinates initially
        updateCenterCoordinates();

        // Function to show the snackbar
        // Toast notification functionalities
        let toastBox = document.getElementById("toastBox");

        function showToast(icon, message, type) {
            let toast = document.createElement('div');
            toast.classList.add('toast');
            toast.innerHTML = "<i class='fa-solid fa-" + icon + "'></i> " + message;
            toastBox.appendChild(toast);

            toast.classList.add(type);

            toast.addEventListener("click", () => {
                toast.remove();
            });

            setTimeout(() => {
                toast.remove();
            }, 5000);
        }
        
        var mapElement = document.getElementById('map');
          var noInternetPrompt = document.getElementById('no-internet-prompt');

         // Detect if there is an Internet connection. If there is, show the map plugin. Otherwise, hide the map plugin.
       window.addEventListener('load', function() {
          

          function updateStatus() {
            if (navigator.onLine) {
              showToast("wifi", "You are connected to the Internet.", "success");
              noInternetPrompt.style.display = "none";
              mapElement.style.display = "block";
            } else {
              showToast("ban", "You are disconnected from the Internet.", "error");
              noInternetPrompt.style.display = "block";
              mapElement.style.display = "none";
            }
          }

          updateStatus();

          window.addEventListener('online', updateStatus);
          window.addEventListener('offline', updateStatus);

          noInternetPrompt.style.display = "none";
            mapElement.style.display = "block";

        });

        noInternetPrompt.style.display = "none";
            mapElement.style.display = "block";


    function returnToMSU() {
        map.setView([defaultLatitude, defaultLongitude], zoomLevel);
    }

    function getMyLocation() {
        // Get user's location
        navigator.geolocation.getCurrentPosition(position => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const accuracy = position.coords.accuracy;

            // Add marker at user's location
            L.marker([lat, lng]).addTo(map).bindTooltip("You are currently here.", {
                        permanent: true,
                        direction: "top",
                        className: 'leaflet-tooltip-hyperlink' // Custom class to allow HTML
                    })
            }, error => {
            console.error("Error getting user's location:", error);
        });
    }

    function goToMyLocation() {
        navigator.geolocation.getCurrentPosition(position => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const accuracy = position.coords.accuracy;

            // Create Leaflet map instance
            map.setView([lat, lng], 16);
        });
    }

    getMyLocation();

    </script>
</body>
</html>