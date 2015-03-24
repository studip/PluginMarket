<?php
require_once 'market_controller.php';

class UpdateController extends MarketController
{
    public function release_action($release_id)
    {
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