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

class ShowSearchPage extends AbstractGamePage
{
    public static $require_module = MODULE_SEARCH;

    public function __construct()
    {
        parent::__construct();
    }

    public static function _getSearchList($search_mode, $search_text, $max_result): array
    {
        $db = Database::get();

        $limit = $max_result === -1 ? '' : 'LIMIT '.((int) $max_result);

        $search_list = [];

        switch ($search_mode)
        {
            case 'playername':

                $sql = "SELECT a.id, a.username, a.ally_id, a.galaxy, a.system, a.planet, b.name, c.total_rank, d.ally_name
				FROM %%USERS%% as a
				INNER JOIN %%PLANETS%% as b ON b.id = a.id_planet
				LEFT JOIN %%USER_POINTS%% as c ON c.id_owner = a.id
				LEFT JOIN %%ALLIANCE%% as d ON d.id = a.ally_id
				WHERE a.universe = :universe AND a.username LIKE :search_text_like
				ORDER BY (
				  IF(a.username = :search_text, 1, 0)
				  + IF(a.username LIKE :search_text_like, 1, 0)
				) DESC, a.username ASC
				".$limit.";";

                $search_result = $db->select($sql, [
                    ':universe'         => Universe::current(),
                    ':search_text'      => $search_text,
                    ':search_text_like' => '%'.$search_text.'%',
                ]);

                foreach ($search_result as $c_result)
                {
                    $search_list[] = [
                        'planetname' => $c_result['name'],
                        'username'   => $c_result['username'],
                        'userid'     => $c_result['id'],
                        'allyname'   => $c_result['ally_name'],
                        'allyid'     => $c_result['ally_id'],
                        'galaxy'     => $c_result['galaxy'],
                        'system'     => $c_result['system'],
                        'planet'     => $c_result['planet'],
                        'rank'       => $c_result['total_rank'],
                    ];
                }
                break;
            case 'planetname':

                $sql = "SELECT a.name, a.galaxy, a.planet, a.system,
				b.id, b.ally_id, b.username,
				c.total_rank,
				d.ally_name
				FROM %%PLANETS%% as a
				INNER JOIN %%USERS%% as b ON b.id = a.id_owner
				LEFT JOIN  %%USER_POINTS%% as c ON c.id_owner = b.id
				LEFT JOIN %%ALLIANCE%% as d ON d.id = b.ally_id
				WHERE a.universe = :universe AND a.name LIKE :search_text_like
				ORDER BY (
				  IF(a.name = :search_text, 1, 0)
				  + IF(a.name LIKE :search_text_like, 1, 0)
				) DESC, a.name ASC
				".$limit.";";

                $search_result = $db->select($sql, [
                    ':universe'         => Universe::current(),
                    ':search_text'      => $search_text,
                    ':search_text_like' => '%'.$search_text.'%',
                ]);

                foreach ($search_result as $c_result)
                {
                    $search_list[] = [
                        'planetname' => $c_result['name'],
                        'username'   => $c_result['username'],
                        'userid'     => $c_result['id'],
                        'allyname'   => $c_result['ally_name'],
                        'allyid'     => $c_result['ally_id'],
                        'galaxy'     => $c_result['galaxy'],
                        'system'     => $c_result['system'],
                        'planet'     => $c_result['planet'],
                        'rank'       => $c_result['total_rank'],
                    ];
                }
                break;
            case "allytag":
                $sql = "SELECT a.id, a.ally_name, a.ally_tag, a.ally_members,
				c.total_points FROM %%ALLIANCE%% as a
				LEFT JOIN %%USER_POINTS%% as c ON c.id_owner = a.id
				WHERE a.ally_universe = :universe AND a.ally_tag LIKE :search_text_like
				ORDER BY (
				  IF(a.ally_tag = :search_text, 1, 0)
				  + IF(a.ally_tag LIKE :search_text_like, 1, 0)
				) DESC, a.ally_tag ASC
				".$limit.";";

                $search_result = $db->select($sql, [
                    ':universe'         => Universe::current(),
                    ':search_text'      => $search_text,
                    ':search_text_like' => '%'.$search_text.'%',
                ]);

                foreach ($search_result as $c_result)
                {
                    $search_list[] = [
                        'allypoints'  => pretty_number($c_result['total_points']),
                        'allytag'     => $c_result['ally_tag'],
                        'allymembers' => $c_result['ally_members'],
                        'allyname'    => $c_result['ally_name'],
                    ];
                }
                break;
            case "allyname":
                $sql = "SELECT a.ally_name, a.ally_tag, a.ally_members,
				b.total_points FROM %%ALLIANCE%% as a
				LEFT JOIN %%USER_POINTS%% as b ON b.id_owner = a.id
				WHERE a.ally_universe = :universe AND a.ally_name LIKE :search_text_like
				ORDER BY (
				  IF(a.ally_name = :search_text, 1, 0)
				  + IF(a.ally_name LIKE :search_text_like, 1, 0)
				) DESC,a.ally_name ASC
				".$limit.";";

                $search_result = $db->select($sql, [
                    ':universe'         => Universe::current(),
                    ':search_text'      => $search_text,
                    ':search_text_like' => '%'.$search_text.'%',
                ]);

                foreach ($search_result as $c_result)
                {
                    $search_list[] = [
                        'allypoints'  => pretty_number($c_result['total_points']),
                        'allytag'     => $c_result['ally_tag'],
                        'allymembers' => $c_result['ally_members'],
                        'allyname'    => $c_result['ally_name'],
                    ];
                }
                break;
        }

        return $search_list;
    }

