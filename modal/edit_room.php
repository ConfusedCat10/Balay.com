<!-- Modal for Add Room -->
<div class="modal" id="editRoomModal">
    <div class="modal-content" style="padding: 20px; height: 80%; width: 80%; overflow: auto">
        <span class="close" onclick="closeModal('editRoomModal')">&times;</span>
        <h1><i class="fa-solid fa-door-open"></i> Update room</h1>
        <br><br>
        <form action="/bookingapp/establishment/room/edit_room.php" method="post" enctype="multipart/form-data">
            <fieldset>
                <input type="hidden" name="room-id" id="edit-room-id">
                <input type="hidden" name="est-id" value="<?php echo $estID; ?>">
                <input type="hidden" name="old-photo" id="edit-old-photo">
                <legend>Basic</legend>
                <div class="form-inline">
                    <div class="form-group" style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                        <img src="/bookingapp/assets/images/room-sample.jpg" id="edit-room-photo-preview" alt="Room Sample" style=" width: 30%; height: auto; object-fit: cover; border-radius: 5px;">                    
                        <label for="photo">Photo (optional):</label>
                        <input type="file" name="room-photo" id="edit-room-photo-input" accept=".jpg, .gif., .jpeg, .png" onchange="displayPhotoPreview('edit-room-photo-input', 'edit-room-photo-preview')">
                    </div>

                    <div class="form-group">
                        <label for="room-name" class="mandatory">Room name:</label>
                        <input type="text" id="edit-room-name" name="room-name" placeholder="E.g.: Room 1, Honesty Room" required>
                    </div>

                    <div class="form-group">
                        <label for="room-type" class="mandatory">Room type:</label>
                        <select name="room-type" id="edit-room-type" required>
                            <option value="Single occupancy">Single occupancy</option>
                            <option value="Double occupancy">Double occupancy</option>
                            <option value="Triple occupancy">Triple occupancy</option>
                            <option value="Quad occupancy">Quad occupancy</option>
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
                            <input type="range" id="edit-room-price" class="slider" min="0" max="15000" step="50" value="500"name="price" oninput="formatPriceCurrency('edit-room-price', 'edit-price-output')" required>
                            <p class="price-output" id="edit-price-output" style="text-align: left; font-size: 14px"></p>
                        </div>
                    </div>

                    

                    <div class="form-group">
                        <label for="payment-rules">Payment rules:</label>
                        <input type="text" id="edit-room-payment-rules" name="payment-rules" placeholder="e.g. One-month deposit">
                    </div>

                    <div class="form-group">
                        <label for="payment-structure" class="mandatory">Payment structure:</label>
                        <select name="payment-structure" id="edit-payment-structure" required>
                            <option value="Per apartment">Per apartment</option>
                            <option value="Per bed">Per bed</option>
                            <option value="Per person">Per person</option>
                            <option value="Per room">Per room</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="payment-options" class="mandatory">Payment schedule:</label>
                        <select name="payment-options" id="edit-payment-options" required>
                            <option value="Monthly">Monthly</option>
                            <option value="Semester-based">Semester</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="floor-level" class="mandatory">Floor level:</label>
                        <select name="floor-level" id="edit-floor-level">
                            <?php 
                            for ($i = 1; $i <= $establishment['NoOfFloors']; $i++) {
                                echo "<option value='$i'>" . numberToOrdinal($i) . " floor </option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="availability" class="mandatory">Availability:</label>
                        <select name="availability" id="edit-room-availability">
                            <option value="Available">Available</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Reserved">Reserved</option>
                            <option value="Unaailable">Unavailable</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gender" class="mandatory">Gender requirement:</label>
                        <select name="gender" id='edit-room-gender'>
                            <option value="Co-ed">Co-ed</option>
                            <option value="Males only">Males only</option>
                            <option value="Females only">Females only</option>
                        </select>
                    </div>
                </div>

                <p style="font-size: 12px; color: red; font-weight: normal;"><?php echo $editRoomError; ?></p>

                <div style="margin-top: 20px; display: flex; flex-direction: row; gap: 10px; justify-content: flex-end">
                    <button type="submit" name="submit-edit-room" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Update Room</button>
                    <button type="button" class="btn btn-secondary"  onclick="closeModal('editRoomModal');"><i class="fa-solid fa-ban"></i> Cancel</button>
                </div>
            </fieldset>

        </form>
    </div>
</div>