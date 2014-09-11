<style>
    .plugins_shortview {
        list-style-type: none;
        text-align: center;
    }
    .plugins_shortview > article {
        display: inline-block;
        margin: 7px;
        padding: 8px;
        background-color: #3c454e;
        box-shadow: 0px 0px 10px rgba(0,0,0,0.3);
        color: white;
        width: 250px;
        height: 250px;
        max-height: 250px;
        max-width: 250px;
        overflow: hidden;
    }
    .plugins_shortview > article > h1 {
        margin: 0px;
        border: none;
        color: white;
    }
    .plugins_shortview > article > h1 > a, .plugins_shortview > article > h1 > a:hover {
        color: white;
    }
    .plugins_shortview > article > .image {
        margin: 4px;
        margin-left: -4px;
        margin-right: -4px;
        width: calc(100% + 8px);
        height: 150px;
        background-position: center center;
        background-size: auto 100%;
        background-repeat: no-repeat;
        background-color: white;
        box-shadow: inset 0px 0px 3px rgba(0,0,0,0.3);
    }
    .plugins_shortview > article > .shortdescription {
        font-size: 0.9em;
        color: #879199;
    }
</style>

<div class="plugins_shortview">
    <article>
        <h1><a href="">BlubberMail</a></h1>
        <div class="image" style="background-image: url(http://plugins.studip.de/content/screenshots/a1d85f695cfe506241d398d272ae43f0_thumb);"></div>
        <p class="shortdescription">
            Blubbern über Email. Man bekommt Mails, wenn die Diskussion weiter geht, man kann per Mail darauf antworten und man kann Blubberstreams per Mail abonnieren.
        </p>
    </article>
    <article>
        <h1>ArchivierungsPlugin</h1>
        <div class="image" style="background-image: url(http://plugins.studip.de/content/screenshots/5c6b989378b09deb4b5a79b32a0aa0bc);"></div>
        <p class="shortdescription">
            Zentrale Archivierung/Löschung von Veranstaltungen durch Admins. Die Veranstaltungen können nach verschiedenen Kriterien eingeschränkt werden.
        </p>
    </article>
    <? foreach ($plugins as $plugin) : ?>
        <article>
            <h1><a href=""><?= htmlReady($plugin['name']) ?></a></h1>
            <div class="image" style="background-image: url(http://plugins.studip.de/content/screenshots/a1d85f695cfe506241d398d272ae43f0_thumb);"></div>
            <p class="shortdescription">
                <?= htmlReady($plugin['short_description']) ?>
            </p>
        </article>
    <? endforeach ?>
</div>