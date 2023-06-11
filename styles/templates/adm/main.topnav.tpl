<div style="height:50px;" class="d-flex align-items-center justify-content-center text-white fw-bold">{$LNG.adm_cp_title}</div>
<div class="d-flex justify-content-center align-items-center">
{if $authlevel == $smarty.const.AUTH_ADM}
<select style="width:auto;" class="form-select bg-dark text-white mx-1" id="universe">
{html_options options=$AvailableUnis selected=$UNI}
</select>
{/if}
<a href="admin.php?page=overview" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;{$LNG.adm_cp_index}&nbsp;</a>
{if $authlevel == $smarty.const.AUTH_ADM}
<a href="?page=universe&amp;sid={$sid}" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;{$LNG.mu_universe}&nbsp;</a>
<a href="?page=rights" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;{$LNG.mu_moderation_page}&nbsp;</a>
<a href="?page=rights&amp;mode=users&amp;sid={$sid}" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;{$LNG.ad_authlevel_title}&nbsp;</a>
{/if}
{if $id == 1}
<a href="?page=reset&amp;sid={$sid}" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;{$LNG.re_reset_universe}&nbsp;</a>
{/if}
<a href="javascript:top.location.href='game.php';" target="_top" class="border mx-1 border-danger rounded text-decoration-none text-danger fs-6">&nbsp;{$LNG.adm_cp_logout}&nbsp;</a>
</div>
<script>
$(function() {
	$('#universe').on('change', function(e) {
		parent.frames['Hauptframe'].location.href = parent.frames['Hauptframe'].location.href+'&uni='+$(this).val();
		parent.frames['rightFrame'].location.reload();
	});
});
</script>
