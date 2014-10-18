<article>
    <div class="headerimagedescription">
        <h1><a href="<?= PluginEngine::getLink($plugin, array(), "presenting/details/".$marketplugin->getId()) ?>"><?= htmlReady($marketplugin['name']) ?></a></h1>
        <div class="image" style="background-image: url(<?= $marketplugin->getLogoURL() ?>);"></div>
        <p class="shortdescription">
            <?= htmlReady($marketplugin['short_description']) ?>
        </p>
    </div>
    <? $tags = $marketplugin->getTags() ?>
    <? if (count($tags)) : ?>
        <div class="tags">
            <? foreach ($tags as $tag) : ?>
                <a href="<?= PluginEngine::getLink($plugin, array('tag' => $tag), "presenting/all") ?>"><?= Assets::img("icons/16/white/tag.svg", array('class' => "text-bottom")) ?> <?= htmlReady(ucwords($tag)) ?></a>
            <? endforeach ?>
        </div>
    <? endif ?>
</article>