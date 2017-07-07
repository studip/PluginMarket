<? if ($show_all) : ?>
    <h2><?= count($plugins) ?>&nbsp;<?= _('Plugins') ?></h2>
    <div class="plugins_shortview">
        <? foreach ($plugins as $marketplugin) : ?>
            <?= $this->render_partial("presenting/_plugin_short.php", compact("marketplugin", "plugin")) ?>
        <? endforeach ?>
    </div>
<? else : ?>
    <? if (isset($new_plugins) && count($new_plugins)) : ?>
        <h2><?= _("Neue Plugins seit Ihrem letzten Besuch") ?></h2>
        <div class="plugins_shortview new">
            <? foreach ($new_plugins as $marketplugin) : ?>
                <?= $this->render_partial("presenting/_plugin_short.php", compact("marketplugin", "plugin")) ?>
            <? endforeach ?>
        </div>
    <? endif ?>

    <h2><?= _("Neueste Plugins") ?></h2>
    <div class="plugins_shortview">
        <? foreach ($latest_plugins as $marketplugin) : ?>
            <?= $this->render_partial("presenting/_plugin_short.php", compact("marketplugin", "plugin")) ?>
        <? endforeach ?>
    </div>

    <h2><?= _("Zufällige Plugins") ?></h2>
    <div class="plugins_shortview">
        <? foreach ($plugins as $marketplugin) : ?>
            <?= $this->render_partial("presenting/_plugin_short.php", compact("marketplugin", "plugin")) ?>
        <? endforeach ?>
    </div>

    <h2><?= _("Bestbewertete Plugins") ?></h2>
    <div class="plugins_shortview">
        <? foreach ($best_plugins as $marketplugin) : ?>
            <?= $this->render_partial("presenting/_plugin_short.php", compact("marketplugin", "plugin")) ?>
        <? endforeach ?>
    </div>
<? endif ?>