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
        </tr>
    </thead>
    <tbody>
    <? foreach ($marketplugin->releases as $release) : ?>
        <tr>
            <td><?= htmlReady($release['version']) ?></td>
            <td><?= $release['studip_min_version'] ? htmlReady($release['studip_min_version']) : " - " ?></td>
            <td><?= $release['studip_max_version'] ? htmlReady($release['studip_max_version']) : " - " ?></td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>

<div style="text-align: center">
    <?= \Studip\LinkButton::create(_("bearbeiten"), PluginEngine::getURL($plugin, array(), "myplugins/edit/".$marketplugin->getId())) ?>
</div>