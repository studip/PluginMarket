<h1><?= htmlReady($marketplugin['name']) ?></h1>
<div>
    <?= formatReady($marketplugin['description']) ?>
</div>

<h2><?= _("Releases") ?></h2>
<table class="default">
    <thead>
        <tr>
            <th><?= _("Version") ?></th>
            <th><?= _("Miniale Stud.IP-Versionsnummer") ?></th>
            <th><?= _("Maximale Stud.IP-Versionsnummer") ?></th>
            <th><?= _("MD5-Prüfsumme") ?></th>
            <th><?= _("Downloads") ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <? foreach ($marketplugin->releases as $release) : ?>
        <tr>
            <td>
                <a href="<?= PluginEngine::getLink($plugin, array(), "presenting/download/".$release->getId()) ?>">
                    <?= Assets::img("icons/20/blue/download", array('class' => "text-bottom")) ?>
                    <?= htmlReady($release['version']) ?>
                </a>
            </td>
            <td><?= $release['studip_min_version'] ? htmlReady($release['studip_min_version']) : " - " ?></td>
            <td><?= $release['studip_max_version'] ? htmlReady($release['studip_max_version']) : " - " ?></td>
            <td><?= htmlReady($release->getChecksum()) ?></td>
            <td><?= htmlReady($release['downloads']) ?></td>
            <td>
                
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>

<div style="text-align: center">
    <?= \Studip\LinkButton::create(_("bearbeiten"), PluginEngine::getURL($plugin, array(), "myplugins/edit/".$marketplugin->getId())) ?>
</div>