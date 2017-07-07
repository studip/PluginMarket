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
                <?= Icon::create('exclaim-circle', 'status-red', ['title' => _("Plugin wurde noch nicht von einem Administrator freigeschaltet."),
                    'class' => "text-bottom"])->asImg(20) ?>
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
                }
                echo htmlReady($max === "0.1" ? "?" : $max);
                ?>
            </td>
            <td>
            <? if (!$marketplugin['publiclyvisible']) :  ?>
                <?= Icon::create('lock-locked', 'inactive', ['title' => _("Plugin ist nicht öffentlich")]) ?>
            <? endif ?>
            </td>
            <td class="actions">
                <a href="<?= $controller->url_for('myplugins/edit/' . $marketplugin->getId()) ?>" data-dialog title="<?= _("Plugin-Info bearbeiten") ?>">
                    <?= Icon::create('edit', 'clickable')->asImg(20) ?>
                </a>
                <a href="<?= $controller->url_for('myplugins/add_release/' . $marketplugin->getId()) ?>" data-dialog title="<?= _("Neues Release hinzufügen") ?>">
                    <?= Icon::create('add', 'clickable')->asImg(20) ?>
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
<? if ($edit_plugin_id = Request::option('edit_plugin_id')): ?>
    <script>
        jQuery(function ($) {
            STUDIP.Dialog.fromURL('<?= $controller->url_for('myplugins/edit/' . $edit_plugin_id) ?>');
        });
    </script>
<? endif; ?>
<?
$sidebar = Sidebar::Get();
$sidebar->setImage(Assets::image_path("sidebar/plugin-sidebar.png"));
$actions = new ActionsWidget();
$actions->addLink(_("Neues Plugin eintragen"),
    $controller->url_for('myplugins/add'),
    Icon::create('add'))->asDialog();
$actions->addElement(new WidgetElement(
        '<form action="' . $controller->url_for('myplugins/addfromzip') .'"
      method="post" enctype="multipart/form-data" class="drag-and-drop">
      <input type="hidden" name="release[type]" value="zipfile">
    '. CSRFProtection::tokenTag() .'
    '._('Plugin via Drag and Drop eintragen') .'
    <input type="file" name="release_file">
    </form>'
));
$sidebar->addWidget($actions);

