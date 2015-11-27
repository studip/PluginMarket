<h2><?= _("Erstellen von Sidebar-Grafiken") ?></h2>

<table>
    <tbody>
    <tr>
        <td><label for="color"><?=_("Farbe")  ?></label></td>
        <td><input type="color" id="color" value="#24437c" onChange="STUDIP.SidebarGraphicsGenerator.drawImage();"></td>
    </tr>
    <tr>
        <td><label for="localicon"><?= _("Bilddatei (SVG, quadratisch, weiß)") ?></label></td>
        <td><input type="file" id="localicon" onChange="STUDIP.SidebarGraphicsGenerator.setFile(this); return false;"></td>
    </tr>
    <tr style="display: none;">
        <td><?= _("Bild") ?></td>
        <td><img id="icon" style="width: 200px; height: 200px; background-color: #36598f; border:thin solid #36598f"></td>
    </tr>
    </tbody>
</table>


<canvas width="320" height="320" style="display: none;" id="pre_icon"></canvas>

<input type="hidden" id="filename" value="">

<div style="border: rgba(54,89,142,0.5) solid 10px; display: inline-block; border-radius: 7px;">
    <canvas width="520px" height="200px" style="border: white solid 3px;" id="sidebar_image"></canvas>
</div>

<div id="save_instructions" style="display: none; padding: 20px;">
    <a href="#" id="downloader" onClick="this.href=window.document.getElementById('sidebar_image').toDataURL('image/png');" download="testXXX.png">
        <?= Assets::img("icons/16/blue/download") ?>
        <?= _("Speichern unter ...") ?>
    </a>
</div>
