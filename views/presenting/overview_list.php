<table id="plugintable" class="default">
    <caption>
        <?= htmlReady(count($plugins)) ?> <?= _('Plugins') ?>
    </caption>
    <thead>
        <tr class="sortable">
            <th class="sortasc" data-sort="asc">
                <?= _('Name') ?>
            </th>
            <th data-sort-type="int">
                <?= _('Standorte') ?>
            </th>
            <th data-sort-type="sorter">
                <?= _('Letzte Änderung') ?>
            </th>
            <th data-sort-type="int">
                <?= _('Downloads') ?>
            </th>
            <th data-sort-type="int">
                <?= _('Bewertung') ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <? foreach ($plugins as $marketplugin): ?>
            <tr>
                <td>
                    <a href="<?= $controller->url_for('presenting/details/' . $marketplugin->getId()) ?>">
                        <?= htmlReady($marketplugin->name) ?>
                    </a>
                </td>
                <td>
                    <?= htmlReady($marketplugin->uses->count()) ?>
                </td>
                <td data-sorter="<?= $marketplugin->releases->orderBy('last_upload_time DESC')->val('last_upload_time') ?>">
                    <?= strftime('%x', $marketplugin->releases->orderBy('last_upload_time DESC')->val('last_upload_time')) ?>
                </td>
                <td>
                    <?= htmlReady($marketplugin->getDownloads()) ?>
                </td>
                <? $score = $marketplugin['rating'] ?>
                <? $score = round($score, 1) / 2 ?>
                <td data-sort="<?= $score ?>">
                    <span class="starscore">
                        <? $v = $score >= 1 ? 3 : ($score >= 0.5 ? 2 : "") ?>
                        <?= Icon::create($plugin->getPluginURL() . "/assets/star$v.svg")->asImg('16px')?>
                        <? $v = $score >= 2 ? 3 : ($score >= 1.5 ? 2 : "") ?>
                        <?= Icon::create($plugin->getPluginURL() . "/assets/star$v.svg")->asImg('16px')?>
                        <? $v = $score >= 3 ? 3 : ($score >= 2.5 ? 2 : "") ?>
                        <?= Icon::create($plugin->getPluginURL() . "/assets/star$v.svg")->asImg('16px') ?>
                        <? $v = $score >= 4 ? 3 : ($score >= 3.5 ? 2 : "") ?>
                        <?= Icon::create($plugin->getPluginURL() . "/assets/star$v.svg")->asImg('16px') ?>
                        <? $v = $score > 4.5 ? 3 : ($score >= 4.5 ? 2 : "") ?>
                        <?= Icon::create($plugin->getPluginURL() . "/assets/star$v.svg")->asImg('16px')?>
                    </span>
                </td>
            </tr>
        <? endforeach; ?>
    </tbody>
</table>