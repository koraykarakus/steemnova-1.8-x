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

class ShowOverviewPage extends AbstractGamePage
{
    public static $require_module = 0;

    public function __construct()
    {
        parent::__construct();
    }

    private function GetTeamspeakData(): array
    {
        global $USER, $LNG;

        $config = Config::get();

        if ($config->ts_modon == 0)
        {
            return [];
        }

        Cache::get()->add('teamspeak', 'TeamspeakBuildCache');
        $ts_info = Cache::get()->getData('teamspeak', false);

        if (empty($ts_info))
        {
            return [
                'error' => $LNG['ov_teamspeak_not_online'],
            ];
        }

        $url = '';

        switch ($config->ts_version)
        {
            case 2:
                $url = 'teamspeak://%s:%s?nickname=%s';
                break;
            case 3:
                $url = 'ts3server://%s?port=%d&amp;nickname=%s&amp;password=%s';
                break;
        }

        return [
            'url'     => sprintf($url, $config->ts_server, $config->ts_tcpport, $USER['username'], $ts_info['password']),
            'current' => $ts_info['current'],
            'max'     => $ts_info['maxuser'],
            'error'   => false,
        ];
    }

    public function changeNewsVisibility(): void
    {
        global $USER;

        $result = 0;

        ($USER['show_news_active']) ? $result = 0 : $result = 1;

        $sql = "UPDATE %%USERS%% SET `show_news_active` = " . $result . " WHERE id = :userId;";

        Database::get()->update($sql, [
            ':userId' => $USER['id'],
        ]);

        $this->sendJSON($result);
    }

