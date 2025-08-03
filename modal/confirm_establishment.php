<div class="modal" id="confirmEstablishmentModal">
    <div class="modal-content" style="width: 600px; height: auto;">
        <span class="close" onclick="closeModal('confirmEstablishmentModal');">&times;</span>
        <h3 id="confirm-modal-title">Confirming establishment...</h3>
        <form method="post">
            <input type="hidden" name="action" id="action-input">
            <input type="hidden" name="est-id" id="est-id">
            <fieldset style="margin-top: 20px;">
                <p>Are you sure you are going to <span id="action"></span> this establishment?</p>
            </fieldset>
            <div class="btn-group" style="display: flex; margin: 10px; gap: 10px;">
                <button type="submit" class="btn approve-btn" name="confirm-establishment"><i class="fa-solid fa-circle-check"></i> Yes</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('confirmEstablishmentModal')"><i class="fa-solid fa-circle-xmark"></i> No</button>
            </div>
        </form>
    </div>
</div>