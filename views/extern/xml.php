<?= '<?xml version="1.0"?>' ?>
<plugins>
<? foreach ($plugins as $marketplugin) : ?>
    <plugin
        name="<?= htmlReady(studip_utf8encode($marketplugin['name'])) ?>"
        homepage="<?= htmlReady(studip_utf8encode($marketplugin['url'])) ?>"
        short_description="<?= htmlReady(studip_utf8encode($marketplugin['short_description'])) ?>"
        description="<?= htmlReady(studip_utf8encode($marketplugin['description'])) ?>"
        image="<?= htmlReady(studip_utf8encode($GLOBALS['ABSOLUTE_URI_STUDIP'].$marketplugin->getLogoURL())) ?>">
        <? foreach ($marketplugin->releases as $release) : ?>
        <release
            version="<?= htmlReady(studip_utf8encode($release['version'])) ?>"
            studipMinVersion="<?= htmlReady(studip_utf8encode($release['studip_min_version'])) ?>"
            studipMaxVersion="<?= htmlReady(studip_utf8encode($release['studip_min_version'])) ?>"
            url="<?= htmlReady(studip_utf8encode($GLOBALS['ABSOLUTE_URI_STUDIP'].$controller->url_for('presenting/download/' . $release->getId()))) ?>"
            />
        <? endforeach ?>
    </plugin>
<? endforeach ?>
</plugins>