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

class ShowResetPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG;

        $this->assign([
            'button_submit'                  => $LNG['button_submit'],
            're_reset_universe_confirmation' => $LNG['re_reset_universe_confirmation'],
            're_reset_all'                   => $LNG['re_reset_all'],
            're_reset_all'                   => $LNG['re_reset_all'],
            're_defenses_and_ships'          => $LNG['re_defenses_and_ships'],
            're_reset_buldings'              => $LNG['re_reset_buldings'],
            're_buildings_lu'                => $LNG['re_buildings_lu'],
            're_buildings_pl'                => $LNG['re_buildings_pl'],
            're_buldings'                    => $LNG['re_buldings'],
            're_reset_hangar'                => $LNG['re_reset_hangar'],
            're_ships'                       => $LNG['re_ships'],
            're_defenses'                    => $LNG['re_defenses'],
            're_resources_met_cry'           => $LNG['re_resources_met_cry'],
            're_resources_dark'              => $LNG['re_resources_dark'],
            're_resources'                   => $LNG['re_resources'],
            're_reset_invest'                => $LNG['re_reset_invest'],
            're_investigations'              => $LNG['re_investigations'],
            're_ofici'                       => $LNG['re_ofici'],
            're_inve_ofis'                   => $LNG['re_inve_ofis'],
            're_reset_statpoints'            => $LNG['re_reset_statpoints'],
            're_reset_messages'              => $LNG['re_reset_messages'],
            're_reset_banned'                => $LNG['re_reset_banned'],
            're_reset_errors'                => $LNG['re_reset_errors'],
            're_reset_fleets'                => $LNG['re_reset_fleets'],
            're_reset_allys'                 => $LNG['re_reset_allys'],
            're_reset_buddies'               => $LNG['re_reset_buddies'],
            're_reset_rw'                    => $LNG['re_reset_rw'],
            're_reset_notes'                 => $LNG['re_reset_notes'],
            're_reset_moons'                 => $LNG['re_reset_moons'],
            're_reset_planets'               => $LNG['re_reset_planets'],
            're_reset_player'                => $LNG['re_reset_player'],
            're_player_and_planets'          => $LNG['re_player_and_planets'],
            're_general'                     => $LNG['re_general'],
        ]);

        $this->display('ResetPage.tpl');
    }

    public function send(): void
    {
        global $reslist, $resource, $LNG;

        $config = Config::get(Universe::getEmulated());

        foreach ($reslist['build'] as $c_id)
        {
            $dbcol['build'][$c_id] = "`".$resource[$c_id]."` = '0'";
        }

        foreach ($reslist['tech'] as $c_id)
        {
            $dbcol['tech'][$c_id] = "`".$resource[$c_id]."` = '0'";
        }

        foreach ($reslist['fleet'] as $c_id)
        {
            $dbcol['fleet'][$c_id] = "`".$resource[$c_id]."` = '0'";
        }

        foreach ($reslist['defense'] as $c_id)
        {
            $dbcol['defense'][$c_id] = "`".$resource[$c_id]."` = '0'";
        }

        foreach ($reslist['officier'] as $c_id)
        {
            $dbcol['officier'][$c_id] = "`".$resource[$c_id]."` = '0'";
        }

        foreach ($reslist['resstype'][1] as $c_id)
        {
            if (isset($config->{$resource[$c_id].'_start'}))
            {
                $dbcol['resource_planet_start'][$c_id] = "`".$resource[$c_id]."` = ".$config->{$resource[$c_id].'_start'};
            }
        }

        foreach ($reslist['resstype'][3] as $c_id)
        {
            if (isset($config->{$resource[$c_id].'_start'}))
            {
                $dbcol['resource_user_start'][$c_id] = "`".$resource[$c_id]."` = ".$config->{$resource[$c_id].'_start'};
            }
        }

        // Players and Planets

        $db = Database::get();

        $delete_players = (HTTP::_GP('players', 'off') == 'on') ? true : false;
        // delete players and their planets, keep admins.
        if ($delete_players)
        {
            $sql = "DELETE FROM %%USERS%% WHERE authlevel = 0 AND universe = :universe;";
            $db->delete($sql, [
                ':universe' => Universe::getEmulated(),
            ]);

            // delete planets of players.
            $sql = "DELETE FROM %%PLANETS%% 
                    WHERE universe = :universe 
                    AND id NOT IN (
                        SELECT id_planet FROM %%USERS%% WHERE universe = :universe AND authlevel != 0
                    );";

            $db->delete($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_planets = (HTTP::_GP('planets', 'off') == 'on') ? true : false;
        // delete planets, not
        if ($delete_planets)
        {
            $sql = "DELETE FROM %%PLANETS%% 
                    WHERE universe = :universe 
                    AND id NOT IN (
                        SELECT id_planet FROM %%USERS%% WHERE universe = :universe
                    )";

            $db->delete($sql, [
                ':universe' => Universe::getEmulated(),
            ]);

            $sql = "UPDATE %%PLANETS%% SET id_luna = '0' WHERE universe = :universe";

            $db->update($sql, [
                ':universe' => Universe::getEmulated(),
            ]);

        }

        $delete_moons = (HTTP::_GP('moons', 'off') == 'on') ? true : false;
        // delete moons
        if ($delete_moons)
        {
            $sql = "DELETE FROM %%PLANETS%% 
            WHERE planet_type = 3 
            AND universe = :universe";

            $db->delete($sql, [
                ':universe' => Universe::getEmulated(),
            ]);

            $sql = "UPDATE %%PLANETS%% 
            SET id_luna = 0 
            WHERE universe = :universe";

            $db->update($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_defenses = (HTTP::_GP('defenses', 'off') == 'on') ? true : false;
        // delete shipyard & defenses
        if ($delete_defenses)
        {
            $sql = "UPDATE %%PLANETS%% 
            SET " . implode(", ", $dbcol['defense']) . " 
            WHERE universe = :universe";

            $db->update($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_ships = (HTTP::_GP('ships', 'off') == 'on') ? true : false;
        // delete ships
        if ($delete_ships)
        {
            $sql = "UPDATE %%PLANETS%% 
            SET " . implode(", ", $dbcol['fleet']) . " 
            WHERE universe = :universe";

            $db->update($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_hd = (HTTP::_GP('h_d', 'off') == 'on') ? true : false;
        if ($delete_hd)
        {
            $sql = "UPDATE %%PLANETS%% 
            SET b_hangar = :b_hangar, b_hangar_id = :b_hangar_id 
            WHERE universe = :universe";

            $db->update($sql, [
                ':b_hangar'    => 0,
                ':b_hangar_id' => '',
                ':universe'    => Universe::getEmulated(),
            ]);
        }

        $delete_edif_p = (HTTP::_GP('edif_p', 'off') == 'on') ? true : false;
        // fix terra bugs.
        if ($delete_edif_p)
        {
            $sql = "UPDATE %%PLANETS%% 
            SET " . implode(", ", $dbcol['build']) . ", 
            field_current = :field_current
            WHERE planet_type = :planet_type 
            AND universe = :universe";

            $db->update($sql, [
                ':field_current' => 0,
                ':planet_type'   => 1,
                ':universe'      => Universe::getEmulated(),
            ]);
        }

        $delete_edif_l = (HTTP::_GP('edif_l', 'off') == 'on') ? true : false;
        if ($delete_edif_l)
        {
            $sql = "UPDATE %%PLANETS%% 
            SET " . implode(", ", $dbcol['build']) . ", 
            field_current = :field_current WHERE planet_type = :planet_type 
            AND universe = :universe";

            $db->update($sql, [
                ':field_current' => 0,
                ':planet_type'   => 3,
                ':universe'      => Universe::getEmulated(),
            ]);
        }

        $delete_edif = (HTTP::_GP('edif', 'off') == 'on') ? true : false;
        if ($delete_edif)
        {
            $sql = "UPDATE %%PLANETS%% SET b_building = :b_building, b_building_id = :b_building_id 
            WHERE universe = :universe";

            $db->update($sql, [
                ':b_building'    => 0,
                ':b_building_id' => '',
                ':universe'      => Universe::getEmulated(),
            ]);
        }

        $delete_inves = (HTTP::_GP('inves', 'off') == 'on') ? true : false;
        // research & officers
        if ($delete_inves)
        {
            $sql = "UPDATE %%USERS%% 
            SET " . implode(", ", $dbcol['tech']) . " 
            WHERE universe = :universe";

            $db->update($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_ofis = (HTTP::_GP('ofis', 'off') == 'on') ? true : false;
        if ($delete_ofis)
        {
            $sql = "UPDATE %%USERS%% 
            SET " . implode(", ", $dbcol['officier']) . " 
            WHERE universe = :universe";

            $db->update($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_inves_c = (HTTP::_GP('inves_c', 'off') == 'on') ? true : false;
        if ($delete_inves_c)
        {
            $sql = "UPDATE %%USERS%% 
            SET b_tech_planet = :b_tech_planet,
            b_tech = :b_tech,
            b_tech_id = :b_tech_id,
            b_tech_queue = :b_tech_queue
            WHERE universe = :universe";

            $db->update($sql, [
                ':b_tech_planet' => 0,
                ':b_tech'        => 0,
                ':b_tech_id'     => 0,
                ':b_tech_queue'  => '',
                ':universe'      => Universe::getEmulated(),
            ]);
        }

        $delete_dark = (HTTP::_GP('dark', 'off') == 'on') ? true : false;
        // Resources
        if ($delete_dark)
        {
            $sql = "UPDATE %%USERS%% 
            SET " . implode(", ", $dbcol['resource_user_start']) . " 
            WHERE universe = :universe";

            $db->update($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_resources = (HTTP::_GP('resources', 'off') == 'on') ? true : false;
        if ($delete_resources)
        {
            $sql = "UPDATE %%PLANETS%% 
            SET " . implode(", ", $dbcol['resource_planet_start']) . " 
            WHERE universe = :universe";

            $db->update($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_notes = (HTTP::_GP('notes', 'off') == 'on') ? true : false;
        // notes
        if ($delete_notes)
        {
            $sql = "DELETE FROM %%NOTES%% WHERE universe = :universe";

            $db->delete($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_rw = (HTTP::_GP('rw', 'off') == 'on') ? true : false;
        if ($delete_rw)
        {
            $sql = "DELETE FROM %%TOPKB%% WHERE universe = :universe";

            $db->delete($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_friends = (HTTP::_GP('rw', 'off') == 'on') ? true : false;
        if ($delete_friends)
        {
            $sql = "DELETE FROM %%BUDDY%% WHERE universe = :universe";

            $db->delete($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_alliances = (HTTP::_GP('alliances', 'off') == 'on') ? true : false;
        if ($delete_alliances)
        {
            $sql = "DELETE FROM %%ALLIANCE%% 
            WHERE ally_universe = :universe";

            $db->delete($sql, [
                ':universe' => Universe::getEmulated(),
            ]);

            $sql = "UPDATE %%USERS%% 
            SET ally_id = :ally_id,
            ally_register_time = :ally_register_time,
            ally_rank_id = :ally_rank_id
            WHERE universe = :universe";

            $db->update($sql, [
                ':ally_id'            => 0,
                ':ally_register_time' => 0,
                ':ally_rank_id'       => 0,
                ':universe'           => Universe::getEmulated(),
            ]);
        }

        $delete_fleets = (HTTP::_GP('fleets', 'off') == 'on') ? true : false;
        if ($delete_fleets)
        {
            $sql = "DELETE FROM %%FLEETS%% 
            WHERE fleet_universe = :universe";

            $db->delete($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_banneds = (HTTP::_GP('banneds', 'off') == 'on') ? true : false;
        if ($delete_banneds)
        {
            $sql = "DELETE FROM %%BANNED%% 
            WHERE universe = :universe";

            $db->delete($sql, [
                ':universe' => Universe::getEmulated(),
            ]);

            $sql = "UPDATE %%USERS%% 
            SET bana = :bana, 
            banaday = :banaday
            WHERE universe = :universe";

            $db->update($sql, [
                ':bana'     => 0,
                ':banaday'  => 0,
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_messages = (HTTP::_GP('messages', 'off') == 'on') ? true : false;
        if ($delete_messages)
        {
            $sql = "DELETE FROM %%MESSAGES%% 
            WHERE message_universe = :universe";

            $db->delete($sql, [
                ':universe' => Universe::getEmulated(),
            ]);
        }

        $delete_statpoints = (HTTP::_GP('statpoints', 'off') == 'on') ? true : false;
        if ($delete_statpoints)
        {
            // TODO : fix
            // if stat points is removed user should also be removed.
        }

        $this->printMessage($LNG['re_reset_excess']);
    }

}
