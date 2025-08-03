<!-- <div style="position: relative; display: flex; justify-content: space-between">
    <h3>Filter by:</h3>
</div> -->
<?php 

$roomAmenityOptions = array(
    "wind" =>  "Air conditioning",
    "wifi" =>  "Wi-Fi",
    "book-open-reader" =>  "Study desk",
    "box-archive" =>  "Closet",
    "restroom" =>  "Private bathroom",
    "shower" =>  "Shared bathroom",
    "kitchen" =>  "Kitchen access"
);
$buildingAmenityOptions = array(
    "people-roof" => "Common area/lounge/rooftop",
    "tv" => "TV set",
    "book-open-reader" => "Study rooms",
    "jug-detergent" => "Laundry facilities",
    "dumbell" => "Gym",
    "utensils" => "Dining hall/cafeteria",
    "book" => "Library",
    "square-parking" => "Parking area",
    "user-shield" => "Security services",
    "computer" => "Internet or computer shop",
    "store" => "Sari-sari or convenience store",
    "mosque" => "Muslim prayer room",
    "church" => "Christian prayer room/area"
);
$outdoorAmenities = array(
    "leaf" => "Garden/outdoor setting",
    "basketball" => "Basketball court",
    "volleyball" => "Volleyball court",
    "table-tennis-paddle-ball" => "Table tennis court",
    "person-biking" => "Bike storage"
);
$sql = "SELECT * FROM `features`";
$result = mysqli_query($conn, $sql);
if ($result->num_rows > 0) {
    while($feature = mysqli_fetch_object($result)) {
        if (!$feature->Code) {
            continue;
        }
        switch($feature->Category) {
            case "room":
                $roomAmenityOptions[$feature->Code] = $feature->Name;
                break;
            case "outdoor":
                $roomAmenityOptions[$feature->Code] = $feature->Name;
                break;
            case "building":
            default:
                $buildingAmenityOptions[$feature->Code] = $feature->Name;
        }
        
    }

}

$amenities = isset($_REQUEST['amenities']) ? $_REQUEST['amenities'] : array();
function isSelected($array, $value) {
    return array_search($value, $array) !== FALSE;
}
function printAmenityOptions($options, $selected) {
?>
<ul class="filter-options">
    <?php 
    foreach ($options as $value => $label) {                
        ?>
        <li>
            <input 
                type="checkbox" 
                name="amenities[]" 
                value="<?php echo $value; ?>"
                <?php echo isSelected($selected, $value) ? ' checked ': '' ?>
            /> <?php echo $label; ?>
        </li>    
        <?php
    }
    ?>          
</ul>
<?php 
}
?>

<form action="/bookingapp/search_result.php" method="get">
    <input type="hidden" name="filter" value="true">
    <div class="filter-group">
        <h4>Budget range:</h4>
        <ul class="filter-options">
            <li>
                <div class="fliter-label">
                    <input type="checkbox" name="check-price-range" id="check-price-range"> Price
                </div>
                <div class="slider-container" style="margin-top: 10px;">
                    <input type="range" class="slider" min="0" max="15000" step="100" name="priceRange" id="priceRangeInput" value="0">
                    <p class="price-output" id="priceRangeOutput" style="text-align: right;"></p>
                </div>
            </li>
        </ul>
    </div>
    <div class="filter-group">
        <h4>Establishment type</h4>
        <ul class="filter-options">
            <li><input type="radio" name="establishmentType" value="Dormitory"> Dormitories</li>
            <li><input type="radio" name="establishmentType" value="Cottage"> Cottages</li>
        </ul>
    </div>
    <div class="filter-group">
        <h4>Gender accommodation:</h4>
        <ul class="filter-options">
            <li><input type="radio" name="genderType" value="Males only"> Male-only</li>
            <li><input type="radio" name="genderType" value="Females only"> Female-only</li>
            <li><input type="radio" name="genderType" value="Coed"> Co-ed (mixed gender)</li>
        </ul>
    </div>
    <div class="filter-group">
        <h4>Amenities:</h4>
        <br>
        <h5>Room Facilities</h5>
        <?php printAmenityOptions($roomAmenityOptions, $amenities) ?>
        <br>
        <h5>Building Facilities</h5>
        <?php printAmenityOptions($buildingAmenityOptions, $amenities) ?>
        <br>
        <h5>Outdoor Facilities</h5>
        <?php printAmenityOptions($outdoorAmenities, $amenities) ?>
        <br>
        
    </div>

    <div class="filter-group">
        <h4>Accessibility Options</h4>
        <ul class="filter-options">
            <li><input type="checkbox" name="amenities" value="wheelchair" onchange="filterRooms()"> Wheelchair accessible</li>
            <li><input type="checkbox" name="amenities" value="elevator"> Elevator access</li>
        </ul>
    </div>
    <button type="submit" class="btn btn-primary" style="float: right"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
</form>