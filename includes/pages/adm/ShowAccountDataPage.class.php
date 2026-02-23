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
class ShowAccountDataPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $USER, $LNG;

        $sql = "SELECT `id`, `username`, `authlevel` FROM %%USERS%% 
        WHERE `authlevel` <= :authLevel AND `universe` = :universe ORDER BY `username` ASC;";

        $users = Database::get()->select($sql, [
            ':authLevel' => $USER['authlevel'],
            ':universe'  => Universe::getEmulated(),
        ]);

        $user_list_html = "";
        foreach ($users as $currentUser)
        {
            $user_list_html .= "<option value=\"".$currentUser['id']."\">".$currentUser['username']."&nbsp;&nbsp;(".$LNG['rank_'.$currentUser['authlevel']].")</option>";
        }

        $this->tplObj->loadscript('./scripts/game/filterlist.js');

        $this->assign([
            'Userlist'         => $user_list_html,
            'ac_enter_user_id' => $LNG['ac_enter_user_id'],
            'bo_select_title'  => $LNG['bo_select_title'],
            'button_filter'    => $LNG['button_filter'],
            'button_deselect'  => $LNG['button_deselect'],
            'ac_select_id_num' => $LNG['ac_select_id_num'],
            'button_submit'    => $LNG['button_submit'],
        ]);

        $this->display('page.accountdata.intro.tpl');
    }

    // TODO : cleanup html and JS from php
    public function FilterByID(): void
    {
        global $reslist, $resource, $LNG, $USER;
        $user_id = HTTP::_GP('id_u', 0);
        $user_id_input = HTTP::_GP('id_u2', 0);

        // override select id, if user entered an id inside input
        if ($user_id_input != 0)
        {
            $user_id = $user_id_input;
        }

        $db = Database::get();

        $sql = "SELECT `id`, `authlevel` FROM %%USERS%% 
        WHERE `id` = :userId AND `universe` = :universe;";

        $user = $db->selectSingle($sql, [
            ':userId'   => $user_id,
            ':universe' => Universe::getEmulated(),
        ]);

        if (!$user)
        {
            $this->printMessage($LNG['ac_username_doesnt']);
            return;
        }

        $user_items = '';
        foreach (array_merge($reslist['officier'], $reslist['tech']) as $ID)
        {
            $user_items .= "u.`".$resource[$ID]."`,";
        }

        // COMIENZA SAQUEO DE DATOS DE LA TABLA DE USUARIOS
        $specify_items_user =
        "u.id,u.username,u.email,u.email_2,u.authlevel,u.id_planet,
        u.galaxy,u.system,u.planet,u.user_lastip,u.ip_at_reg,u.darkmatter,
        u.register_time,u.onlinetime,u.urlaubs_modus,u.urlaubs_until,
        u.ally_id,a.ally_name,".$user_items."
        u.ally_register_time,u.ally_rank_id,u.bana,u.banaday";

        $sql = "SELECT " . $specify_items_user . " FROM %%USERS%% as u LEFT JOIN
        %%ALLIANCE%% as a ON a.id = u.ally_id WHERE
        u.id = :userId;";

        $user = $db->selectSingle($sql, [
            ':userId' => $user_id,
        ]);

        if (!$user)
        {
            return;
        }

        $info = $user['user_ua'] ?? null;
        $alianza = $user['ally_name'];
        $suspen = $LNG['one_is_yes_'.$user['bana']];

        $mo = "<a title=\"".pretty_number($user['darkmatter'])."\">".shortly_number($user['darkmatter'])."</a>";

        foreach ($reslist['officier'] as $ID)
        {
            $officier[] = $ID;
        }

        foreach ($reslist['tech'] as $ID)
        {
            $techno[] = $ID;
        }
        $techoffi = "";
        for ($i = 0; $i < max(count($reslist['officier']), count($reslist['tech'])); $i++)
        {
            $techoffi .= isset($techno[$i]) ? "<tr><td>".$LNG['tech'][$techno[$i]].": <font color=aqua>".$user[$resource[$techno[$i]]]."</font></td>" : "<tr><td>&nbsp;</td>";

            $techoffi .= isset($officier[$i]) ? "<td>".$LNG['tech'][$officier[$i]].": <font color=aqua>".$user[$resource[$officier[$i]]]."</font></td></tr>" : "<td>&nbsp;</td></tr>";
        }

        if ($user['bana'] != 0)
        {
            $mas = '<a ref="#" onclick="$(\'#banned\').slideToggle();return false"> '.$LNG['ac_more'].'</a>';

            $sql = "SELECT theme,time,longer,author FROM %%BANNED%% WHERE `who` = :username;";

            $BannedQuery = $db->selectSingle($sql, [
                ':username' => $user['username'],
            ]);

            $sus_longer = _date($LNG['php_tdformat'], $BannedQuery['longer'], $USER['timezone']);
            $sus_time = _date($LNG['php_tdformat'], $BannedQuery['time'], $USER['timezone']);
            $sus_reason = $BannedQuery['theme'];
            $sus_author = $BannedQuery['author'];

        }

        // COMIENZA EL SAQUEO DE DATOS DE LA TABLA DE PUNTAJE
        $SpecifyItemsS =
        "tech_count,defs_count,fleet_count,
        build_count,build_points,tech_points,
        defs_points,fleet_points,tech_rank,
        build_rank,defs_rank,fleet_rank,total_points";

        $sql = "SELECT " . $SpecifyItemsS . " FROM %%USER_POINTS%% WHERE `id_owner` = :userId;";

        $StatQuery = $db->selectSingle($sql, [
            ':userId' => $user_id,
        ]);

        $count_tecno = pretty_number($StatQuery['tech_count']);
        $count_def = pretty_number($StatQuery['defs_count']);
        $count_fleet = pretty_number($StatQuery['fleet_count']);
        $count_builds = pretty_number($StatQuery['build_count']);

        $point_builds = pretty_number($StatQuery['build_points']);
        $point_tecno = pretty_number($StatQuery['tech_points']);
        $point_def = pretty_number($StatQuery['defs_points']);
        $point_fleet = pretty_number($StatQuery['fleet_points']);

        $ranking_tecno = $StatQuery['tech_rank'];
        $ranking_builds = $StatQuery['build_rank'];
        $ranking_def = $StatQuery['defs_rank'];
        $ranking_fleet = $StatQuery['fleet_rank'];

        $total_points = pretty_number($StatQuery['total_points']);

        // COMIENZA EL SAQUEO DE DATOS DE LA ALIANZA
        $AliID = $user['ally_id'];
        $ali_lider = 0;
        $point_tecno_ali = 0;
        $count_tecno_ali = 0;
        $ranking_tecno_ali = 0;
        $point_def_ali = 0;
        $count_def_ali = 0;
        $ranking_def_ali = 0;
        $point_fleet_ali = 0;
        $count_fleet_ali = 0;
        $ranking_fleet_ali = 0;
        $point_builds_ali = 0;
        $count_builds_ali = 0;
        $ranking_builds_ali = 0;
        $total_points_ali = 0;
        $id_aliz = 0;
        $tag = '';
        if ($alianza == 0 && $AliID == 0)
        {
            $alianza = $LNG['ac_no_ally'];
            $AllianceHave = "<span class=\"no_moon\"><img src=\"./styles/resource/images/admin/arrowright.png\" width=\"16\" height=\"10\"/>
                        ".$LNG['ac_alliance']."&nbsp;".$LNG['ac_no_alliance']."</span>";
        }
        elseif ($alianza != null && $AliID != 0)
        {

            $AllianceHave = '<a href="#" onclick="$(\'#alianza\').slideToggle();return false" class="link">
                        <img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"> '.$LNG['ac_alliance'].'</a>';

            $SpecifyItemsA =
            "ally_owner,id,ally_tag,ally_name,ally_web,ally_description,ally_text,ally_request,ally_image,ally_members,ally_register_time";

            $sql = "SELECT " . $SpecifyItemsA . " FROM %%ALLIANCE%% WHERE `ally_name` = :ally_name;";

            $AllianceQuery = $db->selectSingle($sql, [
                ':ally_name' => $alianza,
            ]);

            $alianza = $alianza;
            $id_ali = " (".$LNG['ac_ali_idid']."&nbsp;".$AliID.")";
            $id_aliz = $AllianceQuery['id'];
            $tag = $AllianceQuery['ally_tag'];
            $ali_nom = $AllianceQuery['ally_name'];
            $ali_cant = $AllianceQuery['ally_members'];
            $ally_register_time = _date($LNG['php_tdformat'], $AllianceQuery['ally_register_time'], $USER['timezone']);
            $ali_lider = $AllianceQuery['ally_owner'];
            $ali_web = $AllianceQuery['ally_web'] != null ? "<a href=".$AllianceQuery['ally_web']." target=_blank>".$AllianceQuery['ally_web']."</a>" : $LNG['ac_no_web'];

            if ($AllianceQuery['ally_description'] != null)
            {
                $ali_ext2 = BBCode::parse($AllianceQuery['ally_description']);
                $ali_ext = "<a href=\"#\" rel=\"toggle[externo]\">".$LNG['ac_view_text_ext']."</a>";
            }
            else
            {
                $ali_ext = $LNG['ac_no_text_ext'];
            }

            if ($AllianceQuery['ally_text'] != null)
            {
                $ali_int2 = BBCode::parse($AllianceQuery['ally_text']);
                $ali_int = "<a href=\"#\" rel=\"toggle[interno]\">".$LNG['ac_view_text_int']."</a>";
            }
            else
            {
                $ali_int = $LNG['ac_no_text_int'];
            }

            if ($AllianceQuery['ally_request'] != null)
            {
                $ali_sol2 = BBCode::parse($AllianceQuery['ally_request']);
                $ali_sol = "<a href=\"#\" rel=\"toggle[solicitud]\">".$LNG['ac_view_text_sol']."</a>";
            }
            else
            {
                $ali_sol = $LNG['ac_no_text_sol'];
            }

            if ($AllianceQuery['ally_image'] != null)
            {
                $ali_logo2 = $AllianceQuery['ally_image'];
                $ali_logo = "<a href=\"#\" rel=\"toggle[imagen]\">".$LNG['ac_view_image2']."</a>";
            }
            else
            {
                $ali_logo = $LNG['ac_no_img'];
            }

            $sql = "SELECT `username` FROM %%USERS%% WHERE `id` = :ally_leader;";

            $SearchLeader = $db->selectSingle($sql, [
                ':ally_leader' => $ali_lider,
            ]);

            $ali_lider = $SearchLeader['username'];

            $sql = "SELECT ".$SpecifyItemsS." FROM %%STATPOINTS%% WHERE `id_owner` = :ally_leader AND `stat_type` = '2';";

            $StatQueryAlly = $db->selectSingle($sql, [
                ':ally_leader' => $ali_lider,
            ]);

            $count_tecno_ali = pretty_number($StatQueryAlly['tech_count']);
            $count_def_ali = pretty_number($StatQueryAlly['defs_count']);
            $count_fleet_ali = pretty_number($StatQueryAlly['fleet_count']);
            $count_builds_ali = pretty_number($StatQueryAlly['build_count']);

            $point_builds_ali = pretty_number($StatQueryAlly['build_points']);
            $point_tecno_ali = pretty_number($StatQueryAlly['tech_points']);
            $point_def_ali = pretty_number($StatQueryAlly['defs_points']);
            $point_fleet_ali = pretty_number($StatQueryAlly['fleet_points']);

            $ranking_tecno_ali = pretty_number($StatQueryAlly['tech_rank']);
            $ranking_builds_ali = pretty_number($StatQueryAlly['build_rank']);
            $ranking_def_ali = pretty_number($StatQueryAlly['defs_rank']);
            $ranking_fleet_ali = pretty_number($StatQueryAlly['fleet_rank']);

            $total_points_ali = pretty_number($StatQueryAlly['total_points']);
        }

        $SpecifyItemsPQ = '';
        foreach (array_merge($reslist['fleet'], $reslist['build'], $reslist['defense']) as $ID)
        {
            $SpecifyItemsPQ .= "`".$resource[$ID]."`,";
            $RES[$resource[$ID]] = "<tr><td width=\"150\">".$LNG['tech'][$ID]."</td>";
        }
        $names = "<tr><th class=\"center\" width=\"150\">&nbsp;</th>";

        // COMIENZA EL SAQUEO DE DATOS DE LOS PLANETAS
        $SpecifyItemsP = "planet_type,id,name,galaxy,system,planet,destruyed,diameter,field_current,field_max,temp_min,temp_max,metal,crystal,deuterium,energy,".$SpecifyItemsPQ."energy_used";

        $sql = "SELECT " . $SpecifyItemsP . " FROM %%PLANETS%% WHERE `id_owner` = :userId;";

        $planets = $db->select($sql, [
            ':userId' => $user_id,
        ]);

        $planets_moons = '';
        $resources = '';
        $MoonZ = 0;
        $DestruyeD = 0;
        foreach ($planets as $current_planet)
        {
            if ($current_planet['planet_type'] == 3)
            {
                $Planettt = $current_planet['name']."&nbsp;(".$LNG['ac_moon'].")<br><font color=aqua>["
                            .$current_planet['galaxy'].":".$current_planet['system'].":".$current_planet['planet']."]</font>";

                $Moons = $current_planet['name']."&nbsp;(".$LNG['ac_moon'].")<br><font color=aqua>["
                            .$current_planet['galaxy'].":".$current_planet['system'].":".$current_planet['planet']."]</font>";
                $MoonZ = 1;
            }
            else
            {
                $Planettt = $current_planet['name']."<br><font color=aqua>[".$current_planet['galaxy'].":".$current_planet['system'].":"
                            .$current_planet['planet']."]</font>";
            }

            if ($current_planet["destruyed"] == 0)
            {
                $planets_moons .= "
                <tr>
                    <td>".$Planettt."</td>
                    <td>".$current_planet['id']."</td>
                    <td>".pretty_number($current_planet['diameter'])."</td>
                    <td>".pretty_number($current_planet['field_current'])." / ".pretty_number(CalculateMaxPlanetFields($current_planet))." (".pretty_number($current_planet['field_current'])." / ".pretty_number($current_planet['field_max']).")</td>
                    <td>".pretty_number($current_planet['temp_min'])." / ".pretty_number($current_planet['temp_max'])."</td>"
                    .(allowedTo('ShowQuickEditorPage') ? "<td><a href=\"javascript:openEdit('".$current_planet['id']."', 'planet');\" border=\"0\"><img src=\"./styles/resource/images/admin/GO.png\" title=".$LNG['se_search_edit']."></a></td>" : "").
                "</tr>";

                $SumOfEnergy = ($current_planet['energy'] + $current_planet['energy_used']);

                if ($SumOfEnergy < 0)
                {
                    $Color = "<font color=#FF6600>".shortly_number($SumOfEnergy)."</font>";
                }
                elseif ($SumOfEnergy > 0)
                {
                    $Color = "<font color=lime>".shortly_number($SumOfEnergy)."</font>";
                }
                else
                {
                    $Color = shortly_number($SumOfEnergy);
                }

                $resources .= "
                <tr>
                    <td>".$Planettt."</td>
                    <td><a title=\"".pretty_number($current_planet['metal'])."\">".shortly_number($current_planet['metal'])."</a></td>
                    <td><a title=\"".pretty_number($current_planet['crystal'])."\">".shortly_number($current_planet['crystal'])."</a></td>
                    <td><a title=\"".pretty_number($current_planet['deuterium'])."\">".shortly_number($current_planet['deuterium'])."</a></td>
                    <td><a title=\"".pretty_number($SumOfEnergy)."\">".$Color."</a>/<a title=\"".pretty_number($current_planet['energy'])."\">".shortly_number($current_planet['energy'])."</a></td>
                </tr>";
                $names .= "<th class=\"center\" width=\"60\">".$Planettt."</th>";
                foreach (array_merge($reslist['fleet'], $reslist['build'], $reslist['defense']) as $ID)
                {
                    $RES[$resource[$ID]] .= "<td width=\"60\"><a title=\"".pretty_number($current_planet[$resource[$ID]])."\">".shortly_number($current_planet[$resource[$ID]])."</a></td>";
                }

                $MoonHave = $MoonZ != 0 ? '<a href="#" onclick="$(\'#especiales\').slideToggle();return false" class="link"><img src="./styles/resource/images/admin/arrowright.png" width="16" height="10"/> '.$LNG['moon_build']."</a>" : "<span class=\"no_moon\"><img src=\"./styles/resource/images/admin/arrowright.png\" width=\"16\" height=\"10\"/>".$LNG['moon_build']."&nbsp;".$LNG['ac_moons_no']."</span>";
            }

            $destroyed = '';
            if ($current_planet["destruyed"] > 0)
            {
                $destroyed .= "
                    <tr>
                        <td>".$current_planet['name']."</td>
                        <td>".$current_planet['id']."</td>
                        <td>[".$current_planet['galaxy'].":".$current_planet['system'].":".$current_planet['planet']."]</td>
                        <td>".date("d-m-Y   H:i:s", $current_planet['destruyed'])."</td>
                    </tr>";
                $DestruyeD++;
            }
        }
        $names .= "</tr>";
        foreach (array_merge($reslist['fleet'], $reslist['build'], $reslist['defense']) as $ID)
        {
            $RES[$resource[$ID]] .= "</tr>";
        }

        $build = '';
        foreach ($reslist['build'] as $ID)
        {
            $build .= $RES[$resource[$ID]];
        }

        $fleet = '';
        foreach ($reslist['fleet'] as $ID)
        {
            $fleet .= $RES[$resource[$ID]];
        }

        $defense = '';
        foreach ($reslist['defense'] as $ID)
        {
            $defense .= $RES[$resource[$ID]];
        }

        $this->assign([
            'DestruyeD'          => $DestruyeD,
            'destroyed'          => $destroyed,
            'resources'          => $resources,
            'mo'                 => $mo,
            'names'              => $names,
            'build'              => $build,
            'fleet'              => $fleet,
            'defense'            => $defense,
            'planets_moons'      => $planets_moons,
            'ali_lider'          => $ali_lider,
            'AllianceHave'       => $AllianceHave,
            'point_tecno'        => $point_tecno,
            'count_tecno'        => $count_tecno,
            'ranking_tecno'      => $ranking_tecno,
            'point_def'          => $point_def,
            'count_def'          => $count_def,
            'ranking_def'        => $ranking_def,
            'point_fleet'        => $point_fleet,
            'count_fleet'        => $count_fleet,
            'ranking_fleet'      => $ranking_fleet,
            'point_builds'       => $point_builds,
            'count_builds'       => $count_builds,
            'ranking_builds'     => $ranking_builds,
            'total_points'       => $total_points,
            'point_tecno_ali'    => $point_tecno_ali,
            'count_tecno_ali'    => $count_tecno_ali,
            'ranking_tecno_ali'  => $ranking_tecno_ali,
            'point_def_ali'      => $point_def_ali,
            'count_def_ali'      => $count_def_ali,
            'ranking_def_ali'    => $ranking_def_ali,
            'point_fleet_ali'    => $point_fleet_ali,
            'count_fleet_ali'    => $count_fleet_ali,
            'ranking_fleet_ali'  => $ranking_fleet_ali,
            'point_builds_ali'   => $point_builds_ali,
            'count_builds_ali'   => $count_builds_ali,
            'ranking_builds_ali' => $ranking_builds_ali,
            'total_points_ali'   => $total_points_ali,
            'input_id'           => $user_id,
            'id_aliz'            => $id_aliz,
            'tag'                => $tag,
            'ali_nom'            => $ali_nom ?? null,
            'ali_ext'            => $ali_ext ?? null,
            'ali_ext'            => $ali_ext2 ?? null,
            'ali_int'            => $ali_int ?? null,
            'ali_int'            => $ali_int2 ?? null,
            'ali_sol2'           => $ali_sol2 ?? null,
            'ali_sol'            => $ali_sol ?? null,
            'ali_logo'           => $ali_logo ?? null,
            'ali_logo2'          => $ali_logo2 ?? null,
            'ali_web'            => $ali_web ?? null,
            'ally_register_time' => $ally_register_time ?? null,
            'ali_cant'           => $ali_cant ?? null,
            'alianza'            => $alianza,
            'id'                 => $user['id'],
            'nombre'             => $user['username'],
            'nivel'              => $LNG['rank_'.$user['authlevel']],
            'vacas'              => $LNG['one_is_yes_'.$user['urlaubs_modus']],
            'suspen'             => $suspen,
            'mas'                => $mas ?? null,
            'id_ali'             => $id_ali ?? null,
            'ip'                 => $user['ip_at_reg'],
            'ip2'                => $user['user_lastip'],
            'ipcheck'            => $LNG['one_is_yes_1'],
            'reg_time'           => _date($LNG['php_tdformat'], $user['register_time'], $USER['timezone']),
            'onlinetime'         => _date($LNG['php_tdformat'], $user['onlinetime'], $USER['timezone']),
            'id_p'               => $user['id_planet'],
            'g'                  => $user['galaxy'],
            's'                  => $user['system'],
            'p'                  => $user['planet'],
            'info'               => $info,
            'email_1'            => $user['email'],
            'email_2'            => $user['email_2'],
            'sus_time'           => $sus_time ?? null,
            'sus_longer'         => $sus_longer ?? null,
            'sus_reason'         => $sus_reason ?? null,
            'sus_author'         => $sus_author ?? null,
            'techoffi'           => $techoffi,
            'canedit'            => allowedTo('ShowQuickEditorPage'),

            'buildings_title'             => $LNG['buildings_title'],
            'researchs_title	'            => $LNG['researchs_title'],
            'ships_title'                 => $LNG['ships_title'],
            'defenses_title'              => $LNG['defenses_title'],
            'ac_recent_destroyed_planets' => $LNG['ac_recent_destroyed_planets'],
            'ac_isnodestruyed'            => $LNG['ac_isnodestruyed'],
            'ac_note_k'                   => $LNG['ac_note_k'],
            'ac_leyend'                   => $LNG['ac_leyend'],
            'ac_account_data'             => $LNG['ac_account_data'],
            'ac_name'                     => $LNG['ac_name'],
            'ac_mail'                     => $LNG['ac_mail'],
            'ac_perm_mail'                => $LNG['ac_perm_mail'],
            'ac_auth_level'               => $LNG['ac_auth_level'],
            'ac_on_vacation'              => $LNG['ac_on_vacation'],
            'ac_banned'                   => $LNG['ac_banned'],
            'ac_alliance'                 => $LNG['ac_alliance'],
            'ac_reg_ip'                   => $LNG['ac_reg_ip'],
            'ac_last_ip'                  => $LNG['ac_last_ip'],
            'ac_checkip_title'            => $LNG['ac_checkip_title'],
            'ac_register_time'            => $LNG['ac_register_time'],
            'ac_act_time'                 => $LNG['ac_act_time'],
            'ac_home_planet_id'           => $LNG['ac_home_planet_id'],
            'ac_home_planet_coord'        => $LNG['ac_home_planet_coord'],
            'ac_user_system'              => $LNG['ac_user_system'],
            'ac_ranking'                  => $LNG['ac_ranking'],
            'ac_see_ranking'              => $LNG['ac_see_ranking'],
            'ac_user_ranking'             => $LNG['ac_user_ranking'],
            'ac_points_count'             => $LNG['ac_points_count'],
            'ac_total_points'             => $LNG['ac_total_points'],
            'ac_suspended_title'          => $LNG['ac_suspended_title'],
            'ac_suspended_time'           => $LNG['ac_suspended_time'],
            'ac_suspended_longer'         => $LNG['ac_suspended_longer'],
            'ac_suspended_reason'         => $LNG['ac_suspended_reason'],
            'ac_suspended_autor'          => $LNG['ac_suspended_autor'],
            'ac_info_ally'                => $LNG['ac_info_ally'],
            'ac_leader'                   => $LNG['ac_leader'],
            'ac_tag'                      => $LNG['ac_tag'],
            'ac_name_ali'                 => $LNG['ac_name_ali'],
            'ac_ext_text'                 => $LNG['ac_ext_text'],
            'ac_int_text'                 => $LNG['ac_int_text'],
            'ac_sol_text'                 => $LNG['ac_sol_text'],
            'ac_image'                    => $LNG['ac_image'],
            'ac_ally_web'                 => $LNG['ac_ally_web'],
            'ac_total_members'            => $LNG['ac_total_members'],
            'ac_view_image'               => $LNG['ac_view_image'],
            'ac_urlnow'                   => $LNG['ac_urlnow'],
            'ac_ally_ranking'             => $LNG['ac_ally_ranking'],
            'ac_id_names_coords'          => $LNG['ac_id_names_coords'],
            'ac_diameter'                 => $LNG['ac_diameter'],
            'ac_fields'                   => $LNG['ac_fields'],
            'ac_temperature'              => $LNG['ac_temperature'],
            'se_search_edit'              => $LNG['se_search_edit'],
            'resources_title'             => $LNG['resources_title'],
            'Metal'                       => $LNG['tech'][901],
            'Crystal'                     => $LNG['tech'][902],
            'Deuterium'                   => $LNG['tech'][903],
            'Energy'                      => $LNG['tech'][911],
            'Darkmatter'                  => $LNG['tech'][921],
            'ac_officier_research'        => $LNG['ac_officier_research'],
            'researchs_title'             => $LNG['researchs_title'],
            'officiers_title'             => $LNG['officiers_title'],
            'ac_coords'                   => $LNG['ac_coords'],
            'ac_time_destruyed'           => $LNG['ac_time_destruyed'],
        ]);

        $this->display('page.accountdata.detail.tpl');

        exit;

    }

}

