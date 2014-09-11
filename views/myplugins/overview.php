
<? foreach ($plugins as $plugin) : ?>
<? endforeach ?>

<?
$sidebar = Sidebar::Get();
$sidebar->setImage(Assets::image_path("sidebar/plugin-sidebar.png"));
$actions = new ActionsWidget();
$actions->addLink(_("Neues Plugin eintragen"), PluginEngine::getURL($plugin, array(), "myplugins/add"));
$sidebar->addWidget($actions);

