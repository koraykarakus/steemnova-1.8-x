{block name="content"}


<form action="" method="post" name="users">
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr>
	<th colspan="7">{$LNG.ad_authlevel_title}</th>
</tr><tr>
	<td colspan="2">
	<select name="id_1" size="20" style="width:80%;">
		{$UserList}
	</select>

	<script type="text/javascript">
		var UserList = new filterlist(document.getElementsByName('id_1')[0]);
	</script>
	<br>
	<a href="?page=rights&sid={$sid}&amp;type=adm">{$LNG.ad_authlevel_aa}</a>&nbsp;
	<a href="?page=rights&sid={$sid}&amp;type=ope">{$LNG.ad_authlevel_oo}</a>&nbsp;
	<a href="?page=rights&sid={$sid}&amp;type=mod">{$LNG.ad_authlevel_mm}</a>&nbsp;
	<a href="?page=rights&sid={$sid}&amp;type=pla">{$LNG.ad_authlevel_jj}</a>&nbsp;
	<a href="?page=rights&sid={$sid}">{$LNG.ad_authlevel_tt}</a>&nbsp;
	<br><br>
	<a href="javascript:UserList.set('^A')" title="{$LNG.bo_select_title} A">A</a>
	<a href="javascript:UserList.set('^B')" title="{$LNG.bo_select_title} B">B</a>
	<a href="javascript:UserList.set('^C')" title="{$LNG.bo_select_title} C">C</a>
	<a href="javascript:UserList.set('^D')" title="{$LNG.bo_select_title} D">D</a>
	<a href="javascript:UserList.set('^E')" title="{$LNG.bo_select_title} E">E</a>
	<a href="javascript:UserList.set('^F')" title="{$LNG.bo_select_title} F">F</a>
	<a href="javascript:UserList.set('^G')" title="{$LNG.bo_select_title} G">G</a>
	<a href="javascript:UserList.set('^H')" title="{$LNG.bo_select_title} H">H</a>
	<a href="javascript:UserList.set('^I')" title="{$LNG.bo_select_title} I">I</a>
	<a href="javascript:UserList.set('^J')" title="{$LNG.bo_select_title} J">J</a>
	<a href="javascript:UserList.set('^K')" title="{$LNG.bo_select_title} K">K</a>
	<a href="javascript:UserList.set('^L')" title="{$LNG.bo_select_title} L">L</a>
	<a href="javascript:UserList.set('^M')" title="{$LNG.bo_select_title} M">M</a>
	<a href="javascript:UserList.set('^N')" title="{$LNG.bo_select_title} N">N</a>
	<a href="javascript:UserList.set('^O')" title="{$LNG.bo_select_title} O">O</a>
	<a href="javascript:UserList.set('^P')" title="{$LNG.bo_select_title} P">P</a>
	<a href="javascript:UserList.set('^Q')" title="{$LNG.bo_select_title} Q">Q</a>
	<a href="javascript:UserList.set('^R')" title="{$LNG.bo_select_title} R">R</a>
	<a href="javascript:UserList.set('^S')" title="{$LNG.bo_select_title} S">S</a>
	<a href="javascript:UserList.set('^T')" title="{$LNG.bo_select_title} T">T</a>
	<a href="javascript:UserList.set('^U')" title="{$LNG.bo_select_title} U">U</a>
	<a href="javascript:UserList.set('^V')" title="{$LNG.bo_select_title} V">V</a>
	<a href="javascript:UserList.set('^W')" title="{$LNG.bo_select_title} W">W</a>
	<a href="javascript:UserList.set('^X')" title="{$LNG.bo_select_title} X">X</a>
	<a href="javascript:UserList.set('^Y')" title="{$LNG.bo_select_title} Y">Y</a>
	<a href="javascript:UserList.set('^Z')" title="{$LNG.bo_select_title} Z">Z</a>

	<BR>
	<INPUT NAME="regexp" onKeyUp="UserList.set(this.value)">
	<INPUT TYPE="button" onClick="UserList.set(this.form.regexp.value)" value="{$LNG.button_filter}">
	<INPUT TYPE="button" onClick="UserList.reset();this.form.regexp.value=''" value="{$LNG.button_deselect}">
	</td>
</tr>
	<td colspan="3"><input type="submit" value="{$LNG.button_submit}"></td>
</tr>
</table>

{/block}
