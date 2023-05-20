{block name="content"}


<form action="?page=sendMessages&mode=send" method="post">
<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
		<tr>
      <th colspan="2">{$LNG.ma_send_global_message}</th>
    </tr>
    <tr>
      <td>{$LNG.ma_mode}</td>
      <td>{html_options name=type options=$modes}</td>
		</tr>
    <tr>
      <td>{$LNG.se_lang}</td>
      <td>{html_options name=globalmessagelang options=$langSelector}</td>
    </tr>
    <tr>
      <td>{$LNG.ma_subject}</td>
      <td><input name="subject" id="subject" size="40" maxlength="40" value="{$LNG.ma_none}" type="text"></td>
    </tr>
		<tr>
      <td>{$LNG.ma_message} (<span id="cntChars">0</span> / 5000 {$LNG.ma_characters})</td>
      <td>
				<textarea name="text" id="text" cols="40" rows="10" onkeyup="$('#cntChars').text($('#text').val().length);"></textarea>
			</td>
    </tr>
    <tr>
    	<td colspan="2"><input type="submit" value="{$LNG.button_submit}"></td>
    </tr>
    </table>
</form>

{/block}
