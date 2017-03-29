<?php
require_once 'market_controller.php';

class UpdateController extends MarketController
{
    public function release_action($release_id)
    {
        if (!Request::isPost()) {
            throw new Exception("Only via POST-request.");
        }
        $release = new MarketRelease($release_id);
        if ($release->isNew()) {
            throw new Exception("Unknown release.");
        }
        if (!$release['repository_download_url']) {
            //might happen more often than we think, so we better be polite and die.
            echo "Nothing to do.";
            die();
        }
        if ($release->getSecurityHash() === Request::get("s")) {
            if ($release['repository_secret']
                    && !$this->verify_secret($release['repository_secret'])) {
                $this->render_text("Incorrect payload.");
                return;
            } else {
                $release->installFile();
                $this->render_text("OK");
            }
        } else {
            $this->render_text("Insecure request.");
        }
    }
    
    public function usage_action() {
        $this->plugins = MarketPlugin::findManyByName(Request::getArray('plugins'));
        $this->mostlikely = MarketPluginUsage::findOneBySQL('user_id = ? GROUP BY name ORDER BY count(*) DESC', array(User::findCurrent()->id))->name;
    }
    
    public function save_usage_action() {
        // delete old usage
        MarketPluginUsage::deleteBySQL('user_id = ? AND name = ?', array(User::findCurrent()->id, Request::get('tag')));
        
        // create new usages
        foreach (Request::getArray('plugins') as $pluginid) {
            MarketPluginUsage::create(array(
                'plugin_id' => $pluginid,
                'user_id' => User::findCurrent()->id,
                'name' => Request::get('tag')
            ));
            $this->done++;
        }
    }

    protected function verify_secret($secret)
    {
        if (!isset($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
            return false;
        }
        $signatureHeader = $_SERVER['HTTP_X_HUB_SIGNATURE'];
        $payload = file_get_contents('php://input');
        list($algorithm, $hash) = explode('=', $signatureHeader, 2);

        $calculatedHash = hash_hmac($algorithm, $payload, $secret);
        return $calculatedHash === $hash;
    }
}