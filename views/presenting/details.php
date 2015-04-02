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
    <div>
        <a href="<?= PluginEngine::getLink($plugin, array(), "myplugins/edit_images/". $marketplugin->getId())  ?>" data-dialog title="<?= _("Galerie bearbeiten / neue Bilder hinzufügen") ?>">
            <?= Assets::img("icons/20/blue/add") ?>
        </a>
    </div>
    <? endif ?>
</ol>
<? endif ?>

<h2><?= _("In Benutzung bei") ?></h2>
<ul class="plugin-usages">
    <? foreach ($marketplugin['uses'] as $use): ?>
        <li>
            <a href="<?= PluginEngine::getLink($plugin, array('search' => htmlReady($use->name)), "presenting/all") ?>">
                <?= htmlReady($use->name) ?>
            </a>
            <? if ($use->plugin->isWritable(User::findCurrent()->id)): ?>
            (<?= ObjectdisplayHelper::link($use->user) ?>)
            <? endif; ?>
            <? if ($use->isEditable()): ?>
                <a href="<?= PluginEngine::getLink($plugin, array(), "presenting/delete_usage/" . $use->id) ?>">
                    <?= Assets::img('icons/blue/trash.svg'); ?>
                </a>
            <? endif; ?>
        </li>
    <? endforeach; ?>
</ul>

<? if ($marketplugin['url']) : ?>
    <h2><?= _("Projekthomepage") ?></h2>
    <div><?= formatLinks($marketplugin['url']) ?></div>
<? endif ?>

<? $tags = $marketplugin->getTags() ?>
<? if (count($tags)) : ?>
    <h2><?= _("Schlagworte") ?></h2>
    <div>
        <? foreach ($tags as $key => $tag) : ?>
            <?= $key > 0 ? "," : "" ?>
            <a href="<?= URLHelper::getLink("plugins.php/pluginmarket/presenting/all", array('tag' => $tag)) ?>">
                <?= Assets::img("icons/16/blue/tag", array('class' => "text-bottom")) ?>
                <?= htmlReady(ucwords($tag)) ?>
            </a>
        <? endforeach ?>
    </div>
<? endif ?>

<h2><?= _("Zum Autor") ?></h2>
<ul class="clean">
    <li>
        <? $author = User::find($marketplugin['user_id']) ?>
        <div>
            <? if ($author) : ?>
            <a href="<?= URLHelper::getLink("dispatch.php/profile", array('username' => $author['username'])) ?>" style="text-align: center; display: inline-block; vertical-align: top;">
                <?= Avatar::getAvatar($marketplugin['user_id'])->getImageTag(Avatar::MEDIUM, array('style' => "display: block;")) ?>
                <?= htmlReady($author->getFullName()) ?>
            </a>
            <? else : ?>
                <?= _("unbekannt") ?>
            <? endif ?>
        </div>
    </li>
    <? foreach ($marketplugin->more_users as $user) : ?>
        <li>
            <div>
                <a href="<?= URLHelper::getLink("dispatch.php/profile", array('username' => $user['username'])) ?>" style="text-align: center; display: inline-block; vertical-align: top;">
                    <?= Avatar::getAvatar($user->getId())->getImageTag(Avatar::MEDIUM, array('style' => "display: block;")) ?>
                    <?= htmlReady($user->getFullName()) ?>
                </a>
            </div>
        </li>
    <? endforeach ?>
</ul>

