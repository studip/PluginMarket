<form action="<?= $controller->url_for('approving/approve/' . $marketplugin->getId()) ?>" method="post" class="default">
    <fieldset>
        <legend>
            <?= _("Review schreiben") ?>
        </legend>
        <label>
            <?= _("Plugin wird akzeptiert") ?>
            <input type="checkbox" name="approved" value="1">
        </label>
        <label>
            <?= _("Begründung") ?>
            <textarea name="review"></textarea>
        </label>
    </fieldset>
    <div data-dialog-button>
        <?= \Studip\Button::create(_("Review abschicken")) ?>
    </div>
</form>