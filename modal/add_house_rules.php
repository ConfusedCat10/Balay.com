<div class="modal" id="addHouseRulesModal">
    <div class="modal-content clearfix" style="height: 80%; overflow: scroll;">
        <span class="close" onclick="closeModal('addHouseRulesModal')">&times;</span>
        <h3>Write a house rules for </h3>
        <h1><?php echo $establishment['Name']; ?></h1>
        <form method="post">
            <textarea name="house-rules" id="" style="height: 500px; width: 100%; margin-top: 10px; border-radius: 5px; font-size: 18px; font-family: 'Roboto', arial, sans-serif; padding: 10px" wrap="hard" placeholder="Write something... (you can format it in HTML)">
                <?php
                $houseRules = $establishment['HouseRules'];
                $houseRules = str_replace("\\r\\n", "\n", $houseRules);
                $houseRules = str_replace("\\'", "'", $houseRules);
                $houseRules = str_replace("\\\"", "\"", $houseRules);

                echo $houseRules;
                ?>
            </textarea>

            <p style="margin: 10px; color: red; font-style: italic; text-align: center;"><?php echo $houseRulesError; ?></p>

            <div class="btn-groups" style="display: flex;justify-content: flex-end; gap: 10px;">
                <button type="submit" class="btn btn-primary" name="updateHouseRules"><i class="fa-solid fa-floppy-disk"></i> Save house rules</button>
                <button type="submit" class="btn btn-secondary" name="resetHouseRules" style="background: grey; color: white"><i class="fa-solid fa-trash"></i> Reset</button>
            </div>
        </form>
    </div>
</div>