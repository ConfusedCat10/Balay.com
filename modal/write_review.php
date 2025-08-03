<div class="modal" id="writeReviewModal">
    <div class="modal-content" style="height: 100%; width: 60%; overflow: scroll">
        <span class="close" onclick="closeModal('writeReviewModal');">&times;</span>
        <h1>Write a review</h1>
        <form action="/bookingapp/establishment/write_review.php" method="post" style="padding: 10px; margin-top: 10px; gap: 10px; display: flex; flex-direction: column; justify-content: flex-end">
        <input type="hidden" name="tenantID" value="<?php echo $tenantID; ?>">
        <input type="hidden" name="estID" value="<?php echo $estID; ?>">
            <div class="form-inline">
                <div class="form-group">
                    <label for="select-room" class="mandatory">Room to review:</label>
                    <select name="reviewed-room" id="reviewed-room" required>
                        <option value="" selected disabled>Select room...</option>
                        <?php
                        $sql = "SELECT rs.ResidencyID, r.RoomID, r.RoomName FROM residency rs INNER JOIN rooms r ON r.RoomID = rs.RoomID WHERE rs.TenantID = $tenantID AND r.EstablishmentID = $estID AND rs.Status != 'deleted'";
                        $result = mysqli_query($conn, $sql);

                        echo mysqli_error($conn);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $roomID = $row['RoomID'];
                                $roomName = $row['RoomName'];
                                echo "<option value='$roomID'>$roomName</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="form-inline">
                <div class="form-group" style="width: 100%">
                    <label for="review">Write your comment</label>
                    <textarea name="comment" id="review" style="font-family: arial, sans-serif; padding: 5px; border-radius: 5px; resize: none; width:auto; height: 200px" placeholder="Comments here." wrap="hard"></textarea>
                </div>
            </div>

            <fieldset>
                <legend>Rate by categories</legend>
                <div class="form-inline">
                    <div class="form-group">
                        <label for="rate-staff"><i class="fa-solid fa-users"></i> Staff service and treatment: 
                            <span id="staff-rate-output" style="float: right;">1</span>
                        </label>
                        <input type="range" name="staff-rate" id="staff-range" min="1" max="10" value="1" oninput="rateCategory('staff-range', 'staff-rate-output')">
                    </div>
                    <div class="form-group">
                        <label for="rate-facilities"><i class="fa-solid fa-building"></i> Facilities & amenities: 
                            <span id="facilities-rate-output" style="float: right;">1</span>
                        </label>
                        <input type="range" name="facilities-rate" id="facilities-range" min="1" max="10" value="1" oninput="rateCategory('facilities-range', 'facilities-rate-output')">
                    </div>
                    <div class="form-group">
                        <label for="rate-cleanliness"><i class="fa-solid fa-broom"></i> Cleanliness: 
                            <span id="cleanliness-rate-output" style="float: right;">1</span>
                        </label>
                        <input type="range" name="cleanliness-rate" id="cleanliness-range" min="1" max="10" value="1" oninput="rateCategory('cleanliness-range', 'cleanliness-rate-output')">
                    </div>
                    <div class="form-group">
                        <label for="rate-comfort"><i class="fa-solid fa-heart"></i> Comfort: 
                            <span id="comfort-rate-output" style="float: right;">1</span>
                        </label>
                        <input type="range" name="comfort-rate" id="comfort-range" min="1" max="10" value="1" oninput="rateCategory('comfort-range', 'comfort-rate-output')">
                    </div>
                    <div class="form-group">
                        <label for="rate-money-value"><i class="fa-solid fa-peso-sign"></i> Value for money: 
                            <span id="money-value-rate-output" style="float: right;">1</span>
                        </label>
                        <input type="range" name="money-value-rate" id="money-value-range" min="1" max="10" value="1" oninput="rateCategory('money-value-range', 'money-value-rate-output')">
                    </div>
                    <div class="form-group">
                        <label for="rate-location"><i class="fa-solid fa-location-pin"></i> Location and environment: 
                            <span id="location-rate-output" style="float: right;">1</span>
                        </label>
                        <input type="range" name="location-rate" id="location-range" min="1" max="10" value="1" oninput="rateCategory('location-range', 'location-rate-output')">
                    </div>
                    <div class="form-group">
                        <label for="rate-signal"><i class="fa-solid fa-wifi"></i> Internet and celllphone signal: 
                            <span id="signal-rate-output" style="float: right;">1</span>
                        </label>
                        <input type="range" name="signal-rate" id="signal-range" min="1" max="10" value="1" oninput="rateCategory('signal-range', 'signal-rate-output')">
                    </div>
                    <div class="form-group">
                        <label for="rate-signal"><i class="fa-solid fa-shield"></i> Security: 
                            <span id="security-rate-output" style="float: right;">1</span>
                        </label>
                        <input type="range" name="security-rate" id="security-range" min="1" max="10" value="1" oninput="rateCategory('security-range', 'security-rate-output')">
                    </div>
                </div>
            </fieldset>
            <div class="btn-group" style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Submit review</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('writeReviewModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>