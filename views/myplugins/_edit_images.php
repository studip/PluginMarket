<fieldset>
    <input type="hidden" name="edit_images" value="1">
    <legend>
        <?= _("Galerie") ?>
    </legend>

    <ol id="galery_edit" class="pluginmarket_galery">
        <? foreach ($marketplugin->images as $image) : ?>
            <li class="image">
                <input type="checkbox" name="delete_image[]" value="<?= htmlReady($image->getId()) ?>"
                    id="delete_image_<?= htmlReady($image->getId()) ?>">
                <div>
                    <a href="<?= htmlReady($image->getURL()) ?>" data-lightbox="plugin_gallery">
                        <img src="<?= htmlReady($image->getURL()) ?>" style="max-height: 150px;">
                    </a>
                    <input type="hidden" name="image_order[]" value="<?= htmlReady($image->getId()) ?>">
                    <label for="delete_image_<?= htmlReady($image->getId()) ?>">
                        <?= Icon::create('trash', 'clickable', ['style' => "cursor: pointer;"]) ?>
                    </label>
                </div>
            </li>
        <? endforeach ?>
    </ol>
    <script>
        jQuery(function () {
            jQuery("#galery_edit").sortable();
        });
    </script>

    <div id="new_image_container">
        <div>
            <label>
                <?= Icon::create('upload', 'clickable', ['class' => "text-bottom", 'style' => "cursor: pointer;"]) ?>
                <input type="file" name="new_images[]">
            </label>
            <a href="#"
                onClick="if (jQuery('#new_image_container > div').length > 1) jQuery(this).closest('div').remove(); else jQuery(this).closest('div').find('input[type=file]').val(''); return false;"><?= Icon::create('trash') ?></a>
        </div>
    </div>
    <?= \Studip\LinkButton::create(_("Weiteres Bild"), "#", ['onClick' => "jQuery('#new_image_container > div').first().clone().appendTo('#new_image_container').find('input[type=file]').val(''); return false;"]) ?>
</fieldset>