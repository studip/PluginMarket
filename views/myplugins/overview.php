
<table class="default">
    <thead>
        <tr>
            <th><?= _("Name") ?></th>
            <th><?= _("Letztes Update") ?></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <? if (count($plugins)) : ?>
        <? foreach ($plugins as $marketplugin) : ?>
        <tr>
            <td>
                <? if ($marketplugin['publiclyvisible'] && !$marketplugin['approved']) : ?>
                    <?= Assets::img("icons/20/red/exclaim-circle", array('title' => _("Plugin wurde noch nicht von einem Administrator freigeschaltet."), 'class' => "text-bottom")) ?>
                <? endif ?>
                <a href="<?= PluginEngine::getLink($plugin, array(), "presenting/details/".$marketplugin->getId()) ?>">
                    <?= htmlReady($marketplugin['name']) ?>
                </a>
            </td>
            <td>
                <?
                $chdate = $marketplugin['chdate'];
                foreach ($marketplugin->releases as $release) {
                    $chdate = max($chdate, $release['chdate']);
                }
                ?>
                <?= date("j.n.Y, G:i", $chdate) ?> <?= _("Uhr") ?>
            </td>
            <td>
                <? if (!$marketplugin['publiclyvisible']) :  ?>
                    <?= Assets::img("icons/20/grey/lock-locked.png.png", array('title' => _("Plugin ist nicht öffentlich"))) ?>
                <? endif ?>
            </td>
            <td>
                <a href="<?= PluginEngine::getLink($plugin, array(), "myplugins/edit/".$marketplugin->getId()) ?>" data-dialog title="<?= _("Plugin-Info bearbeiten") ?>"><?= Assets::img("icons/20/blue/edit") ?></a>
                <a href="<?= PluginEngine::getLink($plugin, array(), "myplugins/add_release/".$marketplugin->getId()) ?>" data-dialog title="<?= _("Neues Release hinzufügen") ?>"><?= Assets::img("icons/20/blue/add") ?></a>
            </td>
        </tr>
        <? endforeach ?>
        <? else : ?>
        <tr>
            <td colspan="4" style="text-align: center;"><?= _("Sie haben noch kein Plugin eingestellt.") ?></td>
        </tr>
        <? endif ?>
    </tbody>
</table>

<?
$sidebar = Sidebar::Get();
$sidebar->setImage(Assets::image_path("sidebar/plugin-sidebar.png"));
$actions = new ActionsWidget();
$actions->addLink(_("Neues Plugin eintragen"), PluginEngine::getURL($plugin, array(), "myplugins/add"), null, array('data-dialog' => 1));
$sidebar->addWidget($actions);

