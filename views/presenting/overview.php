
<? if (isset($new_plugins) && count($new_plugins)) : ?>
    <h2><?= _("Neue Plugins seit Ihrem letzten Besuch") ?></h2>
    <div class="plugins_shortview new">
        <? foreach ($new_plugins as $marketplugin) : ?>
            <?= $this->render_partial("presenting/_plugin_short.php", compact("marketplugin", "plugin")) ?>
        <? endforeach ?>
    </div>
<? endif ?>

<div style="text-align: center; margin-bottom: 30px;">
    <div>
        <form action="<?= PluginEngine::getLink($plugin, array(), "presenting/all") ?>" method="get">
            <input
                type="text"
                name="search"
                placeholder="<?= _("Suche") ?>"
                value="<?= htmlReady(Request::get("search")) ?>"
                style="padding: 4px; width: 300px; border: 1px solid #7e92b0; line-height: 24px; vertical-align: text-bottom;"><button
                    style="background-color: #7e92b0; height: 34px; border: 1px solid #7e92b0; vertical-align: text-bottom;"><?= Assets::img("icons/20/white/search", array('class' => "text-bottom")) ?></button>
        </form>
    </div>
</div>

<div class="plugins_shortview">
    <? foreach ($plugins as $marketplugin) : ?>
        <?= $this->render_partial("presenting/_plugin_short.php", compact("marketplugin", "plugin")) ?>
    <? endforeach ?>
</div>

<? if (count($tags)) : ?>
    <h2><?= _("Beliebte Tags") ?></h2>
    <table style="text-align: center; margin-left: auto; margin-right: auto;" id="tagcloud">
        <tbody>
            <? $max = $tags[0]['number'] ?>
            <tr>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[22], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[15], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[6], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[14], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[21], 'max' => $max)) ?></td>
            </tr>
            <tr>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[16], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[7], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[1], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[5], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[13], 'max' => $max)) ?></td>
            </tr>
            <tr>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[8], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[2], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[0], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[4], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[12], 'max' => $max)) ?></td>
            </tr>
            <tr>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[17], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[9], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[3], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[11], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[20], 'max' => $max)) ?></td>
            </tr>
            <tr>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[23], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[18], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[10], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[19], 'max' => $max)) ?></td>
                <td><?= $this->render_partial("presenting/_cloud_tag.php", array('tag' => $tags[24], 'max' => $max)) ?></td>
            </tr>
        </tbody>
    </table>
<? endif ?>


<h2><?= _("Noch mehr Plugins?") ?></h2>
<div style="text-align: center;">
    <?= \Studip\LinkButton::create(_("Alle Plugins anzeigen ..."), PluginEngine::getLink($plugin, array(), "presenting/all"))?>
</div>