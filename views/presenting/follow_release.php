<? foreach (PageLayout::getMessages() as $message) : ?>
    <?= $message ?>
<? endforeach ?>
<form action="<?= $controller->url_for('presenting/follow_release/' . $release->getId()) ?>" method="post" data-dialog class="default">
    <p class="info">
        <?= _("Immer aktuell bleiben mit automatischen Updates! Sie finden in Ihrem eigenen Stud.IP in der Pluginverwaltung rechts neben dem Plugin ein Icon, das Sie zu einem Popup führt. Geben Sie dort die unten stehende Download-URL ein und geben Sie hier die URL ein, die Sie danach dort in Ihrer Pluginverwaltung sehen. Sind beide Systeme korrekt konfiguriert, wird der Marktplatz dann eine jede neue Version dieses Releases automatisch in ihrem Stud.IP installieren.") ?>
    </p>
    <fieldset>
        <legend><?= _("Konfigurieren Sie Ihr Stud.IP") ?></legend>
        <label>
            <?= _("Download-URL - geben Sie diese URL in Ihrem Stud.IP in der Pluginverwaltung ein") ?>
            <input type="text" readonly value="<?= $controller->absolute_url_for('presenting/download/' . $release->getId()) ?>">
        </label>
    </fieldset>
    <fieldset>
        <legend><?= _("Parameter für den Marktplatz") ?></legend>
        <label>
            <?= _("URL ihres Stud.IP für das automatische Update") ?>
            <input type="url" name="url" value="<?= htmlReady($following['url']) ?>">
        </label>

        <label>
            <?= _("Sicherheitstoken (optional)") ?>
            <input type="text" name="security_token" value="<?= htmlReady($following['security_token']) ?>">
        </label>
    </fieldset>
    <div data-dialog-button>
        <?= \Studip\Button::create(_("speichern")) ?>
    </div>
</form>