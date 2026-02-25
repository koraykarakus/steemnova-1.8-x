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

class ShowAlliancePage extends AbstractGamePage
{
    public static $requireModule = MODULE_ALLIANCE;

    private $allianceData;
    private $ranks;
    private $rights;
    private $hasAlliance = false;
    private $hasApply = false;
    public $availableRanks = [
        'MEMBERLIST',
        'ONLINESTATE',
        'TRANSFER',
        'SEEAPPLY',
        'MANAGEAPPLY',
        'ROUNDMAIL',
        'ADMIN',
        'KICK',
        'DIPLOMATIC',
        'RANKS',
        'MANAGEUSERS',
        'EVENTS',
    ];

    public function __construct()
    {
        global $USER;
        parent::__construct();
        $this->hasAlliance = $USER['ally_id'] != 0;
        $this->hasApply = $this->isApply();
        if ($this->hasAlliance && !$this->hasApply)
        {
            $this->setAllianceData($USER['ally_id']);
        }
    }

    private function setAllianceData($ally_id): void
    {
        global $USER;
        $db = Database::get();

        $sql = 'SELECT * FROM %%ALLIANCE%% WHERE id = :ally_id;';
        $this->allianceData = $db->selectSingle($sql, [
            ':ally_id' => $ally_id,
        ]);

        if ($USER['ally_id'] == $ally_id)
        {
            if ($this->allianceData['ally_owner'] == $USER['id'])
            {
                $this->rights = array_combine($this->availableRanks, 
                array_fill(0, count($this->availableRanks), true));
            }
            elseif ($USER['ally_rank_id'] != 0)
            {
                $sql = 'SELECT ' . implode(', ', $this->availableRanks) . 
                ' FROM %%ALLIANCE_RANK%% WHERE allianceId = :allianceId AND rankID = :ally_rank_id;';
                $this->rights = $db->selectSingle($sql, [
                    ':allianceId'   => $ally_id,
                    ':ally_rank_id' => $USER['ally_rank_id'],
                ]);
            }

            if (!isset($this->rights))
            {
                $this->rights = array_combine($this->availableRanks, array_fill(0, count($this->availableRanks), false));
            }

            if (isset($this->tplObj))
            {
                $this->assign([
                    'rights'        => $this->rights,
                    'AllianceOwner' => $this->allianceData['ally_owner'] == $USER['id'],
                ]);
            }
        }
    }

    // TODO : avoid multiple report type to support lower versions of php.
    private function isApply() : bool|array
    {
        global $USER;
        $db = Database::get();
        $sql = "SELECT COUNT(*) as count FROM %%ALLIANCE_REQUEST%% WHERE userId = :userId;";
        return $db->selectSingle($sql, [
            ':userId' => $USER['id'],
        ], 'count');
    }

    public function info(): void
    {
        global $LNG, $USER;

        $ally_id = HTTP::_GP('id', 0);

        $stats_data = [];
        $diplomacy_data = false;

        $this->setAllianceData($ally_id);

        if (!isset($this->allianceData))
        {
            $this->printMessage($LNG['al_not_exists']);
        }

        if ($this->allianceData['ally_diplo'] == 1)
        {
            $diplomacy_data = $this->getDiplomatic();
        }

        if ($this->allianceData['ally_stats'] == 1)
        {
            $sql = 'SELECT SUM(wons) as wons, 
            SUM(loos) as loos, 
            SUM(draws) as draws, 
            SUM(kbmetal) as kbmetal,
            SUM(kbcrystal) as kbcrystal, 
            SUM(lostunits) as lostunits, 
            SUM(desunits) as desunits
            FROM %%USERS%% WHERE ally_id = :allyID;';

            $stats_res = Database::get()->selectSingle($sql, [
                ':allyID' => $this->allianceData['id'],
            ]);

            $stats_data = [
                'totalfight' => $stats_res['wons'] + $stats_res['loos'] + $stats_res['draws'],
                'fightwon'   => $stats_res['wons'],
                'fightlose'  => $stats_res['loos'],
                'fightdraw'  => $stats_res['draws'],
                'unitsshot'  => pretty_number($stats_res['desunits']),
                'unitslose'  => pretty_number($stats_res['lostunits']),
                'dermetal'   => pretty_number($stats_res['kbmetal']),
                'dercrystal' => pretty_number($stats_res['kbcrystal']),
            ];
        }

        $sql = 'SELECT total_points
		FROM %%USER_POINTS%%
		WHERE id_owner = :userId;';

        $user_points = Database::get()->selectSingle($sql, [
            ':userId' => $USER['id'],
        ], 'total_points');

        $this->assign([
            'diplomaticData'               => $diplomacy_data,
            'statisticData'                => $stats_data,
            'ally_description'             => BBCode::parse($this->allianceData['ally_description']),
            'ally_id'                      => $this->allianceData['id'],
            'ally_image'                   => $this->allianceData['ally_image'],
            'ally_web'                     => $this->allianceData['ally_web'],
            'ally_member_scount'           => $this->allianceData['ally_members'],
            'ally_max_members'             => $this->allianceData['ally_max_members'],
            'ally_name'                    => $this->allianceData['ally_name'],
            'ally_tag'                     => $this->allianceData['ally_tag'],
            'ally_stats'                   => $this->allianceData['ally_stats'],
            'ally_diplo'                   => $this->allianceData['ally_diplo'],
            'ally_request'                 => !$this->hasAlliance && !$this->hasApply && $this->allianceData['ally_request_notallow'] == 0 && $this->allianceData['ally_max_members'] > $this->allianceData['ally_members'],
            'ally_request_min_points'      => $user_points >= $this->allianceData['ally_request_min_points'],
            'ally_request_min_points_info' => sprintf($LNG['al_requests_min_points'], pretty_number($this->allianceData['ally_request_min_points'])),
        ]);

        $this->display('page.alliance.info.tpl');
    }

    public function show(): void
    {
        if ($this->hasAlliance)
        {
            $this->homeAlliance();
        }
        elseif ($this->hasApply)
        {
            $this->applyWaitScreen();
        }
        else
        {
            $this->createSelection();
        }
    }

    private function redirectToHome(): void
    {
        $this->redirectTo('game.php?page=alliance');
    }

    private function getAction(): string
    {
        return HTTP::_GP('action', '');
    }

    private function applyWaitScreen(): void
    {
        global $USER, $LNG;

        $db = Database::get();
        
        $sql = "SELECT a.ally_tag 
        FROM %%ALLIANCE_REQUEST%% r 
        INNER JOIN %%ALLIANCE%% a ON a.id = r.allianceId 
        WHERE r.userId = :userId;";

        $ally_result = $db->selectSingle($sql, [
            ':userId' => $USER['id_planet'],
        ]);

        if (!$ally_result)
        {
            $ally_result = [];
        }

        if (empty($ally_result['ally_tag']))
        {
            $ally_result['ally_tag'] = 0;
        }

        $this->assign([
            'request_text' => sprintf($LNG['al_request_wait_message'], $ally_result['ally_tag']),
        ]);

        $this->display('page.alliance.applyWait.tpl');
    }

    private function createSelection(): void
    {
        $this->display('page.alliance.createSelection.tpl');
    }

    public function search(): void
    {
        if ($this->hasApply)
        {
            $this->redirectToHome();
        }

        $search_text = HTTP::_GP('searchtext', '', UTF8_SUPPORT);
        $search_list = [];

        if (!empty($search_text))
        {
            $db = Database::get();
            $sql = "SELECT id, ally_name, ally_tag, ally_members
			FROM %%ALLIANCE%%
			WHERE ally_universe = :universe AND ally_name LIKE :searchTextEqual
			ORDER BY (
			  IF(ally_name = :searchTextEqual, 1, 0) + IF(ally_name LIKE :searchTextLike, 1, 0)
			) DESC,ally_name ASC LIMIT 25;";

            $searchResult = $db->select($sql, [
                ':universe'        => Universe::current(),
                ':searchTextLike'  => '%' . $search_text . '%',
                ':searchTextEqual' => $search_text,
            ]);

            foreach ($searchResult as $searchRow)
            {
                $search_list[] = [
                    'id'      => $searchRow['id'],
                    'tag'     => $searchRow['ally_tag'],
                    'members' => $searchRow['ally_members'],
                    'name'    => $searchRow['ally_name'],
                ];
            }
        }

        $this->assign([
            'searchText' => $search_text,
            'searchList' => $search_list,
        ]);

        $this->display('page.alliance.search.tpl');
    }

