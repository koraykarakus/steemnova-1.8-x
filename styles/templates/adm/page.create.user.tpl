{block name="content"}

<form action="?page=create&mode=createUser" method="post">
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
<tr><th colspan="2">{$LNG.new_title}</th></tr>
<tr><td>{$LNG.user_reg}</td><td><input type="text" name="name"></td></tr>
<tr><td>{$LNG.pass_reg}</td><td><input type="password" name="password"></td></tr>
<tr><td>{$LNG.pass2_reg}</td><td><input type="password" name="password2"></td></tr>
<tr><td>{$LNG.email_reg}</td><td><input type="text" name="email"></td></tr>
<tr><td>{$LNG.email2_reg}</td><td><input type="text" name="email2"></td></tr>
<tr><td>{$LNG.new_coord}</td><td>
<input type="text" name="galaxy" size="1" maxlength="1"> :
<input type="text" name="system" size="3" maxlength="3"> :
<input type="text" name="planet" size="2" maxlength="2"></td></tr>
<tr><td>{$LNG.new_range}</td>
<td>{html_options name=authlevel options=$Selector.auth}</td></tr>
<tr><td>{$LNG.lang_reg}</td>
<td>{html_options name=lang options=$Selector.lang}</td></tr>
<tr><td colspan="2"><input type="submit" value="{$LNG.new_add_user}"></td></tr>
<tr>
   <td colspan="2" style="text-align:left;"><a href="?page=create">{$LNG.new_creator_go_back}</a>&nbsp;<a href="?page=create&amp;mode=user">{$LNG.new_creator_refresh}</a></td>
</tr>
</table>
</form>

{/block}
