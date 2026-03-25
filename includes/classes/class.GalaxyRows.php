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

require_once 'includes/pages/game/ShowPhalanxPage.php';

class GalaxyRows
{
    private $galaxy;
    private $system;
    private $galaxy_data;
    private $galaxy_row;

    public const PLANET_DESTROYED = false;

    public function __construct()
    {

    }

    public function setGalaxy($galaxy)
    {
        $this->galaxy = $galaxy;
        return $this;
    }

    public function setSystem($system)
    {
        $this->system = $system;
        return $this;
    }

    public function getGalaxyData()
    {
        global $USER;

        $sql = 'SELECT SQL_BIG_RESULT DISTINCT
		p.galaxy, p.system, p.planet, p.id, p.id_owner, p.name, p.image, p.last_update, p.diameter, p.temp_min, p.destroyed, p.debris_metal, p.debris_crystal, p.id_moon,
		u.id as userid, u.ally_id, u.username, u.onlinetime, u.vacation_mode, u.banaday,
		m.id as m_id, m.diameter as m_diameter, m.name as m_name, m.temp_min as m_temp_min, m.last_update as m_last_update,
		s.total_points, s.total_rank,
		a.id as allyid, a.ally_tag, a.ally_web, a.ally_members, a.ally_name,
		allys.total_rank as ally_rank,
		COUNT(buddy.id) as buddy,
		d.level as diploLevel
		FROM %%PLANETS%% p
		LEFT JOIN %%USERS%% u ON p.id_owner = u.id
		LEFT JOIN %%PLANETS%% m ON m.id = p.id_moon
		LEFT JOIN %%USER_POINTS%% s ON s.id_owner = u.id
		LEFT JOIN %%ALLIANCE%% a ON a.id = u.ally_id
		LEFT JOIN %%DIPLO%% as d ON (d.owner_1 = :allianceId AND d.owner_2 = a.id) OR (d.owner_1 = a.id AND d.owner_2 = :allianceId) AND d.accept = :accept
		LEFT JOIN %%ALLIANCE_POINTS%% allys ON allys.id_owner = a.id
		LEFT JOIN %%BUDDY%% buddy ON (buddy.sender = :userId AND buddy.owner = u.id) OR (buddy.sender = u.id AND buddy.owner = :userId)
		WHERE p.universe = :universe AND p.galaxy = :galaxy AND p.system = :system AND p.planet_type = :planetTypePlanet
		GROUP BY p.id;';

        $galaxy_result = Database::get()->select($sql, [
            ':allianceId'       => $USER['ally_id'],
            ':userId'           => $USER['id'],
            ':universe'         => Universe::current(),
            ':galaxy'           => $this->galaxy,
            ':system'           => $this->system,
            ':planetTypePlanet' => 1,
            ':accept'           => 1,
        ]);

        foreach ($galaxy_result as $c_row)
        {
            $this->galaxy_row = $c_row;

            if ($this->galaxy_row['destroyed'] != 0)
            {
                $this->galaxy_data[$this->galaxy_row['planet']] = self::PLANET_DESTROYED;
                continue;
            }

            $this->galaxy_data[$this->galaxy_row['planet']] = [];

            $this->isOwnPlanet();
            $this->setLastActivityPlanet();

            $this->getAllowedMissions();

            $this->getPlayerData();
            $this->getPlanetData();
            $this->getAllianceData();
            $this->getDebrisData();
            $this->getMoonData();
            $this->setLastActivityMoon();
            $this->getActionButtons();
        }

        return $this->galaxy_data;
    }

    protected function setLastActivityPlanet()
    {
        $last_activity = floor((TIMESTAMP - $this->galaxy_row['last_update']) / 60);

        if ($last_activity < 15)
        {
            $this->galaxy_data[$this->galaxy_row['planet']]['lastActivity'] = '*';
        }
        elseif ($last_activity < 60)
        {
            $this->galaxy_data[$this->galaxy_row['planet']]['lastActivity'] = $last_activity;
        }
        else
        {
            $this->galaxy_data[$this->galaxy_row['planet']]['lastActivity'] = '';
        }
    }