<h2><?= _("Releases") ?></h2>
<table class="default">
    <thead>
        <tr>
            <th><?= _("Version") ?></th>
            <th><?= _("Min. Stud.IP Version") ?></th>
            <th><?= _("Max. Stud.IP Version") ?></th>
            <th><?= _("Hochgeladen am") ?></th>
            <th><?= _("MD5-Prüfsumme") ?></th>
            <th><?= _("Downloads") ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <? foreach ($marketplugin->releases as $release) : ?>
        <tr>
            <td>
                <a href="<?= PluginEngine::getLink($plugin, array(), "presenting/download/". $release->getId()) ?>" title="<?= _("Dieses Release runterladen") ?>">
                    <?= Assets::img("icons/20/blue/download", array('class' => "text-bottom")) ?>
                    <?= htmlReady($release['version']) ?>
                </a>
            </td>
            <td><?= $release['studip_min_version'] ? htmlReady($release['studip_min_version']) : " - " ?></td>
            <td><?= $release['studip_max_version'] ? htmlReady($release['studip_max_version']) : " - " ?></td>
            <td><?= strftime("%x", $release['mkdate']) ?></td>
            <td><?= htmlReady($release->getChecksum()) ?></td>
            <td><?= htmlReady($release['downloads']) ?></td>
            <td class="actions">
                <? if ($marketplugin->isWritable()) : ?>
                    <a href="<?= PluginEngine::getLink($plugin, array(), "myplugins/edit_release/" . $release->getId()) ?>" data-dialog>
                        <?= Assets::img("icons/20/blue/edit", array('class' => "text-bottom")) ?>
                    </a>
                    <a href="<?= PluginEngine::getLink($plugin, array(), "myplugins/delete_release/" . $release->getId())  ?>">
                        <?= Assets::img("icons/20/blue/trash", array('class' => "text-bottom", 'onclick' => "return window.confirm('"._("Pluginrelease wirklich unwiderrufbar löschen?")."');")) ?>
                    </a>
                <? endif ?>
                <? if ($GLOBALS['perm']->have_perm("autor")) : ?>
                    <a href="<?= PluginEngine::getLink($plugin, array(), "presenting/follow_release/" . $release->getId()) ?>" title="<?= _("Für automatische Updates registrieren.") ?>" data-dialog>
                        <?= Assets::img("icons/20/blue/rss", array('class' => "text-bottom")) ?>
                    </a>
                <? endif ?>
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
    <? if ($marketplugin->isWritable()) : ?>
        <tfoot>
        <tr>
            <td colspan="7">
                <a href="<?= PluginEngine::getLink($plugin, array(), "myplugins/add_release/" . $marketplugin->getId()) ?>" data-dialog>
                    <?= Assets::img("icons/20/blue/add") ?>
                </a>
            </td>
        </tr>
        </tfoot>
    <? endif ?>
</table>





<? if ($marketplugin['donationsaccepted']) : ?>
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
                <input type="hidden" name="currency_code" value="EUR">
                <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
                <input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
            </form>
        <? endif ?>
    </div>
<? endif ?>

