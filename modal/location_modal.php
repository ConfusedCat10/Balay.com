<div class="modal" id="location-modal">
        <div class="modal-content" style="width: 95%; display: flex; flex-direction: column; gap: 10px; margin: auto;height: auto">
            <div class="modal-dialog" role="document">
                <div class="modal-header">
                    <h1 class="modal-title">Manage Location <br>
                    <span style="font-size: 18px; font-weight: normal; margin-bottom: 10px;">Just click where your establishment is to put a marker</span>
                </h1>
                    <span class="close" onclick="closeModal('location-modal')">&times;</span>  
                </div>

                <form method="post">
                    <div class="modal-body" style="display: flex; flex-direction: column">
                        <div id="map-modal-preview"></div>
                        <div class="form-inline" id="location-form" style="margin: auto; padding: 10px">
                            <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <input type="text" name="latitude" id="latitude" placeholder="Latitude here">
                            </div>
                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <input type="text" name="longitude" id="longitude"  placeholder="Longitude here">
                            </div>
                        </div>
                    </div>                
                    <?php if ($isUserOwner) { ?>
                    <div class="modal-footer" style="display: flex; flex-direction: row; gap: 10px;">
                        <button type="submit" class="btn btn-primary" name="save-location">Save</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal('location-modal')">Close</button>
                    </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</div>