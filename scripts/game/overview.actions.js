$(function() {
	$('#tabs').tabs();
});

function checkrename()
{
	var newname = $('#name').val();

		$.ajax({
		type: 'POST',
		url: 'game.php?page=overview&mode=rename&ajax=1&name=' + newname,
		dataType: 'json',
		success: function (data) {

			alert(data);

		}});
}

function checkcancel()
{
	var planetName = $('#planetName').val();

		$.ajax({
		type: 'POST',
		url: 'game.php?page=overview&mode=delete&ajax=1&planetName=' + planetName,
		dataType: 'json',
		success: function (data) {

			alert(data);

		}});


}
