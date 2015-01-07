<table class="default">
    <? foreach ($plugins as $marketplugin) : ?>
        <tr>
            <td>
                <a href="<?= PluginEngine::getLink($plugin, array(), "presenting/details/".$marketplugin->getId()) ?>">
                    <?= htmlReady($marketplugin['name']) ?>
                </a>
            </td>
            <td>
                <? $score = $marketplugin->getRating() ?>
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
            <td>
                <div style="max-height: 20px; overflow: hidden;">
                    <? foreach ($marketplugin->getTags() as $key => $tag) : ?>
                        <?= $key > 0 ? "," : "" ?>
                        <a href="<?= URLHelper::getLink("plugins.php/pluginmarket/presenting/all", array('tag' => $tag)) ?>">
                            <?= Assets::img("icons/16/blue/tag", array('class' => "text-bottom")) ?>
                            <?= htmlReady(ucwords($tag)) ?>
                        </a>
                    <? endforeach ?>
                </div>
            </td>
        </tr>
    <? endforeach ?>
</table>