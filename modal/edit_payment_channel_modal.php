<div class="modal" id="editPaymentChannelModal">
    <div class="modal-content clearfix" style="height: auto">
        <span class="close" onclick="closeModal('editPaymentChannelModal');">&times;</span>
        <h1>Edit payment channel</h1>
        <form action="/bookingapp/establishment/edit_payment_channel.php" method="post" enctype="multipart/form-data" style="padding: 20px;">
            <fieldset>
                <legend>Fill up the form</legend>
                <input type="hidden" name="editEstID" value="<?php echo $estID; ?>">
                <input type="hidden" name="estPayChannelID" id="estPayChannelID">
                <div class="form-inline">
                    <div class="form-group">
                        <label for="select-bank" class="mandatory">Select payment channel</label>
                        <select name="payment-channel" id="edit-payment-channel" onchange="togglePaymentChannels(this, 'edit-non-cash', 'edit-note-form')" required>
                            <option value="" disabled selected>Select channel...</option>
                            <?php
                            $payChSql = "SELECT * FROM payment_channel ORDER BY ChannelID";
                            $payChResult = mysqli_query($conn, $payChSql);

                            if (mysqli_num_rows($payChResult) > 0) {
                                while ($payChannel = mysqli_fetch_assoc($payChResult)) {
                                    $payChannelID = $payChannel['ChannelID'];
                                    $payChannelName = $payChannel['ChannelName'] ?? '';

                                    echo "<option value='$payChannelID'>$payChannelName</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-inline" id="edit-non-cash">
                    <div class="form-group">
                        <label for="account-name" class="mandatory">Account name:</label>
                        <input type="text" name="account-name" id="edit-account-name" placeholder="Enter your account name">
                    </div>

                    <div class="form-group">
                        <label for="account-number" class="mandatory">Account number:</label>
                        <input type="text" name="account-number" id="edit-account-number" placeholder="Enter your account number">
                    </div>
                </div>

                <div class="form-inline" id="edit-note-form">
                    <div class="form-group" style="width: 100%">
                        <label for="">Notes to the paying tenants:</label>
                        <textarea name="notes" id="edit-notes" wrap="hard" style="font-family: arial, sans-serif; padding: 10px; resize: none; width: 100%; height: 300px;" placeholder="Say what you want to your paying tenants (i.e., payment details)"></textarea>
                    </div>
                </div>
            </fieldset>
            <div class="btn-group" style="display: flex; margin-top: 20px; flex-direction: row; gap: 10px; width: 30%; float: right;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editPaymentChannelModal');"><i class="fa-solid fa-ban"></i> Discard</button>
                <button type="submit" class="btn btn-primary" name="editPaymentChannel"><i class="fa-solid fa-floppy-disk"></i> Update Payment Channel</button>
            </div>
        </form>
    </div>
</div>