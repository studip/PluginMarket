<article class="contentbox">
    <a href="<?= $controller->url_for('presenting/details/' . $marketplugin->getId()) ?>">
        <header>
            <h1><?= htmlReady($marketplugin['name']) ?></h1>
        </header>
        <div class="image" style="background-image: url(<?= $marketplugin->getLogoURL() ?>);"></div>
    </a>
    <p class="shortdescription">
        <?= htmlReady($marketplugin['short_description']) ?>
    </p>
<? $tags = $marketplugin->getTags(); ?>
<? if (count($tags)) : ?>
    <footer class="tags">
    <? foreach ($tags as $tag): ?>
        <a href="<?= $controller->url_for('presenting/all', compact('tag')) ?>">
            <?= htmlReady(ucwords($tag)) ?>
        </a>
    <? endforeach; ?>
    </footer>
<? endif; ?>
</article>