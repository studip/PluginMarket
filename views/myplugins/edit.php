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

    <fieldset>
        <legend>
            <?= _("Release hinzufügen") ?>
        </legend>

        <label>
            <?= _("Releasebezeichnung") ?>
            <input type="text" name="release[version]" placeholder="<?= _("z.B. Rocky Raccoon 3.0.1") ?>">
        </label>

        <div>
            <label>
                <input type="radio" name="release[type]" value="zipfile">
                <?= _("Als Datei") ?>
            </label>
            <label>
                <input type="radio" name="release[type]" value="git">
                <?= _("Als Git-Branch") ?>
            </label>
            <label>
                <input type="radio" name="release[type]" value="">
                <?= _("Kein Release hinzufügen") ?>
            </label>
        </div>

        <fieldset>
            <legend>
                <?= _("ZIP auswählen") ?>
            </legend>
            <label>
                <a style="cursor: pointer">
                    <?= Assets::img("icons/20/blue/upload") ?>
                    <input type="file" name="release_file">
                </a>
            </label>
        </fieldset>

        <fieldset>
            <legend>
                <?= _("Git-Branch") ?>
            </legend>

            <label>
                <?= _("Download-URL des Branches oder des Tags") ?>
                <input type="text" name="release[repository_download_url]">
            </label>
            <p class="info">
                <?= _("Github.com und gitlab bieten zu jedem Branch und Tag den Download als ZIP-Datei an. Klicken Sie dort mit rechter Maustaste auf den Downloadbutton und kopieren Sie die URL, um sie hier einzufügen. Nach dem Speichern hier können Sie auf github bzw. gitlab Webhooks einrichten, damit der Marktplatz sich automatisch die neuste Version des Plugins vom Repository holt. Damit ist das Plugin auf dem Pluginmarktplatz immer brandaktuell.") ?>
            </p>

        </fieldset>
    </fieldset>



    <div data-dialog-button>
        <?= \Studip\Button::create(_("speichern")) ?>
    </div>
</form>