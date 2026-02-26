{block name="title" prepend}{$LNG.siteTitleDisclamer}{/block}
{block name="content"}
	<table id="disclaimer_table">
		<tbody>
			<tr>
				<th>{$LNG.disclamerLabelAddress}</th>
				<td>{$disclaimer_address}</td>
			</tr>
			<tr>
				<th>{$LNG.disclamerLabelPhone}</th>
				<td>{$disclaimer_phone}</td>
			</tr>
			<tr>
				<th>{$LNG.disclamerLabelMail}</th>
				<td><a href="mailto:{$disclaimer_mail}" class="text-decoration-none"
						style="color:#1e90ff;">{$disclaimer_mail}</a>
				</td>
			</tr>
		</tbody>
	</table>

	<div id="disclaimer_notice">
		<strong>{$LNG.disclamerLabelNotice}</strong>
		<p>{$disclaimer_notice}</p>
	</div>
{/block}