    public function autocomplete(): void
    {
        global $LNG;

        $this->setWindow('ajax');

        $seach_mode = HTTP::_GP('type', 'playername');
        $search_text = HTTP::_GP('term', '', UTF8_SUPPORT);

        $search_list = [];

        $seach_modes = explode('|', $seach_mode);

        if (empty($search_text))
        {
            $this->sendJSON([]);
        }

        foreach ($seach_modes as $search)
        {
            $searchData = self::_getSearchList($search, $search_text, 5);
            foreach ($searchData as $data)
            {
                switch ($search)
                {
                    case 'playername':
                        $search_list[] = ['label' => str_replace(
                            $search_text,
                            '<b>'.$search_text.'</b>',
                            $data['username']
                        ),
                            'category' => $LNG['sh_player_name'], 'type' => 'playername'];
                        break;
                    case 'planetname':
                        $search_list[] = ['label' => str_replace($search_text, '<b>'.
                            $search_text.'</b>', $data['username']),
                            'category' => $LNG['sh_planet_name'], 'type' => 'planetname'];
                        break;
                    case "allytag":
                        $search_list[] = ['label' => str_replace($search_text, '<b>'.
                            $search_text.'</b>', $data['allytag']),
                            'category' => $LNG['sh_alliance_tag'], 'type' => 'allytag'];
                        break;
                    case "allyname":
                        $search_list[] = ['label' => str_replace($search_text, '<b>'.
                            $search_text.'</b>', $data['allyname']),
                            'category' => $LNG['sh_alliance_name'], 'type' => 'allyname'];
                        break;
                }
            }
        }

        $this->sendJSON($search_list);
    }

    public function result(): void
    {
        global $THEME;

        $this->initTemplate();
        $this->setWindow('ajax');

        $seach_mode = HTTP::_GP('type', 'playername');
        $search_text = HTTP::_GP('search', '', UTF8_SUPPORT);

        $search_list = [];

        if (!empty($search_text))
        {
            $search_list = self::_getSearchList($seach_mode, $search_text, SEARCH_LIMIT);
        }

        $this->assign([
            'searchList' => $search_list,
            'dpath'      => $THEME->getThemePath(),
        ]);

        $templateSuffix = ($seach_mode === "allyname" || $seach_mode === "allytag") ? "ally" : "default";

        $this->display('page.search.result.'.$templateSuffix.'.tpl');
    }

    public function show(): void
    {
        global $LNG;

        $seach_mode = HTTP::_GP('type', 'playername');

        $mode_selector = ['playername' => $LNG['sh_player_name'],
            'planetname'               => $LNG['sh_planet_name'],
            'allytag'                  => $LNG['sh_alliance_tag'],
            'allyname'                 => $LNG['sh_alliance_name']];
        $this->tpl_obj->loadscript('search.js');
        $this->assign([
            'modeSelector' => $mode_selector,
            'seachMode'    => $seach_mode,
        ]);

        $this->display('page.search.default.tpl');
    }
}
