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
class ShowGiveawayPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $reslist;

        $this->assign([
            'reslist' => $reslist,
        ]);

        $this->display("page.giveaway.default.tpl");
    }

    public function send(): void
    {
        global $LNG, $resource, $reslist;

        $planet = HTTP::_GP('planet', 0);
        $moon = HTTP::_GP('moon', 0);
        $main_planet = HTTP::_GP('mainplanet', 0);
        $no_inactive = HTTP::_GP('no_inactive', 0);

        if (!$planet
            && !$moon)
        {
            $this->printMessage($LNG['ga_selectplanettype']);
        }

        $planet_in = [];

        if ($planet)
        {
            $planet_in[] = "'1'";
        }

        if ($moon)
        {
            $planet_in[] = "'3'";
        }

        $data = [];

        $data_ids = array_merge(
            $reslist['resstype'][1],
            $reslist['resstype'][3],
            $reslist['build'],
            $reslist['tech'],
            $reslist['fleet'],
            $reslist['defense'],
            $reslist['officier']
        );

        $log_old = [];
        $log_new = [];

        foreach ($data_ids as $c_id)
        {
            $amount = max(0, round(HTTP::_GP('element_'.$c_id, 0.0)));
            $data[] = $resource[$c_id]." = ".$resource[$c_id]." + ".$amount;

            $log_old[$c_id] = 0;
            $log_new[$c_id] = $amount;
        }

        $sql = "UPDATE %%PLANETS%% p INNER JOIN %%USERS%% u ON p.id_owner = u.id";

        if ($main_planet == true)
        {
            $sql .= " AND p.id = u.id_planet";
        }

        if ($no_inactive == true)
        {
            $sql .= " AND u.onlinetime > ".(TIMESTAMP - INACTIVE);
        }

        $sql .= " SET ".implode(', ', $data)." WHERE p.universe = :universe AND p.planet_type IN (".implode(',', $planet_in).")";

        Database::get()->update($sql, [
            ':universe' => Universe::getEmulated(),
        ]);

        $log = new Log(4);
        $log->target = 0;
        $log->old = $log_old;
        $log->new = $log_new;
        $log->save();

        $this->printMessage($LNG['ga_success']);
    }

}
