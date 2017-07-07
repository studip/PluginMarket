<form method="post" action="<?= $controller->url_for('myplugins/delete/' . $marketplugin->getId()) ?>">
    <?= CSRFProtection::tokenTag() ?>
    <p><?= sprintf(_('Soll das Plugin %s wirklich unwiederruflich gelöscht werden?'), htmlReady($marketplugin->name)) ?></p>

    <div data-dialog-button>
        <?= \Studip\Button::create(_('Endgültig löschen'), 'delete') ?>
    </div>
</form>