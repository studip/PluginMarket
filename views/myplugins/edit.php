<form action="<?= PluginEngine::getLink($plugin, array(), "myplugins/save") ?>" method="post" class="studip_form" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $marketplugin->getId() ?>">
    <fieldset>
        <legend>
            <?= _("Informationen") ?>
        </legend>
        <label>
            <?= _("Name des Plugins") ?>
            <input type="text" name="data[name]" value="<?= htmlReady($marketplugin['name']) ?>">
        </label>

        <label>
            <?= _("Kurzbeschreibung") ?>
            <input type="text" name="data[short_description]" value="<?= htmlReady($marketplugin['short_description']) ?>" maxlength="160">
        </label>

        <label>
            <?= _("Sprache") ?>
            <select name="data[language]">
                <option value="de"<?= $marketplugin['language'] === "de" ? " selected" : "" ?>><?= _("Deutsch") ?></option>
                <option value="en"<?= $marketplugin['language'] === "en" ? " selected" : "" ?>><?= _("Englisch") ?></option>
                <option value="de_en"<?= $marketplugin['language'] === "de_en" ? " selected" : "" ?>><?= _("Deutsch und Englisch") ?></option>
            </select>
        </label>

        <label>
            <?= _("Lange Beschreibung") ?>
            <textarea class="add_toolbar" name="data[description]"><?= htmlReady($marketplugin['description']) ?></textarea>
        </label>

        <div>
            <?= _("Lizenz") ?>
            <input type="hidden" name="data[license]" value="GPL 2 or later">
            <p class="info">
                <?= _("Stud.IP-Plugins müssen immer mindestens GPL 2 lizensiert sein. Durch das Hochladen erklären Sie, dass auch Ihr Plugin unter der GPL liegt und liegen darf. Wenn Sie nicht das Recht haben, das zu entscheiden, laden Sie jetzt bitte nichts hoch.") ?>
            </p>
        </div>
    </fieldset>

    <?= $this->render_partial("myplugins/_edit_release.php", array('release' => new MarketRelease())) ?>

    <div data-dialog-button>
        <?= \Studip\Button::create(_("speichern")) ?>
    </div>
</form>