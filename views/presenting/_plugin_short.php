<article>
    <div class="headerimagedescription">
        <a href="<?= PluginEngine::getLink($plugin, array(), "presenting/details/".$marketplugin->getId()) ?>">
            <h1><?= htmlReady($marketplugin['name']) ?></h1>
            <div class="image" style="background-image: url(<?= $marketplugin->getLogoURL() ?>);"></div>
        </a>
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