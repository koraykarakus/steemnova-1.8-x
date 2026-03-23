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

class MissionFunctions
{
    public $kill = 0;
    public $_fleet = [];
    public $_upd = [];
    public $event_time = 0;

    public function UpdateFleet($option, $val)
    {
        $this->_fleet[$option] = $val;
        $this->_upd[$option] = $val;
    }

    public function setState($val)
    {
        $this->_fleet['fleet_mess'] = $val;
        $this->_upd['fleet_mess'] = $val;

        switch ($val)
        {
            case FLEET_OUTWARD:
                $this->event_time = $this->_fleet['fleet_start_time'];
                break;
            case FLEET_RETURN:
                $this->event_time = $this->_fleet['fleet_end_time'];
                break;
            case FLEET_HOLD:
                $this->event_time = $this->_fleet['fleet_end_stay'];
                break;
        }
    }

    public function SaveFleet()
    {
        if ($this->kill == 1)
        {
            return;
        }

        $param = [];

        $update_query = [];

        foreach ($this->_upd as $option => $val)
        {
            $update_query[] = "`".$option."` = :".$option;
            $param[':'.$option] = $val;
        }

        if (!empty($update_query))
        {
            $sql = 'UPDATE %%FLEETS%% SET ' .
            implode(', ', $update_query) .
            ' WHERE `fleet_id` = :fleet_id;';

            $param[':fleet_id'] = $this->_fleet['fleet_id'];
            Database::get()->update($sql, $param);

            $sql = 'UPDATE %%FLEETS_EVENT%% SET time = :time WHERE `fleetID` = :fleet_id;';
            Database::get()->update($sql, [
                ':time'     => $this->event_time,
                ':fleet_id' => $this->_fleet['fleet_id'],
            ]);
        }
    }

    public function RestoreFleet($on_start = true)
    {
        global $RESOURCE;

        $fleet_data = FleetFunctions::unserialize($this->_fleet['fleet_array']);

        $update_query = [];

        $param = [
            ':metal'      => $this->_fleet['fleet_resource_metal'],
            ':crystal'    => $this->_fleet['fleet_resource_crystal'],
            ':deuterium'  => $this->_fleet['fleet_resource_deuterium'],
            ':darkmatter' => $this->_fleet['fleet_resource_darkmatter'],
            ':planet_id'  => $on_start == true ? $this->_fleet['fleet_start_id'] : $this->_fleet['fleet_end_id'],
        ];

        foreach ($fleet_data as $ship_id => $ship_amount)
        {
            $update_query[] = "p.`".$RESOURCE[$ship_id]."` = p.`".$RESOURCE[$ship_id]."` + :".$RESOURCE[$ship_id];
            $param[':'.$RESOURCE[$ship_id]] = $ship_amount;
        }

        $sql = 'UPDATE %%PLANETS%% as p, %%USERS%% as u SET
		'.implode(', ', $update_query).',
		p.`metal` = p.`metal` + :metal,
		p.`crystal` = p.`crystal` + :crystal,
		p.`deuterium` = p.`deuterium` + :deuterium,
		p.`version` = p.`version` + 1,
		u.`darkmatter` = u.`darkmatter` + :darkmatter
		WHERE p.`id` = :planet_id AND u.id = p.id_owner;';

        Database::get()->update($sql, $param);

        $this->KillFleet();
    }

    public function StoreGoodsToPlanet($on_start = false)
    {
        $sql = 'UPDATE %%PLANETS%% as p, %%USERS%% as u SET
		`metal`			= `metal` + :metal,
		`crystal`		= `crystal` + :crystal,
		`deuterium` 	= `deuterium` + :deuterium,
		`darkmatter`	= `darkmatter` + :darkmatter,
	    `version` = `version` + 1 
        WHERE p.`id` = :planetId AND u.id = p.id_owner;';

        Database::get()->update($sql, [
            ':metal'      => $this->_fleet['fleet_resource_metal'],
            ':crystal'    => $this->_fleet['fleet_resource_crystal'],
            ':deuterium'  => $this->_fleet['fleet_resource_deuterium'],
            ':darkmatter' => $this->_fleet['fleet_resource_darkmatter'],
            ':planetId'   => ($on_start == true ? $this->_fleet['fleet_start_id'] : $this->_fleet['fleet_end_id']),
        ]);

        $this->UpdateFleet('fleet_resource_metal', '0');
        $this->UpdateFleet('fleet_resource_crystal', '0');
        $this->UpdateFleet('fleet_resource_deuterium', '0');
    }

    public function KillFleet()
    {
        $this->kill = 1;
        $sql = 'DELETE %%FLEETS%%, %%FLEETS_EVENT%%
		FROM %%FLEETS%% LEFT JOIN %%FLEETS_EVENT%% on fleet_id = fleetId
		WHERE `fleet_id` = :fleetId';

        Database::get()->delete($sql, [
            ':fleetId' => $this->_fleet['fleet_id'],
        ]);
    }

    public function getLanguage($language = null, $user_id = null)
    {
        if (is_null($language) && !is_null($user_id))
        {
            $sql = 'SELECT lang FROM %%USERS%% WHERE id = :user_id;';
            $language = Database::get()->selectSingle($sql, [
                ':user_id' => $user_id,
            ], 'lang');
        }

        $LNG = new Language($language);
        $LNG->includeData(['L18N', 'FLEET', 'TECH', 'CUSTOM']);
        return $LNG;
    }

    public function savePlanetProduction($planet_id, $start_time)
    {
        $db = Database::get();

        $sql = "SELECT * FROM %%PLANETS%% WHERE id = :planet_id;";

        $planet = $db->selectSingle($sql, [
            ':planet_id' => $planet_id,
        ]);

        $sql = "SELECT * FROM %%USERS%% WHERE id = :userId;";

        $user = $db->selectSingle($sql, [
            ':userId' => $planet['id_owner'],
        ]);

        if (!$planet || !$user) //moon is destroyed and deleted from planets table
        {
            return;
        }

        $user['factor'] = getFactors($user, 'basic', $start_time);

        $planet_updater = new ResourceUpdate();

        $planet_updater->CalcResource($user, $planet, true, $start_time);
    }

}
