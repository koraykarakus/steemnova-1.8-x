{block name="content"}
	<tr>
		<td colspan="2">
			<div id="main" align="left">
				{if $update}
					<p>{sprintf($LNG.upgrade_success,$revision)}</p>
				{else}
					<p>{sprintf($LNG.upgrade_nothingtodo,$revision)}</p>
				{/if}
			</div><br><a href="../index.php"><button style="cursor: pointer;">{$LNG.upgrade_back}</button></a>
		</td>
	</tr>
{/block}