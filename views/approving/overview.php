<table class="default">
    <thead>
    <tr>
        <th><?= _("Name") ?></th>
        <th><?= _("Letztes Update") ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
<? if (count($plugins)) : ?>
    <? foreach ($plugins as $marketplugin): ?>
        <tr>
            <td>
                <a href="<?= $controller->url_for('presenting/details/' . $marketplugin->getId()) ?>">
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
            <td class="actions">
                <a href="<?= $controller->url_for('approving/review/' . $marketplugin->getId()) ?>" data-dialog>
                    <?= Icon::create('assessment', 'clickable')->asImg(20) ?>
                </a>
            </td>
        </tr>
    <? endforeach; ?>
<? else: ?>
        <tr>
            <td colspan="2" style="text-align: center;">
                <?= _("Keine Plugins warten auf eine Qualitätssicherung") ?>
            </td>
        </tr>
<? endif; ?>
    </tbody>
</table>

<?
$sidebar = Sidebar::Get();
$sidebar->setImage(Assets::image_path("sidebar/plugin-sidebar.png"));
$actions = new ActionsWidget();
//$actions->addLink(_("Neues Plugin eintragen"), PluginEngine::getURL($plugin, array(), "myplugins/add"), null, array('data-dialog' => 1));
//$sidebar->addWidget($actions);

