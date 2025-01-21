<div class="modal" id="addRoomAmenity" >
        <div class="modal-content" style="overflow: auto">
            <span class="close" onclick="closeModal('addRoomAmenity'); window.location.reload();">&times;</span>
            <h1>Add a room amenity by clicking a chip</h1>
            <form method="post">

            <input type="hidden" name="roomID" id="amenityRoomID">
            
            <?php
            $sql = "SELECT * FROM features ORDER BY Name";
            $result = mysqli_query($conn, $sql);


            if (mysqli_num_rows($result) > 0) {
            ?>

            <fieldset style="heigth: 20%; overflow: auto">
                    <legend>Amenities</legend>
                    <div class="chip-group clearfix">
                        <?php while ($row = mysqli_fetch_assoc($result)) { $featureID = $row['FeatureID']; ?>
                            <div class="chip" id="room-chip-<?php echo $featureID; ?>" onclick="toggleRoomAmenity(<?php echo $featureID; ?>, this, 'room')">
                                <i class="fa-solid fa-<?php echo $row['Icon']; ?>"></i>
                                <?php echo $row['Name']; ?>
                                <span class="action-btns" onclick="this.parentElement.style.display = 'none';">
                                <i class="fa-solid fa-plus"></i></span>
                            </div>
                        <?php } ?>
                    </div>
                </fieldset>

            <?php } ?> 
            </form>
        </div>
    </div>