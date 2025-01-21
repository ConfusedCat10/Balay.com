<div class="modal" id="viewMapModal">
        <div class="modal-content" style="width: 95%; display: flex; flex-direction: column; gap: 10px; margin: auto; height: auto">
            <div class="modal-dialog" role="document">
                <div class="modal-header">
                    <h1 class="modal-title">Preview Map <br></h1>
                    <p><i class="fa-solid fa-location-pin"></i> <span id="placeNameView"></span></p>
                    <span class="close" onclick="closeModal('viewMapModal')">&times;</span>  
                </div>
                    <div class="modal-body" style="display: flex; flex-direction: column">
                        <div id="viewMap"></div>
                        <div class="form-inline" id="location-form" style="margin: auto; padding: 10px">
                            <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <input type="text" name="latitude" id="latitudeViewInput" placeholder="Latitude here">
                            </div>
                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <input type="text" name="longitude" id="longitudeViewInput"  placeholder="Longitude here">
                            </div>
                        </div>
                    </div> 
            </div>
        </div>
    </div>
</div>