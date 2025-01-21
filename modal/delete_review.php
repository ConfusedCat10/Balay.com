<div class="modal" id="deleteReviewModal">
    <div class="modal-content" style="width: 600px; height: auto;">
        <span class="close" onclick="closeModal('deleteReviewModal');">&times;</span>
        <h3 id="delete-modal-title">Deleting review...</h3>
        <form action="/bookingapp/establishment/delete_review.php" method="post">
            <input type="hidden" name="review-id" id="delete-review-id">
            <input type="hidden" name="est-id" value="<?php echo $estID; ?>">
            <fieldset style="margin-top: 20px;">
                <p>Are you sure you are going to delete this review?</p>
            </fieldset>
            <div class="btn-group" style="display: flex; margin: 10px; gap: 10px;">
                <button type="submit" class="btn btn-primary" name="delete-review"><i class="fa-solid fa-trash"></i> Yes</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteReviewModal')"><i class="fa-solid fa-xmark"></i> No</button>
            </div>
        </form>
    </div>
</div>