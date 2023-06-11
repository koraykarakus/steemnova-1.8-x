{block name="content"}

<form class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12" method="post" action="?page=passEncripter&mode=send">
	<div class="form-group">
		<span class="text-yellow text-center fw-bold fs-14">{$LNG.et_md5_encripter}</span>
	</div>
	<div class="form-group">
		<label for="md5q" class="text-start my-1 cursor-pointer hover-underline user-select-none w-100">{$LNG.et_pass}</label>
		<input id="md5q" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="md5q" size="80" value="{if isset($md5_md5)}{$md5_md5}{/if}">
	</div>
	<div class="form-group">
		<label for="md5w" class="text-start my-1 cursor-pointer hover-underline user-select-none w-100">{$LNG.et_result}</label>
		<input id="md5w" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="md5w" size="80" value="{if isset($md5_enc)}{$md5_enc}{/if}" readonly>
	</div>
	<div class="form-group">
		<input type="submit" class="btn btn-primary text-white w-100 my-2" value="{$LNG.et_encript}">
	</div>

</form>
{/block}
