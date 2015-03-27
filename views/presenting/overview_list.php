<table id="plugintable" class="default">
    <caption>
        <?= htmlReady(count($plugins)) ?> <?= _('Plugins') ?>
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
                    <? $score = $marketplugin['rating'] ?>
                    <? $score = round($score, 1) / 2 ?>
                    <span class="starscore">
                        <? $v = $score >= 1 ? 3 : ($score >= 0.5 ? 2 : "") ?>
                        <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "16px", 'class' => "big-image-handled")) ?>
                        <? $v = $score >= 2 ? 3 : ($score >= 1.5 ? 2 : "") ?>
                        <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "16px", 'class' => "big-image-handled")) ?>
                        <? $v = $score >= 3 ? 3 : ($score >= 2.5 ? 2 : "") ?>
                        <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "16px", 'class' => "big-image-handled")) ?>
                        <? $v = $score >= 4 ? 3 : ($score >= 3.5 ? 2 : "") ?>
                        <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "16px", 'class' => "big-image-handled")) ?>
                        <? $v = $score > 4.5 ? 3 : ($score >= 4.5 ? 2 : "") ?>
                        <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "16px", 'class' => "big-image-handled")) ?>
                    </span>
                </td>
            </tr>  
        <? endforeach; ?>
    </tbody>
</table>