<fieldset>
    <legend>
        <? if ($release->isNew()) : ?>
            <?= _("Release hinzuf�gen") ?>
        <? else : ?>
            <?= sprintf(_("Release %s bearbeiten"), htmlReady($release['version'])) ?>
        <? endif ?>
    </legend>

    <div>
        <label>
            <input type="radio" name="release[type]" value="zipfile"<?= !$release['repository_download_url'] ? " checked" : "" ?> onChange="if (this.checked) { jQuery('fieldset.release_zip_upload').show(); jQuery('fieldset.release_internet_repository').hide(); } else { jQuery('fieldset.release_zip_upload').hide(); jQuery('fieldset.release_internet_repository').show(); }">
            <?= _("Als Datei") ?>
        </label>
        <label>
            <input type="radio" name="release[type]" value="git"<?= $release['repository_download_url'] ? " checked" : "" ?> onChange="if (!this.checked) { jQuery('fieldset.release_zip_upload').show(); jQuery('fieldset.release_internet_repository').hide(); } else { jQuery('fieldset.release_zip_upload').hide(); jQuery('fieldset.release_internet_repository').show(); }">
            <?= _("Als Git-Branch") ?>
        </label>
    </div>

    <fieldset class="release_zip_upload"<?= $release['repository_download_url'] ? ' style="display: none;"' : "" ?>>
        <legend>
            <?= _("ZIP ausw�hlen") ?>
        </legend>
        <label>
            <a style="cursor: pointer">
                <?= Icon::create('upload') ?>
                <input type="file" name="release_file">
            </a>
        </label>
    </fieldset>

    <fieldset class="release_internet_repository"<?= $release->isNew() || !$release['repository_download_url'] ? ' style="display: none;"' : "" ?>>
        <legend>
            <?= _("Git-Branch") ?>
        </legend>

        <label>
            <?= _("Download-URL des Branches oder des Tags") ?>
            <input type="text" name="release[repository_download_url]" value="<?= htmlReady($release['repository_download_url']) ?>">
        </label>
        <label>
            <input type="checkbox" name="use_secret" <?= $release->isNew() || $release['repository_secret'] ? " checked" : "" ?> value="1">
            <?= _("Automatisches Update absichern �ber Sicherheitstoken (optional)") ?>
        </label>
        <p class="info">
            <?= _("Github.com und gitlab bieten zu jedem Branch und Tag den Download als ZIP-Datei an. Klicken Sie dort mit rechter Maustaste auf den Downloadbutton und kopieren Sie die URL, um sie hier einzuf�gen. Nach dem Speichern hier k�nnen Sie auf github bzw. gitlab Webhooks einrichten, damit der Marktplatz sich automatisch die neuste Version des Plugins vom Repository holt. Damit ist das Plugin auf dem Pluginmarktplatz immer brandaktuell.") ?>
        </p>

        <label>
            <input type="checkbox" name="release[repository_overwrites_descriptionfrom]" value="1"<?= $release['repository_overwrites_descriptionfrom'] ? " checked" : "" ?>>
            <?= _("Readme-Datei dieses Repositorys als Beschreibung des Plugins verwenden") ?>
        </label>

        <? if (!$release->isNew()) : ?>
        <p class="info">
            <?= _("Webhook-URL zum Einf�gen in github oder gitlab:") ?>
            <input type="text" readonly style="border: thin solid #cccccc; background-color: #eeeeee; width:100%;" value="<?= $controller->absolute_url_for('update/release/' . $release->getId(), array('s' => $release->getSecurityHash())) ?>">
        </p>
        <? if ($release['repository_secret']) : ?>
            <label>
                <?= _("Secret (muss in github beim Webhook eingetragen werden)") ?>
                <input type="text" readonly style="border: thin solid #cccccc; background-color: #eeeeee;" name="release[repository_secret]" value="<?= htmlReady($release['repository_secret']) ?>">
            </label>
        <? endif ?>

            <? if ($domain_warning) : ?>
            <p class="info"><?= htmlReady($domain_warning)  ?></p>
            <? endif ?>
        <? endif ?>

    </fieldset>
</fieldset>