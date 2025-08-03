<div class="modal" id="confirmPhotoDeleteModal">
    <div class="modal-content" style="width: 600px; height: auto;">
        <span class="close" onclick="closeModal('confirmPhotoDeleteModal');">&times;</span>
        <h3 id="confirm-modal-title">Delete photo...</h3>
        <form method="post">
            <input type="hidden" name="photoIndex" id="photo-index">
            <fieldset style="margin-top: 20px;">
                <p>Are you sure you are going to delete the photo?</p>
            </fieldset>
            <div class="btn-group" style="display: flex; margin: 10px; gap: 10px;">
                <button type="submit" class="btn approve-btn" name="delete-est-photo" onclick="deletePhoto()"><i class="fa-solid fa-trash"></i> Yes</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('confirmPhotoDeleteModal')"><i class="fa-solid fa-circle-xmark"></i> No</button>
            </div>
        </form>
    </div>
</div>