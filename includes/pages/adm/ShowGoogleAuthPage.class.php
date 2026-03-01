<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.x Koray Karakuş <koraykarakus@yahoo.com>
 * @link https://github.com/jkroepke/2Moons
 */

/**
 *
 */
class ShowGoogleAuthPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $sql = "SELECT client_id, client_secret, callback_url 
        FROM %%GOOGLE_AUTH%% 
        LIMIT 1";

        $google_config = Database::get()->selectSingle($sql,[]);

        if (!$google_config
            || !isset($google_config['client_id'])
            || !isset($google_config['client_secret'])
            || !isset($google_config['callback_url'])) 
        {
            $this->printMessage('db error, table not found, check mysql');
        }


        $this->assign([
            'client_id' => $google_config['client_id'],
            'client_secret' => $google_config['client_secret'],
            'callback_url' => $google_config['callback_url'],
        ]);

        $this->display('page.google_auth.default.tpl');
    }

    public function saveSettings(): void
    {
        global $LNG;

        $client_id = HTTP::_GP('client_id', '');
        $client_secret = HTTP::_GP('client_secret', '');
        $callback_url = HTTP::_GP('callback_url', '');

        $sql = "UPDATE %%GOOGLE_AUTH%% SET client_id = :client_id, client_secret = :client_secret, 
        callback_url = :callback_url;";

        Database::get()->update($sql,[
            ':client_id' => trim($client_id),
            ':client_secret' => trim($client_secret),
            ':callback_url' => trim($callback_url)
        ]);

        $redirect_button = [];
        $redirect_button[] = [
            'url'   => 'admin.php?page=googleAuth&mode=show',
            'label' => $LNG['uvs_back'],
        ];

        $this->printMessage($LNG['settings_successful'], $redirect_button);
    }

}
