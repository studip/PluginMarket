<form class="studip_form" method="post" action="<?= PluginEngine::getLink($plugin, array(), 'update/save_usage/') ?>">
    <fieldset>
        <legend>
            <?= _('Pluginnutzung') ?>
        </legend>

        <fieldset>
            <legend>
                <?= _('Plugins') ?>
            </legend>
            <? foreach ($plugins as $plugin): ?>
                <label>
                    <input type="checkbox" name="plugins[]" value="<?= $plugin->id ?>" checked>
                    <?= htmlReady($plugin->name); ?>
                </label>
            <? endforeach; ?>
        </fieldset>
        
        <label>
            <?= _('In Benutzung bei') ?>
            <input type="text" name="tag" value="<?= htmlReady($mostlikely) ?>">
        </label>
        
        <?= Studip\Button::create(_('Eintragen')) ?>
    </fieldset>
</form>

