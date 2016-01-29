<form action="<?= $controller->url_for('myplugins/save') ?>" method="post" enctype="multipart/form-data" class="default">
    <input type="hidden" name="id" value="<?= $marketplugin->getId() ?>">
    <?= $this->render_partial("myplugins/_edit_images.php", compact("marketplugin")) ?>

    <div data-dialog-button>
        <?= \Studip\Button::create(_("speichern")) ?>
    </div>
</form>