    public function show(): void
    {
        global $LNG, $PLANET, $USER, $config;

        $db = Database::get();
        $moon = [];
        if ($PLANET['id_luna'] != 0)
        {
            $sql = "SELECT id, name, planet_type, image FROM %%PLANETS%% WHERE id = :moonID;";

            $moon = $db->selectSingle($sql, [
                ':moonID' => $PLANET['id_luna'],
            ]);
        }
        elseif ($PLANET['planet_type'] == 3)
        {
            $sql = "SELECT id, name, planet_type, image FROM %%PLANETS%% WHERE id_luna = :moonID;";

            $moon = $db->selectSingle($sql, [
                ':moonID' => $PLANET['id'],
            ]);
        }

        $build_info = [];
        if ($PLANET['b_building'] - TIMESTAMP > 0)
        {
            $queue = unserialize($PLANET['b_building_id']);
            $build_info['buildings'] = [
                'id'        => $queue[0][0],
                'level'     => $queue[0][1],
                'timeleft'  => $PLANET['b_building'] - TIMESTAMP,
                'time'      => $PLANET['b_building'],
                'starttime' => pretty_time($PLANET['b_building'] - TIMESTAMP),
            ];
        }
        else
        {
            $build_info['buildings'] = false;
        }

        if (!empty($PLANET['b_hangar_id']))
        {

            $queue = unserialize($PLANET['b_hangar_id']);

            $time = BuildFunctions::getBuildingTime($USER, $PLANET, $queue[0][0]) * $queue[0][1];

            $build_info['fleet'] = [
                'id'        => $queue[0][0],
                'level'     => $queue[0][1],
                'timeleft'  => $time - $PLANET['b_hangar'],
                'time'      => $time,
                'starttime' => pretty_time($time - $PLANET['b_hangar']),
            ];

        }
        else
        {
            $build_info['fleet'] = false;
        }

        if ($USER['b_tech'] - TIMESTAMP > 0)
        {

            $queue = unserialize($USER['b_tech_queue']);

            $build_info['tech'] = [
                'id'        => $queue[0][0],
                'level'     => $queue[0][1],
                'timeleft'  => $USER['b_tech'] - TIMESTAMP,
                'time'      => $USER['b_tech'],
                'starttime' => pretty_time($USER['b_tech'] - TIMESTAMP),
            ];

        }
        else
        {
            $build_info['tech'] = false;
        }

        $sql = "SELECT id, username FROM %%USERS%% 
        WHERE universe = :universe AND onlinetime >= :onlinetime AND authlevel > :authlevel;";

        $online_admins = $db->select($sql, [
            ':universe'   => Universe::current(),
            ':onlinetime' => TIMESTAMP - 10 * 60,
            ':authlevel'  => AUTH_USR,
        ]);

        $admins_online = [];
        foreach ($online_admins as $c_admin)
        {
            $admins_online[$c_admin['id']] = $c_admin['username'];
        }

        $sql = "SELECT userName FROM %%CHAT_ON%% WHERE 
        dateTime > DATE_SUB(NOW(), interval 2 MINUTE) AND channel = 0";

        $chatUsers = $db->select($sql);

        $chat_online = [];
        foreach ($chatUsers as $c_user)
        {
            $chat_online[] = $c_user['userName'];
        }

        $ref_links = [];
        if ($config->ref_active)
        {
            // Fehler: Wenn Spieler gelöscht werden, werden sie nicht mehr in der Tabelle angezeigt.
            $sql = "SELECT u.id, u.username, s.total_points FROM %%USERS%% as u
            LEFT JOIN %%USER_POINTS%% as s ON s.id_owner = u.id WHERE ref_id = :userID;";

            $ref_links_db = $db->select($sql, [
                ':userID' => $USER['id'],
            ]);

            foreach ($ref_links_db as $c_ref)
            {
                $ref_links[$c_ref['id']] = [
                    'username' => $c_ref['username'],
                    'points'   => min($c_ref['total_points'], $config->ref_minpoints),
                ];
            }
        }

        $sql = 'SELECT total_points, total_rank
		FROM %%USER_POINTS%%
		WHERE id_owner = :userId;';

        $stat_data = $db->selectSingle($sql, [
            ':userId' => $USER['id'],
        ]);

        if (!$stat_data)
        {
            $rank_info = "-";
        }
        else
        {
            $rank_info = sprintf(
                $LNG['ov_userrank_info'],
                pretty_number($stat_data['total_points']),
                $LNG['ov_place'],
                $stat_data['total_rank'],
                $stat_data['total_rank'],
                $LNG['ov_of'],
                $config->users_amount
            );
        }

        $sql = "SELECT COUNT(*) as count FROM %%USERS%% 
        WHERE onlinetime >= UNIX_TIMESTAMP(NOW() - INTERVAL 15 MINUTE);";

        $users_online = $db->selectSingle($sql, [], 'count');

        $sql = "SELECT COUNT(*) as count FROM %%FLEETS%%;";
        $fleets_online = $db->selectSingle($sql, [], 'count');

        // get news

        $sql = "SELECT * FROM %%NEWS%%;";
        $news = $db->select($sql);

        if (!empty($news))
        {
            foreach ($news as &$c_news)
            {
                $c_news['date'] = _date(
                    $LNG['php_tdformat'],
                    $c_news['date'],
                    $USER['timezone']
                );
            }

            unset($c_news);
        }

        $this->assign([
            'rankInfo'             => $rank_info,
            'news'                 => $news,
            'usersOnline'          => $users_online,
            'fleetsOnline'         => $fleets_online,
            'planetname'           => $PLANET['name'],
            'planetimage'          => $PLANET['image'],
            'galaxy'               => $PLANET['galaxy'],
            'system'               => $PLANET['system'],
            'planet'               => $PLANET['planet'],
            'planet_type'          => $PLANET['planet_type'],
            'username'             => $USER['username'],
            'userid'               => $USER['id'],
            'buildInfo'            => $build_info,
            'Moon'                 => $moon,
            'AdminsOnline'         => $admins_online,
            'teamspeakData'        => $this->GetTeamspeakData(),
            'planet_diameter'      => pretty_number($PLANET['diameter']),
            'planet_field_current' => $PLANET['field_current'],
            'planet_field_max'     => CalculateMaxPlanetFields($PLANET),
            'planet_temp_min'      => $PLANET['temp_min'],
            'planet_temp_max'      => $PLANET['temp_max'],
            'planet_id'            => $PLANET['id'],
            'ref_active'           => $config->ref_active,
            'ref_minpoints'        => $config->ref_minpoints,
            'RefLinks'             => $ref_links,
            'chatOnline'           => $chat_online,
            'path'                 => HTTP_PATH,
            'show_news_active'     => $USER['show_news_active'],
        ]);

        $this->display('page.overview.default.tpl');
    }

