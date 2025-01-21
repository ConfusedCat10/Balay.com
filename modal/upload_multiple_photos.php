<div class="modal" id="establishmentPhotosModal">
    <div class="modal-content" style="height: auto; margin: auto">
        <span class="close" onclick="closeModal('establishmentPhotosModal')">&times;</span>
        <h1>Manage establishment photos</h1>
        <?php
        $sql = "SELECT * FROM establishment_photos WHERE EstablishmentID = $estID";
        $photoResult = mysqli_query($conn, $sql);
        ?>
        <form method="POST" enctype="multipart/form-data" id="photoUploadForm" style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px; margin-bottom: 20px">
            <div class="form-inline" style="display: flex; flex-direction: row; flex-wrap: wrap; justify-content: center">
                <input type="hidden" name="establishmentID" value="<?php echo $estID; ?>">
                <?php
                $images = [];
                $descriptions = [];
                if (mysqli_num_rows($photoResult) > 0) {
                    while ($row = mysqli_fetch_assoc($photoResult)) {
                        for ($i = 1; $i <= 6; $i++) {
                            $images[] = isset($row['Photo' . $i]) && $row['Photo' . $i] !== '' ? "/bookingapp/establishment/" . $row['Photo' . $i] : "/bookingapp/assets/images/msu-facade.jpg";
                            $descriptions[] = isset($row['Description' . $i]) && $row['Description' . $i] !== '' ? $row['Description' . $i] : "";
                        }
                    }
                } else {
                    for ($i = 1; $i <= 6; $i++) {
                        $images[] = "/bookingapp/assets/images/msu-facade.jpg";
                        $descriptions[] = "";
                    }
                }
                foreach ($images as $key => $image) {
                    ?>
                    <div class="form-group" style="display: flex; flex-direction: column; border: 1px solid #ccc; padding: 10px; align-items: center; justify-content: space-between; gap: 10px;">
                        <h3><?php echo ($key + 1); ?></h3>
                        <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($descriptions[$key]); ?>" style="width: 250px; height: 100px; object-fit: cover; border: 1px solid black" id="photo-preview-<?php echo ($key + 1); ?>">
                        <input type="text" name="photo-description-<?php echo ($key + 1); ?>" id="photo-description-<?php echo ($key + 1); ?>" placeholder="Enter description" value="<?php echo htmlspecialchars($descriptions[$key]); ?>">
                        <input type="file" name="photo<?php echo ($key + 1); ?>" id="photo-input-<?php echo ($key + 1); ?>" onchange="displayPhotoPreview('photo-input-<?php echo ($key + 1); ?>', 'photo-preview-<?php echo ($key + 1); ?>')">
                        <?php
                        if ($key === 0) {
                            echo "<span style='font-size: 12px;'>Featured photo</span>";
                        }
                        ?>
                        <button type="submit" id="upload-photo-btn-<?php echo ($key + 1); ?>" name="upload-est-photo" onclick="uploadPhoto(<?php echo ($key + 1); ?>)"><i class="fa-solid fa-upload"></i> Upload photo</button>
                        <button type="submit" style="background-color: grey" id="upload-photo-btn-<?php echo ($key + 1); ?>" name="delete-est-photo" onclick="deletePhoto(<?php echo ($key + 1); ?>)"><i class="fa-solid fa-trash"></i> Delete photo</button>
                    </div>
                <?php } ?>
            </div>
        </form>
    </div>
</div>