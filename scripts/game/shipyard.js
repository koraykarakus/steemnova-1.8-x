var v = new Date();

function ShipyardInit() 
{
	shipyard = data.queue;
	amount = new DecimalNumber(shipyard[0][1],0);
	// time between planet last update and page refresh
	hangar_id = data.b_hangar_id_plus;
	$('#timeleft').text(data.pretty_time_b_hangar);
	min_build_time = data.min_build_time;
	ShipyardList();
	BuildlistShipyard();
	shipyard_interval = window.setInterval(BuildlistShipyard, 1000);
}

function BuildlistShipyard() {
	var n = new Date();
	// element build time - time elapsed
	var s = shipyard[0][2] - hangar_id - Math.round((n.getTime() - v.getTime()) / 1000);
	s = Math.round(s);

	if (s <= 0) 
	{
		amount.sub('1');
		$('#val_'+shipyard[0][3]).text(function(i, old)
		{
			return ' (' + bd_available + NumberGetHumanReadable(parseInt(old.replace(/.* (.*)\)/, '$1').replace(/\./g, '')) + 1) + ')';
		})
		
		if (amount.toString() == '0') 
		{
			shipyard.shift();
			if (shipyard.length == 0) 
			{
				$("#bx").html(Ready);
				document.getElementById('auftr').options[0] = new Option(Ready);
				document.location.href	= document.location.href;
				window.clearInterval(shipyard_interval);
				return;
			}
			amount = amount.reset(shipyard[0][1]);
			ShipyardList();
		} 
		else 
		{
			document.getElementById('auftr').options[0].innerHTML = amount.toString() + " " + shipyard[0][0] + " " + bd_operating;
		}

		hangar_id = 0;
		v = new Date();
		if (s < 0) 
		{
			s = 0;
		}
	}
	
	$("#bx").html(shipyard[0][0] + " " + GetRestTimeFormat(Math.max(s, min_build_time)));
}

function ShipyardList() 
{
	while (document.getElementById('auftr').length > 0)
		document.getElementById('auftr').options[document.getElementById('auftr').length - 1] = null;

	for (i = 0; i <= shipyard.length - 1; i++) 
	{
		if (i == 0)
		{
			document.getElementById('auftr').options[i] = 
			new Option(amount.toString() + " " + shipyard[i][0] + " " + bd_operating, i);
		}
		else
		{
			document.getElementById('auftr').options[i] = 
			new Option(shipyard[i][1]+ " " + shipyard[i][0] + " " + bd_operating, i);
		}	
	}
}
