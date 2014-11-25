<form action="<?= PluginEngine::getLink($plugin, array(), "presenting/register_for_pluginnews/".$marketplugin->getId()) ?>" method="post" class="studipform" data-dialog>
    <?= MessageBox::info(sprintf(_("Durch das Abonnieren des Plugins %s erhalten Sie Stud.IP-Nachrichten, wenn neue Releases hochgeladen werden."), $marketplugin['name'])) ?>
    <div style="text-align: center">
        <?= \Studip\Button::create(_("Plugin abonnieren"), "follow") ?>
    </div>
</form>