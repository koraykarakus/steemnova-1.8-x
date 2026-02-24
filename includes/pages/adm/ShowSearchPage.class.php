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

// TODO : strip old database
class ShowSearchPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    private static function getSelector(): array
    {
        global $LNG;
        return [
            'list' => [
                'users'     => $LNG['se_users'],
                'planet'    => $LNG['se_planets'],
                'moon'      => $LNG['se_moons'],
                'alliance'  => $LNG['se_allys'],
                'vacation'  => $LNG['se_vacations'],
                'banned'    => $LNG['se_suspended'],
                'admin'     => $LNG['se_authlevels'],
                'inactives' => $LNG['se_inactives'],
                'online'    => $LNG['online_users'],
                'p_connect' => $LNG['se_planets_act'],
            ],
            'search' => [
                'name' => $LNG['se_input_name'],
                'id'   => $LNG['input_id'],
            ],
            'filter' => [
                'normal' => $LNG['se_type_all'],
                'exacto' => $LNG['se_type_exact'],
                'last'   => $LNG['se_type_last'],
                'first'  => $LNG['se_type_first'],
            ],
            'order' => [
                'ASC'  => $LNG['se_input_asc'],
                'DESC' => $LNG['se_input_desc'],
            ],
            'limit' => [
                '1'   => '1',
                '5'   => '5',
                '10'  => '10',
                '15'  => '15',
                '20'  => '20',
                '25'  => '25',
                '50'  => '50',
                '100' => '100',
                '200' => '200',
                '500' => '500',
            ],
        ];
    }

    public function show(): void
    {
        global $LNG, $USER;

        if (!isset($_GET['delete']))
        {
            $_GET['delete'] = '';
        }

        $search_file = HTTP::_GP('search', '');
        $search_for = HTTP::_GP('search_in', '');
        $search_method = HTTP::_GP('fuki', '');
        $search_key = HTTP::_GP('key_user', '', UTF8_SUPPORT);

        $page = HTTP::_GP('side', 0);
        $order = HTTP::_GP('key_order', '');
        $order_by = HTTP::_GP('key_acc', '');
        $limit = HTTP::_GP('limit', 25);

        $order_by_parse = [];
        $result = [];

        $minimize = '';
        if (HTTP::_GP('minimize', '') == 'on')
        {
            $minimize = "&amp;minimize=on";

            $this->assign([
                'minimize'  => 'checked = "checked"',
                'diisplaay' => 'style="display:none;"',
            ]);
        }

        $special_specify = "";

        switch ($search_method)
        {
            case 'exacto':
                $specify_where = "= '".$GLOBALS['DATABASE']->sql_escape($search_key)."'";
                break;
            case 'last':
                $specify_where = "LIKE '".$GLOBALS['DATABASE']->sql_escape($search_key, true)."%'";
                break;
            case 'first':
                $specify_where = "LIKE '%".$GLOBALS['DATABASE']->sql_escape($search_key, true)."'";
                break;
            default:
                $specify_where = "LIKE '%".$GLOBALS['DATABASE']->sql_escape($search_key, true)."%'";
                break;
        };

        if (!empty($search_file))
        {
            $array_users = ["users", "vacation", "admin", "inactives", "online"];
            $array_planets = ["planet", "moon", "p_connect"];
            $array_banned = ["banned"];
            $array_alliance = ["alliance"];

            if (in_array($search_file, $array_users))
            {
                $table = "users";
                $lang_name = [
                    0 => $LNG['se_search_users_0'],
                    1 => $LNG['se_search_users_1'],
                    2 => $LNG['se_search_users_2'],
                    3 => $LNG['se_search_users_3'],
                    4 => $LNG['se_search_users_4'],
                    5 => $LNG['se_search_users_5'],
                    6 => $LNG['se_search_users_6'],
                    7 => $LNG['se_search_users_7'],
                    8 => $LNG['se_search_users_8'],
                ];

                $specify_items = "id,username,email_2,onlinetime,
                register_time,user_lastip,authlevel,bana,urlaubs_modus";

                $s_name = $LNG['se_input_userss'];
                if ($search_file == "vacation")
                {
                    $special_specify = "AND urlaubs_modus = '1'";
                    $s_name = $LNG['se_input_vacatii'];
                }

                if ($search_file == "online")
                {
                    $special_specify = "AND onlinetime >= '".(TIMESTAMP - 15 * 60)."'";
                    $s_name = $LNG['se_input_connect'];
                }

                if ($search_file == "inactives")
                {
                    $special_specify = "AND onlinetime < '".(TIMESTAMP - 60 * 60 * 24 * 7)."'";
                    $s_name = $LNG['se_input_inact'];
                }

                if ($search_file == "admin")
                {
                    $special_specify = "AND authlevel <= '".$USER['authlevel']."' AND authlevel > '0'";
                    $s_name = $LNG['se_input_admm'];
                }

                $special_specify .= " AND universe = '".Universe::getEmulated()."'";

                (($search_for == "name") ? $where_item = "WHERE username" : $where_item = "WHERE id");
                $array_o_sec = ["id", "username", "email_2", "onlinetime",
                    "register_time", "user_lastip", "authlevel",
                    "bana", "urlaubs_modus"];
                $array_0_sec_count = count($array_o_sec);

                for ($order_num = 0; $order_num < $array_0_sec_count; $order_num++)
                {
                    $order_by_parse[$array_o_sec[$order_num]] = $LNG['se_search_users_'.$order_num];
                }
            }
            elseif (in_array($search_file, $array_planets))
            {
                $table = "planets p";
                $lang_name = [
                    0 => $LNG['se_search_planets_0'],
                    1 => $LNG['se_search_planets_1'],
                    2 => $LNG['se_search_planets_2'],
                    3 => $LNG['se_search_planets_3'],
                    4 => $LNG['se_search_planets_4'],
                    5 => $LNG['se_search_planets_5'],
                    6 => $LNG['se_search_planets_6'],
                    7 => $LNG['se_search_planets_7'],
                ];
                $specify_items = "p.id,p.name,CONCAT(u.username, ' (ID:&nbsp;', p.id_owner, ')'),p.last_update,p.galaxy,p.system,p.planet,p.id_luna";

                if ($search_file == "planet")
                {
                    $special_specify = "AND planet_type = '1'";
                    $s_name = $LNG['se_input_planett'];
                }
                elseif ($search_file == "moon")
                {
                    $special_specify = "AND planet_type = '3'";
                    $s_name = $LNG['se_input_moonn'];
                }
                elseif ($search_file == "p_connect")
                {
                    $special_specify = "AND last_update >= ".(TIMESTAMP - 60 * 60)."";
                    $s_name = $LNG['se_input_act_pla'];
                }

                $special_specify .= " AND p.universe = ".Universe::getEmulated();
                $where_item = "LEFT JOIN ".USERS." u ON u.id = p.id_owner ";
                if ($search_for == "name")
                {
                    $where_item .= "WHERE p.name";
                }
                else
                {
                    $where_item .= "WHERE p.id";
                }

                $array_o_sec = ["id", "name", "id_owner", "id_luna", "last_update", "galaxy", "system", "planet"];
                $array_0_sec_count = count($array_o_sec);

                for ($order_num = 0; $order_num < $array_0_sec_count; $order_num++)
                {
                    $order_by_parse[$array_o_sec[$order_num]] = $LNG['se_search_planets_'.$order_num];
                }
            }
            elseif (in_array($search_file, $array_banned))
            {
                $table = "banned";
                $lang_name = [
                    0 => $LNG['se_search_banned_0'],
                    1 => $LNG['se_search_banned_1'],
                    2 => $LNG['se_search_banned_2'],
                    3 => $LNG['se_search_banned_3'],
                    4 => $LNG['se_search_banned_4'],
                    5 => $LNG['se_search_banned_5'],
                ];
                $specify_items = "id,who,time,longer,theme,author";
                $s_name = $LNG['se_input_susss'];
                $special_specify = " AND universe = '".Universe::getEmulated()."'";

                (($search_for == "name") ? $where_item = "WHERE who" : $where_item = "WHERE id");

                $array_o_sec = ["id", "who", "time", "longer", "theme", "author"];
                $array_0_sec_count = count($array_o_sec);

                for ($order_num = 0; $order_num < $array_0_sec_count; $order_num++)
                {
                    $order_by_parse[$array_o_sec[$order_num]] = $LNG['se_search_banned_'.$order_num];
                }

            }
            elseif (in_array($search_file, $array_alliance))
            {
                $table = "alliance";
                $lang_name = [
                    0 => $LNG['se_search_alliance_0'],
                    1 => $LNG['se_search_alliance_1'],
                    2 => $LNG['se_search_alliance_2'],
                    3 => $LNG['se_search_alliance_3'],
                    4 => $LNG['se_search_alliance_4'],
                    5 => $LNG['se_search_alliance_5'],
                ];
                $specify_items = "id,ally_name,ally_tag,ally_owner,ally_register_time,ally_members";
                $s_name = $LNG['se_input_allyy'];
                $special_specify = " AND ally_universe = '".Universe::getEmulated()."'";

                (($search_for == "name") ? $where_item = "WHERE ally_name" : $where_item = "WHERE id");

                $array_o_sec = ["id", "ally_name", "ally_tag", "ally_owner", "ally_register_time", "ally_members"];
                $array_0_sec_count = count($array_o_sec);

                for ($order_num = 0; $order_num < $array_0_sec_count; $order_num++)
                {
                    $order_by_parse[$array_o_sec[$order_num]] = $LNG['se_search_alliance_'.$order_num];
                }
            }

            $result = $this->MyCrazyLittleSearch(
                $specify_items,
                $where_item,
                $specify_where,
                $special_specify,
                $order,
                $order_by,
                $limit,
                $table,
                $page,
                $lang_name,
                $array_o_sec,
                $minimize,
                $s_name,
                $search_file
            );
        }

        $this->assign([
            'Selector'             => self::getSelector(),
            'limit'                => $limit,
            'search'               => $search_key,
            'SearchFile'           => $search_file,
            'SearchFor'            => $search_for,
            'SearchMethod'         => $search_method,
            'Order'                => $order,
            'OrderBY'              => $order_by,
            'OrderBYParse'         => $order_by_parse,
            'se_search'            => $LNG['se_search'],
            'se_limit'             => $LNG['se_limit'],
            'se_asc_desc'          => $LNG['se_asc_desc'],
            'se_filter_title'      => $LNG['se_filter_title'],
            'se_search_in'         => $LNG['se_search_in'],
            'se_type_typee'        => $LNG['se_type_typee'],
            'se_intro'             => $LNG['se_intro'],
            'se_search_title'      => $LNG['se_search_title'],
            'se_contrac'           => $LNG['se_contrac'],
            'se_search_order'      => $LNG['se_search_order'],
            'ac_minimize_maximize' => $LNG['ac_minimize_maximize'],
            'LIST'                 => $result['LIST'] ?? '',
            'PAGES'                => isset($result['PAGES']) ? $result['PAGES'] : '',
        ]);

        $this->display('page.search.default.tpl');

    }

    public function deleteUser(): void
    {
        global $LNG;

        $uid = HTTP::_GP('user', 0);

        PlayerUtil::deletePlayer($uid);

        $this->printMessage($LNG['se_delete_succes_p']);
    }

    public function deletePlanet(): void
    {
        global $LNG;

        $pid = HTTP::_GP('planet', 0);

        PlayerUtil::deletePlanet($pid);

        $this->printMessage($LNG['se_delete_succes_p']);

    }

    public function MyCrazyLittleSearch(
        $specify_items,
        $where_item,
        $specify_where,
        $special_specify,
        $order,
        $order_by,
        $limit,
        $table,
        $page,
        $name_lang,
        $array_o_sec,
        $minimize,
        $s_name,
        $search_file
    ): array {
        global $USER, $LNG;

        $page = max(1, $page);
        $INI = max(0, ($page - 1) * $limit);

        $array_ex = explode(
            ",",
            str_replace("CONCAT(u.username, ' (ID:&nbsp;', p.id_owner, ')')", '', $specify_items)
        );

        if (!$order
            || !in_array($order, $array_o_sec))
        {
            $order = $array_ex[0];
        }

        $count_array = count($array_ex);

        $query_search = "SELECT " . $specify_items . " FROM " . DB_PREFIX . $table." ";
        $query_search .= $where_item . " ";
        $query_search .= $specify_where . " " . $special_specify." ";
        $query_search .= "ORDER BY " . $order . " " . $order_by . " ";
        $query_search .= "LIMIT " . $INI . "," . $limit;
        $final_query = $GLOBALS['DATABASE']->query($query_search);

        $query_c_search = "SELECT COUNT(".$array_ex[0].") AS total FROM ".DB_PREFIX.$table." ";
        $query_c_search .= $where_item." ";
        $query_c_search .= $specify_where." ".$special_specify." ";
        $count_query = $GLOBALS['DATABASE']->getFirstRow($query_c_search);

        if ($count_query['total'] > 0)
        {
            $page_num = ceil($count_query['total'] / $limit);

            $url_for_page = "?page=search&search=" .
                            $search_file .
                            "&search_in=" . ($_GET['search_in'] ?? '') .
                            "&fuki=" . ($_GET['fuki'] ?? '') .
                            "&key_user=" . ($_GET['key_user'] ?? '') .
                            "&key_order=" . ($_GET['key_order'] ?? '') .
                            "&key_acc=" . ($_GET['key_acc'] ?? '') .
                            "&limit=" . $limit;

            if ($page_num > 1)
            {
                $before_page = ($page - 1);
                $next_page = ($page + 1);

                $page_e = "";

                for ($i = 1; $i <= $page_num; $i++)
                {
                    $page_e .= $page == $i ?
                    "&nbsp;".$page."&nbsp;" :
                    " <a href='".$url_for_page."&amp;side=".$i.$minimize."'>".$i."</a> ";
                }

                if (($page - 1) > 0)
                {
                    $before = "<a href='" .
                    $url_for_page .
                    "&amp;side=" .
                    $before_page .
                    $minimize .
                    "'><img src=\"./styles/resource/images/admin/arrowleft.png\" title=" .
                    $LNG['se__before'] . " height=10 width=14></a> ";
                }
                else
                {
                    $before = "";
                }

                if (($page + 1) <= $page_num)
                {
                    $next = "<a href='" .
                    $url_for_page .
                    "&amp;side=".
                    $next_page .
                    $minimize .
                    "'><img src=\"./styles/resource/images/admin/arrowright.png\" title=" .
                    $LNG['se__next'] .
                    " height=10 width=14></a>";
                }
                else
                {
                    $next = "";
                }

                $Search['PAGES'] = '<tr>
                <td colspan="3" style="color:#00CC33;border: 1px lime solid;text-align:center;">' .
                $before .
                '&nbsp;' .
                $page_e .
                '&nbsp;' .
                $next .
                '</td></tr>';
            }

            $search['LIST'] = "<table class = 'table table-dark'>";
            $search['LIST'] .= "<tr>";

            for ($i = 0; $i < $count_array; $i++)
            {
                $search['LIST'] .= "<th>".$name_lang[$i]."</th>";
            }

            if ($table == "users")
            {
                if (allowedTo('ShowAccountDataPage'))
                {
                    $search['LIST'] .= "<th>".$LNG['se_search_info']."</th>";
                }

                if ($USER['authlevel'] == AUTH_ADM)
                {
                    $search['LIST'] .= "<th>".$LNG['button_delete']."</th>";
                }
            }

            if ($table == "planets p")
            {
                if (allowedTo('ShowQuickEditorPage'))
                {
                    $search['LIST'] .= "<th>".$LNG['se_search_edit']."</th>";
                }

                if ($USER['authlevel'] == AUTH_ADM)
                {
                    $search['LIST'] .= "<th>".$LNG['button_delete']."</th>";
                }
            }

            $search['LIST'] .= "</tr>";

            while ($while_result = $GLOBALS['DATABASE']->fetch_num($final_query))
            {
                $search['LIST'] .= "<tr>";
                if ($table == "users")
                {
                    $while_result[3] = (isset($_GET['search']) && $_GET['search'] == "online") ?
                    pretty_time(TIMESTAMP - $while_result[3]) :
                    _date($LNG['php_tdformat'], $while_result[3], $USER['timezone']);

                    $while_result[4] = _date($LNG['php_tdformat'], $while_result[4], $USER['timezone']);

                    $while_result[6] = $LNG['rank_'.$while_result[6]];
                    (($while_result[7] == '1') ?
                    $while_result[7] = "<font color=lime>".$LNG['one_is_no_1']."</font>" :
                    $while_result[7] = $LNG['one_is_no_0']);

                    (($while_result[8] == '1') ?
                    $while_result[8] = "<font color=lime>".$LNG['one_is_no_1']."</font>" :
                    $while_result[8] = $LNG['one_is_no_0']);

                }

                if ($table == "banned")
                {
                    $while_result[2] = _date($LNG['php_tdformat'], $while_result[2], $USER['timezone']);
                    $while_result[3] = _date($LNG['php_tdformat'], $while_result[3], $USER['timezone']);
                }

                if ($table == "alliance")
                {
                    $while_result[4] = _date($LNG['php_tdformat'], $while_result[4], $USER['timezone']);
                }

                if ($table == "planets p")
                {
                    $while_result[3] = pretty_time(TIMESTAMP - $while_result[3]);
                    $while_result[7] = $while_result[7] > 0 ?
                    "<font color=lime>".$LNG['one_is_no_1'] . "</font>"
                    : $LNG['one_is_no_0'];
                }

                for ($i = 0; $i < $count_array; $i++)
                {
                    $search['LIST'] .= "<td>".$while_result[$i]."</td>";
                }

                if ($table == "users")
                {
                    if (allowedTo('ShowQuickEditorPage'))
                    {
                        $search['LIST'] .= "<td>
                        <a href=\"javascript:openEdit('".$while_result[0]."', 'player');\" border=\"0\">
                        <img title=\"".$while_result[1]."\" src=\"./styles/resource/images/admin/GO.png\">
                        </a>
                        </td>";
                    }

                    if ($USER['authlevel'] == AUTH_ADM)
                    {
                        $delete_button = $while_result[0] != $USER['id']
                        || $while_result[0] != ROOT_USER ?
                        '<a href="?page=search&mode=deleteUser&amp;user='.$while_result[0].'" 
                        border="0" 
                        onclick="return confirm(\''.$LNG['ul_sure_you_want_dlte'].' '.$while_result[1].'?\');">
                        <img src="./styles/resource/images/alliance/CLOSE.png" 
                        width="16" 
                        height="16" 
                        title='.$while_result[1].'>
                        </a>' :
                        '-';

                        $search['LIST'] .= "<td>".$delete_button."</td>";
                    }
                }

                if ($table == "planets p")
                {

                    if (allowedTo('ShowQuickEditorPage'))
                    {
                        $search['LIST'] .= "<td>
                        <a href=\"javascript:openEdit('".$while_result[0]."', 'planet');\" 
                        border=\"0\">
                        <img src=\"./styles/resource/images/admin/GO.png\" 
                        title=".$LNG['se_search_edit'].">
                        </a>
                        </td>";
                    }

                    if ($USER['authlevel'] == AUTH_ADM)
                    {
                        $search['LIST'] .= '<td>
                        <a href="?page=search&mode=deletePlanet&amp;planet='.$while_result[0].'" 
                        border="0" 
                        onclick="return confirm(\''.$LNG['se_confirm_planet'] . ' ' .
                        $while_result[1].'\');">
                        <img src="./styles/resource/images/alliance/CLOSE.png" 
                        width="16" 
                        height="16" 
                        title='.$LNG['button_delete'].'>
                        </a>
                        </td>';
                    }
                }

                $search['LIST'] .= "</tr>";
            }

            $search['LIST'] .= "<tr><td colspan=\"20\">" .
            $LNG['se_input_hay'] . "<font color=lime>" .
            $count_query['total'] . "</font>" . $s_name . "</td></tr>";

            $search['LIST'] .= "</table>";

            $GLOBALS['DATABASE']->free_result($final_query);

            return $search;
        }
        else
        {
            $result['LIST'] = "<br>
            <table border='0px' style='background:url(images/Adm/blank.gif);' width='90%'>";
            $result['LIST'] .= "<tr>
            <td style='color:#00CC33;
            border: 2px red solid;' 
            height='25px'>
            <font color=red>".$LNG['se_no_data'].
            "</font>
            </td>
            </tr>";
            $result['LIST'] .= "</table>";
            return $result;
        }
    }

}
