<div class="modal" id="editReviewModal">
    <div class="modal-content" style="height: 100%; width: 60%; overflow:scroll">
        <span class="close" onclick="closeModal('editReviewModal');">&times;</span>
        <h1>Edit review</h1>
        <form action="/bookingapp/establishment/edit_review.php" method="post" style="padding: 10px; margin-top: 10px; gap: 10px; display: flex; flex-direction: column; justify-content: flex-end">
        <input type="hidden" name="reviewID" id="reviewID">
        <input type="hidden" name="estID" value="<?php echo $estID; ?>">
            <div class="form-inline">
                <div class="form-group">
                    <label for="select-room" class="mandatory">Room to review:</label>
                    <select name="reviewed-room" id="reviewed-room-edit" required>
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
                    <textarea name="comment" id="comment-edit" style="font-family: arial, sans-serif; padding: 5px; border-radius: 5px; resize: none; width:auto; height: 200px" placeholder="Comments here." wrap="hard"></textarea>
                </div>
            </div>

            <fieldset>
                <legend>Rate by categories</legend>
                <div class="form-inline">
                    <div class="form-group">
                        <label for="rate-staff"><i class="fa-solid fa-users"></i> Staff service and treatment: 
                            <span id="staff-rate-output-edit" style="float: right;">1</span>
                        </label>
                        <input type="range" name="staff-rate" id="staff-range-edit" min="1" max="10" value="1" oninput="rateCategory('staff-range-edit', 'staff-rate-output-edit')">
                    </div>
                    <div class="form-group">
                        <label for="rate-facilities"><i class="fa-solid fa-building"></i> Facilities & amenities: 
                            <span id="facilities-rate-output-edit" style="float: right;">1</span>
                        </label>
                        <input type="range" name="facilities-rate" id="facilities-range-edit" min="1" max="10" value="1" oninput="rateCategory('facilities-range-edit', 'facilities-rate-output-edit')">
                    </div>
                    <div class="form-group">
                        <label for="rate-cleanliness"><i class="fa-solid fa-broom"></i> Cleanliness: 
                            <span id="cleanliness-rate-output-edit" style="float: right;">1</span>
                        </label>
                        <input type="range" name="cleanliness-rate" id="cleanliness-range-edit" min="1" max="10" value="1" oninput="rateCategory('cleanliness-range-edit', 'cleanliness-rate-output-edit')">
                    </div>
                    <div class="form-group">
                        <label for="rate-comfort"><i class="fa-solid fa-heart"></i> Comfort: 
                            <span id="comfort-rate-output-edit" style="float: right;">1</span>
                        </label>
                        <input type="range" name="comfort-rate" id="comfort-range-edit" min="1" max="10" value="1" oninput="rateCategory('comfort-range-edit', 'comfort-rate-output-edit')">
                    </div>
                    <div class="form-group">
                        <label for="rate-money-value"><i class="fa-solid fa-peso-sign"></i> Value for money: 
                            <span id="money-value-rate-output-edit" style="float: right;">1</span>
                        </label>
                        <input type="range" name="money-value-rate" id="money-value-range-edit" min="1" max="10" value="1" oninput="rateCategory('money-value-range-edit', 'money-value-rate-output-edit')">
                    </div>
                    <div class="form-group">
                        <label for="rate-location"><i class="fa-solid fa-location-pin"></i> Location and environment: 
                            <span id="location-rate-output-edit" style="float: right;">1</span>
                        </label>
                        <input type="range" name="location-rate" id="location-range-edit" min="1" max="10" value="1" oninput="rateCategory('location-range-edit', 'location-rate-output-edit')">
                    </div>
                    <div class="form-group">
                        <label for="rate-signal"><i class="fa-solid fa-wifi"></i> Internet and celllphone signal: 
                            <span id="signal-rate-output-edit" style="float: right;">1</span>
                        </label>
                        <input type="range" name="signal-rate" id="signal-range-edit" min="1" max="10" value="1" oninput="rateCategory('signal-range-edit', 'signal-rate-output-edit')">
                    </div>
                    <div class="form-group">
                        <label for="rate-security"><i class="fa-solid fa-shield"></i> Security: 
                            <span id="security-rate-output-edit" style="float: right;">1</span>
                        </label>
                        <input type="range" name="security-rate" id="security-range-edit" min="1" max="10" value="1" oninput="rateCategory('security-range-edit', 'security-rate-output-edit')">
                    </div>
                </div>
            </fieldset>
            <div class="btn-group" style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Submit review</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('editReviewModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>