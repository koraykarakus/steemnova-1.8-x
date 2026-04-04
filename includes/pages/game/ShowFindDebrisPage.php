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
		global $USER, $PLANET, $resource, $pricelist;
		
		$mode = HTTP::_GP('y', '');
		$table = "";
		$range = $PLANET['hangar'] * 4;
        if ($mode == '1')
        {
            $cautare = $GLOBALS['DATABASE']->query("SELECT *from ".PLANETS." where (`der_metal` >0 OR `der_crystal` >0) AND (`system` > '".($PLANET['system'] - $range)."' AND `system` < '".($PLANET['system'] + $range)."') AND `galaxy` = '".$PLANET['galaxy']."' and `planet_type` = '1' ;");
                $table = "<table><tr><td>Galaxy</td><td>System</td><td>Planet</td><td>Debris Metal</td><td>Debris Crystal</td><td>Collect</td></tr>";
                //print_r($cautare);
            if($GLOBALS['DATABASE']->numRows($cautare) > 0)
            while($GalaxyRowPlanet = $GLOBALS['DATABASE']->fetch_array($cautare)){
            
            $GRecNeeded = min(ceil(($GalaxyRowPlanet['der_metal'] + $GalaxyRowPlanet['der_crystal']) / $pricelist[219]['capacity']), $PLANET[$resource[219]]);
            
                $table .= "<tr><td>".$GalaxyRowPlanet['galaxy']."</td><td>".$GalaxyRowPlanet['system']."</td><td>".$GalaxyRowPlanet['planet']."</td><td>".$GalaxyRowPlanet['der_metal']."</td><td>".$GalaxyRowPlanet['der_crystal']."</td><td><a href='javascript:doit(8,".$GalaxyRowPlanet['id'].");'>Collect</a></td></tr>";
            }
            else
            $table .= "<tr><td colspan='5'>There are no debris in your range</td></tr>";
            $table .= "</table>";
        }

		$this->assign(
			[
				'range' => $range,
				'debris' => $table,
				'user_maxfleetsettings' => $USER['settings_fleetactions'],
			]);

		$this->display("page.findDebris.default.tpl");
	}
}
?>