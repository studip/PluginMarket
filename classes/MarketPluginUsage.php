<?php
/**
 * MarketPluginUsage.php
 * model class for table MarketPluginUsage
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Florian Bieringer <florian.bieringer@uni-passau.de>
 * @copyright   2014 Stud.IP Core-Group
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       3.0
 */

class MarketPluginUsage extends SimpleORMap
{

    protected static function configure($config = array()) {
        $config['db_table'] = 'pluginmarket_plugin_usages';
        $config['belongs_to']['plugin'] = array(
            'class_name' => 'MarketPlugin',
            'foreign_key' => 'plugin_id',
        );
        $config['belongs_to']['user'] = array(
            'class_name' => 'User',
            'foreign_key' => 'user_id',
        );
        parent::configure($config);
    }

    public function isEditable() {
        return $GLOBALS['perm']->have_perm('root')
                || $this->user_id == User::findCurrent()
                || $this->plugin->user_id == User::findCurrent()->id
                || RolePersistence::isAssignedRole(User::findCurrent()->id, "Pluginbeauftragter");
    }

}
