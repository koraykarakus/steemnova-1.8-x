{block name="content"}

<form action="admin.php?page=dump&mode=dump" class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12" method="post">
	<input type="hidden" name="action" value="dump">
	<div class="form-group">
		<span class="text-yellow text-center fw-bold fs-14">{$LNG.du_header}</span>
	</div>
	<div class="form-group d-flex justify-content-start">
		<input type="checkbox" id="selectAll">
		<label class="text-start my-1 cursor-pointer hover-underline user-select-none" for="selectAll">&nbsp;{$LNG.du_select_all_tables}</label>
	</div>
	<div class="form-group">
		<span>{$LNG.du_choose_tables}</span>
		{html_options multiple="multiple" class="w-100" size="10" name="dbtables[]" id="dbtables" values=$dumpData.sqlTables output=$dumpData.sqlTables}
	</div>
	<div class="form-group">
		<input class="btn btn-primary text-white w-100 my-2" type="submit" value="{$LNG.du_submit}">
	</div>
</form>

<script>
$(function() {
	$('#selectAll').on('click', function() {
		if($('#selectAll').prop('checked') === true)
		{
			$('#dbtables').val(function() {
				return $(this).children().map(function() {
					return $(this).attr('value');
				}).toArray();
			});
		}
		else
		{
			$('#dbtables').val(null);
		}
	});
});
</script>

{/block}
