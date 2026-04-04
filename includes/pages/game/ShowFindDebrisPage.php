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
		
		$table = "";
		$range = $PLANET['hangar'] * 4;
        
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

        $table = "<table><tr><td>Galaxy</td><td>System</td><td>Planet</td><td>Debris Metal</td><td>Debris Crystal</td><td>Collect</td></tr>";
        //print_r($cautare);
        if(count($debris_data) > 0)
        foreach($debris_data as $c_row){
        
        $GRecNeeded = min(ceil(($c_row['der_metal'] + $c_row['der_crystal']) / $PRICELIST[219]['capacity']), $PLANET[$RESOURCE[219]]);
        
            $table .= "<tr><td>" . 
            $c_row['galaxy'] . 
            "</td><td>" . 
            $c_row['system'] . 
            "</td><td>" . 
            $c_row['planet'] . 
            "</td><td>" . 
            $c_row['der_metal'] . 
            "</td><td>" . 
            $c_row['der_crystal'] . 
            "</td><td><a href='javascript:doit(8," . 
            $c_row['id'] . 
            ");'>Collect</a></td></tr>";
        }
        else
        $table .= "<tr><td colspan='5'>There are no debris in your range</td></tr>";
        $table .= "</table>";

		$this->assign([
            'range' => $range,
            'debris' => $table,
            'user_maxfleetsettings' => $USER['settings_fleetactions'],
		]);

		$this->display("page.findDebris.default.tpl");
	}
}
?>