
<? if (isset($new_plugins) && count($new_plugins)) : ?>
    <h2><?= _("Neue Plugins seit Ihrem letzten Besuch") ?></h2>
    <div class="plugins_shortview">
        <? foreach ($new_plugins as $marketplugin) : ?>
            <?= $this->render_partial("presenting/_plugin_short.php", compact("marketplugin", "plugin")) ?>
        <? endforeach ?>
    </div>
<? endif ?>

<div style="text-align: center;">
    <div>
        <form class="studip_form">
            <input type="text" name="search" placeholder="<?= _("Suche") ?>" value="<?= htmlReady(Request::get("search")) ?>">
        </form>
    </div>
</div>

<div class="plugins_shortview">
    <? foreach ($plugins as $marketplugin) : ?>
        <?= $this->render_partial("presenting/_plugin_short.php", compact("marketplugin", "plugin")) ?>
    <? endforeach ?>
</div>