    public function apply(): void
    {
        global $LNG, $USER;

        if ($this->hasApply)
        {
            $this->redirectToHome();
        }

        $text = HTTP::_GP('text', '', true);
        $ally_id = HTTP::_GP('id', 0);

        $db = Database::get();
        $sql = "SELECT ally_tag, ally_request, ally_request_notallow, ally_owner 
        FROM %%ALLIANCE%% 
        WHERE id = :ally_id AND ally_universe = :universe;";

        $ally_result = $db->selectSingle($sql, [
            ':ally_id' => $ally_id,
            ':universe'   => Universe::current(),
        ]);

        if (!isset($ally_result))
        {
            $this->redirectToHome();
        }

        if ($ally_result['ally_request_notallow'] == 1)
        {
            $this->printMessage($LNG['al_alliance_closed'], [[
                'label' => $LNG['sys_forward'],
                'url'   => '?page=alliance',
            ]]);
        }

        if ($USER['ally_id'] != 0)
        {
            $this->redirectToHome();
        }

        if (!empty($text))
        {
            $sql = "INSERT INTO %%ALLIANCE_REQUEST%% SET
                allianceId	= :ally_id,
                text		= :text,
                time		= :time,
                userId		= :userId;";

            $db->insert($sql, [
                ':ally_id' => $ally_id,
                ':text'       => $text,
                ':time'       => TIMESTAMP,
                ':userId'     => $USER['id'],
            ]);

            $this->printMessage($LNG['al_request_confirmation_message'], [[
                'label' => $LNG['sys_forward'],
                'url'   => '?page=alliance',
            ]]);
        }

        $apply_msg = sprintf(
            $LNG['al_new_apply'],
            $USER['id'],
            $USER['username'],
            $USER['username']
        );

        PlayerUtil::sendMessage(
            $ally_result['ally_owner'],
            0,
            $LNG['al_alliance'],
            2,
            $LNG['al_request'],
            $apply_msg,
            TIMESTAMP
        );

        $this->assign([
            'allyid'           => $ally_id,
            'applytext'        => $ally_result['ally_request'],
            'al_write_request' => sprintf($LNG['al_write_request'], $ally_result['ally_tag']),
        ]);

        $this->display('page.alliance.apply.tpl');
    }

    public function cancelApply(): void
    {
        global $LNG, $USER;

        if (!$this->hasApply)
        {
            $this->redirectToHome();
        }

        $db = Database::get();
        
        $sql = "SELECT a.ally_tag 
        FROM %%ALLIANCE_REQUEST%% r 
        INNER JOIN %%ALLIANCE%% a ON a.id = r.allianceId 
        WHERE r.userId = :userId;";

        $ally_tag = $db->selectSingle($sql, [
            ':userId' => $USER['id'],
        ], 'ally_tag');

        $sql = "DELETE FROM %%ALLIANCE_REQUEST%% WHERE userId = :userId;";
        
        $db->delete($sql, [
            ':userId' => $USER['id'],
        ]);

        $this->printMessage(sprintf($LNG['al_request_deleted'], $ally_tag), [[
            'label' => $LNG['sys_forward'],
            'url'   => '?page=alliance',
        ]]);
    }

    public function create(): void
    {
        global $USER, $LNG;

        if ($this->hasApply)
        {
            $this->redirectToHome();
        }

        $sql = 'SELECT total_points
		FROM %%USER_POINTS%%
		WHERE id_owner = :userId;';

        $user_points = Database::get()->selectSingle($sql, [
            ':userId' => $USER['id'],
        ], 'total_points');

        $min_points = Config::get()->alliance_create_min_points;

        if ($user_points >= $min_points)
        {
            $action = $this->getAction();
            if ($action == "send")
            {
                $this->createAlliance();
            }
            else
            {
                $this->display('page.alliance.create.tpl');
            }
        }
        else
        {
            $diff_points = $min_points - $user_points;
            $text_msg = sprintf(
                $LNG['al_make_ally_insufficient_points'],
                pretty_number($min_points),
                pretty_number($diff_points)
            );

            $this->printMessage($text_msg, [[
                'label' => $LNG['sys_back'],
                'url'   => '?page=alliance',
            ]]);
        }
    }

    private function createAlliance(): void
    {
        $action = $this->getAction();
        if ($action == "send")
        {
            $this->createAllianceProcessor();
        }
        else
        {
            $this->display('page.alliance.create.tpl');
        }
    }

    private function createAllianceProcessor(): void
    {
        global $USER, $LNG;
        $ally_tag = HTTP::_GP('atag', '', UTF8_SUPPORT);
        $ally_name = HTTP::_GP('aname', '', UTF8_SUPPORT);

        if (empty($ally_tag))
        {
            $this->printMessage($LNG['al_tag_required'], [[
                'label' => $LNG['sys_back'],
                'url'   => '?page=alliance&mode=create',
            ]]);
        }

        if (empty($ally_name))
        {
            $this->printMessage($LNG['al_name_required'], [[
                'label' => $LNG['sys_back'],
                'url'   => '?page=alliance&mode=create',
            ]]);
        }

        if (!PlayerUtil::isNameValid($ally_name) 
            || !PlayerUtil::isNameValid($ally_tag))
        {
            $this->printMessage($LNG['al_newname_specialchar'], [[
                'label' => $LNG['sys_back'],
                'url'   => '?page=alliance&mode=create',
            ]]);
        }

        $db = Database::get();

        $sql = 'SELECT COUNT(*) as count FROM %%ALLIANCE%% WHERE ally_universe = :universe
        AND (ally_tag = :allianceTag OR ally_name = :ally_name);';

        $allianceCount = $db->selectSingle($sql, [
            ':universe'     => Universe::current(),
            ':allianceTag'  => $ally_tag,
            ':ally_name'    => $ally_name,
        ], 'count');

        if ($allianceCount != 0)
        {
            $this->printMessage(sprintf($LNG['al_already_exists'], $ally_name), [[
                'label' => $LNG['sys_back'],
                'url'   => '?page=alliance&mode=create',
            ]]);
        }

        $sql = "INSERT INTO %%ALLIANCE%% SET ally_name = :ally_name, 
        ally_tag = :ally_tag, ally_owner = :userId,
        ally_owner_range = :allianceOwnerRange, ally_members = 1, 
        ally_register_time = :time, ally_universe = :universe;";

        $db->insert($sql, [
            ':ally_name'          => $ally_name,
            ':ally_tag'           => $ally_tag,
            ':userId'             => $USER['id'],
            ':allianceOwnerRange' => $LNG['al_default_leader_name'],
            ':time'               => TIMESTAMP,
            ':universe'           => Universe::current(),
        ]);

        $ally_id = $db->lastInsertId();

        $sql = "UPDATE %%USERS%% SET ally_id = :ally_id, ally_rank_id	= 0, 
        ally_register_time = :time WHERE id = :userId;";
        $db->update($sql, [
            ':ally_id'    => $ally_id,
            ':time'       => TIMESTAMP,
            ':userId'     => $USER['id'],
        ]);

        $sql = "UPDATE %%USER_POINTS%% SET id_ally = :ally_id WHERE id_owner = :userId;";
        $db->update($sql, [
            ':ally_id'    => $ally_id,
            ':userId'     => $USER['id'],
        ]);

        $this->printMessage(sprintf($LNG['al_created'], $ally_name . ' [' . $ally_tag . ']'), [[
            'label' => $LNG['sys_forward'],
            'url'   => '?page=alliance',
        ]]);
    }

