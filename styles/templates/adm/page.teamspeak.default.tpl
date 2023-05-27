{block name="content"}

<script type='text/javascript'>
function change2(){
	$("#lang_udp label").text("{$ts_udpport}:");
	document.getElementsByName("ts_v")[0].checked = true;
	$(".v3only").addClass('d-none');
}
function change3(){
	$("#lang_udp label").text("{$ts_server_query}:");
	document.getElementsByName("ts_v")[1].checked = true;
	$(".v3only").removeClass('d-none');
}
</script>

<form class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12" action="?page=teamspeak&mode=saveSettings" method="post">
<input type="hidden" name="opt_save" value="1">
<div class="form-gorup d-flex justify-content-between">
	<span>{$ts_settings}</span>
</div>
<div class="form-gorup d-flex my-1 p-2 ">
	<label class="text-start my-1 cursor-pointer hover-underline" for="ts_on">{$ts_active}</label>
	<input id="ts_on" class="mx-2" name="ts_on"{if $ts_on == 1} checked="checked"{/if} type="checkbox">
</div>
<div class="form-gorup d-flex my-1 p-2 align-items-center">
	<label class="text-start my-1 cursor-pointer hover-underline" for="">{$ts_version}</label>
	<input class="mx-2" type="radio" name="ts_v" value="2" onclick="change2();"> 2
  <input class="mx-2" type="radio" name="ts_v" value="3" onclick="change3();"> 3
</div>
<div class="form-gorup d-flex flex-column my-1 p-2 ">
	<label class="text-start my-1 cursor-pointer hover-underline" for="ts_ip">{$ts_serverip}:</label>
	<input id="ts_ip" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="ts_ip" maxlength="15" size="10" value="{$ts_ip}" type="text">
</div>
<div class="form-gorup d-flex flex-column my-1 p-2 ">
	<label class="text-start my-1 cursor-pointer hover-underline" for="ts_tcp">{$ts_tcpport}:</label>
	<input id="ts_tcp" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="ts_tcp" maxlength="5" size="10" value="{$ts_tcp}" type="text">
</div>
<div id="lang_udp" class="form-gorup d-flex flex-column my-1 p-2 ">
	<label class="text-start my-1 cursor-pointer hover-underline" for="ts_udp">{$ts_udpport}:</label>
	<input id="ts_udp" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="ts_udp" maxlength="5" size="10" value="{$ts_udp}" type="text">
</div>
<div class="form-gorup d-flex flex-column my-1 p-2 v3only">
	<label class="text-start my-1 cursor-pointer hover-underline" for="ts_login">{$ts_sq_login}:</label>
	<input id="ts_login" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="ts_login" size="20" value="{$ts_login}" type="text">
</div>
<div class="form-gorup d-flex flex-column my-1 p-2 v3only">
	<label class="text-start my-1 cursor-pointer hover-underline" for="ts_password">{$ts_sq_pass}:</label>
	<input id="ts_password" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="ts_password" size="20" value="{$ts_password}" type="password">
</div>
<div class="form-gorup d-flex flex-column my-1 p-2 ">
	<label class="text-start my-1 cursor-pointer hover-underline" for="ts_to">{$ts_timeout}:</label>
	<input id="ts_to" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="ts_to" maxlength="2" size="10" value="{$ts_to}" type="text">
</div>
<div class="form-gorup d-flex flex-column my-1 p-2 ">
	<label class="text-start my-1 cursor-pointer hover-underline" for="ts_cron">{$ts_lng_cron}:</label>
	<input id="ts_cron" class="form-control py-1 bg-dark text-white my-1 border border-secondary" name="ts_cron" maxlength="2" size="10" value="{$ts_cron}" type="text">
</div>
<div class="form-gorup d-flex flex-column my-1 p-2 ">
	<input class="btn btn-primary text-white" value="{$se_save_parameters}" type="submit">
</div>
</form>

<script type="text/javascript">
change{$ts_v}();
</script>

{/block}
