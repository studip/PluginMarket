<?php

require_once __DIR__."/../vendor/Parsedown.php";

class MarketRelease extends SimpleORMap {

    static public function getReleaseDataPath() {
        return $GLOBALS['STUDIP_BASE_PATH'] . "/data/pluginmarket_releases";
    }

    static public function findByPlugin_id($plugin_id) {
        return self::findBySQL("plugin_id = ? ORDER BY version DESC", array($plugin_id));
    }

    protected static function configure($config = array())
    {
        $config['db_table'] = 'pluginmarket_releases';
        $config['belongs_to']['plugin'] = array(
            'class_name' => 'MarketPlugin',
            'foreign_key' => 'plugin_id',
        );
        $config['has_many']['followers'] = array(
            'class_name' => 'MarketReleaseFollower',
            'on_delete' => 'delete',
            'on_store' => 'store',
        );
        $config['additional_fields']['last_upload_time']['get'] = 'getFileMTime';
        parent::configure($config);
    }

    public function delete() {
        parent::delete();
        @unlink($this->getFilePath());
    }

    public function installFile() {
        $hash = md5(uniqid());
        $tmp_folder = $GLOBALS['TMP_PATH']."/temp_plugin_".$hash;
        mkdir($tmp_folder);
        $file = $GLOBALS['TMP_PATH']."/temp_plugin_".$hash.".zip";
        if ($this['repository_download_url']) {
            file_put_contents($file, file_get_contents($this['repository_download_url']));
        } elseif ($_FILES['release_file']['tmp_name']) {
            move_uploaded_file($_FILES['release_file']['tmp_name'], $file);
        } else {
            return false;
        }
        Studip\ZipArchive::extractToPath($file, $tmp_folder);
        $objects = scandir($tmp_folder);
        if (count($objects) === 3) {
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $plugin_dir = $tmp_folder."/".$object;
                }
            }
        } else {
            $plugin_dir = $tmp_folder;
        }
        try {
            $this->installFromDirectory($plugin_dir, $file);
        } catch (PluginInstallationException $e) {
            rmdirr($tmp_folder);
            unlink($file);
            throw $e;
        }

        rmdirr($tmp_folder);
        unlink($file);
        $this['chdate'] = time();

        NotificationCenter::postNotification("PluginReleaseDidUpdateCode", $this);
    }

    protected function getFilePath() {
        if (!file_exists(self::getReleaseDataPath())) {
            mkdir(self::getReleaseDataPath());
        }
        if (!$this->getId()) {
            $this->setId($this->getNewId());
        }
        return self::getReleaseDataPath()."/".$this->getId();
    }

    public function outputZip()
    {
        header('Content-Type: ' . $this->getMimeType());
        header('Content-Disposition: attachment; filename="' . $this->getFilename() . '"');

        @readfile($this->getFilePath());
    }

    public function getMimeType()
    {
        return 'application/zip';
    }

    public function getFilename()
    {
        return $this->plugin['pluginname'] . '.zip';
    }

    public function getContentLength()
    {
        return @filesize($this->getFilePath());
    }

    public function getFileMtime()
    {
        return @filemtime($this->getFilePath());
    }

    public function getContent()
    {
        return @file_get_contents($this->getFilePath());
    }

    public function getChecksum() {
        return @md5_file($this->getFilePath());
    }

    public function getSecurityHash() {
        return md5($this->getId()."-".$this['mkdate']);
    }

    protected function installFromDirectory($dir, $originalfile = null) {
        $manifest = PluginManager::getInstance()->getPluginManifest($dir);
        if ($manifest['pluginname']) {
            if ($this->plugin->isNew()) {
                $this->plugin['pluginname'] = $manifest['pluginname'];
                $this->plugin['name'] = @$manifest['displayname'] ?: $manifest['pluginname'];
                $this->plugin['short_description'] = @$manifest['description'] ?: '';
                $this->plugin['description'] = @$manifest['descriptionlong'] ?: '';
                if (!$this->plugin['description']) {
                    $this['repository_overwrites_descriptionfrom'] = 1;
                }
                if (MarketPlugin::findOneByPluginname($this->plugin->pluginname)) {
                    throw new PluginInstallationException(_("Ein Plugin mit diesem Namen ist schon im Marktplatz vorhanden!"));
                }
                $this->plugin->store();
            } else {
                if ($this->plugin['pluginname'] != $manifest['pluginname']) {
                    throw new PluginInstallationException(sprintf(_("Release hat falschen Pluginnamen, erwartet:%s gefunden:%s"), $this->plugin['pluginname'], $manifest['pluginname']));
                }
            }
        } else {
            throw new PluginInstallationException(_("Im Manifest fehlt der Pluginname"));
        }
        $this['studip_min_version'] = $manifest['studipMinVersion'];
        $this['studip_max_version'] = $manifest['studipMaxVersion'];
        if (!$this['studip_max_version']) {
            $versions = PluginMarket::getStudipReleases();
            preg_match("/^(\d+\.\d+)/", StudipVersion::getStudipVersion(false), $matches);
            $manifest['studipMaxVersion']
                = $this['studip_max_version']
                = $matches[1].".99";
            if (!$this['studip_max_version']) {
                PageLayout::postMessage(MessageBox::info(sprintf(_("Die studipMaxVersion wurde auf %s gesetzt, da keine andere angegeben wurde."), $manifest['studipMaxVersion'])));
            }
        }
        if (version_compare($this['studip_min_version'], $this['studip_max_version'], ">")) {
            $this['studip_max_version'] = $this['studip_min_version'];
        }
        $this['version'] = $manifest['version'];
        if ($this['repository_overwrites_descriptionfrom']) {
            $readme = "";
            $scanner = scandir($dir);
            foreach ($scanner as $file) {
                if (strtolower($file) === "readme.md" || strtolower($file) === "readme.markdown") {
                    $readme = file_get_contents($dir."/".$file);
                }
            }
            if ($readme) {
                $html = Parsedown::instance()->text($readme);
                $this->plugin['description'] = "<div>".$html."</div>";
                $this->plugin->store();
            }
        }
        $this->store();
        file_put_contents($dir."/plugin.manifest", $this->createManifest($manifest));
        $hash = md5(uniqid());
        $plugin_raw = $GLOBALS['TMP_PATH']."/plugin_$hash.zip";
        $zip = Studip\ZipArchive::create($plugin_raw);
        $zip->addFromPath($dir);
        $zip->close();
        if ($manifest['studipMaxVersion'] !== $this['studip_max_version']) {
            copy($plugin_raw, $this->getFilePath());
        } else {
            copy($originalfile, $this->getFilePath());
        }
        unlink($plugin_raw);
        return true;
    }

    /**
     * Checks if the release works with the given Stud.IP version
     *
     * @param String $version Version to check for
     */
    public function checkVersion($version) {
        return ( !$this->studip_min_version || version_compare($version, $this->studip_min_version) >= 0 )
                && ( !$this->studip_max_version || version_compare($version, $this->studip_max_version) <= 0 );
    }

    protected function createManifest($manifest) {
        $arr = array();
        foreach ($manifest as $index => $value) {
            if (is_array($value)) {
                if ($index == 'screenshots') {
                    $arr[] = "screenshots=".$value['path'];
                    foreach ($value['pictures'] as $one) {
                        $arr[] = "screenshots." . $one['source'] . "=" . $one['title'];
                    }
                }
                if ($index == 'additionalclasses') {
                    foreach ($value as $one) {
                        $arr[] = 'pluginclassname' . "=" . $one;
                    }
                }
                if ($index == 'additionalscreenshots') {
                    foreach ($value as $one) {
                        $arr[] = 'screenshot' . "=" . $one;
                    }
                }
            } else {
               $arr[] = $index."=".$value;
            }
        }
        return implode("\n", $arr);
    }

    public function getPluginName()
    {
        $zip = new ZipArchive();
        if ($zip->open($this->getFilePath())) {
            $manifest = $zip->getFromIndex($zip->locateName('plugin.manifest', ZipArchive::FL_NOCASE|ZipArchive::FL_NODIR));
            foreach (array_map('trim',explode("\n", $manifest)) as $line) {
                list($key, $value) = explode('=', $line);
                if ($key === 'pluginname') {
                    return trim($value);
                }
            }
        }
    }

}