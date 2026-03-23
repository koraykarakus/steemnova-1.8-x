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
		var tbody = statustable.find('tbody');

		statustable.removeClass('hidden');
		var messages = tbody.find('tr');
		if(messages.length == MaxFleetSetting) {
			messages.last().remove();
		}

		var color = '';
		if (data.code === 600) {
			color = 'success';
		}
		else
		{
			color = 'fail';
		}

		var element = '<tr>' + '<td colspan="8" class="'+color+'">' + data.mess + '</td>' + '</tr>';
		tbody.prepend(element);

		setTimeout(function () {
    	
		tbody.find('tr').last().fadeOut(500, function () {
        $(this).remove();

        // hide table if no messages.
        if (tbody.find('tr').length === 0) {
            statustable.addClass('hidden');
        }
    	});
		}, 3000);

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
