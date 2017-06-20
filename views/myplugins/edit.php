<form action="<?= $controller->url_for('myplugins/save') ?>" method="post" class="default" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $marketplugin->getId() ?>">
    <fieldset>
        <legend>
            <?= _("Informationen") ?>
        </legend>
        <label>
            <?= _("Angezeigter Name des Plugins") ?>
            <input required type="text" name="data[name]" value="<?= htmlReady($marketplugin['name']) ?>">
        </label>
        <label>
            <?= _("Interner Name des Plugins (aus dem Manifest)") ?>
            <input required type="text" name="data[pluginname]" value="<?= htmlReady($marketplugin['pluginname']) ?>" <?=!$marketplugin->isNew() ? 'disabled' : ''?>>
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
            <textarea class="wysiwyg" name="data[description]"><?= htmlReady($marketplugin['description']) ?></textarea>
        </label>

        <label>
            <?= _("Sichtbar für alle") ?>
            <input type="checkbox" name="data[publiclyvisible]" value="1"<?= $marketplugin->isNew() || $marketplugin['publiclyvisible'] ? " checked" : "" ?>>
        </label>

        <label>
            <?= _("Projekthomepage") ?>
            <input type="text" name="data[url]" value="<?= htmlReady($marketplugin['url']) ?>">
        </label>

        <label>
            <?= _("Schlagworte (kommasepariert)") ?>
            <input type="text" name="tags" value="<?= htmlReady(ucwords(implode(", ", $marketplugin->getTags()))) ?>">
        </label>

        <div style="margin-bottom: 10px; margin-top: 10px;">
            <?= _("Mitarbeiter") ?>
            <ul class="clean" style="margin-bottom: 5px;" id="plugincollaborators">
                <?= $this->render_partial("myplugins/_collaborator.php", array('user' => $marketplugin->user)) ?>
                <? foreach ($marketplugin->more_users as $user) : ?>
                    <?= $this->render_partial("myplugins/_collaborator.php", array('user' => $user)) ?>
                <? endforeach ?>
            </ul>
            <?= QuickSearch::get("user_id", new StandardSearch("user_id"))->fireJSFunctionOnSelect("STUDIP.PluginMarket.addCollaborator")->render() ?>
        </div>

        <div>
            <?= _("Lizenz") ?>
            <input type="hidden" name="data[license]" value="GPL 2 or later">
            <p class="info">
                <?= _("Stud.IP-Plugins müssen immer mindestens GPL 2 lizensiert sein. Durch das Hochladen erklären Sie, dass auch Ihr Plugin unter der GPL liegt und liegen darf. Wenn Sie nicht das Recht haben, das zu entscheiden, laden Sie jetzt bitte nichts hoch.") ?>
            </p>
        </div>

        <label>
            <?= _("Möglichkeit zum Spenden einblenden") ?>
            <input type="checkbox" name="data[donationsaccepted]" value="1"<?= $marketplugin->isNew() || $marketplugin['donationsaccepted'] ? " checked" : "" ?>>
        </label>

        <? if ($marketplugin->isRootable()) : ?>
            <label>
                <?= _("Plugin veraltet") ?>
                <input type="checkbox" name="data[deprecated]" value="1"<?= $marketplugin['deprecated'] ? " checked" : "" ?>>
            </label>
        <? endif ?>


    </fieldset>

    <?= $this->render_partial("myplugins/_edit_images.php", compact("marketplugin")) ?>

    <? if ($marketplugin->isNew()) : ?>
    <?= $this->render_partial("myplugins/_edit_release.php", array('release' => new MarketRelease())) ?>
    <? endif ?>

    <div data-dialog-button>
        <?= \Studip\Button::createAccept(_('Speichern')) ?>
    </div>
</form>