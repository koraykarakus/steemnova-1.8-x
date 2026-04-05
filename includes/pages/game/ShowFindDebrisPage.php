<?php

class ShowFindDebrisPage extends AbstractGamePage
{
    public static $require_module = MODULE_FIND_DEBRIS;

	function __construct() 
	{
		parent::__construct();
	}
	
	public function show(): void
    {
		global $USER, $PLANET, $RESOURCE, $PRICELIST;
		
		$range = $PLANET['shipyard'] * 4;
        
        $db = Database::get();

        $sql = "SELECT * FROM %%PLANETS%% 
        WHERE (`debris_metal` > 0 OR `debris_crystal` > 0) 
        AND (`system` > :system_min  
        AND `system` < :system_max) 
        AND `galaxy` = :galaxy  
        AND `planet_type` = :planet_type";

        $debris_data = $db->select($sql,[
            ':system_min' => $PLANET['system'] - $range,
            ':system_max' => $PLANET['system'] + $range,
            ':galaxy' => $PLANET['galaxy'],
            ':planet_type' => 1,
        ]);

        foreach($debris_data as &$c_row)
        {
            $c_row['needed_recycler_num'] = min(ceil(($c_row['debris_metal'] + $c_row['debris_metal']) / $PRICELIST[219]['capacity']), $PLANET[$RESOURCE[219]]);
            $c_row['debris_metal'] = pretty_number($c_row['debris_metal']); 
            $c_row['debris_crystal'] = pretty_number($c_row['debris_crystal']);
        }
        unset($c_row);
        
		$this->assign([
            'debris_data' => $debris_data,
            'range' => $range,
            'user_maxfleetsettings' => $USER['settings_fleetactions'],
		]);

		$this->display("page.findDebris.default.tpl");
	}
}
?>