    protected function setLastActivityMoon(): void
    {
        if ($this->galaxy_data[$this->galaxy_row['planet']]['moon'] !== false)
        {
            $last_activity = floor((TIMESTAMP - $this->galaxy_row['m_last_update']) / 60);
            if ($last_activity < 15)
            {
                $this->galaxy_data[$this->galaxy_row['planet']]['moon']['lastActivity'] = '*';
            }
            elseif ($last_activity < 60)
            {
                $this->galaxy_data[$this->galaxy_row['planet']]['moon']['lastActivity'] = $last_activity;
            }
            else
            {
                $this->galaxy_data[$this->galaxy_row['planet']]['moon']['lastActivity'] = '';
            }
        }
    }

    protected function isOwnPlanet()
    {
        global $USER;

        $this->galaxy_data[$this->galaxy_row['planet']]['ownPlanet'] = $this->galaxy_row['id_owner'] == $USER['id'];
    }

    protected function getAllowedMissions()
    {
        global $PLANET, $RESOURCE;

        $this->galaxy_data[$this->galaxy_row['planet']]['missions'] = [
            1  => !$this->galaxy_data[$this->galaxy_row['planet']]['ownPlanet'] && isModuleAvailable(MODULE_MISSION_ATTACK),
            3  => isModuleAvailable(MODULE_MISSION_TRANSPORT),
            4  => $this->galaxy_data[$this->galaxy_row['planet']]['ownPlanet'] && isModuleAvailable(MODULE_MISSION_STATION),
            5  => !$this->galaxy_data[$this->galaxy_row['planet']]['ownPlanet'] && isModuleAvailable(MODULE_MISSION_HOLD),
            6  => !$this->galaxy_data[$this->galaxy_row['planet']]['ownPlanet'] && isModuleAvailable(MODULE_MISSION_SPY),
            8  => isModuleAvailable(MODULE_MISSION_RECYCLE),
            9  => !$this->galaxy_data[$this->galaxy_row['planet']]['ownPlanet'] && $PLANET[$RESOURCE[214]] > 0 && isModuleAvailable(MODULE_MISSION_DESTROY),
            10 => !$this->galaxy_data[$this->galaxy_row['planet']]['ownPlanet'] && $PLANET[$RESOURCE[503]] > 0 && isModuleAvailable(MODULE_MISSION_ATTACK) && isModuleAvailable(MODULE_MISSILEATTACK) && $this->inMissileRange(),
        ];
    }

    protected function inMissileRange()
    {
        global $USER, $PLANET, $RESOURCE;

        if ($this->galaxy_row['galaxy'] != $PLANET['galaxy'])
        {
            return false;
        }

        $range = FleetFunctions::GetMissileRange($USER[$RESOURCE[117]]);
        $system_min = $PLANET['system'] - $range;
        $system_max = $PLANET['system'] + $range;

        return $this->galaxy_row['system'] >= $system_min && $this->galaxy_row['system'] <= $system_max;
    }

    protected function getActionButtons()
    {
        global $USER;
        if ($this->galaxy_data[$this->galaxy_row['planet']]['ownPlanet'])
        {
            $this->galaxy_data[$this->galaxy_row['planet']]['action'] = false;
        }
        else
        {
            $this->galaxy_data[$this->galaxy_row['planet']]['action'] = [
                'esp'     => $USER['settings_esp'] == 1 && $this->galaxy_data[$this->galaxy_row['planet']]['missions'][6],
                'message' => $USER['settings_wri'] == 1 && isModuleAvailable(MODULE_MESSAGES),
                'buddy'   => $USER['settings_bud'] == 1 && isModuleAvailable(MODULE_BUDDYLIST) && $this->galaxy_row['buddy'] == 0,
                'missle'  => $USER['settings_mis'] == 1 && $this->galaxy_data[$this->galaxy_row['planet']]['missions'][10],
            ];
        }
    }

