<article class="contentbox">
    <a href="<?= PluginEngine::getLink($plugin, array(), "presenting/details/" . $marketplugin->getId()) ?>">
        <header>
            <h1><?= htmlReady($marketplugin['name']) ?></h1>
        </header>
        <div class="image" style="background-image: url(<?= $marketplugin->getLogoURL() ?>);"></div>
    </a>
    <p class="shortdescription">
        <?= htmlReady($marketplugin['short_description']) ?>
    </p>
    <? $tags = $marketplugin->getTags() ?>
    <? if (count($tags)) : ?>
        <footer class="tags">
            <? foreach ($tags as $tag) : ?>
                <a href="<?= PluginEngine::getLink($plugin, array('tag' => $tag), "presenting/all") ?>"><?= Assets::img("icons/16/white/tag.svg", array('class' => "text-bottom")) ?> <?= htmlReady(ucwords($tag)) ?></a>
            <? endforeach ?>
        </footer>
    <? endif ?>
</article>