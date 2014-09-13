<?
if (strpos($_SERVER['SERVER_NAME'], ':') !== false) {
    list($_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT']) =
        explode(':', $_SERVER['SERVER_NAME']);
}
if ($_SERVER['SERVER_NAME'] === "localhost" || $_SERVER['SERVER_NAME'] = "127.0.0.1") {
    $domain_warning = sprintf(_("Achtung, mit %s als Domain kann der Webhook-Aufruf von github nicht funktionieren."), $_SERVER['SERVER_NAME']);
}
$DOMAIN_STUDIP = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
$DOMAIN_STUDIP .= '://'.$_SERVER['SERVER_NAME'];

if ($_SERVER['HTTPS'] == 'on' && $_SERVER['SERVER_PORT'] != 443 ||
    $_SERVER['HTTPS'] != 'on' && $_SERVER['SERVER_PORT'] != 80) {
    $DOMAIN_STUDIP .= ':'.$_SERVER['SERVER_PORT'];
}

?>

<fieldset>
    <legend>
        <? if ($release->isNew()) : ?>
            <?= _("Release hinzufügen") ?>
        <? else : ?>
            <?= sprintf(_("Release %s bearbeiten"), htmlReady($release['version'])) ?>
        <? endif ?>
    </legend>

    <div>
        <label>
            <input type="radio" name="release[type]" value="zipfile"<?= !$release['repository_download_url'] ? " checked" : "" ?>>
            <?= _("Als Datei") ?>
        </label>
        <label>
            <input type="radio" name="release[type]" value="git"<?= $release['repository_download_url'] ? " checked" : "" ?>>
            <?= _("Als Git-Branch") ?>
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
            <input type="text" name="release[repository_download_url]" value="<?= htmlReady($release['repository_download_url']) ?>">
        </label>
        <p class="info">
            <?= _("Github.com und gitlab bieten zu jedem Branch und Tag den Download als ZIP-Datei an. Klicken Sie dort mit rechter Maustaste auf den Downloadbutton und kopieren Sie die URL, um sie hier einzufügen. Nach dem Speichern hier können Sie auf github bzw. gitlab Webhooks einrichten, damit der Marktplatz sich automatisch die neuste Version des Plugins vom Repository holt. Damit ist das Plugin auf dem Pluginmarktplatz immer brandaktuell.") ?>
        </p>
        <? if (!$release->isNew()) : ?>
        <p class="info">
            <?= _("Webhook-URL zum Einfügen in github oder gitlab:") ?>
            <input type="text" style="border: thin solid #cccccc; background-color: #eeeeee; width:100%;" value="<?= $DOMAIN_STUDIP.URLHelper::getLink("plugins.php/pluginmarket/upate/".$release->getId(), array('s' => $release->getSecurityHash()), true) ?>">
        </p>
            <? if ($domain_warning) : ?>
            <p class="info"><?= htmlReady($domain_warning)  ?></p>
            <? endif ?>
        <? endif ?>

    </fieldset>
</fieldset>