<h2><?= _("Bewertungen") ?></h2>
<div>
    <div style="text-align: center;">
        <? if ($marketplugin['rating'] === null) : ?>
            <? if (!$marketplugin->isWritable()) : ?>
                <a style="opacity: 0.3;" title="<?= $GLOBALS['perm']->have_perm("autor") ? _("Geben Sie die erste Bewertung ab.") : _("Noch keine bewertung abgegeben.") ?>" <?= ($GLOBALS['perm']->have_perm("autor") && !$marketplugin->isWritable()) ? 'href="' . $controller->url_for('presenting/review/' . $marketplugin->getId()) . '" data-dialog' : "" ?>>
            <? endif ?>
                <?= Assets::img($plugin->getPluginURL()."/assets/star.svg", array('width' => "50px")) ?>
                <?= Assets::img($plugin->getPluginURL()."/assets/star.svg", array('width' => "50px")) ?>
                <?= Assets::img($plugin->getPluginURL()."/assets/star.svg", array('width' => "50px")) ?>
                <?= Assets::img($plugin->getPluginURL()."/assets/star.svg", array('width' => "50px")) ?>
                <?= Assets::img($plugin->getPluginURL()."/assets/star.svg", array('width' => "50px")) ?>
            <? if (!$marketplugin->isWritable()) : ?>
                </a>
            <? endif ?>
        <? else : ?>
            <? if (!$marketplugin->isWritable()) : ?>
                <a <?= ($GLOBALS['perm']->have_perm("autor") && !$marketplugin->isWritable()) ? 'href="' . $controller->url_for('presenting/review/' . $marketplugin->getId()) . '" data-dialog' : "" ?> title="<?= sprintf(_("%s von 5 Sternen"), round($marketplugin['rating'] / 2, 1)) ?>">
            <? endif ?>
                <? $marketplugin['rating'] = round($marketplugin['rating'], 1) / 2 ?>
                <? $v = $marketplugin['rating'] >= 1 ? 3 : ($marketplugin['rating'] >= 0.5 ? 2 : "") ?>
                <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "50px")) ?>
                <? $v = $marketplugin['rating'] >= 2 ? 3 : ($marketplugin['rating'] >= 1.5 ? 2 : "") ?>
                <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "50px")) ?>
                <? $v = $marketplugin['rating'] >= 3 ? 3 : ($marketplugin['rating'] >= 2.5 ? 2 : "") ?>
                <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "50px")) ?>
                <? $v = $marketplugin['rating'] >= 4 ? 3 : ($marketplugin['rating'] >= 3.5 ? 2 : "") ?>
                <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "50px")) ?>
                <? $v = $marketplugin['rating'] > 4.5 ? 3 : ($marketplugin['rating'] >= 4.5 ? 2 : "") ?>
                <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "50px")) ?>
            <? if (!$marketplugin->isWritable()) : ?>
                </a>
            <? endif ?>
        <? endif ?>
    </div>

    <ol style="list-style-type: none; padding: none; margin: none;">
    <? foreach ($marketplugin->reviews as $review) : ?>
        <? if ($review['review']) : ?>
        <li style="padding: none; margin: none;">
            <blockquote class="quote">
                <div class="author">
                    <div style="float: right;"><?= date("j.n.Y", $review['chdate']) ?></div>
                    <?= sprintf(_("Rezension von %s"), $GLOBALS['user']->id !== "nobody" ? '<a style="color: white;" href="'.URLHelper::getLink("dispatch.php/profile", array('username' => get_username($review['user_id']))).'">'.Assets::img("icons/16/white/link-intern", array('class' => "text-bottom"))." ".htmlReady(get_fullname($review['user_id'])).'</a>' : htmlReady(get_fullname($review['user_id'])) ) ?>:
                </div>
                <div>
                    <? $v = $review['rating'] >= 1 ? 3 : "" ?>
                    <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "20px")) ?>
                    <? $v = $review['rating'] >= 2 ? 3 : "" ?>
                    <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "20px")) ?>
                    <? $v = $review['rating'] >= 3 ? 3 : "" ?>
                    <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "20px")) ?>
                    <? $v = $review['rating'] >= 4 ? 3 : "" ?>
                    <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "20px")) ?>
                    <? $v = $review['rating'] >= 5 ? 3 : "" ?>
                    <?= Assets::img($plugin->getPluginURL()."/assets/star$v.svg", array('width' => "20px")) ?>
                </div>
                <?= htmlReady($review['review']) ?>
            </blockquote>
        </li>
        <? endif ?>
    <? endforeach ?>
    </ol>

</div>

<div style="text-align: center">
<? if ($marketplugin->isWritable()) : ?>
    <?= \Studip\LinkButton::create(_("bearbeiten"), PluginEngine::getURL($plugin, array(), "myplugins/edit/" . $marketplugin->getId()), array('data-dialog' => 1)) ?>
    <?= \Studip\LinkButton::create(_("Release hinzufügen"), PluginEngine::getURL($plugin, array(), "myplugins/add_release/" . $marketplugin->getId()), array('data-dialog' => 1)) ?>
<? endif ?>
<? if ($marketplugin['user_id'] !== $GLOBALS['user']->id) : ?>
    <?= \Studip\LinkButton::create(_("Plugin abonnieren"), PluginEngine::getURL($plugin, array(), "presenting/register_for_pluginnews/" . $marketplugin->getId()), array('title' => _("Neuigkeiten des Plugins per Nachricht bekommen."), 'data-dialog' => "1")) ?>
<? endif ?>
<? if ($marketplugin->isRootable()) : ?>
    <form action="?" method="post" style="display: inline-block; margin: 0px;">
        <input type="hidden" name="plugin_id" value="<?= htmlReady($marketplugin->getId()) ?>">
        <?= \Studip\Button::create(_("Löschen"), "delete_plugin", array('onclick' => "return window.confirm('"._("Plugin wirklich unwiderrufbar löschen?")."');")) ?>
    </form>
<? endif ?>
</div>