    private function getDiplomatic(): array
    {
        $Return = [];
        $db = Database::get();

        $sql = "SELECT d.level, d.accept, 
        d.accept_text, d.id, a.id as ally_id, 
        a.ally_name, a.ally_tag, d.owner_1, d.owner_2 
        FROM %%DIPLO%% as d 
        INNER JOIN %%ALLIANCE%% as a ON 
        IF(:allianceId = d.owner_1, a.id = d.owner_2, a.id = d.owner_1) 
        WHERE :allianceId = d.owner_1 OR :allianceId = d.owner_2;";

        $diplo_result = $db->select($sql, [
            ':allianceId' => $this->allianceData['id'],
        ]);

        foreach ($diplo_result as $c_diplo)
        {
            if ($c_diplo['accept'] == 0 
                && $c_diplo['owner_2'] == $this->allianceData['id'])
            {
                $Return[5][$c_diplo['id']] = [$c_diplo['ally_name'], $c_diplo['ally_id'], $c_diplo['level'], $c_diplo['accept_text'], $c_diplo['ally_tag']];
            }
            elseif ($c_diplo['accept'] == 0 
                && $c_diplo['owner_1'] == $this->allianceData['id'])
            {
                $Return[6][$c_diplo['id']] = [$c_diplo['ally_name'], $c_diplo['ally_id'], $c_diplo['level'], $c_diplo['accept_text'], $c_diplo['ally_tag']];
            }
            else
            {
                $Return[$c_diplo['level']][$c_diplo['id']] = [$c_diplo['ally_name'], $c_diplo['ally_id'], $c_diplo['owner_1'], $c_diplo['ally_tag']];
            }
        }
        return $Return;
    }

    private function homeAlliance(): void
    {
        global $USER, $LNG;

        $db = Database::get();

        if ($this->allianceData['ally_owner'] == $USER['id'])
        {
            $rank_name = ($this->allianceData['ally_owner_range'] != '') ? 
                        $this->allianceData['ally_owner_range'] : 
                        $LNG['al_founder_rank_text'];
        }
        elseif ($USER['ally_rank_id'] != 0)
        {
            $sql = "SELECT rankName FROM %%ALLIANCE_RANK%% WHERE rankID = :UserRankID;";
            $rank_name = $db->selectSingle($sql, [
                ':UserRankID' => $USER['ally_rank_id'],
            ], 'rankName');
        }

        if (empty($rank_name))
        {
            $rank_name = $LNG['al_new_member_rank_text'];
        }

        $sql = "SELECT SUM(wons) as wons, SUM(loos) as loos, SUM(draws) as draws, 
        SUM(kbmetal) as kbmetal, SUM(kbcrystal) as kbcrystal, 
        SUM(lostunits) as lostunits, SUM(desunits) as desunits 
        FROM %%USERS%% WHERE ally_id = :AllianceID;";

        $stats_result = $db->selectSingle($sql, [
            ':AllianceID' => $this->allianceData['id'],
        ]);

        $sql = "SELECT COUNT(*) as count FROM %%ALLIANCE_REQUEST%% WHERE allianceId = :AllianceID;";
        $apply_count = $db->selectSingle($sql, [
            ':AllianceID' => $this->allianceData['id'],
        ], 'count');

        $ally_events = [];

        if (!empty($this->allianceData['ally_events']))
        {
            $sql = "SELECT id, username FROM %%USERS%% WHERE ally_id = :ally_id;";
            $result = $db->select($sql, [
                ':ally_id' => $this->allianceData['id'],
            ]);

            require_once('includes/classes/class.FlyingFleetsTable.php');
            $FlyingFleetsTable = new FlyingFleetsTable();

            $this->tplObj->loadscript('overview.js');

            foreach ($result as $row)
            {
                $FlyingFleetsTable->setUser($row['id']);
                $FlyingFleetsTable->setMissions($this->allianceData['ally_events']);
                $ally_events[$row['username']] = $FlyingFleetsTable->renderTable();
            }

            $ally_events = array_filter($ally_events);
        }

        $this->assign([
            'DiploInfo'        => $this->getDiplomatic(),
            'ally_web'         => $this->allianceData['ally_web'],
            'ally_tag'         => $this->allianceData['ally_tag'],
            'ally_members'     => $this->allianceData['ally_members'],
            'ally_max_members' => $this->allianceData['ally_max_members'],
            'ally_name'        => $this->allianceData['ally_name'],
            'ally_image'       => $this->allianceData['ally_image'],
            'ally_description' => BBCode::parse($this->allianceData['ally_description']),
            'ally_text'        => BBCode::parse($this->allianceData['ally_text']),
            'rankName'         => $rank_name,
            'requests'         => sprintf($LNG['al_new_requests'], $apply_count),
            'applyCount'       => $apply_count,
            'totalfight'       => $stats_result['wons'] + $stats_result['loos'] + $stats_result['draws'],
            'fightwon'         => $stats_result['wons'],
            'fightlose'        => $stats_result['loos'],
            'fightdraw'        => $stats_result['draws'],
            'unitsshot'        => pretty_number($stats_result['desunits']),
            'unitslose'        => pretty_number($stats_result['lostunits']),
            'dermetal'         => pretty_number($stats_result['kbmetal']),
            'dercrystal'       => pretty_number($stats_result['kbcrystal']),
            'isOwner'          => $this->allianceData['ally_owner'] == $USER['id'],
            'ally_events'      => $ally_events,
        ]);

        $this->display('page.alliance.home.tpl');
    }

    public function memberList(): void
    {
        global $USER, $LNG;
        if (!$this->rights['MEMBERLIST'])
        {
            $this->redirectToHome();
        }

        $rank_list = [];

        $db = Database::get();
        $sql = "SELECT rankID, rankName FROM %%ALLIANCE_RANK%% WHERE allianceId = :AllianceID";
        $rank_result = $db->select($sql, [
            ':AllianceID' => $this->allianceData['id'],
        ]);

        foreach ($rank_result as $c_rank)
        {
            $rank_list[$c_rank['rankID']] = $c_rank['rankName'];
        }

        $member_list = [];

        $sql = "SELECT DISTINCT u.id, u.username,u.galaxy, u.system, u.planet, 
        u.banaday, u.urlaubs_modus, u.ally_register_time, u.onlinetime, 
        u.ally_rank_id, s.total_points 
        FROM %%USERS%% u LEFT JOIN %%USER_POINTS%% as s 
        ON s.id_owner = u.id 
        WHERE ally_id = :ally_id;";

        $member_list_result = $db->select($sql, [
            ':ally_id' => $this->allianceData['id'],
        ]);

        try
        {
            $USER += $db->selectSingle('SELECT total_points FROM %%USER_POINTS%% WHERE id_owner = :userId;', [
                ':userId' => $USER['id'],
            ]);
        }
        catch (Exception $e)
        {
            $USER['total_points'] = 0;
        }

        foreach ($member_list_result as $c_member_list)
        {
            $IsNoobProtec = CheckNoobProtec($USER, $c_member_list, $c_member_list);
            $Class = userStatus($c_member_list, $IsNoobProtec);

            if ($this->allianceData['ally_owner'] == $c_member_list['id'])
            {
                $c_member_list['ally_rankName'] = empty($this->allianceData['ally_owner_range']) ? $LNG['al_founder_rank_text'] : $this->allianceData['ally_owner_range'];
            }
            elseif ($c_member_list['ally_rank_id'] != 0 
                && isset($rank_list[$c_member_list['ally_rank_id']]))
            {
                $c_member_list['ally_rankName'] = $rank_list[$c_member_list['ally_rank_id']];
            }
            else
            {
                $c_member_list['ally_rankName'] = $LNG['al_new_member_rank_text'];
            }

            $member_list[$c_member_list['id']] = [
                'class'         => $Class,
                'username'      => $c_member_list['username'],
                'galaxy'        => $c_member_list['galaxy'],
                'system'        => $c_member_list['system'],
                'planet'        => $c_member_list['planet'],
                'register_time' => _date($LNG['php_tdformat'], $c_member_list['ally_register_time'], $USER['timezone']),
                'points'        => $c_member_list['total_points'],
                'rankName'      => $c_member_list['ally_rankName'],
                'onlinetime'    => floor((TIMESTAMP - $c_member_list['onlinetime']) / 60),
            ];
        }

        $this->assign([
            'memberList'    => $member_list,
            'al_users_list' => sprintf($LNG['al_users_list'], count($member_list)),
            'ShortStatus'   => [
                'vacation'     => $LNG['gl_short_vacation'],
                'banned'       => $LNG['gl_short_ban'],
                'inactive'     => $LNG['gl_short_inactive'],
                'longinactive' => $LNG['gl_short_long_inactive'],
                'noob'         => $LNG['gl_short_newbie'],
                'strong'       => $LNG['gl_short_strong'],
                'enemy'        => $LNG['gl_short_enemy'],
                'friend'       => $LNG['gl_short_friend'],
                'member'       => $LNG['gl_short_member'],
            ],
        ]);

        $this->display('page.alliance.memberList.tpl');
    }

