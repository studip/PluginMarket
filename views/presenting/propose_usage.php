<form class="default" method="post" action="<?= $controller->url_for('presenting/propose_usage/' . $plugin->id) ?>">
    <fieldset>
        <?= CSRFProtection::tokenTag(); ?>
        <legend><?= sprintf(_('Pluginnutzung mitteilen für %s'), htmlReady($plugin->name)) ?></legend>
        <h3><?= _('Eingetragene Standorte') ?></h3>
        <p class="plugin-usages"><?= join(', ', $plugin->uses->pluck('name')) ?></p>
        <input type="hidden" name="plugin_id" value="<?= $plugin->id ?>">
        <label>
            <?= _('Neuer Standort') ?>
            <input id="used_at" type="text" name="used_at" placeholder="<?= _('Hogwarts') ?>" value="<?= Request::get('usage') ?>">
        </label>
        <?= Studip\Button::create(_('Eintragen'), 'propose'); ?>
        <? if ($most_used): ?>
            <h3><?= _('Vorschläge') ?></h3>
            <p class="usage-proposes">
                <? foreach ($most_used as $used): ?>
                    <a class="usage-proposal" href="<?= $controller->url_for('presenting/propose_usage/' . $plugin->id, array('usage' => $used)) ?>">
                        <?= htmlReady($used) ?>
                    </a>
                <? endforeach; ?>
            </p>
        <? endif; ?>
    </fieldset>
</form>
