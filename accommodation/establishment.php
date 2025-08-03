<!DOCTYPE html>
<html lang="en">
<head>
    <title>Establishment</title>

    <?php include "../php/head_tag.php"; ?>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>

      <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

    <style>
        #previewMap {
            width: 100%;
            height: 200px;
            margin-top: 20px;
            z-index: 1;
        }

        #map {
            width: 100%;
            height: 300px;
        }

        .modal-content {
            width: 80%;
            height: 80%;
        }

        .search-map {
            width: 100%;
        }
    
        .search-map button {
            width: auto;
        }

        .header-container {

            .search-map {
                width: 100%;
            }
            padding-block: 0rem 5rem;
        }

        .container {
            display: block;
            padding: 30px;
            max-width: var(--max-width);
            margin: auto;
            height: auto;
        }

        .page-nav {
            margin: auto;
            /* max-width: var(--max-width); */
            border-bottom: 1px solid black;
            z-index: 100;
        }

        .page-nav button {
            padding: 10px 24px;
            float: left;
            color: black;
            background-color: white;
            border: 1px solid rgba(0,0,0,0.1);
            outline: none;
            cursor: pointer;
        }

        .page-nav:after {
            content: "";
            clear: both;
            display: table;
        }

        .page-nav button:not(:last-child) {
            border-right: none;
        }

        .page-nav button:hover {
            background-color: #ccc;
        }

        .sticky-nav {
            position: fixed;
            top: 0;
            width: 100%;
            margin: auto;
        }

        .sticky-nav + .container {
            padding-top: 102px;
        }

        .btn {
            padding: 10px;
            cursor: pointer;
        }

        .special-btn {
            border: none;
            outline: none;
            color: maroon;
            background: transparent;
        }

        .btn-primary {
            background-color: maroon;
            color: white;
        }

        .btn-primary:hover {
            background-color: #ffd700;
            color: black;
        }

        .special-btn:hover {
            outline: 2px solid maroon;
            background: transparent;
            color: maroon;
        }

        .acc-row {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        /* Image Gallery */
        .gallery-container {
            position: relative;
            box-shadow: var(--box-shadow);
            border-radius: 20px;
        }

        /* Hide the images by default */
        .image-item {
            display: none;
        }

        .image-item img {
            height: 360px;
            object-fit: cover;
            object-position: middle;
        }

        .cursor {
            cursor: pointer;
        }

        .prev-image, .next-image {
            cursor: pointer;
            position: absolute;
            top: 40%;
            width: auto;
            padding: 16px;
            margin-top: -50px;
            color: white;
            font-weight: bold;
            font-size: 20px;
            border-radius: 0 3px 3px 0;
            user-select: none;
            -webkit-user-select: none;
        }

        .next-image {
            right: 0;
            border-radius: 3px 0 0 3px;
        }

        .prev-image:hover, .next-image:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .numbertext {
            color: #f2f2f2;
            font-size: 12px;
            padding: 8px 12px;
            position: absolute;
            top: 0;
        }

        .gallery-caption-container {
            text-align: center;
            color: black;
            padding: 2px 16px;
        }

        .image-row:after {
            content: "";
            display: table;
            clear: both;
        }

        .image-column {
            float: left;
            width: 16.66%;
        }

        .image-column img {
            height: 100px;
            object-fit: cover;
        }

        .demo {
            opacity: 0.6;
        }

        .selected-image, .demo:hover {
            opacity: 1;
        }

        .rating-col {
            text-align: left;
            padding: 10px;
        }

        .rating-col span.score   {
            text-align: center;
            background-color: maroon;
            color: white;
            padding: 10px;
            border-radius: 15px;
        }

        .score {
            text-align: center;
            background-color: maroon;
            color: white;
            padding: 10px;
            border-radius: 15px;
        }

        .chip-group {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
            gap: 1rem;
        }

        .clearfix:after, .clearfix:before {
            content: "";
            display: table;
            clear: both;
        }

        .chip {
            display: inline-block;
            padding: 0 25px;
            height: 50px;
            font-size: 16px;
            line-height: 50px;
            border-radius: 10px;
            background-color: #f1f1f1;
        }

        .container-section {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            padding-bottom: 50px;
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

        /* Selects */
        .sort-container {
            float: left;
        }

        .sort-select {
            padding: 5px;
            border-radius: 20px;
            margin: 5px;
        }

        .room-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        /* Availability status */
        .room-availability {
            background-color: #00552b;
            color: white;
            font-size: 10px;
            padding: 5px;
            border-radius: 25px;
            float: right;
        }

        /* Table */
        table {
            padding: 10px;
            border: 1px solid rgba(0, 0, 0, 0.5);
            border-radius: 25px;
        }

        .thead {
            padding-right: 10px;
            font-weight: bold;
        }

        td, tr {
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding: 5px;
        }

        td ul li {
            font-size: 12px;
        }

        /* Category progress bars */

        .category {
            padding: 10px;
            border: 1px solid rgba(0,0,0,0.4);
            border-radius: 10px;
        }

        .category-ratings {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 20px;
        }

        .category-label {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }

        .category-progress {
            width: 100%;
            background-color: grey;
            border-radius: 25px;
        }

        .progress-bar {
            width: 5%;
            height: 10px;
            background-color: #ffd700;
            border-radius: 25px;
        }

        .sort-container {
            float: right;
            font-size: 12px;
        }

        .sort-container select {
            padding: 5px 25px;
            margin-left: 5px;
            border-radius: 10px;
        }

        /* Review */
        .review-container {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            gap: 1rem;
            align-items: center;
            justify-content: flex-start;
            white-space: nowrap;
            overflow: auto;
        }

        .review-card {
            max-width: 300px;
            height: 200px;
            cursor: pointer;
        }

        .review-card:hover {
            outline: 1px solid maroon;
        }

        .reviewer-name strong {
            font-size: 12px;
        }

        .reviewer-name {
            line-height: 1rem;
        }

        .reviewer-profile-header {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding-bottom: 10px;
        }

        .review-content {
            font-size: 12px;
            text-align: left;
            padding: 10px;
        }

        .text-container {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: normal;
            position: relative;
            max-width: 400px;
        }

        .read-more {
            color: blue;
            cursor: pointer;
            position: relative;
            background-color: white;
        }

        .reviewer-name {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .expanded. .review-content {
            display: block;
            -webkit-line-clamp: unset;
        }

        .hidden {
            display: none;
        }

        @media (max-width: 1000px) {
            .acc-row {
                display: block;
            }
            .accommodation-title {
                margin-bottom: 10px;
            }
            .accommodation-header-btn-group {
                display: flex;
                justify-content: flex-end;
            }
            .review-container {
                display: block;
            }
            .review-card {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <?php include "../php/header.php"; ?>        
        <!-- Search section -->
        <?php include "../php/search_section.php"; ?>
    </header>


    <!-- Breadcrumb -->
    <ul class="breadcrumb" style="margin-bottom: 20px;">
        <li><a href="#">Mindanao State University Main Campus</a></li>
        <li><a href="#">2nd Street</a></li>
        <li><a href="#">Near College of Information and Computing Sciences</a></li>
        <li>Rajah Indapatra Hall</li>
    </ul>

    <div class="page-nav" style="width: 100%;" id="sticky-nav">
        <button style="width: 20%" onclick="redirect('#overview');">Overview</button>
        <button style="width: 20%" onclick="redirect('#amenities');">Amenities</button>
        <button style="width: 20%" onclick="redirect('#availability');">Availability</button>
        <button style="width: 20%" onclick="redirect('#houserules');">House rules</button>
        <button style="width: 20%" onclick="redirect('#reviews');">Reviews</button>
    </div>

    <div class="container">
        <!-- Overview -->
        <div class="container-section" id="overview">
            <!-- Header -->
            <div class="acc-row accommodation-header">
                <div class="accommodation-title">
                    <p class="section-subheader">DORMITORY</p>
                    <h2 class="section-header">Rajah Indapatra Hall</h2>
                    <p style="font-size: 14px;">
                        <i class="fa-solid fa-location-pin"></i> 2nd Street, MSU Main Campus, Marawi City (<a href="#">Show map</a>) <br>
                        <i class="fa-solid fa-venus-mars"></i> Females only &middot;
                        <i class="fa-solid fa-users"></i> Students only
                        <i class="fa-solid fa-stairs"></i>
                        2 Floors
                    </p>
                </div>
                <div class="accommodation-header-btn-group">
                    <button type="button" class="btn special-btn" title="Add to favorites"><i class="fa-solid fa-heart"></i></button>
                    <button type="button" class="btn special-btn" title="Share"><i class="fa-solid fa-share-nodes"></i></button>
                    <button type="button" class="btn btn-primary"><i class="fa-solid fa-calendar-check"></i> Reserve your stay</button>
                </div>
            </div>

            <!-- Gallery -->
            <div class="acc-row">
                <div class="main-content">
                    <div class="gallery-container">
                        <div class="image-item">
                            <div class="numbertext">1 / 6</div>
                            <img src="/bookingapp/assets/images/header.jpg" alt="image" style="width: 100%">
                        </div>

                        <div class="image-item">
                            <div class="numbertext">2/6</div>
                            <img src="/bookingapp/assets/images/tanggol.jpg" alt="image" style="width: 100%">
                        </div>

                        <div class="image-item">
                            <div class="numbertext">3/6</div>
                            <img src="/bookingapp/assets/images/dimaporoGym.jpg" alt="image" style="width: 100%">
                        </div>

                        <div class="image-item">
                            <div class="numbertext">4/6</div>
                            <img src="/bookingapp/assets/images/grandstand.jpg" alt="image" style="width: 100%">
                        </div>

                        <div class="image-item">
                            <div class="numbertext">5/6</div>
                            <img src="/bookingapp/assets/images/gym.jpg" alt="image" style="width: 100%">
                        </div>

                        <div class="image-item">
                            <div class="numbertext">6/6</div>
                            <img src="/bookingapp/assets/images/oval.jpg" alt="image" style="width: 100%">
                        </div>

                        <a class="prev-image" onclick="plusSlides(-1)">&laquo;</a>
                        <a class="next-image" onclick="plusSlides(1)">&raquo;</a>

                        <div class="gallery-caption-container">
                            <p id="imageCaption"></p>
                        </div>

                        <div class="image-row">
                            <div class="image-column">
                                <img src="/bookingapp/assets/images/header.jpg" alt="University" class="demo cursor" style="width: 100%" onclick="currentSlide(1)">
                            </div>
                            <div class="image-column">
                                <img src="/bookingapp/assets/images/tanggol.jpg" alt="Pangulo" class="demo cursor" style="width: 100%" onclick="currentSlide(2)">
                            </div>
                            <div class="image-column">
                                <img src="/bookingapp/assets/images/dimaporoGym.jpg" alt="Dimaporo Gymnasium" class="demo cursor" style="width: 100%" onclick="currentSlide(3)">
                            </div><div class="image-column">
                                <img src="/bookingapp/assets/images/grandstand.jpg" alt="Grandstand" class="demo cursor" style="width: 100%" onclick="currentSlide(4)">
                            </div><div class="image-column">
                                <img src="/bookingapp/assets/images/gym.jpg" alt="Dimaporo Gymnasium (side view)" class="demo cursor" style="width: 100%" onclick="currentSlide(5)">
                            </div><div class="image-column">
                                <img src="/bookingapp/assets/images/oval.jpg" alt="Oval" class="demo cursor" style="width: 100%" onclick="currentSlide(6)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sidebar">
                    <div class="panel map-container" id="mapContainer">
                        <div id="previewMap"></div>
                        <button class="btn" onclick="openModal('viewMapModal');">See Map</button>
                    </div>

                    <div class="panel" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="rating-col">
                            <span style="font-weight: bold;">Good</span> <br>
                            <span style="font-size: 10px">805 reviews</span>
                        </div>
                        <div class="rating-col">
                            <span class="score">5.6</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="acc-row" style="display: flex;">
                <div class="accommodation-details main-content">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Reprehenderit delectus iure nam itaque dolorum aperiam, a quia quae pariatur, enim vero similique ipsa perspiciatis dolore? Ipsum quae repellat dicta ab?</p>
                </div>
                
                <div class="sidebar">
                    <div class="panel">
                        <span style="font-size: 12px">Owner:</span>
                        <h5>Mindanao State University</h5>
                    </div>

                    <div class="panel">
                        <span style="font-size: 12px">Management:</span>
                        <h5>Housing Division</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Amenities -->
        <div class="container-section" id="amenities">
            <h2>Amenities</h2>
            <p>Most popular features and facilities</p>

            <div class="chip-group clearfix">
                <span class="chip">
                    <i class="fa-solid fa-wifi"></i> Free Wi-Fi
                </span>

                <span class="chip">
                    <i class="fa-solid fa-eye"></i> View
                </span>

                <span class="chip">
                    <i class="fa-solid fa-car"></i> Parking space
                </span>

                <span class="chip">
                    <i class="fa-solid fa-snowflake"></i> Air conditioning
                </span>

                <span class="chip">
                    <i class="fa-solid fa-fan"></i> Fan
                </span>

                <span class="chip">
                    <i class="fa-solid fa-shirt"></i> Laundry area
                </span>

                <span class="chip">
                    <i class="fa-solid fa-wheelchair"></i> Access for disabled persons
                </span>

                <span class="chip">
                    <i class="fa-solid fa-star-and-crescent"></i> Muslim prayer room
                </span>

                <span class="chip">
                    <i class="fa-solid fa-cross"></i> Christian prayer area
                </span>

                <span class="chip">
                    <i class="fa-solid fa-utensils"></i> Shared dining hall
                </span>

                <span class="chip">
                    <i class="fa-solid fa-basketball"></i> Basketball court
                </span>

                <span class="chip">
                    <i class="fa-brands fa-pagelines"></i> Garden
                </span>

                <span class="chip">
                    <i class="fa-solid fa-store"></i> Store
                </span>

                <span class="chip">
                    <i class="fa-solid fa-tv"></i> TV
                </span>

                
            </div>
        </div>

        <!-- Availability and Prices -->
        <div class="container-section" id="availability">
            <h2>Availability and prices</h2>

            <div class="sort-container" style="float: left;">
                    <label for="sort-select">Sort:</label>
                    <select name="" class="sort-select" id="">
                        <option value="">Our top picks</option>
                        <option value="">Dormitories first</option>
                        <option value="">Cottages first</option>
                        <option value="">Price (highest first)</option>
                        <option value="">Price (lowest first)</option>
                        <option value="">Rating (high to low)</option>
                        <option value="">Rating (low to high)</option>
                        <option value="">Top reviewed</option>
                    </select>
                </div>

            <div class="list-grid-switcher">
                <label for=""></label>
                <button class="toggle-btn list-mode" title="Select to toggle list view mode." id="toggleListView" onclick="setListView();"><i class="fa-solid fa-list"></i> List</button>
                <button class="toggle-btn grid-mode toggle-active" title="Select to toggle grid view mode." id="toggleGridView" onclick="setGridView();"><i class="class fa-solid fa-grip"></i> Grid</button>
            </div>

            <div class="room-grid">
                <?php for ($i =0; $i < 10; $i++) { ?>
                <div class="room-card">
                    <div class="room-card-image">
                        <img src="/bookingapp/assets/images/header.jpg" alt="room" />
                        <div class="room-card-icons">
                            <span title="Add to favorites"><i class="fa-solid fa-heart"></i></span>
                            <span title="View Photos"><i class="fa-solid fa-photo-film"></i></span>
                            <div class="card-dropdown">
                                <span title="Show Options"><i class="fa-solid fa-caret-down"></i></span>
                                <div class="card-dropdown-menu">
                                    <a href="#">View tenants</a>
                                    <a href="#">Check-in</a>
                                    <a href="#">Rate and review</a>
                                </div>
                            </div>
                        </div>
                        <div class="room-review">
                            Good <span class="room-score">5.6</span><br>
                            15 reviews
                        </div>
                    </div>
                    <div class="room-card-details" style="position: relative">
                        <span class="room-price" style="color: maroon; position: relative; float: right; padding: 5px; border-radius: 5px; margin: 10px 10px 0 0; font-size: 14px; background: #ffd700;">
                            3500
                        </span>
                        <span class="room-availability">Available</span>
                        </span>
                        <h4>Room 1</h4>
                        <p>
                            <span>
                                <i class="fa-solid fa-people-roof"></i> Studio-type
                                &nbsp;
                                <i class="fa-solid fa-bed"></i> 2 Double-Deck Bed
                                &nbsp;
                                <i class="fa-solid fa-users"></i> 4 tenants
                            </span>
                        </p>
                        <p>
                            <span><i class="fa-solid fa-stairs"></i> 1st Floor</span>                             
                            <span><i class="fa-solid fa-venus-mars"></i> Females only</span>
                            <span><i class="fa-solid fa-users"></i> For students</span>
                        </p>
                        
                        <h6>Amenities</h6>
                        <p>
                            <span><i class="fa-solid fa-wifi"></i> Free Wi-Fi</span>
                            <span><i class="fa-solid fa-restroom"></i> Private Bathroom</span>
                            <span><i class="fa-solid fa-sink"></i> Private sink</span>
                            <span><i class="fa-solid fa-stapler"></i> Desk</span>
                            <span><i class="fa-solid fa-box"></i> Cabinet</span>
                            <span><i class="fa-solid fa-plug-circle-bolt"></i> Power outlet</span>
                        </p>

                        <h6>Payment rules</h6>
                        <p>
                            <span>One month advance, one month deposit</span><br>
                            <span>Payment due every 1st day of the month</span>
                        </p>

                        <h6>Rating</h6>
                        <p>
                            <span class="room-stars" style="color: #ffd700;">
                                <span>&#9733;</span>
                                <span>&#9733;</span>
                                <span>&#9733;</span>
                                <span>&#9733;</span>
                                <span>&#9733;</span>
                            </span>
                        </p>
                        <button class="book-now-btn btn" onclick="redirect('/bookingapp/accommodation/establishment.php');" style="margin-top: 10px; position: absolute; right: 1rem; bottom: 1rem;">Book Now</button>
                    </div>
                </div>
                <?php } ?>
            </div> 
        </div>

        <!-- House Rules -->
        <div class="container-section" id="houserules">
            <h2>House rules</h2>
            <p>Rajah Indapatra Hall</p>

            <table class="house-rules">
                <tr>
                    <td class="thead">
                        <i class="fa-solid fa-moon"></i> Quiet Time
                    </td>
                    <td>
                        <ul class="rule-group">
                            <li>Quiet hours are in effect from <b>10:00 PM to 7:00 AM</b>. During this time, noise should be kept to a minimum to respect other residents.</li>
                            <li>Use headphones for music or other audio, and keep conversations and activities quiet, especially in shared spaces.</li>
                            <li>Please be mindful that some residents may pray during specific hours. Avoid loud activities near prayer rooms or individual rooms during prayer times.</li>
                        </ul>
                    </td>
                </tr>

                <tr>
                    <td class="thead">
                        <i class="fa-solid fa-users"></i> Guest Policy
                    </td>
                    <td>
                        <ul class="rule-group">
                            <li>All guest must be signed in at the front desk. Each resident is allowed up to two guests at a time, with strict adherence to gender-specific areas.</li>
                            <li>Guests are only permitted between <b>9:00 AM and 9:00 PM</b> and must be accompanied by the resident at all times.</li>
                            <li>Overnight guests are not allowed unless prior permission is granted by dorm management. <b>Non-family members of the opposite gender are not permitted</b> in private rooms or shared living spaces designated for a specific gender.</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="thead">
                        <i class="fa-solid fa-broom"></i> Cleanliness and Maintenance
                    </td>
                    <td>
                        <ul class="rule-group">
                            <li>Maintaining cleanliness is essential. Residents are responsible for keeping their rooms and shared spaces clean and tidy.</li>
                            <li>Personal cleanliness and proper disposal of trash are highly encouraged to respect the shared environment.</li>
                            <li>Report any maintenance issues (e.g., leaks, broken fixtures) to dorm management immediately.</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="thead">
                        <i class="fa-solid fa-star-and-crescent"></i> Prayer Rooms and Worship Areas
                    </td>
                    <td>
                        <ul class="rule-group">
                            <li>A dedicated <b>prayer room</b> is available for Muslim residents, open 24/7. Please maintain cleanliness and respect the quiet, reflective nature of this space.</li>
                            <li><b>Friday prayers</b> take place at local mosques or community centers. Dorm management can provide information on nearby mosques and prayer schedules.</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="thead">
                        <i class="fa-solid fa-shirt"></i> Modesty and Dress Code
                    </td>
                    <td>
                        <ul class="rule-group">
                            <li>Modest dress is encouraged in all common areas in consideration of Muslim cultural values. Dressing modestly helps maintain comfort and respect for all residents.</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="thead">
                        <i class="fa-solid fa-ban"></i>     Prohibited Items
                    </td>
                    <td>
                        <ul class="rule-group">
                            <li>The following items are strictly prohibited in dorm rooms and common areas:
                                <ul style="margin-left: 10px;">
                                    <li><b>Alcoholic beverages</b> and <b>illegal substances</b></li>
                                    <li><b>Non-halal food items</b> in shared kitchens (for Muslim residents)</li>
                                    <li><b>Pork products</b> in shared spaces</li>
                                    <li><b>Weapons</b> of any kind</li>
                                </ul>
                            </li>
                            <li>Violation of this policy may result in disciplinary and legal action.</li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Reviews -->
        <div class="container-section" id="reviews">
            <h2>Reviews</h2>
            
            <div class="acc-row" style="justify-content: flex-start; align-items: center; gap: 10px;">
                <span class="score">7.9</span>
                <span class="rating">Very Good</span> &middot;
                <span>356 reviews</span>
                <span style="float: right; font-size: 12px;"><a href="#">Read all reviews</a></span>
            </div>

            <h4 class="acc-row">Categories</h4>

            <div class="acc-row category-ratings">
                <div class="category">
                    <label for="" class="category-label">
                        <span>Staff</span>
                        <span>8.8</span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar"></div>
                    </div>
                </div>

                <div class="category">
                    <label for="" class="category-label">
                        <span>Facilities</span>
                        <span>8.8</span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar"></div>
                    </div>
                </div>

                <div class="category">
                    <label for="" class="category-label">
                        <span>Cleanliness</span>
                        <span>8.8</span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar"></div>
                    </div>
                </div>

                <div class="category">
                    <label for="" class="category-label">
                        <span>Comfort</span>
                        <span>8.8</span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar"></div>
                    </div>
                </div>

                <div class="category">
                    <label for="" class="category-label">
                        <span>Value for money</span>
                        <span>8.8</span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar"></div>
                    </div>
                </div>

                <div class="category">
                    <label for="" class="category-label">
                        <span>Location</span>
                        <span>8.8</span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar"></div>
                    </div>
                </div>

                <div class="category">
                    <label for="" class="category-label">
                        <span>Wi-Fi</span>
                        <span>8.8</span>
                    </label>
                    <div class="category-progress">
                        <div class="progress-bar"></div>
                    </div>
                </div>
            </div>

            <h4 class="acc-row">Guests who loved staying here</h4>
            <!-- <div class="sort-container">
                <label for="">Sort reviews by: </label>
                <select name="" id="">
                    <option value="most-relevant">Most relevant</option>
                    <option value="newest-first">Newest first</option>
                    <option value="oldest-first">Oldest first</option>
                    <option value="highest-score">Highest score</option>
                    <option value="lowest-score">Lowest score</option>
                </select>
            </div> -->

            <div class="acc-row review-container">
               <?php 
               for ($i = 0; $i <= 6; $i++) {
                ?>
                     <div class="panel review-card" title="Click to see reviews from guests.">
                    <div class="reviewer-profile-header">
                        <div class="reviewer-profile-pic">
                            <img src="/bookingapp/user/profile-pictures/iman.jpg" alt="Iman" style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover;">
                        </div>
                        <div class="reviewer-name">
                            <strong>Mohandis Iman M. Lucman</strong><br>
                            <span style="font-size: 10px;">Bangsamoro Commission for the Preservation of Cultural Heritage</span>
                        </div>
                    </div>
                    <div class="review-content">
                        <p class="text-container" id="text-content-<?php echo $i; ?>">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi eum sunt dicta dolorum quibusdam consequuntur cumque autem voluptas magnam omnis enim cum expedita, dignissimos eius quia hic id repellendus temporibus!"</p>
                        <span class="read-more" id="read-more-<?php echo $i; ?>" onclick="toggleReadMore('<?php echo $i; ?>')">Read More</span>
                    </div>
                </div>
                <?php 
               } ?>
            </div>

            <button class="btn">Read all reviews</button>
        </div>
    </div>

    <!-- Modals -->
    <?php include "../modal/view_map_modal.php"; ?>
    
    <!-- Footer -->
    <?php include "../php/footer.php"; ?>

    <div id="snackbar"></div>


    <script src="/bookingapp/js/jquery-1.10.2.min.js"></script>
    <script src="/bookingapp/js/bootstrap.bundle.min.js"></script>
    <script src="/bookingapp/js/script.js"></script>
    <script src="/bookingapp/js/scrollreveal.js "></script>
    <script src="/bookingapp/js/rayal.js"></script>
    <script type="text/javascript"></script>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();

        // Stick Page Nav
        window.onscroll = function() {stickPageNav()};

        var header = document.getElementById("sticky-nav");
        var sticky = header.offsetTop;

        function stickPageNav() {
            if (window.pageYOffset > sticky) {
                header.classList.add("sticky-nav");
                // header.style.maxWidth = "0";
            } else {
                header.classList.remove("sticky-nav");
                // header.style.maxWidth = "1200px";
            }
        }

        // Function to show the snackbar
        function showSnackbar(message) {
            const snackbar = document.getElementById('snackbar');
            snackbar.textContent = message;
            snackbar.classList.add('show');

            // After 3 seconds, hide the snackbar
            setTimeout(() => {
                snackbar.classList.remove('show');
                snackbar.classList.add('hide');
            }, 3000);

            // Reset hide class after animation
            setTimeout(() => {
                snackbar.classList.remove('hide');
            }, 3500);
        }

        // Image Slideshow Gallery
        let slideIndex = 1;
        showSlides(slideIndex);

        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            let i;
            let imageItems = document.getElementsByClassName("image-item");
            let dots = document.getElementsByClassName("demo");
            let captionText = document.getElementById("imageCaption");

            if (n > imageItems.length) {
                slideIndex = 1;
            }

            if (n < 1) {
                slideIndex = imageItems.length;
            }

            for (i = 0; i < imageItems.length; i++) {
                imageItems[i].style.display = "none";
            }

            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" selected-image", "");
            }

            imageItems[slideIndex - 1].style.display = "block";
            dots[slideIndex - 1].className += " selected-image";
            captionText.innerHTML = dots[slideIndex - 1].alt;
        }

        // Map
        var map = L.map('map').setView([7.99685, 124.26171], 16);
        var previewMap = L.map('previewMap').setView([7.996624, 124.261186], 13);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(previewMap);

        var debounceTimeout;
        map.on('moveend', function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(updateCenterCoordinates, 500);
        });

        // Function to update the input fields with the current map center
        function updateCenterCoordinates() {
            const center = map.getCenter();
            const longitude = center.lng;
            const latitude = center.lat;
            document.getElementById('longitude').value = longitude;
            document.getElementById('latitude').value = latitude;

            var placeNameOutput = document.getElementById("placeName");

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
                placeNameOutput.innerText = placeName;
            })
            .catch(error => console.error('Error:', error));
        }

        // Search Location on the Map
        function searchLocation() {
            const query = document.getElementById("mapSearchInput").value;

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
                        showSnackbar("Location not found.");
                    }
                })
                .catch(error => {
                    // console.error("Error fetching location:", error);
                    showSnackbar("Error finding location.");
                });
        }

        // Toggle View

        const toggleGridBtn = document.getElementById("toggleGridView");
        const toggleListBtn = document.getElementById("toggleListView");
        const roomGrid = document.querySelector(".room-grid");
        const roomCard = document.getElementsByClassName("room-card");

        function setGridView() {
            var i;

            for (i = 0; i < roomCard.length; i++) {
                roomCard[i].classList.remove("list-view");
            }

            roomGrid.classList.remove("list-view");
            toggleGridBtn.classList.add("toggle-active");
            toggleListBtn.classList.remove("toggle-active");
        }
        
        function setListView() {
            var i;
            roomGrid.classList.add("list-view");

            for (i = 0; i < roomCard.length; i++) {
                roomCard[i].classList.add("list-view");
            }

            toggleGridBtn.classList.remove("toggle-active");
            toggleListBtn.classList.add("toggle-active");
        }

        // Set default view
        setListView();

        // Format price
        var roomPrices = document.getElementsByClassName("room-price");

        var i;
        let formattedValue;

        for (i = 0; i < roomPrices.length; i++) {
            formattedValue = new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(roomPrices[i].innerText);
            roomPrices[i].innerHTML = formattedValue;
        }
        // priceOutput.innerHTML = formattedValue;
    
        // Read more functionality
        
        function toggleReadMore(index) {
            var readMoreLink = document.getElementById(`read-more-${index}`);
            var textContent = document.getElementById(`text-content-${index}`);

            textContent.classList.toggle('expanded');
            readMoreLink.textContent = textContent.classList.contains('expanded') ? 'Read Less' : 'Read More';
        }

        // Modal
        // Modal
        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }

        function openModal(id) {
            document.getElementById(id).style.display = "block";
        }

    </script>
</body>
</html>