    protected function getPlayerData()
    {
        global $USER, $LNG;

        $is_noob_protec = CheckNoobProtec($USER, $this->galaxy_row, $this->galaxy_row);
        $class = userStatus($this->galaxy_row, $is_noob_protec);

        $user_name = htmlspecialchars($this->galaxy_row['username'] ?? 'DELETED_USER', ENT_QUOTES, "UTF-8");
        $this->galaxy_data[$this->galaxy_row['planet']]['user'] = [
            'id'         => $this->galaxy_row['userid'],
            'username'   => $user_name,
            'rank'       => $this->galaxy_row['total_rank'],
            'points'     => pretty_number($this->galaxy_row['total_points']),
            'playerrank' => isModuleAvailable(MODULE_STATISTICS) ?
                                sprintf(
                                    $LNG['gl_in_the_rank'],
                                    $user_name,
                                    $this->galaxy_row['total_rank']
                                ) :
                                $user_name,
            'class'   => $class,
            'isBuddy' => $this->galaxy_row['buddy'] == 0,
        ];
    }

    protected function getAllianceData()
    {
        global $USER, $LNG;
        if (empty($this->galaxy_row['allyid']))
        {
            $this->galaxy_data[$this->galaxy_row['planet']]['alliance'] = false;
        }
        else
        {
            $class = [];
            switch ($this->galaxy_row['diploLevel'])
            {
                case 1:
                case 2:
                    $class = ['member'];
                    break;
                case 4:
                    $class = ['friend'];
                    break;
                case 5:
                    $class = ['enemy'];
                    break;
            }

            if ($USER['ally_id'] == $this->galaxy_row['ally_id'])
            {
                $class = ['member'];
            }

            $this->galaxy_data[$this->galaxy_row['planet']]['alliance'] = [
                'id'     => $this->galaxy_row['allyid'],
                'name'   => htmlspecialchars($this->galaxy_row['ally_name'], ENT_QUOTES, "UTF-8"),
                'member' => sprintf(($this->galaxy_row['ally_members'] == 1) ? $LNG['gl_member_add'] : $LNG['gl_member'], $this->galaxy_row['ally_members']),
                'web'    => $this->galaxy_row['ally_web'],
                'tag'    => $this->galaxy_row['ally_tag'],
                'rank'   => $this->galaxy_row['ally_rank'],
                'class'  => $class,
            ];
        }
    }

    protected function getDebrisData()
    {
        $total = $this->galaxy_row['debris_metal'] + $this->galaxy_row['debris_crystal'];
        if ($total == 0)
        {
            $this->galaxy_data[$this->galaxy_row['planet']]['debris'] = false;
        }
        else
        {
            $this->galaxy_data[$this->galaxy_row['planet']]['debris'] = [
                'metal'   => $this->galaxy_row['debris_metal'],
                'crystal' => $this->galaxy_row['debris_crystal'],
            ];
        }
    }

    protected function getMoonData()
    {
        if (!isset($this->galaxy_row['m_id']))
        {
            $this->galaxy_data[$this->galaxy_row['planet']]['moon'] = false;
        }
        else
        {
            $this->galaxy_data[$this->galaxy_row['planet']]['moon'] = [
                'id'            => $this->galaxy_row['m_id'],
                'name'          => htmlspecialchars($this->galaxy_row['m_name'], ENT_QUOTES, "UTF-8"),
                'temp_min'      => $this->galaxy_row['m_temp_min'],
                'diameter'      => $this->galaxy_row['m_diameter'],
            ];
        }
    }

    protected function getPlanetData()
    {
        $this->galaxy_data[$this->galaxy_row['planet']]['planet'] = [
            'id'      => $this->galaxy_row['id'],
            'name'    => htmlspecialchars($this->galaxy_row['name'], ENT_QUOTES, "UTF-8"),
            'image'   => $this->galaxy_row['image'],
            'phalanx' => isModuleAvailable(MODULE_PHALANX) && ShowPhalanxPage::allowPhalanx($this->galaxy_row['galaxy'], $this->galaxy_row['system']),
        ];
    }
}