    public function close(): void
    {
        global $USER;

        $db = Database::get();

        $sql = "UPDATE %%USERS%% SET ally_id = 0, ally_register_time = 0, 
        ally_register_time = 5 
        WHERE id = :user_id;";

        $db->update($sql, [
            ':user_id' => $USER['id'],
        ]);

        $sql = "UPDATE %%USER_POINTS%% SET id_ally = 0 WHERE id_owner = :user_id;";
       
        $db->update($sql, [
            ':user_id' => $USER['id'],
        ]);

        $sql = "UPDATE %%ALLIANCE%% SET ally_members = (SELECT COUNT(*) 
        FROM %%USERS%% WHERE ally_id = :ally_id) 
        WHERE id = :ally_id;";
        
        $db->update($sql, [
            ':ally_id' => $this->allianceData['id'],
        ]);

        $this->redirectTo('game.php?page=alliance');
    }

    public function circular(): void
    {
        global $LNG, $USER;

        if (!$this->rights['ROUNDMAIL'])
        {
            $this->redirectToHome();
        }

        $action = HTTP::_GP('action', '');

        if ($action == "send")
        {
            $rank_id = HTTP::_GP('rankID', 0);
            $subject = HTTP::_GP('subject', '', true);
            $text = HTTP::_GP('text', $LNG['mg_no_subject'], true);

            if (empty($text))
            {
                $this->sendJSON(['message' => $LNG['mg_empty_text'], 'error' => true]);
            }

            $db = Database::get();

            if ($rank_id == 0)
            {
                $sql = 'SELECT id, username FROM %%USERS%% WHERE ally_id = :AllianceID;';
                $send_user_result = $db->select($sql, [
                    ':AllianceID' => $this->allianceData['id'],
                ]);
            }
            else
            {
                $sql = 'SELECT id, username FROM %%USERS%% WHERE ally_id = :AllianceID AND ally_rank_id = :RankID;';
                $send_user_result = $db->select($sql, [
                    ':AllianceID' => $this->allianceData['id'],
                    ':RankID'     => $rank_id,
                ]);
            }

            $send_list = $LNG['al_circular_sended'];
            $title = $LNG['al_circular_alliance'] . $this->allianceData['ally_tag'];
            $text = sprintf($LNG['al_circular_front_text'], $USER['username']) . "\r\n" . $text;

            foreach ($send_user_result as $c_send_user)
            {
                PlayerUtil::sendMessage($c_send_user['id'], $USER['id'], $title, 2, $subject, makebr($text), TIMESTAMP);
                $send_list .= "\n" . $c_send_user['username'];
            }

            $this->sendJSON(['message' => $send_list, 'error' => false]);
        }

        $this->initTemplate();
        $this->setWindow('popup');
        $range_list[] = $LNG['al_all_players'];

        if (is_array($this->ranks))
        {
            foreach ($this->ranks as $id => $array)
            {
                $range_list[$id + 1] = $array['name'];
            }
        }

        $this->assign([
            'RangeList' => $range_list,
        ]);

        $this->display('page.alliance.circular.tpl');
    }

    public function admin(): void
    {
        global $LNG;

        $action = HTTP::_GP('action', 'overview');
        $method_name = 'admin' . ucwords($action);

        if (!is_callable([$this, $method_name]))
        {
            ShowErrorPage::printError($LNG['page_doesnt_exist']);
        }

        $this->{$method_name}();
    }

    protected function adminOverview(): void
    {
        global $LNG;
        $send = HTTP::_GP('send', 0);
        $text_mode = HTTP::_GP('textMode', 'external');

        if ($send)
        {
            $db = Database::get();

            $this->allianceData['ally_owner_range'] = HTTP::_GP('owner_range', '', true);
            $this->allianceData['ally_web'] = filter_var(HTTP::_GP('web', ''), FILTER_VALIDATE_URL);
            $this->allianceData['ally_image'] = filter_var(HTTP::_GP('image', ''), FILTER_VALIDATE_URL);
            $this->allianceData['ally_request_notallow'] = HTTP::_GP('request_notallow', 0);
            $this->allianceData['ally_max_members'] = max(HTTP::_GP('ally_max_members', ''), $this->allianceData['ally_members']);
            $this->allianceData['ally_request_min_points'] = HTTP::_GP('request_min_points', 0);
            $this->allianceData['ally_stats'] = HTTP::_GP('stats', 0);
            $this->allianceData['ally_diplo'] = HTTP::_GP('diplo', 0);
            $this->allianceData['ally_events'] = implode(',', HTTP::_GP('events', [0]));

            $new_ally_tag = HTTP::_GP('ally_tag', $this->allianceData['ally_tag'], UTF8_SUPPORT);
            $new_ally_name = HTTP::_GP('ally_name', $this->allianceData['ally_name'], UTF8_SUPPORT);

            if (!empty($new_ally_tag) 
                && $this->allianceData['ally_tag'] != $new_ally_tag)
            {
                $sql = "SELECT COUNT(*) as count FROM %%ALLIANCE%% 
                WHERE ally_universe = :universe AND ally_tag = :NewAllianceTag;";

                $ally_count = $db->selectSingle($sql, [
                    ':universe'       => Universe::current(),
                    ':NewAllianceTag' => $new_ally_tag,
                ], 'count');

                if ($ally_count != 0)
                {
                    $this->printMessage(sprintf($LNG['al_already_exists'], $new_ally_tag), [[
                        'label' => $LNG['sys_back'],
                        'url'   => 'game.php?page=alliance&mode=admin',
                    ]]);
                }
                else
                {
                    $this->allianceData['ally_tag'] = $new_ally_tag;
                }
            }

            if (!empty($new_ally_name) 
                && $this->allianceData['ally_name'] != $new_ally_name)
            {
                $sql = "SELECT COUNT(*) as count FROM %%ALLIANCE%% 
                WHERE ally_universe = :universe AND ally_name = :NewAllianceName;";
                
                $ally_count = $db->selectSingle($sql, [
                    ':universe'        => Universe::current(),
                    ':NewAllianceName' => $new_ally_name,
                ], 'count');

                if ($ally_count != 0)
                {
                    $this->printMessage(sprintf($LNG['al_already_exists'], $new_ally_name), [[
                        'label' => $LNG['sys_back'],
                        'url'   => 'game.php?page=alliance&mode=admin',
                    ]]);
                }
                else
                {
                    $this->allianceData['ally_name'] = $new_ally_name;
                }
            }

            if ($this->allianceData['ally_request_notallow'] != 0 
                && $this->allianceData['ally_request_notallow'] != 1)
            {
                $this->allianceData['ally_request_notallow'] = 0;
            }

            $text = HTTP::_GP('text', '', true);
            $text_mode = HTTP::_GP('textMode', 'external');

            $sql_text = "";

            switch ($text_mode)
            {
                case 'external':
                    $sql_text = "ally_description = :text, ";
                    break;
                case 'internal':
                    $sql_text = "ally_text = :text, ";
                    break;
                case 'apply':
                    $sql_text = "ally_request = :text, ";
                    break;
            }

            $sql = "UPDATE %%ALLIANCE%% SET
			" . $sql_text . "
			ally_tag = :AllianceTag,
			ally_name = :AllianceName,
			ally_owner_range = :AllianceOwnerRange,
			ally_image = :AllianceImage,
			ally_web = :AllianceWeb,
			ally_request_notallow = :AllianceRequestNotAllow,
			ally_max_members = :AllianceMaxMember,
			ally_request_min_points = :AllianceRequestMinPoints,
			ally_stats = :AllianceStats,
			ally_diplo = :AllianceDiplo,
			ally_events = :AllianceEvents
			WHERE id = :AllianceID;";

            $db->update($sql, [
                ':AllianceTag'              => $this->allianceData['ally_tag'],
                ':AllianceName'             => $this->allianceData['ally_name'],
                ':AllianceOwnerRange'       => $this->allianceData['ally_owner_range'],
                ':AllianceImage'            => $this->allianceData['ally_image'],
                ':AllianceWeb'              => $this->allianceData['ally_web'],
                ':AllianceRequestNotAllow'  => $this->allianceData['ally_request_notallow'],
                ':AllianceMaxMember'        => $this->allianceData['ally_max_members'],
                ':AllianceRequestMinPoints' => $this->allianceData['ally_request_min_points'],
                ':AllianceStats'            => $this->allianceData['ally_stats'],
                ':AllianceDiplo'            => $this->allianceData['ally_diplo'],
                ':AllianceEvents'           => $this->allianceData['ally_events'],
                ':AllianceID'               => $this->allianceData['id'],
                ':text'                     => $text,
            ]);
        }
        else
        {
            switch ($text_mode)
            {
                case 'internal':
                    $text = $this->allianceData['ally_text'];
                    break;
                case 'apply':
                    $text = $this->allianceData['ally_request'];
                    break;
                default:
                    $text = $this->allianceData['ally_description'];
                    break;
            }
        }

        require_once 'includes/classes/class.FlyingFleetHandler.php';

        $available_events = [];

        foreach (array_keys(FlyingFleetHandler::$missionObjPattern) as $missionId)
        {
            $available_events[$missionId] = $LNG['type_mission_' . $missionId];
        }

        $this->assign([
            'RequestSelector'         => [0 => $LNG['al_requests_allowed'], 1 => $LNG['al_requests_not_allowed']],
            'YesNoSelector'           => [1 => $LNG['al_go_out_yes'], 0 => $LNG['al_go_out_no']],
            'textMode'                => $text_mode,
            'text'                    => $text,
            'ally_tag'                => $this->allianceData['ally_tag'],
            'ally_name'               => $this->allianceData['ally_name'],
            'ally_web'                => $this->allianceData['ally_web'],
            'ally_image'              => $this->allianceData['ally_image'],
            'ally_request_notallow'   => $this->allianceData['ally_request_notallow'],
            'ally_members'            => $this->allianceData['ally_members'],
            'ally_max_members'        => $this->allianceData['ally_max_members'],
            'ally_request_min_points' => $this->allianceData['ally_request_min_points'],
            'ally_owner_range'        => $this->allianceData['ally_owner_range'],
            'ally_stats_data'         => $this->allianceData['ally_stats'],
            'ally_diplo_data'         => $this->allianceData['ally_diplo'],
            'ally_events'             => explode(',', $this->allianceData['ally_events']),
            'available_events'        => $available_events,
        ]);

        $this->display('page.alliance.admin.overview.tpl');
    }

    protected function adminClose(): void
    {
        global $USER;
        if ($this->allianceData['ally_owner'] == $USER['id'])
        {
            $db = Database::get();

            $sql = "UPDATE %%USERS%% SET ally_id = '0' WHERE ally_id = :AllianceID;";
            $db->update($sql, [
                ':AllianceID' => $this->allianceData['id'],
            ]);

            $sql = "UPDATE %%ALLIANCE_POINTS%% SET id_ally = '0' WHERE id_ally = :AllianceID;";
            $db->update($sql, [
                ':AllianceID' => $this->allianceData['id'],
            ]);

            $sql = "DELETE FROM %%ALLIANCE_POINTS%% WHERE id_owner = :AllianceID;";
            $db->delete($sql, [
                ':AllianceID' => $this->allianceData['id'],
            ]);

            $sql = "DELETE FROM %%ALLIANCE%% WHERE id = :AllianceID;";
            $db->delete($sql, [
                ':AllianceID' => $this->allianceData['id'],
            ]);

            $sql = "DELETE FROM %%ALLIANCE_REQUEST%% WHERE allianceId = :AllianceID;";
            $db->delete($sql, [
                ':AllianceID' => $this->allianceData['id'],
            ]);

            $sql = "DELETE FROM %%DIPLO%% WHERE owner_1 = :AllianceID OR owner_2 = :AllianceID;";
            $db->delete($sql, [
                ':AllianceID' => $this->allianceData['id'],
            ]);
        }

        $this->redirectToHome();
    }

    protected function adminTransfer(): void
    {
        global $USER;

        if ($this->allianceData['ally_owner'] != $USER['id'])
        {
            $this->redirectToHome();
        }

        $db = Database::get();

        $post_leader = HTTP::_GP('newleader', 0);
        if (!empty($post_leader))
        {
            $sql = "SELECT ally_rank_id FROM %%USERS%% WHERE id = :LeaderID;";
            $rank = $db->selectSingle($sql, [
                ':LeaderID' => $post_leader,
            ]);

            $sql = "UPDATE %%USERS%% SET ally_rank_id = :AllyRank WHERE id = :UserID;";
            $db->update($sql, [
                ':UserID'   => $USER['id'],
                ':AllyRank' => $rank['ally_rank_id'],
            ]);

            $sql = "UPDATE %%USERS%% SET ally_rank_id = 0 WHERE id = :LeaderID;";
            $db->update($sql, [
                ':LeaderID' => $post_leader,
            ]);

            $sql = "UPDATE %%ALLIANCE%% SET ally_owner = :LeaderID WHERE id = :AllianceID;";
            $db->update($sql, [
                ':LeaderID'   => $post_leader,
                ':AllianceID' => $this->allianceData['id'],
            ]);

            $this->redirectToHome();
        }
        else
        {
            $sql = "SELECT u.id, r.rankName, u.username FROM %%USERS%% u INNER JOIN %%ALLIANCE_RANK%% r ON r.rankID = u.ally_rank_id AND r.TRANSFER = 1 WHERE u.ally_id = :allianceId AND id != :allianceOwner;";
            $transfer_user_result = $db->select($sql, [
                ':allianceOwner' => $this->allianceData['ally_owner'],
                ':allianceId'    => $this->allianceData['id'],
            ]);

            $transfer_user_list = [];

            foreach ($transfer_user_result as $c_transfer_user)
            {
                $transfer_user_list[$c_transfer_user['id']] = $c_transfer_user['username'] . 
                " [" .
                 $c_transfer_user['rankName'] .
                 "]";
            }

            $this->assign([
                'transferUserList' => $transfer_user_list,
            ]);

            $this->display('page.alliance.admin.transfer.tpl');
        }
    }

    protected function adminMangeApply(): void
    {
        global $LNG, $USER;
        if (!$this->rights['SEEAPPLY'] 
            || !$this->rights['MANAGEAPPLY'])
        {
            $this->redirectToHome();
        }

        $db = Database::get();

        $sql = "SELECT applyID, u.username, r.time 
        FROM %%ALLIANCE_REQUEST%% r 
        INNER JOIN %%USERS%% u ON r.userId = u.id 
        WHERE r.allianceId = :allianceId;";

        $apply_result = $db->select($sql, [
            ':allianceId' => $this->allianceData['id'],
        ]);

        $apply_list = [];

        foreach ($apply_result as $c_apply)
        {
            $apply_list[] = [
                'username' => $c_apply['username'],
                'id'       => $c_apply['applyID'],
                'time'     => _date($LNG['php_tdformat'], $c_apply['time'], $USER['timezone']),
            ];
        }

        $this->assign([
            'applyList' => $apply_list,
        ]);

        $this->display('page.alliance.admin.mangeApply.tpl');
    }

    protected function adminDetailApply(): void
    {
        global $LNG, $USER;
        if (!$this->rights['SEEAPPLY'] || !$this->rights['MANAGEAPPLY'])
        {
            $this->redirectToHome();
        }

        $id = HTTP::_GP('id', 0);

        $db = Database::get();

        $sql = "SELECT
			r.`applyID`,
			r.`time`,
			r.`text`,
			u.`username`,
			u.`register_time`,
			u.`onlinetime`,
			u.`galaxy`,
			u.`system`,
			u.`planet`,
			CONCAT_WS(':', u.`galaxy`, u.`system`, u.`planet`) AS `coordinates`,
			@total_fights := u.`wons` + u.`loos` + u.`draws`,
			@total_fights_percentage := @total_fights / 100,
			@total_fights AS `total_fights`,
			u.`wons`,
			ROUND(u.`wons` / @total_fights_percentage, 2) AS `wons_percentage`,
			u.`loos`,
			ROUND(u.`loos` / @total_fights_percentage, 2) AS `loos_percentage`,
			u.`draws`,
			ROUND(u.`draws` / @total_fights_percentage, 2) AS `draws_percentage`,
			u.`kbmetal`,
			u.`kbcrystal`,
			u.`lostunits`,
			u.`desunits`,
			stat.`tech_rank`,
			stat.`tech_points`,
			stat.`build_rank`,
			stat.`build_points`,
			stat.`defs_rank`,
			stat.`defs_points`,
			stat.`fleet_rank`,
			stat.`fleet_points`,
			stat.`total_rank`,
			stat.`total_points`,
			p.`name`
		FROM %%ALLIANCE_REQUEST%% AS r
		LEFT JOIN %%USERS%% AS u ON r.userId = u.id
		INNER JOIN %%USER_POINTS%% AS stat ON r.userId = stat.id_owner
		LEFT JOIN %%PLANETS%% AS p ON p.id = u.id_planet
		WHERE applyID = :applyID;";

        $apply_detail = $db->selectSingle($sql, [
            ':applyID' => $id,
        ]);

        if (empty($apply_detail))
        {
            $this->printMessage($LNG['al_apply_not_exists'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=alliance&mode=admin&action=mangeApply',
            ]]);
        }

        $apply_detail['text'] = BBCode::parse($apply_detail['text']);
        $apply_detail['kbmetal'] = pretty_number($apply_detail['kbmetal']);
        $apply_detail['kbcrystal'] = pretty_number($apply_detail['kbcrystal']);
        $apply_detail['lostunits'] = pretty_number($apply_detail['lostunits']);
        $apply_detail['desunits'] = pretty_number($apply_detail['desunits']);

        $this->assign([
            'applyDetail'   => $apply_detail,
            'apply_time'    => _date($LNG['php_tdformat'], $apply_detail['time'], $USER['timezone']),
            'register_time' => _date($LNG['php_tdformat'], $apply_detail['register_time'], $USER['timezone']),
            'onlinetime'    => _date($LNG['php_tdformat'], $apply_detail['onlinetime'], $USER['timezone']),
        ]);

        $this->display('page.alliance.admin.detailApply.tpl');
    }

    protected function adminSendAnswerToApply(): void
    {
        global $LNG, $USER;
        
        if (!$this->rights['SEEAPPLY'] 
            || !$this->rights['MANAGEAPPLY'])
        {
            $this->redirectToHome();
        }

        $db = Database::get();

        $text = makebr(HTTP::_GP('text', '', true));
        $answer = HTTP::_GP('answer', '');
        $apply_id = HTTP::_GP('id', 0);

        $sql = "SELECT userId FROM %%ALLIANCE_REQUEST%% WHERE applyID = :applyID;";
        
        $userId = $db->selectSingle($sql, [
            ':applyID' => $apply_id,
        ], 'userId');

        // only if alliance request still exist
        if ($userId)
        {
            if ($answer == 'yes')
            {
                $sql = "DELETE FROM %%ALLIANCE_REQUEST%% 
                WHERE applyID = :apply_id";

                $db->delete($sql, [
                    ':apply_id' => $apply_id,
                ]);

                $sql = "UPDATE %%USERS%% 
                SET ally_id = :allianceId, ally_register_time = :time, ally_rank_id = 0 
                WHERE id = :userId;";

                $db->update($sql, [
                    ':allianceId' => $this->allianceData['id'],
                    ':time'       => TIMESTAMP,
                    ':userId'     => $userId,
                ]);

                $sql = "UPDATE %%USER_POINTS%% SET id_ally = :allianceId 
                WHERE id_owner = :userId;";

                $db->update($sql, [
                    ':allianceId' => $this->allianceData['id'],
                    ':userId'     => $userId,
                ]);

                $sql = "UPDATE %%ALLIANCE%% SET ally_members = (SELECT COUNT(*) 
                FROM %%USERS%% WHERE ally_id = :allianceId) 
                WHERE id = :allianceId;";

                $db->update($sql, [
                    ':allianceId' => $this->allianceData['id'],
                ]);

                $text = $LNG['al_hi_the_alliance'] . $this->allianceData['ally_name'] . $LNG['al_has_accepted'] . $text;
                $subject = $LNG['al_you_was_acceted'] . $this->allianceData['ally_name'];
            }
            else
            {
                $sql = "DELETE FROM %%ALLIANCE_REQUEST%% WHERE applyID = :apply_id";
                $db->delete($sql, [
                    ':apply_id' => $apply_id,
                ]);

                $text = $LNG['al_hi_the_alliance'] . 
                $this->allianceData['ally_name'] . 
                $LNG['al_has_declined'] . $text;

                $subject = $LNG['al_you_was_declined'] . $this->allianceData['ally_name'];
            }

            $sender_name = $LNG['al_the_alliance'] . 
            $this->allianceData['ally_name'] . 
            ' [' . 
            $this->allianceData['ally_tag'] . 
            ']';
            
            PlayerUtil::sendMessage($userId, $USER['id'], 
                $sender_name, 2, $subject, $text, TIMESTAMP);

        }

        $this->redirectTo('game.php?page=alliance&mode=admin&action=mangeApply');
    }

    protected function adminPermissions(): void
    {
        if (!$this->rights['RANKS'])
        {
            $this->redirectToHome();
        }

        $sql = "SELECT * FROM %%ALLIANCE_RANK%% WHERE allianceId = :allianceId;";
        $rank_result = Database::get()->select($sql, [
            ':allianceId' => $this->allianceData['id'],
        ]);

        $rank_list = [];
        foreach ($rank_result as $c_rank)
        {
            $rank_list[$c_rank['rankID']] = $c_rank;
        }

        $available_ranks = [];
        foreach ($this->availableRanks as $c_rank_id => $c_rank_name)
        {
            if ($this->rights[$c_rank_name])
            {
                $available_ranks[$c_rank_id] = $c_rank_name;
            }
        }

        $this->assign([
            'rankList'       => $rank_list,
            'ownRights'      => $this->rights,
            'availableRanks' => $available_ranks,
        ]);

        $this->display('page.alliance.admin.permissions.tpl');
    }

    protected function adminPermissionsSend(): void
    {
        global $LNG;
        if (!$this->rights['RANKS'])
        {
            $this->redirectToHome();
        }

        $new_rank = HTTP::_GP('newrank', [], true);
        $delete = HTTP::_GP('deleteRank', 0);
        $rank_data = HTTP::_GP('rank', []);

        $db = Database::get();

        if (!empty($new_rank['rankName']))
        {
            if (!PlayerUtil::isNameValid($new_rank['rankName']))
            {
                $this->printMessage($LNG['al_invalid_rank_name'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => '?page=alliance&mode=admin&action=permission',
                ]]);
            }

            $sql = 'INSERT INTO %%ALLIANCE_RANK%% SET rankName = :rankName, allianceID = :allianceID';
            $params = [
                ':rankName'   => $new_rank['rankName'],
                ':allianceID' => $this->allianceData['id'],
            ];

            unset($new_rank['rankName']);

            foreach ($new_rank as $key => $value)
            {
                if (isset($this->availableRanks[$key]) 
                    && $this->rights[$this->availableRanks[$key]])
                {
                    $sql .= ', `' . $this->availableRanks[$key] . '` = :' . $this->availableRanks[$key];
                    $params[':' . $this->availableRanks[$key]] = $value == 1 ? 1 : 0;
                }
            }

            $db->insert($sql, $params);
        }
        else
        {
            if (!empty($delete))
            {
                $sql = "DELETE FROM %%ALLIANCE_RANK%% 
                WHERE rankID = :rankID AND allianceId = :allianceId;";

                $db->delete($sql, [
                    ':allianceId' => $this->allianceData['id'],
                    ':rankID'     => $delete,
                ]);

                $sql = "UPDATE %%USERS%% SET ally_rank_id = 0 
                WHERE ally_rank_id = :rankID AND ally_id = :allianceId;";

                $db->update($sql, [
                    ':allianceId' => $this->allianceData['id'],
                    ':rankID'     => $delete,
                ]);
            }
            else
            {
                foreach ($rank_data as $rank_id => $c_rank_data)
                {
                    $sql = 'UPDATE %%ALLIANCE_RANK%% SET rankName = :rank_name';
                    
                    $params = [
                        ':rank_name'   => $c_rank_data['rankName'],
                        ':allianceID' => $this->allianceData['id'],
                        ':rank_id'     => $rank_id,
                    ];

                    unset($c_rank_data['rankName']);

                    foreach ($this->availableRanks as $key => $value)
                    {
                        if (isset($this->availableRanks[$key]) 
                            && $this->rights[$this->availableRanks[$key]])
                        {
                            $sql .= ', `' . 
                            $this->availableRanks[$key] . 
                            '` = :' . 
                            $this->availableRanks[$key];

                            $params[':' . $this->availableRanks[$key]] = 
                            (isset($c_rank_data[$key])) ? 1 : 0;

                        }
                    }

                    $sql .= ' WHERE rankID = :rank_id AND allianceID = :allianceID';

                    $db->update($sql, $params);
                }
            }
        }

        $this->redirectTo('game.php?page=alliance&mode=admin&action=permissions');
    }

    protected function adminMembers(): void
    {
        global $USER, $LNG;
        if (!$this->rights['MANAGEUSERS'])
        {
            $this->redirectToHome();
        }

        $db = Database::get();

        $sql = "SELECT rankID, rankName FROM %%ALLIANCE_RANK%% WHERE allianceId = :allianceId;";
        $rank_result = $db->select($sql, [
            ':allianceId' => $this->allianceData['id'],
        ]);

        $rank_list = [$LNG['al_new_member_rank_text']];
        $rank_select_list = $rank_list;

        foreach ($rank_result as $c_rank)
        {
            $has_rank_right = true;
            foreach ($this->availableRanks as $rankName)
            {
                if (!$this->rights[$rankName])
                {
                    $has_rank_right = false;
                    break;
                }
            }

            if ($has_rank_right)
            {
                $rank_select_list[$c_rank['rankID']] = $c_rank['rankName'];
            }

            $rank_list[$c_rank['rankID']] = $c_rank['rankName'];
        }

        $sql = "SELECT DISTINCT u.id, u.username, u.galaxy, u.system, u.planet, u.banaday, u.urlaubs_modus, u.ally_register_time, u.onlinetime, u.ally_rank_id, s.total_points
		FROM %%USERS%% u
		LEFT JOIN %%USER_POINTS%% as s ON s.id_owner = u.id
		WHERE ally_id = :allianceId;";

        $member_list_result = $db->select($sql, [
            ':allianceId' => $this->allianceData['id'],
        ]);

        $member_list = [];

        $total_points = ['total_points' => 0]; // default
        $sql = 'SELECT `total_points` FROM %%USER_POINTS%% WHERE id_owner = :userId;';
        $total_points = $db->selectSingle($sql, [
            ':userId' => $USER['id'],
        ]);
        $USER += $total_points;

        foreach ($member_list_result as $c_member_list)
        {
            $is_noob_protect = CheckNoobProtec($USER, $c_member_list, $c_member_list);
            $class = userStatus($c_member_list, $is_noob_protect);

            if ($this->allianceData['ally_owner'] == $c_member_list['id'])
            {
                $c_member_list['ally_rank_id'] = -1;
            }

            $member_list[$c_member_list['id']] = [
                'class'         => $class,
                'username'      => $c_member_list['username'],
                'galaxy'        => $c_member_list['galaxy'],
                'system'        => $c_member_list['system'],
                'planet'        => $c_member_list['planet'],
                'register_time' => _date($LNG['php_tdformat'], 
                                    $c_member_list['ally_register_time'], 
                                    $USER['timezone']),
                'points'        => $c_member_list['total_points'],
                'rankID'        => $c_member_list['ally_rank_id'],
                'onlinetime'    => floor((TIMESTAMP - $c_member_list['onlinetime']) / 60),
                'kickQuestion'  => sprintf($LNG['al_kick_player'], $c_member_list['username']),
            ];
        }

        $this->assign([
            'memberList'     => $member_list,
            'rankList'       => $rank_list,
            'rankSelectList' => $rank_select_list,
            'founder'        => empty($this->allianceData['ally_owner_range']) ? $LNG['al_founder_rank_text'] : $this->allianceData['ally_owner_range'],
            'al_users_list'  => sprintf($LNG['al_users_list'], count($member_list)),
            'canKick'        => $this->rights['KICK'],
            'ShortStatus'    => [
                'vacation'     => $LNG['gl_short_vacation'],
                'banned'       => $LNG['gl_short_ban'],
                'inactive'     => $LNG['gl_short_inactive'],
                'longinactive' => $LNG['gl_short_long_inactive'],
                'noob'         => $LNG['gl_short_newbie'],
                'strong'       => $LNG['gl_short_strong'],
                'enemy'        => $LNG['gl_short_enemy'],
                'friend'       => $LNG['gl_short_friend'],
                'member'       => $LNG['gl_short_member'],
            ],
        ]);

        $this->display('page.alliance.admin.members.tpl');
    }

    protected function adminRank(): void
    {
        global $LNG;
        if (!$this->rights['MANAGEUSERS'])
        {
            $this->sendJSON('');
        }

        $user_ranks = HTTP::_GP('rank', []);

        $db = Database::get();

        $sql = 'SELECT rankID, ' . 
                implode(', ', $this->availableRanks) . 
                ' FROM %%ALLIANCE_RANK%% WHERE allianceID = :allianceId;';

        $rank_result = $db->select($sql, [
            ':allianceId' => $this->allianceData['id'],
        ]);

        $rank_list = [];
        $rank_list[0] = array_combine($this->availableRanks, array_fill(0, count($this->availableRanks), true));

        foreach ($rank_result as $rankRow)
        {
            $hasRankRight = true;
            foreach ($this->availableRanks as $rankName)
            {
                if (!$this->rights[$rankName])
                {
                    $hasRankRight = false;
                    break;
                }
            }

            if ($hasRankRight)
            {
                $rank_list[$rankRow['rankID']] = $rankRow;
            }
        }

        foreach ($user_ranks as $userId => $rankId)
        {
            if ($userId == $this->allianceData['ally_owner'] 
                || !isset($rank_list[$rankId]))
            {
                continue;
            }

            $sql = 'UPDATE %%USERS%% SET ally_rank_id = :rankID 
            WHERE id = :userId AND ally_id = :allianceId;';

            $db->update($sql, [
                ':allianceId' => $this->allianceData['id'],
                ':rankID'     => (int) $rankId,
                ':userId'     => (int) $userId,
            ]);
        }

        $this->sendJSON($LNG['fl_shortcut_saved']);
    }

    protected function adminMembersKick(): void
    {
        if (!$this->rights['KICK'])
        {
            $this->redirectToHome();
        }

        $db = Database::get();

        $id = HTTP::_GP('id', 0);

        $sql = "SELECT ally_id FROM %%USERS%% WHERE id = :id;";
        
        $kick_user_ally_id = $db->selectSingle($sql, [
            ':id' => $id,
        ], 'ally_id');

        # Check, if user is in alliance, see #205
        if (empty($kick_user_ally_id) 
            || $kick_user_ally_id != $this->allianceData['id'])
        {
            $this->redirectToHome();
        }

        $sql = "UPDATE %%USERS%% 
        SET ally_id = 0, ally_register_time = 0, ally_rank_id = 0 
        WHERE id = :id;";

        $db->update($sql, [
            ':id' => $id,
        ]);

        $sql = "UPDATE %%USER_POINTS%% SET id_ally = 0 WHERE id_owner = :id;";
        $db->update($sql, [
            ':id' => $id,
        ]);

        $sql = "UPDATE %%ALLIANCE%% SET ally_members = (SELECT COUNT(*) FROM %%USERS%% WHERE ally_id = :allianceId) WHERE id = :allianceId;";
        $db->update($sql, [
            ':id'         => $id,
            ':allianceId' => $this->allianceData['id'],
        ]);

        $this->redirectTo('game.php?page=alliance&mode=admin&action=members');
    }

    protected function adminDiplomacy(): void
    {
        if (!$this->rights['DIPLOMATIC'])
        {
            $this->redirectToHome();
        }

        $db = Database::get();

        $diplomacy_list = [
            0 => [
                1 => [],
                2 => [],
                3 => [],
                4 => [],
                5 => [],
                6 => [],
            ],
            1 => [
                1 => [],
                2 => [],
                3 => [],
                4 => [],
                5 => [],
                6 => [],
            ],
            2 => [
                1 => [],
                2 => [],
                3 => [],
                4 => [],
                5 => [],
                6 => [],
            ],
        ];

        $sql = "SELECT d.id, d.level, d.accept, d.owner_1, d.owner_2, a.ally_name FROM %%DIPLO%% d
		INNER JOIN %%ALLIANCE%% a ON IF(:allianceId = d.owner_1, a.id = d.owner_2, a.id = d.owner_1)
		WHERE owner_1 = :allianceId OR owner_2 = :allianceId;";
        
        $diplomacy_result = $db->select($sql, [
            ':allianceId' => $this->allianceData['id'],
        ]);

        foreach ($diplomacy_result as $c_diplomacy)
        {
            $own = $c_diplomacy['owner_1'] == $this->allianceData['id'];
            if ($c_diplomacy['accept'] == 1)
            {
                $diplomacy_list[0][$c_diplomacy['level']][$c_diplomacy['id']] = $c_diplomacy['ally_name'];
            }
            elseif ($own)
            {
                $diplomacy_list[2][$c_diplomacy['level']][$c_diplomacy['id']] = $c_diplomacy['ally_name'];
            }
            else
            {
                $diplomacy_list[1][$c_diplomacy['level']][$c_diplomacy['id']] = $c_diplomacy['ally_name'];
            }
        }

        $this->assign([
            'diploList' => $diplomacy_list,
        ]);

        $this->display('page.alliance.admin.diplomacy.default.tpl');
    }

    protected function adminDiplomacyAccept(): void
    {
        if (!$this->rights['DIPLOMATIC'])
        {
            $this->redirectToHome();
        }

        $db = Database::get();

        $sql = "UPDATE %%DIPLO%% SET accept = 1 WHERE id = :id AND owner_2 = :allianceId;";
        
        $db->update($sql, [
            ':allianceId' => $this->allianceData['id'],
            ':id'         => HTTP::_GP('id', 0),
        ]);

        $this->redirectTo('game.php?page=alliance&mode=admin&action=diplomacy');
    }

    protected function adminDiplomacyDelete(): void
    {
        if (!$this->rights['DIPLOMATIC'])
        {
            $this->redirectToHome();
        }

        $db = Database::get();

        $sql = "DELETE FROM %%DIPLO%% WHERE id = :id AND (owner_1 = :allianceId OR owner_2 = :allianceId);";
        
        $db->delete($sql, [
            ':allianceId' => $this->allianceData['id'],
            ':id'         => HTTP::_GP('id', 0),
        ]);

        $this->redirectTo('game.php?page=alliance&mode=admin&action=diplomacy');
    }

    protected function adminDiplomacyCreate(): void
    {
        global $USER;
        if (!$this->rights['DIPLOMATIC'])
        {
            $this->redirectToHome();
        }

        $db = Database::get();

        $this->initTemplate();
        $this->setWindow('popup');

        $diplomacy_mode = HTTP::_GP('diploMode', 0);

        $sql = "SELECT ally_tag,ally_name,id FROM %%ALLIANCE%% WHERE id != :allianceId ORDER BY ally_tag ASC;";
        
        $diplomacy_ally = $db->select($sql, [
            ':allianceId' => $USER['ally_id'],
        ]);

        $AllyList = [];
        $IdList = [];
        foreach ($diplomacy_ally as $i)
        {
            $IdList[] = $i['id'];
            $AllyList[] = $i['ally_name'];
        }

        $this->assign([
            'diploMode' => $diplomacy_mode,
            'AllyList'  => $AllyList,
            'IdList'    => $IdList,
        ]);

        $this->display('page.alliance.admin.diplomacy.create.tpl');
    }

    protected function adminDiplomacyCreateProcessor(): void
    {
        global $LNG, $USER;
        if (!$this->rights['DIPLOMATIC'])
        {
            $this->redirectToHome();
        }

        $db = Database::get();

        $id = HTTP::_GP('ally_id', '', UTF8_SUPPORT);

        $sql = "SELECT id, ally_name, ally_owner, ally_tag, (SELECT level 
        FROM %%DIPLO%% WHERE (owner_1 = :id AND owner_2 = :allianceId) 
        OR (owner_2 = :id AND owner_1 = :allianceId)) as diplo 
        FROM %%ALLIANCE%% WHERE ally_universe = :universe AND id = :id;";

        $target_ally = $db->selectSingle($sql, [
            ':allianceId' => $USER['ally_id'],
            ':id'         => $id,
            ':universe'   => Universe::current(),
        ]);

        if (empty($target_ally))
        {
            $this->sendJSON([
                'error'   => true,
                'message' => sprintf($LNG['al_diplo_no_alliance'], $target_ally['id']),
            ]);
        }

        if (!empty($target_ally['diplo']))
        {
            $this->sendJSON([
                'error'   => true,
                'message' => sprintf($LNG['al_diplo_exists'], $target_ally['ally_name']),
            ]);
        }
        if ($target_ally['id'] == $this->allianceData['id'])
        {
            $this->sendJSON([
                'error'   => true,
                'message' => $LNG['al_diplo_same_alliance'],
            ]);
        }

        $this->setWindow('ajax');

        $level = HTTP::_GP('level', 0);
        $text = HTTP::_GP('text', '', true);

        if ($level == 5)
        {
            PlayerUtil::sendMessage($target_ally['ally_owner'], 
            $USER['id'], $LNG['al_circular_alliance'] . 
            $this->allianceData['ally_tag'], 1, $LNG['al_diplo_war'], 
            sprintf($LNG['al_diplo_war_mes'], "[" . 
            $this->allianceData['ally_tag'] . "] " . 
            $this->allianceData['ally_name'], "[" . 
            $target_ally['ally_tag'] . "] " . 
            $target_ally['ally_name'], $LNG['al_diplo_level'][$level], $text), TIMESTAMP);
        }
        else
        {
            PlayerUtil::sendMessage($target_ally['ally_owner'], 
            $USER['id'], $LNG['al_circular_alliance'] . 
            $this->allianceData['ally_tag'], 1, $LNG['al_diplo_war'], 
            sprintf($LNG['al_diplo_ask_mes'], $LNG['al_diplo_level'][$level], 
            "[" . $this->allianceData['ally_tag'] . "] " . 
            $this->allianceData['ally_name'], "[" . $target_ally['ally_tag'] 
            . "] " . $target_ally['ally_name'], $text), TIMESTAMP);
        }

        $sql = "INSERT INTO %%DIPLO%% SET owner_1 = :allianceId, 
        owner_2 = :allianceTargetID, level	= :level, accept = 0, 
        accept_text = :text, universe	= :universe";

        $db->insert($sql, [
            ':allianceId'       => $USER['ally_id'],
            ':allianceTargetID' => $target_ally['id'],
            ':level'            => $level,
            ':text'             => $text,
            ':universe'         => Universe::current(),
        ]);

        $this->sendJSON([
            'error'   => false,
            'message' => $LNG['al_diplo_create_done'],
        ]);
    }
}
