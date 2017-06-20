<article class="contentbox<?= $marketplugin['deprecated'] ? " deprecated" : "" ?>">
    <a href="<?= $controller->url_for('presenting/details/' . $marketplugin->getId()) ?>">
        <header>
            <h1><?= htmlReady($marketplugin['name']) ?></h1>
        </header>
        <div class="image" style="background-image: url(<?= $marketplugin->getLogoURL() ?>);"></div>
        <p class="shortdescription">
            <?= htmlReady($marketplugin['short_description']) ?>
        </p>
    </a>
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