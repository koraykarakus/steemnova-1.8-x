function doit(missionID, planetID) {
	$.getJSON("game.php?page=fleetAjax&ajax=1&mission="+missionID+"&planetID="+planetID, function(data)
	{
		$('#slots').text(data.slots);
		if(typeof data.ships !== "undefined")
		{
			$.each(data.ships, function(elementID, value) {
				$('#elementID'+elementID).text(number_format(value));
			});
		}

		var statustable	= $('#fleetstatusrow');
		var messages	= statustable.find("~tr");
		if(messages.length == MaxFleetSetting) {
			messages.filter(':last').remove();
		}
		var element		= $('<td />').attr('colspan', 8).attr('class', data.code == 600 ? "text-success text-center" : "text-danger text-center").text(data.mess).wrap('<tr />').parent();
		statustable.removeAttr('style').after(element);
	});
}

function galaxy_submit(value) {
	$('#auto').attr('name', value);
	$('#galaxy_form').submit();
}


// on keyboard click <> change page
$(document).on('keydown', function(event){

if  (event.keyCode === 39) {
      $("input[name=systemRight]").trigger('click');
   } else if (event.keyCode === 37) {
       $('input[name=systemLeft]').trigger('click');
   }

});

//on F5 refresh the current page
$(document).on('keydown', function(event){

if  (event.keyCode === 116) {

		$('#galaxySubmit').trigger('click');
 }
});
