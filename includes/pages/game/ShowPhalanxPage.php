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

class ShowPhalanxPage extends AbstractGamePage
{
    public static $require_module = MODULE_PHALANX;

    public function __construct()
    {

    }

    public static function allowPhalanx($to_galaxy, $to_system): bool
    {
        global $PLANET, $resource;

        if ($PLANET['galaxy'] != $to_galaxy
            || $PLANET[$resource[42]] == 0
            || !isModuleAvailable(MODULE_PHALANX)
            || $PLANET[$resource[903]] < PHALANX_DEUTERIUM)
        {
            return false;
        }

        $ph_range = self::GetPhalanxRange($PLANET[$resource[42]]);
        $system_min = max(1, $PLANET['system'] - $ph_range);
        $system_max = $PLANET['system'] + $ph_range;

        return $to_system >= $system_min && $to_system <= $system_max;
    }

    public static function GetPhalanxRange($phalanx_lvl): int
    {
        return ($phalanx_lvl == 1) ? 1 : pow($phalanx_lvl, 2) - 1;
    }

    public function show(): void
    {
        global $PLANET, $LNG, $resource;

        $this->initTemplate();
        $this->setWindow('popup');
        $this->tpl_obj->loadscript('phalanx.js');

        $galaxy = HTTP::_GP('galaxy', 0);
        $system = HTTP::_GP('system', 0);
        $planet = HTTP::_GP('planet', 0);

        if (!$this->allowPhalanx($galaxy, $system))
        {
            $this->printMessage($LNG['px_out_of_range']);
        }

        if ($PLANET[$resource[903]] < PHALANX_DEUTERIUM)
        {
            $this->printMessage($LNG['px_no_deuterium']);
        }

        $db = Database::get();

        $sql = "UPDATE %%PLANETS%% SET deuterium = deuterium - :phalanxDeuterium 
        WHERE id = :planetID;";

        $db->update($sql, [
            ':phalanxDeuterium' => PHALANX_DEUTERIUM,
            ':planetID'         => $PLANET['id'],
        ]);

        $sql = "SELECT id, name, id_owner FROM %%PLANETS%% WHERE universe = :universe
		AND galaxy = :galaxy AND system = :system AND planet = :planet AND :type;";

        $target_info = $db->selectSingle($sql, [
            ':universe' => Universe::current(),
            ':galaxy'   => $galaxy,
            ':system'   => $system,
            ':planet'   => $planet,
            ':type'     => 1,
        ]);

        if (empty($target_info))
        {
            $this->printMessage($LNG['px_out_of_range']);
        }

        require 'includes/classes/class.FlyingFleetsTable.php';

        $fleet_table_obj = new FlyingFleetsTable();
        $fleet_table_obj->setPhalanxMode();
        $fleet_table_obj->setUser($target_info['id_owner']);
        $fleet_table_obj->setPlanet($target_info['id']);
        $fleet_table = $fleet_table_obj->renderTable();

        $this->assign([
            'galaxy'     => $galaxy,
            'system'     => $system,
            'planet'     => $planet,
            'name'       => $target_info['name'],
            'fleetTable' => $fleet_table,
        ]);

        $this->display('page.phalanx.default.tpl');
    }
}
