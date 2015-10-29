<form action="<?= $controller->url_for('myplugins/save_release') ?>" method="post" class="default" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $release->getId() ?>">
    <input type="hidden" name="plugin_id" value="<?= $marketplugin->getId() ?>">
    <?= $this->render_partial("myplugins/_edit_release.php", array('release' => $release)) ?>

    <div data-dialog-button>
        <?= \Studip\Button::create(_("speichern")) ?>
    </div>
</form>