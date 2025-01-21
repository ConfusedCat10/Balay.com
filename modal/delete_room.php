<div class="modal" id="deleteRoomModal">
    <div class="modal-content" style="width: 600px; height: auto;">
        <span class="close" onclick="closeModal('deleteRoomModal');">&times;</span>
        <h3 id="delete-modal-title">Deleting room...</h3>
        <form action="/bookingapp/establishment/room/delete_room.php" method="post">
            <input type="hidden" name="room-id" id="delete-room-id">
            <input type="hidden" name="est-id" id="est-id" value="<?php echo $estID; ?>">
            <fieldset style="margin-top: 20px;">
                <p>Are you sure you are going to delete this room?</p>
            </fieldset>
            <div class="btn-group" style="display: flex; margin: 10px; gap: 10px;">
                <button type="submit" class="btn btn-primary" name="delete-room"><i class="fa-solid fa-trash"></i> Yes</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteRoomModal')"><i class="fa-solid fa-xmark"></i> No</button>
            </div>
        </form>
    </div>
</div>