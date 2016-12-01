<table class="default">
    <thead>
        <tr>
            <th><?= _("Name") ?></th>
            <th><?= _("Letztes Update") ?></th>
            <th><?= _("Maximale Stud.IP-Version") ?></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
<? if (count($plugins)):  ?>
    <? foreach ($plugins as $marketplugin): ?>
        <tr>
            <td>
            <? if ($marketplugin['publiclyvisible'] && !$marketplugin['approved']) : ?>
                <?= Assets::img("icons/20/red/exclaim-circle", array('title' => _("Plugin wurde noch nicht von einem Administrator freigeschaltet."), 'class' => "text-bottom")) ?>
            <? endif; ?>
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
            <td>
                <?
                $max = "0.1";
                foreach ($marketplugin->releases as $release) {
                    if (version_compare($max, $release['studip_max_version'], "<")) {
                        $max = $release['studip_max_version'];
                    }
                    echo htmlReady($max === "0.1" ? "?" : $max);
                }
                ?>
            </td>
            <td>
            <? if (!$marketplugin['publiclyvisible']) :  ?>
                <?= Assets::img("icons/20/grey/lock-locked.png.png", array('title' => _("Plugin ist nicht öffentlich"))) ?>
            <? endif ?>
            </td>
            <td class="actions">
                <a href="<?= $controller->url_for('myplugins/edit/' . $marketplugin->getId()) ?>" data-dialog title="<?= _("Plugin-Info bearbeiten") ?>">
                    <?= Assets::img('icons/20/blue/edit') ?>
                </a>
                <a href="<?= $controller->url_for('myplugins/add_release/' . $marketplugin->getId()) ?>" data-dialog title="<?= _("Neues Release hinzufügen") ?>">
                    <?= Assets::img("icons/20/blue/add") ?>
                </a>
            </td>
        </tr>
    <? endforeach; ?>
<? else: ?>
        <tr>
            <td colspan="4" style="text-align: center;">
                <?= _("Sie haben noch kein Plugin eingestellt.") ?>
            </td>
        </tr>
<? endif; ?>
    </tbody>
</table>

<?
$sidebar = Sidebar::Get();
$sidebar->setImage(Assets::image_path("sidebar/plugin-sidebar.png"));
$actions = new ActionsWidget();
$actions->addLink(_("Neues Plugin eintragen"),
                  $controller->url_for('myplugins/add'),
                  'icons/16/blue/add.png')->asDialog();
$sidebar->addWidget($actions);

