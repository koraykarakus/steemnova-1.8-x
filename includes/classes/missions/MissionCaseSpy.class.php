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

class MissionCaseSpy extends MissionFunctions implements Mission
{
    public function __construct($Fleet)
    {
        $this->_fleet = $Fleet;
    }

    public function TargetEvent()
    {
        global $PRICELIST, $RESLIST, $RESOURCE;

        $fail_return = function ()
        {
            $this->setState(FLEET_RETURN);
            $this->SaveFleet();
        };

        $db = Database::get();

        $sql = 'SELECT * FROM %%USERS%% WHERE id = :user_id;';
        $sender_user = $db->selectSingle($sql, [
            ':user_id' => $this->_fleet['fleet_owner'],
        ]);

        $target_user = $db->selectSingle($sql, [
            ':user_id' => $this->_fleet['fleet_target_owner'],
        ]);

        $sql = 'SELECT * FROM %%PLANETS%% WHERE id = :planet_id;';
        $target_planet = $db->selectSingle($sql, [
            ':planet_id' => $this->_fleet['fleet_end_id'],
        ]);

        $sql = 'SELECT name FROM %%PLANETS%% WHERE id = :planet_id;';
        $sender_planet = $db->selectSingle($sql, [
            ':planet_id' => $this->_fleet['fleet_start_id'],
        ]);

        if (!$sender_user
            || !$target_user
            || !$target_planet
            || !$sender_planet)
        {
            return $fail_return();
        }

        $LNG = $this->getLanguage($sender_user['lang']);

        $sender_user['factor'] = getFactors($sender_user, 'basic', $this->_fleet['fleet_start_time']);
        $target_user['factor'] = getFactors($target_user, 'basic', $this->_fleet['fleet_start_time']);

        $planet_updater = new ResourceUpdate();
        list($target_user, $target_planet) = $planet_updater->CalcResource($target_user, $target_planet, true, $this->_fleet['fleet_start_time']);

        $sql = 'SELECT * FROM %%FLEETS%%
		WHERE fleet_end_id 		= :planet_id
		AND fleet_mission 		= 5
		AND fleet_start_time 	<= :time
		AND fleet_end_stay 		>= :time;';

        $target_stay_fleets = $db->select($sql, [
            ':planet_id' => $this->_fleet['fleet_end_id'],
            ':time'      => TIMESTAMP,
        ]);

        foreach ($target_stay_fleets as $fleet_row)
        {
            $fleet_data = FleetFunctions::unserialize($fleet_row['fleet_array']);
            foreach ($fleet_data as $ship_id => $ship_amount)
            {
                $target_planet[$RESOURCE[$ship_id]] += $ship_amount;
            }
        }

        $fleet_amount = $this->_fleet['fleet_amount'] * (1 + $sender_user['factor']['SpyPower']);

        $sender_spy_tech = max($sender_user['spy_tech'], 1);
        $target_spy_tech = max($target_user['spy_tech'], 1);

        $tech_difference = abs($sender_spy_tech - $target_spy_tech);
        $min_amount = ($sender_spy_tech > $target_spy_tech ? -1 : 1) * pow($tech_difference * SPY_DIFFENCE_FACTOR, 2);
        $spy_fleet = $fleet_amount >= $min_amount;
        $spy_def = $fleet_amount >= $min_amount + 1 * SPY_VIEW_FACTOR;
        $spy_build = $fleet_amount >= $min_amount + 3 * SPY_VIEW_FACTOR;
        $spy_techno = $fleet_amount >= $min_amount + 5 * SPY_VIEW_FACTOR;

        $class_ids[900] = array_merge($RESLIST['resstype'][1], $RESLIST['resstype'][2]);

        if ($spy_fleet)
        {
            $class_ids[200] = $RESLIST['fleet'];
        }

        if ($spy_def)
        {
            $class_ids[400] = array_merge($RESLIST['defense'], $RESLIST['missile']);
        }

        if ($spy_build)
        {
            $class_ids[0] = $RESLIST['build'];
        }

        if ($spy_techno)
        {
            $class_ids[100] = $RESLIST['tech'];
        }

        $target_chance = mt_rand(0, ceil(min(($fleet_amount / 4) * ($target_spy_tech / $sender_spy_tech), 100)));
        $spy_chance = mt_rand(0, 100);
        $spy_data = [];

        foreach ($class_ids as $class_id => $element_ids)
        {
            foreach ($element_ids as $element_id)
            {
                if (isset($target_user[$RESOURCE[$element_id]]))
                {
                    $spy_data[$class_id][$element_id] = $target_user[$RESOURCE[$element_id]];
                }
                else
                {
                    $spy_data[$class_id][$element_id] = $target_planet[$RESOURCE[$element_id]];
                }
            }

            if ($sender_user['spyMessagesMode'] == 1)
            {
                $spy_data[$class_id] = array_filter($spy_data[$class_id]);
            }
        }

        // I'm use template class here, because i want to exclude HTML in PHP.

        require_once 'includes/classes/class.template.php';

        $template = new template();

        $template->caching = true;
        $template->compile_id = $sender_user['lang'];
        $template->loadFilter('output', 'trimwhitespace');
        list($tpl_dir) = $template->getTemplateDir();
        $template->setTemplateDir($tpl_dir.'game/');
        $template->assign_vars([
            'spyData'      => $spy_data,
            'targetPlanet' => $target_planet,
            'targetChance' => $target_chance,
            'spyChance'    => $spy_chance,
            'isBattleSim'  => ENABLE_SIMULATOR_LINK == true && isModuleAvailable(MODULE_SIMULATOR),
            'title'        => sprintf($LNG['sys_mess_head'],
                            $target_planet['name'],
                            $target_planet['galaxy'],
                            $target_planet['system'],
                            $target_planet['planet'],
                            _date($LNG['php_tdformat'],
                                $this->_fleet['fleet_end_time'],
                                $sender_user['timezone'],
                                $LNG
                            )),
        ]);

        $template->assign_vars([
            'LNG' => $LNG,
        ], false);

        $spy_report = $template->fetch('styles/templates/game/shared.mission.spyReport.tpl');

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_mess_qg'],
            0,
            $LNG['sys_mess_spy_report'],
            $spy_report,
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $LNG = $this->getLanguage($target_user['lang']);
        $target_message = $LNG['sys_mess_spy_ennemyfleet'] ." ". $sender_planet['name'];

        if ($this->_fleet['fleet_start_type'] == 3)
        {
            $target_message .= $LNG['sys_mess_spy_report_moon'].' ';
        }

        $text = '<a href="game.php?page=galaxy&amp;galaxy=%1$s&amp;system=%2$s">[%1$s:%2$s:%3$s]</a> %7$s
		%8$s <a href="game.php?page=galaxy&amp;galaxy=%4$s&amp;system=%5$s">[%4$s:%5$s:%6$s]</a>';

        $target_message .= sprintf(
            $text,
            $this->_fleet['fleet_start_galaxy'],
            $this->_fleet['fleet_start_system'],
            $this->_fleet['fleet_start_planet'],
            $this->_fleet['fleet_end_galaxy'],
            $this->_fleet['fleet_end_system'],
            $this->_fleet['fleet_end_planet'],
            $LNG['sys_mess_spy_seen_at'],
            $target_planet['name']
        );

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_target_owner'],
            0,
            $LNG['sys_mess_spy_control'],
            0,
            $LNG['sys_mess_spy_activity'],
            $target_message,
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        if ($target_chance >= $spy_chance)
        {
            $config = Config::get($this->_fleet['fleet_universe']);
            $where_col = $this->_fleet['fleet_end_type'] == 3 ? "id_moon" : "id";

            $sql = 'UPDATE %%PLANETS%% SET
			debris_metal	= debris_metal + :metal,
			debris_crystal = debris_crystal + :crystal
			WHERE '.$where_col.' = :planet_id;';

            $db->update($sql, [
                ':metal'     => $fleet_amount * $PRICELIST[210]['cost'][901] * $config->debris_percentage_fleet / 100,
                ':crystal'   => $fleet_amount * $PRICELIST[210]['cost'][902] * $config->debris_percentage_fleet / 100,
                ':planet_id' => $this->_fleet['fleet_end_id'],
            ]);

            $this->KillFleet();
        }
        else
        {
            $this->setState(FLEET_RETURN);
            $this->SaveFleet();
        }
    }

    public function EndStayEvent()
    {
        return;
    }

    public function ReturnEvent()
    {
        $this->RestoreFleet();
    }
}
