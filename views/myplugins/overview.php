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
        <? if (count($plugins)): ?>
            <? foreach ($plugins as $marketplugin): ?>
                <tr>
                    <td>
                        <? if ($marketplugin['publiclyvisible'] && !$marketplugin['approved']) : ?>
                            <?= Icon::create('exclaim-circle', 'status-red', ['title' => _("Plugin wurde noch nicht von einem Administrator freigeschaltet."),
                                                                              'class' => "text-bottom"]) ?>
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
                        <? if (!$marketplugin['publiclyvisible']) : ?>
                            <?= Icon::create('lock-locked', 'inactive', ['title' => _("Plugin ist nicht öffentlich")]) ?>
                        <? endif ?>
                    </td>
                    <td class="actions">
                        <a href="<?= $controller->url_for('myplugins/edit/' . $marketplugin->getId()) ?>" data-dialog
                            title="<?= _("Plugin-Info bearbeiten") ?>">
                            <?= Icon::create('edit') ?>
                        </a>
                        <a href="<?= $controller->url_for('myplugins/add_release/' . $marketplugin->getId()) ?>"
                            data-dialog title="<?= _("Neues Release hinzufügen") ?>">
                            <?= Icon::create('add') ?>
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
    Icon::create('add'))->asDialog();
$sidebar->addWidget($actions);

