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

abstract class AbstractGamePage
{
    /**
     * reference of the template object
     * @var template
     */
    protected $tplObj;

    /**
     * reference of the template object
     * @var ResourceUpdate
     */
    protected $eco_obj;
    protected $window;
    protected $disable_eco_system = false;

    protected function __construct()
    {
        if (!AJAX_REQUEST)
        {
            $this->setWindow('full');
            if (!$this->disable_eco_system)
            {
                $this->eco_obj = new ResourceUpdate();
                $this->eco_obj->CalcResource();
            }
            $this->initTemplate();
        }
        else
        {
            $this->setWindow('ajax');
        }
    }

    protected function GetFleets(): array
    {
        global $USER, $PLANET;

        if (empty($USER)
            || empty($PLANET))
        {
            return [];
        }

        require_once 'includes/classes/class.FlyingFleetsTable.php';
        $fleet_table_obj = new FlyingFleetsTable();
        $fleet_table_obj->setUser($USER['id']);
        $fleet_table_obj->setPlanet($PLANET['id']);
        return $fleet_table_obj->renderTable();
    }

    public function getAttack(): void
    {
        global $USER;

        $db = Database::get();

        $sql = "SELECT (SELECT
		COUNT(*) FROM %%FLEETS%% WHERE
		fleet_owner != :userId AND fleet_mess = 0 AND fleet_universe = :universe AND fleet_target_owner = :userId AND (fleet_mission = 1 OR fleet_mission = 9) AND hasCanceled=0) AS attack,
		(SELECT
		COUNT(*) FROM %%FLEETS%% WHERE
		fleet_owner != :userId AND fleet_mess = 0 AND fleet_universe = :universe AND fleet_target_owner = :userId AND fleet_mission = 6 AND hasCanceled=0) AS spy
		FROM DUAL ";

        $fleets = $db->selectSingle($sql, [
            ':userId'   => $USER['id'],
            ':universe' => Universe::current(),
        ]);

        if ($fleets['attack'] > 0
            && $fleets['spy'] > 0)
        {
            $data = "spy";
        }
        elseif ($fleets['attack'] > 0
            && $fleets['spy'] == 0)
        {
            $data = "attack";
        }
        elseif ($fleets['spy'] > 0
            && $fleets['attack'] == 0)
        {
            $data = "spy";
        }
        else
        {
            $data = "noattack";
        }

