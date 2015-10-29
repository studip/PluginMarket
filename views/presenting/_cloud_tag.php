<? if ($tag) : ?>
    <div style="font-size: <?= (double) ($tag['number'] / $max) * 6 ?>em;">
        <a href="<?= URLHelper::getLink("plugins.php/pluginmarket/presenting/all", array('tag' => $tag['tag'])) ?>">
            <?= htmlReady($tag['tag']) ?>
        </a>
    </div>
<? endif ?>