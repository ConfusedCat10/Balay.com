<div class="modal" id="statusRemarkModal">
        <div class="modal-content" style="width: 50%">
            <div class="modal-header">
                <span class="modal-close" onclick="closeModal('statusRemarkModal')">&times;</span>
                <h1>Write a remark</h1>
            </div>

            <div class="modal-body">
                <input type="text" name="status-remark" id="status-remark" placeholder="Write why do you want to reject the tenant">
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-primary btn" id="modal-submit-button" onclick='toggleResidencyStatus("rejected", <?php echo $residencyID; ?>, "rejected")'>Confirm rejection</button>
                <button type="button" class="btn btn-secondary">Discard</button>
            </div>
        </div>
    </div>