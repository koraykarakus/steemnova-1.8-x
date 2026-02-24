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

class ShowBanListPage extends AbstractLoginPage
{
    public static $requireModule = MODULE_BANLIST;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG;

        $db = Database::get();

        $page = HTTP::_GP('side', 1);

        $sql = "SELECT COUNT(*) as count FROM %%BANNED%% 
        WHERE universe = :universe 
        ORDER BY time DESC;";

        $ban_count = $db->selectSingle($sql, [
            ':universe' => Universe::current(),
        ], 'count');

        $max_page = ceil($ban_count / BANNED_USERS_PER_PAGE);
        $page = max(1, min($page, $max_page));

        $sql = "SELECT * FROM %%BANNED%% 
        WHERE universe = :universe 
        ORDER BY time DESC LIMIT :offset, :limit;";

        $banned_users = $db->select($sql, [
            ':universe' => Universe::current(),
            ':offset'   => (($page - 1) * BANNED_USERS_PER_PAGE),
            ':limit'    => BANNED_USERS_PER_PAGE,
        ]);

        $ban_list = [];

        foreach ($banned_users as $c_user)
        {
            $ban_list[] = [
                'player' => $c_user['who'],
                'theme'  => $c_user['theme'],
                'from'   => _date($LNG['php_tdformat'], $c_user['time'], Config::get()->timezone),
                'to'     => _date($LNG['php_tdformat'], $c_user['longer'], Config::get()->timezone),
                'admin'  => $c_user['author'],
                'mail'   => $c_user['email'],
                'info'   => sprintf($LNG['bn_writemail'], $c_user['author']),
            ];
        }

        $universe_select = $this->getUniverseSelector();
        $this->assign([
            'universeSelect' => $universe_select,
            'banList'        => $ban_list,
            'banCount'       => $ban_count,
            'pageNumber'     => $page,
            'maxPage'        => $max_page,
        ]);

        $this->display('page.banList.default.tpl');
    }
}
