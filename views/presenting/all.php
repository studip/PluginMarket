<div class="plugins_shortview">
    <? foreach ($plugins as $marketplugin) : ?>
        <?= $this->render_partial("presenting/_plugin_short.php", compact("marketplugin", "plugin")) ?>
    <? endforeach ?>
</div>