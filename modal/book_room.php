<div class="modal" id="bookRoomModal">
    <div class="modal-content clearfix" style="height: auto; width: 900px;">
        <span class="close" onclick="closeModal('bookRoomModal')">&times;</span>
        <h1 id="book-room-modal-title">Book room</h1>
        <form action="/bookingapp/establishment/room/book_room.php" method="post" style="margin-top: 10px; padding: 10px;">
            <input type="hidden" name="tenantID" value="<?php echo $tenantID; ?>">
            <input type="hidden" name="paymentPrice" id="book-payment-price">
            <fieldset>
                <legend>Booking information</legend>
                <div class="form-inline">
                    <div class="form-group">
                        <label for="room-id">Room ID</label>
                        <input type="text" name="room-id" id="book-room-id" placeholder="Room ID" readonly required>
                    </div>

                    <div class="form-group">
                        <label for="room-name">Room name:</label>
                        <input type="text" name="room-name" id="book-room-name" placeholder="Room Name" readonly required>
                    </div>

                    <div class="form-group">
                        <label for="room-establishment">Establishment:</label>
                        <input type="text" name="room-establishment" id="book-room-establishment" placeholder="Establishment" readonly required>
                    </div>

                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input type="text" name="room-price" id="book-room-price" placeholder="Price" readonly required>
                    </div>

                    <div class="form-group">
                        <label for="book-date">Date of the start of residency:</label>
                        <input type="date" name="date-of-entry" id="book-date" required>
                    </div>
                </div>
            </fieldset>

            <div class="btn-group" style="margin: 20px; display: flex; justify-content: flex-end; width: 30%; flex-direction: row; float: right; gap: 10px;">
                <button type="submit" class="btn btn-primary" name="book-now">Book Now</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('bookRoomModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>