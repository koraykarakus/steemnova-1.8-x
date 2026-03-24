<div style="height:50px;" class="d-flex align-items-center justify-content-center  fw-bold">
	{$LNG.adm_cp_title}</div>
<div class="d-flex justify-content-center align-items-center">
	{if $authlevel == $smarty.const.AUTH_ADM}
		<select style="width:auto;" class="form-select bg-dark  mx-1" id="universe_select">
			{html_options options=$AvailableUnis selected=$UNI}
		</select>
	{/if}
	<a href="admin.php?page=overview" target="Hauptframe"
		class="border mx-1 p-1 border-white rounded text-decoration-none  fs-6">{$LNG.adm_cp_index}</a>
	{if $authlevel == $smarty.const.AUTH_ADM}
		<a href="?page=universe&amp;sid={$sid}" target="Hauptframe"
			class="border mx-1 p-1 border-white rounded text-decoration-none  fs-6">{$LNG.mu_universe}</a>
		<a href="?page=rights" target="Hauptframe"
			class="border mx-1 p-1 border-white rounded text-decoration-none  fs-6">{$LNG.mu_moderation_page}</a>
		<a href="?page=rights&amp;mode=users&amp;sid={$sid}" target="Hauptframe"
			class="border mx-1 p-1 border-white rounded text-decoration-none  fs-6">{$LNG.ad_authlevel_title}</a>
	{/if}
	{if $id == 1}
		<a href="?page=reset&amp;sid={$sid}" target="Hauptframe"
			class="border mx-1 p-1 border-white rounded text-decoration-none  fs-6">{$LNG.re_reset_universe}</a>
	{/if}
	<a href="javascript:top.location.href='game.php?page=overview&amp;mode=show';" target="_top"
		class="border mx-1 p-1 border-danger rounded text-decoration-none text-danger fs-6">{$LNG.adm_cp_logout}</a>
</div>
<script>
	$(document).on('change', '#universe_select', function() {
		const val = $('#universe_select').val();
		if (val === undefined || val === null) return;
		const url = new URL(window.location.href);
		const params = url.searchParams;
		params.set('uni', val);
		window.location.href = url.pathname + '?' + params.toString();
	});
</script>