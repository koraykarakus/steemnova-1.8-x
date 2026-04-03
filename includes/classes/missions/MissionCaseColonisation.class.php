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

class MissionCaseColonisation extends MissionFunctions implements Mission
{
    public function __construct($fleet)
    {
        $this->_fleet = $fleet;
    }

    public function TargetEvent()
    {
        $db = Database::get();

        $sql = 'SELECT * FROM %%USERS%% WHERE `id` = :user_id;';

        $sender_user = $db->selectSingle($sql, [
            ':user_id' => $this->_fleet['fleet_owner'],
        ]);

        $sender_user['factor'] = getFactors(
            $sender_user,
            'basic',
            $this->_fleet['fleet_start_time']
        );

        $LNG = $this->getLanguage($sender_user['lang']);

        $check_position = PlayerUtil::checkPosition(
            $this->_fleet['fleet_universe'],
            $this->_fleet['fleet_end_galaxy'],
            $this->_fleet['fleet_end_system'],
            $this->_fleet['fleet_end_planet']
        );

        $is_position_free = PlayerUtil::isPositionFree(
            $this->_fleet['fleet_universe'],
            $this->_fleet['fleet_end_galaxy'],
            $this->_fleet['fleet_end_system'],
            $this->_fleet['fleet_end_planet']
        );

        if (!$is_position_free || !$check_position)
        {
            $message = sprintf(
                $LNG['sys_colo_notfree'],
                GetTargetAddressLink($this->_fleet, '')
            );
        }
        else
        {
            $allow_planet_position = PlayerUtil::allowPlanetPosition(
                $this->_fleet['fleet_end_planet'],
                $sender_user
            );
            if (!$allow_planet_position)
            {
                $message = sprintf(
                    $LNG['sys_colo_notech'],
                    GetTargetAddressLink($this->_fleet, '')
                );
            }
            else
            {
                $sql = 'SELECT COUNT(*) as state
				FROM %%PLANETS%%
				WHERE `id_owner`	= :user_id
				AND `planet_type`	= :type
				AND `destroyed`		= :destroyed;';

                $current_planet_count = $db->selectSingle($sql, [
                    ':user_id'   => $this->_fleet['fleet_owner'],
                    ':type'      => 1,
                    ':destroyed' => 0,
                ], 'state');

                $max_planet_count = PlayerUtil::maxPlanetCount($sender_user);

                if ($current_planet_count >= $max_planet_count)
                {
                    $message = sprintf(
                        $LNG['sys_colo_maxcolo'],
                        GetTargetAddressLink($this->_fleet, ''),
                        $max_planet_count
                    );
                }
                else
                {
                    $new_owner_planet = PlayerUtil::createPlanet(
                        $this->_fleet['fleet_end_galaxy'],
                        $this->_fleet['fleet_end_system'],
                        $this->_fleet['fleet_end_planet'],
                        $this->_fleet['fleet_universe'],
                        $this->_fleet['fleet_owner'],
                        $LNG['fcp_colony'],
                        false,
                        $sender_user['authlevel']
                    );

                    if ($new_owner_planet === false)
                    {
                        $message = sprintf(
                            $LNG['sys_colo_badpos'],
                            GetTargetAddressLink($this->_fleet, '')
                        );
                        $this->setState(FLEET_RETURN);
                    }
                    else
                    {
                        $this->_fleet['fleet_end_id'] = $new_owner_planet;
                        $message = sprintf(
                            $LNG['sys_colo_allisok'],
                            GetTargetAddressLink($this->_fleet, '')
                        );

                        PlayerUtil::updateColonyWithStartValues($new_owner_planet);

                        $this->StoreGoodsToPlanet();
                        if ($this->_fleet['fleet_amount'] == 1)
                        {
                            $this->KillFleet();
                        }
                        else
                        {
                            $current_fleet = explode(";", $this->_fleet['fleet_array']);
                            $new_fleet = '';
                            foreach ($current_fleet as $group)
                            {
                                if (empty($group))
                                {
                                    continue;
                                }

                                $class = explode(",", $group);
                                if ($class[0] == 208 && $class[1] > 1)
                                {
                                    $new_fleet .= $class[0].",".($class[1] - 1).";";
                                }
                                elseif ($class[0] != 208 && $class[1] > 0)
                                {
                                    $new_fleet .= $class[0].",".$class[1].";";
                                }
                            }

                            $this->UpdateFleet('fleet_array', $new_fleet);
                            $this->UpdateFleet('fleet_amount', ($this->_fleet['fleet_amount'] - 1));
                            $this->UpdateFleet('fleet_resource_metal', 0);
                            $this->UpdateFleet('fleet_resource_crystal', 0);
                            $this->UpdateFleet('fleet_resource_deuterium', 0);
                        }
                    }
                }
            }
        }

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_colo_mess_from'],
            4,
            $LNG['sys_colo_mess_report'],
            $message,
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->setState(FLEET_RETURN);
        $this->SaveFleet();
    }

    public function EndStayEvent()
    {
        return;
    }

    public function ReturnEvent()
    {
        $this->savePlanetProduction(
            $this->_fleet['fleet_start_id'],
            $this->_fleet['fleet_end_time']
        );

        $this->RestoreFleet();
    }
}
