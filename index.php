<?php
include "database/database.php";

session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search for accommodation</title>
    
    <?php
    include "php/head_tag.php";
    ?>


     <!-- <link rel="stylesheet" href="/bookingapp/assets/leaflet/leaflet.css">
     <script src="/bookingapp/assets/leaflet/leaflet.js"></script> -->

    <style>
        #previewMap {
            width: 100%;
            height: 500px;
        }

        /* Pagination links */
        .pagination {
            margin-top: 40px;
            float: right;
        }

        .pagination a {
            color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color .3s;
        }
        
        /* Style the active/current link */
        .pagination a.page-active {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Add a grey background color on mouse-over */
        .pagination a:hover:not(.page-active) {
            background-color: #ddd;
        }

        .pagination a.page-active:hover {
            background-color: var(--secondary-color);
            color: black;
        }

        #viewMapModal .modal-content {
            width: 80%; height: 90%; 
        }

        .main-content {
            height: auto;
        }

        /* Selects */
        .sort-container {
            float: left;
        }

        .sort-select {
            padding: 5px;
            border-radius: 20px;
            margin: 5px;
        }

        .card-dropdown {
            position: relative;
            display: inline-block;
        }

        .card-dropdown-menu {
            display: none;
            position: absolute;
            bottom: 100%;
            right: 0;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            z-index: 200;
            min-width: 150px;
            padding: 10px 0;
            overflow: hidden;
        }

        .card-dropdown-menu a {
            padding: 10px 15px;
            display: block;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }

        .card-dropdown-menu a:hover {
            background-color: #f0f0f0;
        }

        .card-dropdown:hover .card-dropdown-menu {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }

        .video-container {
            position: relative;
            width: 80%;
            height: 0;
            margin: auto;
            display: block;
            margin: auto;
            padding-bottom: 56.25%;
            overflow: hidden;
        }        

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        .container {
            height: auto;
            display: block;
            margin-bottom: 10rem;
        }

        .video-details {
            margin-top: 10px;
        }

        .video-details p {
            margin: auto;
            text-align: center;
        }

        .container h1 {
            margin-left: 10%;
            margin-bottom: 20px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media screen and (max-width: 1000px) {
            #viewMapModal .modal-content {
                width: 90%;
                height: 95%;
            }

            .search-map {
                display: inline-flex;
            }
        }
    </style>

</head>
<body>
    <!-- Header -->
    <header class="header">
        <?php include "php/header.php"; ?>
        <div class="section-container header-container" id="home">
            <a href="index.php"><img src="/bookingapp/assets/site-logo/logo-text-white.png" style="width: 300px;" alt="Balay.com logo"></a>
            <h1>Your Premier Online Accommodation System
                in <span>Mindanao State University</span>.
            </h1>

          
    <!-- End of search section -->
    </header>

   

     <!-- Footer -->
    <?php
    include "php/footer.php";
    ?>

    <div id="toastBox"></div>

    <script src="/bookingapp/js/script.js"></script>
    <script src="/bookingapp/js/scrollreveal.js "></script>
    <script src="/bookingapp/js/rayal.js"></script>
    <!-- <script src="/bookingapp/assets/leaflet/leaflet.js"></script> -->
    <!-- <script src="/bookingapp/assets/leaflet/leaflet.js"></script> -->
    <!-- <script src="/bookingapp/js/maps.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/ol/6.5.0/ol.js"></script> -->

    <script>
        // Set the current year in the footer dynamically
        document.getElementById('year').textContent = new Date().getFullYear();


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

    </script>

</body>
</html>