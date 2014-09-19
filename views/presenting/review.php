<form action="<?= PluginEngine::getLink($plugin, array(), "presenting/save_review/".$review['plugin_id']) ?>" method="post" class="studip_form">
    <fieldset>
        <legend>
            <?= _("Bewertung") ?>
        </legend>
        <input type="hidden" name="data[plugin_id]" value="<?= htmlReady($review['plugin_id']) ?>">
        <label>
            <?= _("Note") ?>
            <select name="data[rating]" required>
                <option value="0"<?= $review['rating'] == 0 ? " selected" : "" ?>><?= _("0 Sterne") ?></option>
                <option value="1"<?= $review['rating'] == 1 ? " selected" : "" ?>><?= _("1 Stern") ?></option>
                <option value="2"<?= $review['rating'] == 2 ? " selected" : "" ?>><?= _("2 Sterne") ?></option>
                <option value="3"<?= $review['rating'] == 3 ? " selected" : "" ?>><?= _("3 Sterne") ?></option>
                <option value="4"<?= $review['rating'] == 4 ? " selected" : "" ?>><?= _("4 Sterne") ?></option>
                <option value="5"<?= $review['rating'] == 5 ? " selected" : "" ?>><?= _("5 Sterne") ?></option>
            </select>
        </label>
        <label>
            <?= _("Review (optional)") ?>
            <textarea name="data[review]"><?= htmlReady($review['review']) ?></textarea>
        </label>
    </fieldset>
    <div data-dialog-button>
        <?= \Studip\Button::create(_("speichern")) ?>
    </div>
</form>