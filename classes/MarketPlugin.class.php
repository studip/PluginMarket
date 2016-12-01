<?php

class MarketPlugin extends SimpleORMap {
    protected static function configure($config = array())
    {
        $config['db_table'] = 'pluginmarket_plugins';
        $config['has_many']['releases'] = array(
            'class_name' => 'MarketRelease',
            'on_delete' => 'delete',
            'on_store' => 'store',
        );
        $config['has_many']['images'] = array(
            'class_name' => 'MarketImage',
            'on_delete' => 'delete',
            'on_store' => 'store',
        );
        $config['has_many']['reviews'] = array(
            'class_name' => 'MarketReview',
            'on_delete' => 'delete',
            'on_store' => 'store',
        );
        $config['has_many']['follower'] = array(
            'class_name' => 'MarketPluginFollower',
            'on_delete' => 'delete',
            'on_store' => 'store',
        );
        $config['has_many']['uses'] = array(
            'class_name' => 'MarketPluginUsage',
            'on_delete' => 'delete',
            'on_store' => 'store',
        );
        $config['belongs_to']['user'] = array(
            'class_name' => 'User',
            'foreign_key' => 'user_id',
        );
        $config['has_and_belongs_to_many']['more_users'] = array(
            'class_name' => "User",
            'thru_table' => 'pluginmarket_user_plugins',
            'on_delete' => 'delete',
            'on_store' => 'store'
        );
        parent::configure($config);
    }

    public function __construct($id = null)
    {
        $this->registerCallback('before_store', 'requestReview');
        parent::__construct($id);
    }

    public function requestReview() {
        if ($this->content['publiclyvisible'] && !$this->content_db['publiclyvisible'] && !$this['approved']) {
            $messaging = new messaging();
            $statement = DBManager::get()->prepare("
                SELECT roles_user.user_id
                FROM roles
                    INNER JOIN roles_user ON (roles.roleid = roles_user.roleid)
                WHERE roles.rolename = 'Pluginbeauftragter'
            ");
            $statement->execute();
            foreach ($statement->fetchAll(PDO::FETCH_COLUMN, 0) as $beauftragter) {
                $messaging->sendSystemMessage(
                    $beauftragter,
                    sprintf(_("Plugin %s braucht ein Review"), $this['name']),
                    _("Auf dem Marktplatz wurde ein neues Plugin öffentlich geschaltet. Es kann allerdings erst öffentlich auf dem Marktplatz erscheinen, wenn Sie das Plugin einmal reviewt haben und freischalten. Gehen Sie auf den Pluginmarktplatz und den Reiter 'Qualitätssicherung'.")
                );
            }
        }
    }

    public function isWritable($user_id = null) {
        $user_id || $user_id = $GLOBALS['user']->id;
        return ($this['user_id'] === $user_id) || $this->isRootable($user_id);
    }

    public function isRootable($user_id = null) {
        $user_id || $user_id = $GLOBALS['user']->id;
        return $GLOBALS['perm']->have_perm("root", $user_id)
                || RolePersistence::isAssignedRole($user_id, "Pluginbeauftragter");
    }

    public function getLogoURL($absolute_url = false)
    {
        $firstimage = $this->images->first();
        return $firstimage ? $firstimage->getURL($absolute_url) : Assets::image_path("icons/blue/plugin.svg");
    }

    public function setTags($tags) {
        if (!$this->getId()) {
            return false;
        }
        $tags = array_map("strtolower", $tags);
        foreach ($tags as $key => $tag) {
            if (!trim($tag)) {
                unset($tags[$key]);
            }
        }

        $old_tags = $this->getTags();
        $insert = DBManager::get()->prepare("
            INSERT IGNORE INTO pluginmarket_tags
            SET plugin_id = :plugin_id,
                tag = :tag,
                user_id = :user_id
        ");
        $delete = DBManager::get()->prepare("
            DELETE FROM pluginmarket_tags
            WHERE plugin_id = :plugin_id
              AND tag = :tag
        ");
        foreach (array_diff($old_tags, $tags) as $tag_to_delete) {
            $delete->execute(array(
                'plugin_id' => $this->getId(),
                'tag' => $tag_to_delete
            ));
        }
        foreach ($tags as $tag) {
            $insert->execute(array(
                'plugin_id' => $this->getId(),
                'tag' => $tag,
                'user_id' => $GLOBALS['user']->id
            ));
        }
    }

    public function getTags() {
        $statement = DBManager::get()->prepare("
            SELECT tag
            FROM pluginmarket_tags
            WHERE plugin_id = ?
            ORDER BY (SELECT COUNT(*) FROM pluginmarket_tags AS t2 WHERE t2.tag = pluginmarket_tags.tag) DESC
        ");
        $statement->execute(array($this->getId()));
        return $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * Returns sum of downloads
     *
     * @return int Number of downloads
     */
    public function getDownloads() {
        return DBManager::get()->fetchColumn('SELECT SUM(downloads) FROM pluginmarket_releases WHERE plugin_id = ?', array($this->id));
    }

    /**
     * Checks if the plugin has a release for a given studip version
     *
     * @param String $version the requested version
     * @param boolean $all_releases Defines if all releases are checked for compatibility
     */
    public function checkVersion($version) {
        return $this->releases[0] ? $this->releases[0]->checkVersion($version) : false;
    }

    public function calculateRating() {
        $rating = 0;
        $factors = 0;
        foreach ($this->reviews as $review) {
            $age = time() - $review['chdate'];
            $factor = (pi() - 2 * atan($age / (86400 * 180))) / pi();
            $rating += $review['rating'] * $factor * 2;
            $factors += $factor;
        }
        if ($factors > 0) {
            $rating /= $factors;
        } else {
            return $rating = null;
        }

        return $rating;
    }
}