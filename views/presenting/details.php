<h1><?= htmlReady($marketplugin['name']) ?></h1>
<div>
    <?= formatReady($marketplugin['description']) ?>
</div>

<h2><?= _("Galerie") ?></h2>

<div></div>

<h2><?= _("Releases") ?></h2>
<table class="default">
    <thead>
        <tr>
            <th><?= _("Version") ?></th>
            <th><?= _("Miniale Stud.IP-Versionsnummer") ?></th>
            <th><?= _("Maximale Stud.IP-Versionsnummer") ?></th>
            <th><?= _("MD5-Prüfsumme") ?></th>
            <th><?= _("Downloads") ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <? foreach ($marketplugin->releases as $release) : ?>
        <tr>
            <td>
                <a href="<?= PluginEngine::getLink($plugin, array(), "presenting/download/".$release->getId()) ?>" title="<?= _("Dieses Release runterladen") ?>">
                    <?= Assets::img("icons/20/blue/download", array('class' => "text-bottom")) ?>
                    <?= htmlReady($release['version']) ?>
                </a>
            </td>
            <td><?= $release['studip_min_version'] ? htmlReady($release['studip_min_version']) : " - " ?></td>
            <td><?= $release['studip_max_version'] ? htmlReady($release['studip_max_version']) : " - " ?></td>
            <td><?= htmlReady($release->getChecksum()) ?></td>
            <td><?= htmlReady($release['downloads']) ?></td>
            <td>
                <? if ($marketplugin['user_id'] === $GLOBALS['user']->id) : ?>
                    <a href="<?= PluginEngine::getLink($plugin, array(), "myplugins/edit_release/".$release->getId()) ?>" data-dialog>
                        <?= Assets::img("icons/20/blue/edit", array('class' => "text-bottom")) ?>
                    </a>
                <? endif ?>
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>





<?
//Donations, wenn die Datenfelder "Flattr", "Bitcoin-Wallet" gesetzt sind
$author = User::find($marketplugin['user_id']);
$flattr_username = $author['datafields']->findBy("name", "Flattr")->val("content");
$bitcoin_datafield = $author['datafields']->findBy("name", "Bitcoin-Wallet")->val("content");
?>

<? if ($flattr_username || $bitcoin_wallet) : ?>
    <h2><?= _("Spenden") ?></h2>
    <p class="info">
        <?= _("Der Download ist kostenlos, aber man kann dem Autor mit einer Spende danken und zukünftige Entwicklungen anregen.") ?>
    </p>
<? endif ?>

<div style="text-align: center;">
    <? if ($flattr_username) : ?>
        <script id='fbowlml'>(function(i){var f,s=document.getElementById(i);f=document.createElement('iframe');f.src='//api.flattr.com/button/view/?uid=<?= urlencode(studip_utf8encode($flattr_username)) ?>&url='+encodeURIComponent(document.URL)+'&title=<?= urlencode(studip_utf8encode($marketplugin['name']." "._("für Stud.IP"))) ?>';f.title='Flattr';f.height=62;f.width=55;f.style.borderWidth=0;s.parentNode.insertBefore(f,s);})('fbowlml');</script>
    <? endif ?>

    <? if ($bitcoin_wallet) : ?>
        <script src="http://coinwidget.com/widget/coin.js"></script>
        <script>
            CoinWidgetCom.go({
                wallet_address: "<?= htmlReady($bitcoin_wallet) ?>"
                , currency: "bitcoin"
                , counter: "count"
                , alignment: "bl"
                , qrcode: true
                , auto_show: false
                , lbl_button: "Donate"
                , lbl_address: "My Bitcoin Address:"
                , lbl_count: "donations"
                , lbl_amount: "BTC"
            });
        </script>
    <? endif ?>
</div>




<? if ($marketplugin['user_id'] === $GLOBALS['user']->id) : ?>
<div style="text-align: center">
    <?= \Studip\LinkButton::create(_("bearbeiten"), PluginEngine::getURL($plugin, array(), "myplugins/edit/".$marketplugin->getId()), array('data-dialog' => 1)) ?>
</div>
<? endif ?>