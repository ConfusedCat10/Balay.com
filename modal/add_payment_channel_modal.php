<div class="modal" id="addPaymentChannelModal">
    <div class="modal-content clearfix" style="height: auto">
        <span class="close" onclick="closeModal('addPaymentChannelModal');">&times;</span>
        <h1>Add a payment channel</h1>
        <form action="/bookingapp/establishment/add_payment_channel.php" method="post" enctype="multipart/form-data" style="padding: 20px;">
            <fieldset>
                <legend>Fill up the form</legend>
                <input type="hidden" name="estID" value="<?php echo $estID; ?>">
                <div class="form-inline">
                    <div class="form-group">
                        <label for="select-bank" class="mandatory">Select payment channel</label>
                        <select name="payment-channel" onchange="togglePaymentChannels(this, 'non-cash', 'note-form')" required>
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
                <div class="form-inline" id="non-cash">
                    <div class="form-group">
                        <label for="account-name" class="mandatory">Account name:</label>
                        <input type="text" name="account-name" id="account-name" placeholder="Enter your account name">
                    </div>

                    <div class="form-group">
                        <label for="account-number" class="mandatory">Account number:</label>
                        <input type="text" name="account-number" id="account-number" placeholder="Enter your account number">
                    </div>
                </div>

                <div class="form-inline" id="note-form">
                    <div class="form-group" style="width: 100%">
                        <label for="">Notes to the paying tenants:</label>
                        <textarea name="notes" id="notes" wrap="hard" style="font-family: arial, sans-serif; padding: 10px; resize: none; width: 100%; height: 300px;" placeholder="Say what you want to your paying tenants (i.e., payment details)"></textarea>
                    </div>
                </div>
            </fieldset>
            <div class="btn-group" style="display: flex; margin-top: 20px; flex-direction: row; gap: 10px; width: 30%; float: right;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addPaymentChannelModal');"><i class="fa-solid fa-ban"></i> Discard</button>
                <button type="submit" class="btn btn-primary" name="addPaymentChannel"><i class="fa-solid fa-add"></i> Add Payment Channel</button>
            </div>
        </form>
    </div>
</div>