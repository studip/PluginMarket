<? if (!Request::get("tag")) : ?>
    <div style="text-align: center;">
        <div>
            <form action="<?= PluginEngine::getLink($plugin, array(), "presenting/all") ?>" method="get">
                <input
                    type="text"
                    name="search"
                    placeholder="<?= _("Suche") ?>"
                    value="<?= htmlReady(Request::get("search")) ?>"
                    style="padding: 4px; width: 300px; border: 1px solid #7e92b0; line-height: 25px;"><button
                    style="background-color: #7e92b0; height: 34px; border: 1px solid #7e92b0;"><?= Assets::img("icons/20/white/search", array('class' => "text-bottom")) ?></button>
            </form>
        </div>
    </div>
<? endif ?>

<div class="plugins_shortview">
    <? foreach ($plugins as $marketplugin) : ?>
        <?= $this->render_partial("presenting/_plugin_short.php", compact("marketplugin", "plugin")) ?>
    <? endforeach ?>
</div>