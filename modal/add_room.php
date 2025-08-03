<!-- Modal for Add Room -->
<div class="modal" id="addRoomModal">
    <div class="modal-content" style="padding: 20px; height: 80%; width: 80%; overflow: auto">
        <span class="close" onclick="closeModal('addRoomModal')">&times;</span>
        <h1><i class="fa-solid fa-door-open"></i> Add a room</h1>
        <br><br>
        <form action="/bookingapp/establishment/room/add_room.php" method="post" enctype="multipart/form-data">
            <fieldset>
                <input type="hidden" name="establishment-id" value="<?php echo $estID; ?>">
                <legend>Basic</legend>
                <div class="form-inline">
                    <div class="form-group" style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                        <img src="/bookingapp/assets/images/room-sample.jpg" id="add-room-photo-preview" alt="Room Sample" style=" width: 30%; height: auto; object-fit: cover; border-radius: 5px;">                    
                        <label for="photo">Photo (optional):</label>
                        <input type="file" name="room-photo" id="add-room-photo-input" accept=".jpg, .gif., .jpeg, .png" onchange="displayPhotoPreview('add-room-photo-input', 'add-room-photo-preview')">
                    </div>

                    <div class="form-group">
                        <label for="room-name" class="mandatory">Room name:</label>
                        <input type="text" name="room-name" placeholder="E.g.: Room 1, Honesty Room" required>
                    </div>

                    <div class="form-group">
                        <label for="room-type" class="mandatory">Room type:</label>
                        <select name="room-type" required>
                            <option value="Single occupancy">Single occupancy</option>
                            <option value="Double occupancy">Double occupancy</option>
                            <option value="Triple occupancy">Triple occupancy</option>
                            <option value="Quad occupancy">Quad occupancy</option>
                            <option value="Five-person occupancy">Five-person occupancy</option>
                            <option value="Six-person occupancy">Six-person occupancy</option>
                            <option value="Ten-person occupancy">Ten-person occupancy</option>
                            <option value="Suite">Suite</option>
                            <option value="Suite double">Suite double</option>
                            <option value="Suite triple">Suite triple</option>
                            <option value="Studio apartment">Studio apartment</option>
                            <option value="One-bedroom apartment">One-bedroom apartment</option>
                            <option value="Two-bedroom apartment">Two-bedroom apartment</option>
                            <option value="Three-bedroom apartment">Three-bedroom apartment</option>                
                            <option value="Luxury suite">Luxury suite</option>                            
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price:</label><br>
                        <div class="slider-container" style="display: flex; flex-wrap: wrap; flex-direction: column; gap: 10px">
                            <input type="range" class="slider" min="0" max="15000" step="50" value="500" id="add-price-input" name="price" oninput="formatPriceCurrency('add-price-input', 'add-price-output')" required>
                            <p class="price-output" id="add-price-output" style="text-align: left; font-size: 14px"></p>
                        </div>
                    </div>

                    

                    <div class="form-group">
                        <label for="payment-rules">Payment rules:</label>
                        <input type="text" name="payment-rules" placeholder="e.g. One-month deposit">
                    </div>

                    <div class="form-group">
                        <label for="payment-structure" class="mandatory">Payment structure:</label>
                        <select name="payment-structure" required>
                            <option value="Per apartment">Per apartment</option>
                            <option value="Per bed">Per bed</option>
                            <option value="Per person">Per person</option>
                            <option value="Per room">Per room</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="payment-options" class="mandatory">Payment schedule:</label>
                        <select name="payment-options" required>
                            <option value="Monthly">Monthly</option>
                            <option value="Semester-based">Semester</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="floor-level" class="mandatory">Floor level:</label>
                        <select name="floor-level">
                            <?php 
                            for ($i = 1; $i <= $establishment['NoOfFloors']; $i++) {
                                echo "<option value='$i'>" . numberToOrdinal($i) . " floor </option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="availability" class="mandatory">Availability:</label>
                        <select name="availability">
                            <option value="Available">Available</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Reserved">Reserved</option>
                            <option value="Unaailable">Unavailable</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gender" class="mandatory">Gender requirement:</label>
                        <select name="gender">
                            <option value="Co-ed">Co-ed</option>
                            <option value="Males only">Males only</option>
                            <option value="Females only">Females only</option>
                        </select>
                    </div>
                </div>

                <p style="font-size: 12px; color: red; font-weight: normal;"><?php echo $addRoomError; ?></p>

                <div style="margin-top: 20px; display: flex; flex-direction: row; gap: 10px; justify-content: flex-end">
                    <button type="submit" name="submit-add-room" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Room</button>
                    <button type="button" class="btn btn-secondary"  onclick="closeModal('addRoomModal');"><i class="fa-solid fa-ban"></i> Cancel</button>
                </div>
            </fieldset>

        </form>
    </div>
</div>