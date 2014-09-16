<?
//OpenGraph attributes
PageLayout::addHeadElement("meta", array('property' => "og:site_name", 'content' => _("Stud.IP Plugin-Marktplatz")));
PageLayout::addHeadElement("meta", array('property' => "og:type", 'content' => "article"));
PageLayout::addHeadElement("meta", array('property' => "og:title", 'content' => $marketplugin['name']));
PageLayout::addHeadElement("meta", array('property' => "og:description", 'content' => $marketplugin['short_description']));
$icon = $marketplugin->images->first();
if ($icon) {
    PageLayout::addHeadElement("meta", array('property' => "og:image", 'content' => $icon->getURL()));
}
?>

<? if (!$marketplugin['publiclyvisible']) : ?>
    <?= PageLayout::postMessage(MessageBox::info(_("Dieses Plugin ist nicht öffentlich."))) ?>
<? endif ?>

<h1><?= htmlReady($marketplugin['name']) ?></h1>
<div>
    <?= formatReady($marketplugin['description']) ?>
</div>

<? if (count($marketplugin->images) > 0 || $marketplugin->isWritable()) : ?>
<h2><?= _("Galerie") ?></h2>

<ol id="pluginmarket_galery_view" class="pluginmarket_galery">
    <? foreach ($marketplugin->images as $image) : ?>
    <div class="image">
        <img src="<?= htmlReady($image->getURL()) ?>">
    </div>
    <? endforeach ?>
    <? if ($marketplugin->isWritable()) : ?>
    <div><a href="<?= PluginEngine::getLink($plugin, array(), "myplugins/edit_images/".$marketplugin->getId()) ?>" data-dialog title="<?= _("Galerie bearbeiten / neue Bilder hinzufügen") ?>"><?= Assets::img("icons/20/blue/add") ?></a></div>
    <? endif ?>
</ol>
<? endif ?>

<h2><?= _("Zum Autor") ?></h2>
<ul class="clean">
    <li>
        <? $author = User::find($marketplugin['user_id']) ?>
        <div>
            <a href="<?= URLHelper::getLink("dispatch.php/profile", array('username' => $author['username'])) ?>" style="text-align: center; display: inline-block; vertical-align: top;">
                <?= Avatar::getAvatar($marketplugin['user_id'])->getImageTag(Avatar::MEDIUM, array('style' => "display: block;")) ?>
                <?= htmlReady($author->getFullName()) ?>
            </a>
        </div>
    </li>
</ul>

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
$paypal_datafield = $author['datafields']->findBy("name", "Paypal-Account (Email)")->val("content");
?>

<? if ($flattr_username || $bitcoin_wallet || $paypal_datafield) : ?>
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

    <? if ($paypal_datafield) : ?>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="display: inline-block;">
            <div><strong><img src="http://pics.ebaystatic.com/aw/pics/logos/logoPayPal_51x14.gif"></strong></div>
            <input type="hidden" name="cmd" value="_donations">
            <input type="hidden" name="business" value="<?= htmlReady($paypal_datafield) ?>">
            <input type="hidden" name="lc" value="DE">
            <input type="hidden" name="no_note" value="0">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
            <input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>

    <? endif ?>
</div>




<? if ($marketplugin->isWritable()) : ?>
<div style="text-align: center">
    <?= \Studip\LinkButton::create(_("bearbeiten"), PluginEngine::getURL($plugin, array(), "myplugins/edit/".$marketplugin->getId()), array('data-dialog' => 1)) ?>
    <?= \Studip\LinkButton::create(_("Release hinzufügen"), PluginEngine::getURL($plugin, array(), "myplugins/add_release/".$marketplugin->getId()), array('data-dialog' => 1)) ?>
</div>
<? endif ?>