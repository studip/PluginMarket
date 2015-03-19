<table id="plugintable" class="default">
    <caption>
        <?= _('Plugins') ?>
    </caption>
    <thead>
        <tr>
            <th>
                <?= _('Name') ?>
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
                <td data-sort="<?= htmlReady($marketplugin->name) ?>">
                    <a href="<?= $controller->url_for('presenting/details/' . $marketplugin->getId()) ?>">
                        <?= htmlReady($marketplugin->name) ?>
                    </a>
                </td>
                <td>
                    <?= htmlReady($marketplugin->getDownloads()) ?>
                </td>
                <td>
                    <?= htmlReady($marketplugin->getRating()) ?>
                </td>
            </tr>  
        <? endforeach; ?>
    </tbody>
</table>