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
class ShowDiscordAuthPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $sql = "SELECT client_id, client_secret, redirect_url, callback_url 
        FROM %%DISCORD_AUTH%% 
        LIMIT 1";

        $discord_config = Database::get()->selectSingle($sql, []);

        if (!$discord_config
            || !isset($discord_config['client_id'])
            || !isset($discord_config['client_secret'])
            || !isset($discord_config['redirect_url'])
            || !isset($discord_config['callback_url']))
        {
            $this->printMessage('db error, table not found, check mysql');
        }

        $this->assign([
            'client_id'     => $discord_config['client_id'],
            'client_secret' => $discord_config['client_secret'],
            'redirect_url'  => $discord_config['redirect_url'],
            'callback_url'  => $discord_config['callback_url'],
        ]);

        $this->display('page.discordAuth.default.tpl');
    }

    public function saveSettings(): void
    {
        global $LNG;

        $client_id = HTTP::_GP('client_id', '');
        $client_secret = HTTP::_GP('client_secret', '');
        $redirect_url = HTTP::_GP('redirect_url', '');
        $callback_url = HTTP::_GP('callback_url', '');

        $sql = "UPDATE %%DISCORD_AUTH%% SET client_id = :client_id, client_secret = :client_secret,
        redirect_url = :redirect_url, callback_url = :callback_url;";

        Database::get()->update($sql, [
            ':client_id'     => trim($client_id),
            ':client_secret' => trim($client_secret),
            ':redirect_url'  => trim($redirect_url),
            ':callback_url'  => trim($callback_url),
        ]);

        $redirect_button = [];
        $redirect_button[] = [
            'url'   => 'admin.php?page=discordAuth&mode=show',
            'label' => $LNG['uvs_back'],
        ];

        $this->printMessage($LNG['settings_successful'], $redirect_button);
    }

}
