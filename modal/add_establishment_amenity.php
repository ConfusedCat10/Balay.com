

<div class="modal" id="addEstablishmentAmenityModal" >
    <div class="modal-content" style="overflow: auto">
        <span class="close" onclick="closeModal('addEstablishmentAmenityModal'); window.location.reload()">&times; Close and Save</span>
        <h2>Manage establishment amenities</h2><br><br>
        <form action="" method="post">
        
            <?php
            $sql = "SELECT * FROM features WHERE Category IN ('building', 'both') ORDER BY Name";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
            ?>

            <fieldset style="height: 20%; overflow: auto">
                    <legend>Amenities</legend>
                    <div class="chip-group clearfix">
                        <?php while ($row = mysqli_fetch_assoc($result)) { $featureID = $row['FeatureID']; ?>
                            <div class="chip <?php if (checkAmenity($featureID, $conn, $estID)) { echo 'added'; } ?>" id="est-chip-<?php echo $featureID; ?>" onclick="toggleAmenity(<?php echo $featureID; ?>, this, <?php echo $estID; ?>, 'est')">
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