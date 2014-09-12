<form action="<?= PluginEngine::getLink($plugin, array(), "myplugins/save_release") ?>" method="post" class="studip_form" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $release->getId() ?>">
    <?= $this->render_partial("myplugins/_edit_release.php", array('release' => $release)) ?>

    <div data-dialog-button>
        <?= \Studip\Button::create(_("speichern")) ?>
    </div>
</form>