    public function actions(): void
    {
        global $LNG, $PLANET;

        $this->initTemplate();

        $this->setWindow('popup');

        $this->assign([
            'ov_security_confirm' => sprintf($LNG['ov_security_confirm'], $PLANET['name'].' ['.$PLANET['galaxy'].':'.$PLANET['system'].':'.$PLANET['planet'].']'),
        ]);

        $this->display('page.overview.actions.tpl');
    }

    public function rename(): void
    {
        global $LNG, $PLANET;

        $newname = HTTP::_GP('name', '', UTF8_SUPPORT);

        $error = [];

        if (empty($newname))
        {
            $error[] = $LNG['ov_ac_error_1'];
        }

        if (strlen($newname) > 20)
        {
            $error[] = $LNG['ov_ac_error_2'];
        }

        if (!PlayerUtil::isNameValid($newname))
        {
            $error[] = $LNG['ov_newname_specialchar'];
        }

        if (!empty($error))
        {
            $this->sendJSON($error);
        }

        $db = Database::get();

        $sql = "UPDATE %%PLANETS%% SET name = :newName WHERE id = :planetID;";

        $db->update($sql, [
            ':newName'  => $newname,
            ':planetID' => $PLANET['id'],
        ]);

        $this->sendJSON($LNG['ov_newname_done']);

    }

    public function delete(): void
    {
        global $LNG, $PLANET, $USER;

        $password = HTTP::_GP('password', '', true);

        $error = [];
        if (empty($password))
        {
            $error[] = $LNG['ov_ac_error_4'];
        }

        $db = Database::get();

        $sql = "SELECT COUNT(*) as count FROM %%FLEETS%% WHERE
						(fleet_owner = :userID AND (fleet_start_id = :planetID OR fleet_start_id = :lunaID)) OR
						(fleet_target_owner = :userID AND (fleet_end_id = :planetID OR fleet_end_id = :lunaID));";

        $fleet_count = $db->selectSingle($sql, [
            ':userID'   => $USER['id'],
            ':planetID' => $PLANET['id'],
            ':lunaID'   => $PLANET['id_luna'],
        ], 'count');

        if ($fleet_count > 0)
        {
            $error[] = $LNG['ov_abandon_planet_not_possible'];
        }

        if ($USER['id_planet'] == $PLANET['id'])
        {
            $error[] = $LNG['ov_principal_planet_cant_abanone'];
        }

        if (!(password_verify($password, $USER['password'])))
        {
            $error[] = $LNG['ov_ac_error_5'];
        }

        if (!empty($error))
        {
            $this->sendJSON($error);
        }

        if ($USER['b_tech_planet'] == $PLANET['id']
            && !empty($USER['b_tech_queue']))
        {
            $tech_q = unserialize($USER['b_tech_queue']);
            $new_current_q = [];
            foreach ($tech_q as $ID => $ListIDArray)
            {
                if ($ListIDArray[4] == $PLANET['id'])
                {
                    $ListIDArray[4] = $USER['id_planet'];
                    $new_current_q[] = $ListIDArray;
                }
            }

            $USER['b_tech_planet'] = $USER['id_planet'];
            $USER['b_tech_queue'] = serialize($new_current_q);
        }

        if ($PLANET['planet_type'] == 1)
        {
            $sql = "UPDATE %%PLANETS%% SET destruyed = :time WHERE id = :planetID;";

            $db->update($sql, [
                ':time'     => TIMESTAMP + 86400,
                ':planetID' => $PLANET['id'],
            ]);

            $sql = "DELETE FROM %%PLANETS%% WHERE id = :lunaID;";

            $db->delete($sql, [
                ':lunaID' => $PLANET['id_luna'],
            ]);
        }
        else
        {
            $sql = "UPDATE %%PLANETS%% SET id_luna = 0 WHERE id_luna = :planetID;";

            $db->update($sql, [
                ':planetID' => $PLANET['id'],
            ]);

            $sql = "DELETE FROM %%PLANETS%% WHERE id = :planetID;";

            $db->delete($sql, [
                ':planetID' => $PLANET['id'],
            ]);
        }

        Session::load()->planetId = $USER['id_planet'];
        $this->sendJSON($LNG['ov_planet_abandoned']);
    }
}
