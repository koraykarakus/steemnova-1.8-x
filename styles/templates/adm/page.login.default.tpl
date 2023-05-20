{block name="content"}

<div id="content">
	<form action="?page=login&mode=validate" method="post">
    <table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
		<tr>
      <th>{$LNG.adm_login}</th>
    </tr>
		<tr>
      <td>
				<div><label style="display:inline-block;width:100px;">{$LNG.adm_username}:</label><input type="text" readonly value="{$username}"></div>
				<div><label style="display:inline-block;width:100px;">{$LNG.adm_password}:</label><input type="password" name="admin_pw"></div>
				<div><input type="submit" value="{$LNG.adm_absenden}"></div>
			</td>
    </tr>
		<tr>
		</tr>
    </table>
	</form>
</div>

{/block}
