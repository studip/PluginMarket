<ul class="clean">
    <? foreach ($plugins as $marketplugin) : ?>
        <li>
            <a href="<?= PluginEngine::getLink($plugin, array(), "presenting/details/".$marketplugin->getId()) ?>">
                <?= htmlReady($marketplugin['name']) ?>
                <span>
                    <? $score = $marketplugin->getRating() ?>
                        <? $score = round($score, 1) / 2 ?>
                        <? $v = $score >= 1 ? 3 : ($score >= 0.5 ? 2 : "") ?>
                        <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "16px")) ?>
                        <? $v = $score >= 2 ? 3 : ($score >= 1.5 ? 2 : "") ?>
                        <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "16px")) ?>
                        <? $v = $score >= 3 ? 3 : ($score >= 2.5 ? 2 : "") ?>
                        <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "16px")) ?>
                        <? $v = $score >= 4 ? 3 : ($score >= 3.5 ? 2 : "") ?>
                        <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "16px")) ?>
                        <? $v = $score > 4.5 ? 3 : ($score >= 4.5 ? 2 : "") ?>
                        <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "16px")) ?>
                </span>
            </a>
        </li>
    <? endforeach ?>
</ul>