        $this->sendJSON($data);
    }

    protected function initTemplate(): void
    {
        global $config, $USER;

        if (isset($this->tplObj))
        {
            return;
        }

        $this->tplObj = new template();
        list($tpl_dir) = $this->tplObj->getTemplateDir();

        $path = $theme = "";

        $theme = ($config->let_users_change_theme) ? $USER['dpath'] : $config->server_default_theme;

        $path = "theme/" . $theme;

        $this->tplObj->setTemplateDir($tpl_dir. $path);
    }

    protected function setWindow($window): void
    {
        $this->window = $window;
    }

    protected function getWindow(): string
    {
        return $this->window;
    }

    protected function getQueryString(): string
    {
        $query_string = [];
        $page = HTTP::_GP('page', '');

        if (!empty($page))
        {
            $query_string['page'] = $page;
        }

        $mode = HTTP::_GP('mode', '');
        if (!empty($mode))
        {
            $query_string['mode'] = $mode;
        }

        return http_build_query($query_string);
    }

    protected function getCronjobsTodo(): void
    {
        require_once 'includes/classes/Cronjob.class.php';

        $this->assign([
            'cronjobs' => Cronjob::getNeedTodoExecutedJobs(),
        ]);
    }

    protected function getNavigationData(): void
    {
        global $PLANET, $LNG, $USER, $THEME, $resource, $reslist, $config;

        $planet_select = [];

        if ($USER['bana'] == 1)
        {
            echo "You received a Ban. If you think this is a mistake, 
            write on our <a href='".DISCORD_URL."'>discord</a>";
            die();
        }

        if (!isset($USER['PLANETS']))
        {
            $USER['PLANETS'] = getPlanets($USER);
        }

        foreach ($USER['PLANETS'] as $c_planet)
        {
            $planet_select[$c_planet['id']] = $c_planet['name'] .
            (($c_planet['planet_type'] == 3) ?
            " (" . $LNG['fcm_moon'] . ")" :
            "") .
            " [" .
            $c_planet['galaxy'] . ":" .
            $c_planet['system'] . ":" .
            $c_planet['planet'] . "]";
        }

        $resource_table = [];
        $resource_speed = $config->resource_multiplier;
        foreach ($reslist['resstype'][1] as $c_id)
        {
            $resource_table[$c_id]['name'] = $resource[$c_id];
            $resource_table[$c_id]['current'] = $PLANET[$resource[$c_id]];
            $resource_table[$c_id]['max'] = $PLANET[$resource[$c_id].'_max'];

            if ($USER['urlaubs_modus'] == 1
                || $PLANET['planet_type'] != 1)
            {
                $resource_table[$c_id]['production'] = $PLANET[$resource[$c_id].'_perhour'];
            }
            else
            {
                $resource_table[$c_id]['production'] = $PLANET[$resource[$c_id].'_perhour']
                + $config->{$resource[$c_id].'_basic_income'} * $resource_speed;
            }
        }

        foreach ($reslist['resstype'][2] as $c_id)
        {
            $resource_table[$c_id]['name'] = $resource[$c_id];
            $resource_table[$c_id]['used'] = $PLANET[$resource[$c_id].'_used'];
            $resource_table[$c_id]['max'] = $PLANET[$resource[$c_id]];
        }

        foreach ($reslist['resstype'][3] as $c_id)
        {
            $resource_table[$c_id]['name'] = $resource[$c_id];
            $resource_table[$c_id]['current'] = $USER[$resource[$c_id]];
        }

        $theme_settings = $THEME->getStyleSettings();

        $commit = '';
        $commit_short = '';
        if (file_exists('.git/FETCH_HEAD'))
        {
            $commit = explode('	', file_get_contents('.git/FETCH_HEAD'))[0];
            $commit_short = substr($commit, 0, 7);
        }

        $avatar = 'styles/resource/images/user.png';
        if (Session::load()->data !== null)
        {
            try
            {
                $avatar = json_decode(Session::load()->data->account->json_metadata)->profile->profile_image;
            }
            catch (Exception $e)
            {
            }
        }

        $this->assign([
            'PlanetSelect'   => $planet_select,
            'new_message'    => $USER['messages'],
            'commit'         => $commit,
            'commitShort'    => $commit_short,
            'vacation'       => $USER['urlaubs_modus'] ? _date($LNG['php_tdformat'], $USER['urlaubs_until'], $USER['timezone']) : false,
            'delete'         => $USER['db_deaktjava'] ? sprintf($LNG['tn_delete_mode'], _date($LNG['php_tdformat'], $USER['db_deaktjava'] + ($config->del_user_manually * 86400)), $USER['timezone']) : false,
            'darkmatter'     => $USER['darkmatter'],
            'current_pid'    => $PLANET['id'],
            'image'          => $PLANET['image'],
            'username'       => $USER['username'],
            'avatar'         => $avatar,
            'resourceTable'  => $resource_table,
            'shortlyNumber'  => $theme_settings['TOPNAV_SHORTLY_NUMBER'],
            'closed'         => !$config->game_disable,
            'hasBoard'       => filter_var($config->forum_url, FILTER_VALIDATE_URL),
            'hasAdminAccess' => !empty(Session::load()->adminAccess),
            'hasGate'        => $PLANET[$resource[43]] > 0,
            'discordUrl'     => DISCORD_URL,
            //overwrite messages, to do : delete from other pages
            'messages' => ($USER['messages'] > 0) ? (($USER['messages'] == 1) ? $LNG['ov_have_new_message'] : "(" . $USER['messages'] . ")") : false,
        ]);
    }

    protected function getPageData(): void
    {
        global $USER, $THEME, $config, $PLANET, $LNG;

        if ($this->getWindow() === 'full')
        {
            $this->getNavigationData();
            $this->getCronjobsTodo();
        }

        $date_time_server = new DateTime("now");
        if (isset($USER['timezone']))
        {
            try
            {
                $date_time_user = new DateTime("now", new DateTimeZone($USER['timezone']));
            }
            catch (Exception $e)
            {
                $date_time_user = $date_time_server;
            }
        }
        else
        {
            $date_time_user = $date_time_server;
        }

        // TODO: this is not good...
        $all_planets = $all_moons = [];
        if (!empty($USER['PLANETS']))
        {
            foreach ($USER['PLANETS'] as $c_planet)
            {
                if (!empty($c_planet['b_building'])
                    && $c_planet['b_building'] > TIMESTAMP)
                {
                    $queue = unserialize($c_planet['b_building_id']);
                    $build_planet = $LNG['tech'][$queue[0][0]] .
                    " (" .
                    $queue[0][1] .
                    ")<br><span style=\"color:#7F7F7F;\">(" .
                    pretty_time($queue[0][3] - TIMESTAMP) .
                    ")</span>";
                }
                else
                {
                    $build_planet = $LNG['ov_free'];
                }

                if ($c_planet['planet_type'] == 3)
                {

                    $all_moons[] = [
                        'id'            => $c_planet['id'],
                        'name'          => (strlen($c_planet['name']) >= 12) ? substr($c_planet['name'], 0, 12) . ".." : $c_planet['name'],
                        'image'         => $c_planet['image'],
                        'build'         => $build_planet,
                        'galaxy'        => $c_planet['galaxy'],
                        'system'        => $c_planet['system'],
                        'planet'        => $c_planet['planet'],
                        'selected'      => ($c_planet['id'] == $PLANET['id']) ? true : false,
                        'field_current' => $c_planet['field_current'],
                        'field_max'     => $c_planet['field_max'],
                        'diameter'      => pretty_number($c_planet['diameter']) . " km",
                        'temp_min'      => $c_planet['temp_min'] . " °C",
                        'temp_max'      => $c_planet['temp_max'] . " °C",
                    ];

                }
                else
                {

                    $all_planets[] = [
                        'id'   => $c_planet['id'],
                        'name' => (strlen($c_planet['name']) >= 12) ?
                                   substr($c_planet['name'], 0, 12) . ".." :
                                   $c_planet['name'],
                        'image'         => $c_planet['image'],
                        'build'         => $build_planet,
                        'galaxy'        => $c_planet['galaxy'],
                        'system'        => $c_planet['system'],
                        'planet'        => $c_planet['planet'],
                        'selected'      => ($c_planet['id'] == $PLANET['id']) ? true : false,
                        'field_current' => $c_planet['field_current'],
                        'field_max'     => $c_planet['field_max'],
                        'diameter'      => pretty_number($c_planet['diameter']) . " km",
                        'temp_min'      => $c_planet['temp_min'] . " °C",
                        'temp_max'      => $c_planet['temp_max'] . " °C",
                        'id_luna'       => $c_planet['id_luna'],
                    ];

                }

            }
        }

        // NOTE: add moon array inside planet array

        foreach ($all_planets as &$c_planet)
        {
            if ($c_planet['id_luna'] == 0)
            {
                continue;
            }

            foreach ($all_moons as $c_moon)
            {
                if ($c_moon['id'] == $c_planet['id_luna'])
                {
                    $c_planet['moonInfo'][] = $c_moon;
                }
            }
        }
        unset($c_planet);

        $this->assign([
            'vmode'              => $USER['urlaubs_modus'],
            'authlevel'          => $USER['authlevel'],
            'userID'             => $USER['id'],
            'bodyclass'          => $this->getWindow(),
            'game_name'          => $config->game_name,
            'uni_name'           => $config->uni_name,
            'game_speed'         => pretty_number($config->game_speed / 2500),
            'fleet_speed'        => pretty_number($config->fleet_speed / 2500),
            'production_speed'   => pretty_number($config->resource_multiplier),
            'storage_multiplier' => pretty_number($config->storage_multiplier),
            'ga_active'          => $config->ga_active,
            'ga_key'             => $config->ga_key,
            'debug'              => $config->debug,
            'VERSION'            => $config->VERSION,
            'date'               => explode("|", date('Y\|n\|j\|G\|i\|s\|Z', TIMESTAMP)),
            'isPlayerCardActive' => isModuleAvailable(MODULE_PLAYERCARD),
            'REV'                => substr($config->VERSION, -4),
            'Offset'             => $date_time_user->getOffset() - $date_time_server->getOffset(),
            'queryString'        => $this->getQueryString(),
            'themeSettings'      => $THEME->getStyleSettings(),
            'page'               => HTTP::_GP('page', ''),
            'mode'               => HTTP::_GP('mode', ''),
            'servertime'         => _date("M D d H:i:s", TIMESTAMP, $USER['timezone']),
            'AllPlanets'         => $all_planets,
            'fleets'             => $this->GetFleets(),
            'show_fleets_active' => $USER['show_fleets_active'],
            'attackListenTime'   => ATTACK_LISTEN_TIME,
        ]);
    }

    protected function printMessage($msg, $redirect_buttons = null, $redirect = null, $full = true): void
    {
        $this->assign([
            'message'         => $msg,
            'redirectButtons' => $redirect_buttons,
        ]);

        if (isset($redirect))
        {
            $this->tplObj->gotoside($redirect[0], $redirect[1]);
        }

        if (!$full)
        {
            $this->setWindow('popup');
        }

        $this->display('error.default.tpl');
    }

    protected function save(): void
    {
        if (isset($this->eco_obj))
        {
            $this->eco_obj->SavePlanetToDB();
        }
    }

    protected function assign($array, $not_cache = true): void
    {
        $this->tplObj->assign_vars($array, $not_cache);
    }

    protected function display($file): void
    {
        global $THEME, $LNG;

        $this->save();

        if ($this->getWindow() !== 'ajax')
        {
            $this->getPageData();
        }

        $this->assign([
            'lang'       => $LNG->getLanguage(),
            'dpath'      => $THEME->getThemePath(),
            'scripts'    => $this->tplObj->jsscript,
            'execscript' => implode("\n", $this->tplObj->script),
            'basepath'   => PROTOCOL.HTTP_HOST.HTTP_BASE,
        ]);

        $this->assign([
            'LNG' => $LNG,
        ], false);

        $this->tplObj->display('extends:layout.'.$this->getWindow().'.tpl|'.$file);
        exit;
    }

    protected function sendJSON($data): void
    {
        $this->save();
        echo json_encode($data);
        exit;
    }

    protected function redirectTo($url): void
    {
        $this->save();
        HTTP::redirectTo($url);
        exit;
    }
}
