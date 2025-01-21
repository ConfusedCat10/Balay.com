<!-- <div style="position: relative; display: flex; justify-content: space-between">
    <h3>Filter by:</h3>
</div> -->
<form action="/bookingapp/search_result.php" method="get">
    <input type="hidden" name="filter" value="true">
    <div class="filter-group">
        <h4>Budget range:</h4>
        <ul class="filter-options">
            <li>
                <div class="fliter-label">
                    <input type="checkbox" name="check-price-range" id="check-price-range" <?php echo isset($_GET['check-price-range']) && $_GET['check-price-range'] === "on" ? "checked" : ""; ?>> Price
                </div>
                <div class="slider-container" style="margin-top: 10px;">
                    <input type="range" class="slider" min="0" max="15000" step="100" name="priceRange" id="priceRangeInput" value="<?php echo isset($_GET['priceRange']) ? $_GET['priceRange'] : 0; ?>">
                    <p class="price-output" id="priceRangeOutput" style="text-align: right;"></p>
                </div>
            </li>
        </ul>
    </div>
    <div class="filter-group">
        <h4>Establishment type</h4>
        <ul class="filter-options">
            <li><input type="checkbox" name="establishmentType" value="Dormitory" <?php echo isset($_GET['establishmentType']) && $_GET['establishmentType'] === "Dormitory" ? "checked" : ""; ?>> Dormitories</li>
            <li><input type="checkbox" name="establishmentType" value="Cottage" <?php echo isset($_GET['establishmentType']) && $_GET['establishmentType'] === "Cottage" ? "checked" : ""; ?>> Cottages</li>
        </ul>
    </div>
    <div class="filter-group">
        <h4>Gender accommodation:</h4>
        <ul class="filter-options">
            <li><input type="checkbox" name="genderType" value="Males only" <?php echo isset($_GET['genderType']) && $_GET['genderType'] === "Males only" ? "checked" : ""; ?>> Male-only</li>
            <li><input type="checkbox" name="genderType" value="Females only" <?php echo isset($_GET['genderType']) && $_GET['genderType'] === "Females only" ? "checked" : ""; ?>> Female-only</li>
            <li><input type="checkbox" name="genderType" value="Coed" <?php echo isset($_GET['genderType']) && $_GET['genderType'] === "Coed" ? "checked" : ""; ?>> Co-ed (mixed gender)</li>
        </ul>
    </div>
    <div class="filter-group">
        <h4>Room Type:</h4>
        <ul class="filter-options">
            <li><input type="checkbox" name="roomType" value="Single occupancy" <?php echo isset($_GET['roomType']) && $_GET['roomType'] === "Single occupancy" ? "checked" : ""; ?>> Single occupancy</li>
            <li><input type="checkbox" name="roomType" value="Double occupancy" <?php echo isset($_GET['roomType']) && $_GET['roomType'] === "Double occupancy" ? "checked" : ""; ?>> Double occupancy</li>
            <li><input type="checkbox" name="roomType" value="Triple occupancy" <?php echo isset($_GET['roomType']) && $_GET['roomType'] === "Triple occupancy" ? "checked" : ""; ?>> Triple occupancy</li>
            <li><input type="checkbox" name="roomType" value="Quad occupancy" <?php echo isset($_GET['roomType']) && $_GET['roomType'] === "Quad occupancy" ? "checked" : ""; ?>> Quad occupancy</li>
            <li><input type="checkbox" name="roomType" value="Suite" <?php echo isset($_GET['roomType']) && $_GET['roomType'] === "Suite" ? "checked" : ""; ?>> Suite</li>
            <li><input type="checkbox" name="roomType" value="Suite double" <?php echo isset($_GET['roomType']) && $_GET['roomType'] === "Suite double" ? "checked" : ""; ?>> Suite double</li>
            <li><input type="checkbox" name="roomType" value="Suite triple" <?php echo isset($_GET['roomType']) && $_GET['roomType'] === "Suite triple" ? "checked" : ""; ?>> Suite triple</li>
            <li><input type="checkbox" name="roomType" value="Studio apartment" <?php echo isset($_GET['roomType']) && $_GET['roomType'] === "Studio apartment" ? "checked" : ""; ?>> Studio apartment</li>
            <li><input type="checkbox" name="roomType" value="One-bedroom apartment" <?php echo isset($_GET['roomType']) && $_GET['roomType'] === "One-bedroom apartment" ? "checked" : ""; ?>> One-bedroom apartment</li>
            <li><input type="checkbox" name="roomType" value="Two-bedroom apartment" <?php echo isset($_GET['roomType']) && $_GET['roomType'] === "Two-bedroom apartment" ? "checked" : ""; ?>> Two-bedroom apartment</li>
            <li><input type="checkbox" name="roomType" value="Three-bedroom apartment" <?php echo isset($_GET['roomType']) && $_GET['roomType'] === "Three-bedroom apartment" ? "checked" : ""; ?>> Three-bedroom apartment </li>
            <li><input type="checkbox" name="roomType" value="Luxury suite" <?php echo isset($_GET['roomType']) && $_GET['roomType'] === "Luxury suite" ? "checked" : ""; ?>> Luxury suite</li>
        </ul>
    </div> 
    <div class="filter-group">
        <h4>Room Capacity:</h4>
        <ul class="filter-options">
            <li><input type="checkbox" name="maxCapacity" value="1" <?php echo isset($_GET['maxCapacity']) && $_GET['maxCapacity'] === "1" ? "checked" : ""; ?>> 1 person</li>
            <li><input type="checkbox" name="maxCapacity" value="2" <?php echo isset($_GET['maxCapacity']) && $_GET['maxCapacity'] === "2" ? "checked" : ""; ?>> 2 persons</li>
            <li><input type="checkbox" name="maxCapacity" value="3" <?php echo isset($_GET['maxCapacity']) && $_GET['maxCapacity'] === "3" ? "checked" : ""; ?>> 3 persons</li>
            <li><input type="checkbox" name="maxCapacity" value="4" <?php echo isset($_GET['maxCapacity']) && $_GET['maxCapacity'] === "4" ? "checked" : ""; ?>> 4 persons</li>
        </ul>
    </div> 
    <div class="filter-group">
        <h4>Amenities:</h4>
        <br>
        <h5>Room Facilities</h5>
        <ul class="filter-options">
            <li><input type="checkbox" name="amenities" value="wind" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "wind" ? "checked" : ""; ?>> Air conditioning</li>
            <li><input type="checkbox" name="amenities" value="wifi" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "wifi" ? "checked" : ""; ?>> Wi-Fi</li>
            <li><input type="checkbox" name="amenities" value="book-open-reader" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "book-open-reader" ? "checked" : ""; ?>> Study desk</li>
            <li><input type="checkbox" name="amenities" value="box-archive" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "box-archive" ? "checked" : ""; ?>> Closet</li>
            <li><input type="checkbox" name="amenities" value="restroom" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "restroom" ? "checked" : ""; ?>> Private bathroom</li>
            <li><input type="checkbox" name="amenities" value="shower" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "shower" ? "checked" : ""; ?>> Shared bathroom</li>
            <li><input type="checkbox" name="amenities" value="kitchen" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "kitchen" ? "checked" : ""; ?>> Kitchen access</li>
        </ul>
        <br>
        <h5>Building Facilities</h5>
        <ul class="filter-options">
            <li><input type="checkbox" name="amenities" value="people-roof" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "people-roof" ? "checked" : ""; ?>> Common area/lounge/rooftop</li>
            <li><input type="checkbox" name="amenities" value="tv" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "tv" ? "checked" : ""; ?>> TV set</li>
            <li><input type="checkbox" name="amenities" value="book-open-reader" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "book-open-reader" ? "checked" : ""; ?>> Study rooms</li>
            <li><input type="checkbox" name="amenities" value="jug-detergent" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "jug-detergent" ? "checked" : ""; ?>> Laundry facilities</li>
            <li><input type="checkbox" name="amenities" value="dumbell" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "dumbell" ? "checked" : ""; ?>> Gym</li>
            <li><input type="checkbox" name="amenities" value="utensils" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "utensils" ? "checked" : ""; ?>> Dining hall/cafeteria</li>
            <li><input type="checkbox" name="amenities" value="book" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "book" ? "checked" : ""; ?>> Library</li>
            <li><input type="checkbox" name="amenities" value="square-parking" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "square-parking" ? "checked" : ""; ?>> Parking area</li>
            <li><input type="checkbox" name="amenities" value="user-shield" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "user-shield" ? "checked" : ""; ?>> Security services</li>
            <li><input type="checkbox" name="amenities" value="computer" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "computer" ? "checked" : ""; ?>> Internet or computer shop</li>
            <li><input type="checkbox" name="amenities" value="store" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "store" ? "checked" : ""; ?>> Sari-sari or convenience store</li>
            <li><input type="checkbox" name="amenities" value="mosque" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "mosque" ? "checked" : ""; ?>> Mosque</li>
            <li><input type="checkbox" name="amenities" value="mosque" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "mosque" ? "checked" : ""; ?>> Muslim prayer room</li>
            <li><input type="checkbox" name="amenities" value="church" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "church" ? "checked" : ""; ?>> Church</li>
            <li><input type="checkbox" name="amenities" value="church" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "church" ? "checked" : ""; ?>> Christian prayer room/area</li>
        </ul>
        <br>
        <h5>Outdoor Facilities</h5>
        <ul class="filter-options">
            <li><input type="checkbox" name="amenities" value="leaf" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "leaf" ? "checked" : ""; ?>> Garden/outdoor setting</li>
            <li><input type="checkbox" name="amenities" value="basketball" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "basketball" ? "checked" : ""; ?>> Sports facilities (e.g., basketball court)</li>
            <li><input type="checkbox" name="amenities" value="person-biking" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "person-biking" ? "checked" : ""; ?>> Bike storage</li>
        </ul>
    </div>

    <div class="filter-group">
        <h4>Accessibility Options</h4>
        <ul class="filter-options">
            <li><input type="checkbox" name="amenities" value="wheelchair" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "wheelchair" ? "checked" : ""; ?>> Wheelchair accessible</li>
            <li><input type="checkbox" name="amenities" value="elevator" <?php echo isset($_GET['amenities']) && $_GET['amenities'] === "elevator" ? "checked" : ""; ?>> Elevator access</li>
        </ul>
    </div>

    <button type="submit" class="btn btn-primary" style="float: right"><i class="fa-solid fa-filter"></i> Apply filter</button>
</form>