/* OLD
function ShowAccountDataPage()
{
    global $USER, $reslist, $resource, $LNG;

    $template = new template();

    $id_u = HTTP::_GP('id_u', 0);
    if (!empty($id_u))
    {

    }
    $Userlist = "";

    $sql = "SELECT `id`, `username`, `authlevel` FROM %%USERS%% WHERE `authlevel` <= :authLevel AND `universe` = :universe ORDER BY `username` ASC;";

    $UserWhileLogin = Database::get()->select($sql, [
        ':authLevel' => $USER['authlevel'],
        ':universe'  => Universe::getEmulated(),
    ]);

    foreach ($UserWhileLogin as $UserList)
    {
        $Userlist .= "<option value=\"".$UserList['id']."\">".$UserList['username']."&nbsp;&nbsp;(".$LNG['rank_'.$UserList['authlevel']].")</option>";
    }

    $template->loadscript('filterlist.js');
    $template->assign_vars([
        'Userlist'         => $Userlist,
        'ac_enter_user_id' => $LNG['ac_enter_user_id'],
        'bo_select_title'  => $LNG['bo_select_title'],
        'button_filter'    => $LNG['button_filter'],
        'button_deselect'  => $LNG['button_deselect'],
        'ac_select_id_num' => $LNG['ac_select_id_num'],
        'button_submit'    => $LNG['button_submit'],
    ]);
    $template->show('AccountDataPageIntro.tpl');
}
*/
