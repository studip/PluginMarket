<?php
require_once 'app/controllers/plugin_controller.php';

class MarketController extends PluginController
{
    public function absolute_url_for($to)
    {
        $old_base = URLHelper::setBaseURL($GLOBALS['ABSOLUTE_URI_STUDIP']);

        $args = func_get_args();
        $url  = call_user_func_array(array($this, 'url_for'), $args);

        URLHelper::setBaseURL($old_base);

        return $url;
    }
}
