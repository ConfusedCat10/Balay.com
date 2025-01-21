<div class="modal" id="togglePaymentChannelModal">
    <div class="modal-content" style="width: 600px; height: auto;">
        <span class="close" onclick="closeModal('togglePaymentChannelModal');">&times;</span>
        <h3 id="payment-channel-modal-title"></h3>
        <form action="/bookingapp/establishment/toggle_payment_channel.php" method="post">
            <input type="hidden" name="epcid" id="epcid">
            <input type="hidden" name="estid" value="<?php echo $estID; ?>">
            <input type="hidden" name="action" id="toggle-action">
            <fieldset style="margin-top: 20px;">
                <p id="payment-toggle-action"></p>
            </fieldset>
            <div class="btn-group" style="display: flex; margin: 10px; gap: 10px;">
                <button type="submit" class="btn btn-primary" name="toggle-payment-channel"><i class="fa-solid fa-trash"></i> Yes</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('togglePaymentChannelModal')"><i class="fa-solid fa-xmark"></i> No</button>
            </div>
        </form>
    </div>
</div>