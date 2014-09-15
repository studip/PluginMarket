<fieldset>
    <input type="hidden" name="edit_images" value="1">
    <legend>
        <?= _("Galerie") ?>
    </legend>

    <ol id="galery_edit">
    <? foreach ($marketplugin->images as $image) : ?>
        <li>
            <input type="checkbox" name="delete_image[]" value="<?= htmlReady($image->getId()) ?>" id="delete_image_<?= htmlReady($image->getId()) ?>">
            <div>
                <img src="<?= htmlReady($image->getURL()) ?>" style="max-height: 200px;">
                <input type="hidden" name="image_order[]" value="<?= htmlReady($image->getId()) ?>">
                <label for="delete_image_<?= htmlReady($image->getId()) ?>">
                    <?= Assets::img("icons/20/blue/delete") ?>
                </label>
            </div>
        </li>
    <? endforeach ?>
    </ol>
    <script>
        jQuery(function() {
            jQuery("#galery_edit").sortable();
        });
    </script>

    <div id="new_image_container">
        <div>
            <label>
                <?= Assets::img("icons/20/blue/upload", array('class' => "text-bottom", 'style' => "cursor: pointer;")) ?>
                <input type="file" name="new_images[]">
            </label>
            <a href="#" onClick="if (jQuery('#new_image_container > div').length > 1) jQuery(this).closest('div').remove(); else jQuery(this).closest('div').find('input[type=file]').val(''); return false;"><?= Assets::img("icons/20/blue/trash") ?></a>
        </div>
    </div>
    <?= \Studip\LinkButton::create(_("Weiteres Bild"), "#", array('onClick' => "jQuery('#new_image_container > div').first().clone().appendTo('#new_image_container').find('input[type=file]').val(''); return false;")) ?>
</fieldset>