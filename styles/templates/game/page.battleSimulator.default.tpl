{block name="title" prepend}{$LNG.lm_battlesim}{/block}
{block name="content"}
<form id="form" name="battlesim">
	<input type="hidden" name="slots" id="slots" value="{$slots + 1}">
	
	<table class="table-gow table_full">
		<thead>
			<tr>
				<th colspan="2">{$LNG.lm_battlesim}</th>
			</tr>
			<tr>
				<th colspan="2">
					<span class="color-blue">{$LNG.bs_steal}</span>			
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<label for="steal_metal">{$LNG.tech.901}:</label>
				</td>
				<td>
					<input id="steal_metal"
					type="text" value="{if isset($battle_input.0.1.901)}{$battle_input.0.1.901}{else}0{/if}"
					name="battle_input[0][1][901]">
				</td>
			</tr>
			<tr>
				<td>
					<label for="steal_crystal">{$LNG.tech.902}:</label>
				</td>
				<td>
					<input id="steal_crystal"
					type="text" value="{if isset($battle_input.0.1.902)}{$battle_input.0.1.902}{else}0{/if}"
					name="battle_input[0][1][902]">
				</td>
			</tr>
			<tr>
				<td>
					<label for="steal_deuterium">{$LNG.tech.903}:</label>
				</td>
				<td>
					<input id="steal_deuterium"
								type="text" value="{if isset($battle_input.0.1.903)}{$battle_input.0.1.903}{else}0{/if}"
								name="battle_input[0][1][903]">
				</td>
			</tr>
		</tbody>
	</table>

	<table class="table-gow table_full">
		<thead>
			<tr>
				<td>
					<input type="button" onClick="return add();" value="{$LNG.bs_add_acs_slot}">
				</td>
			</tr>
		</thead>
	</table>
	
	<table class="table-gow table_full">
		<tbody>
			<tr>
				<td style="padding: 0;">
					<div style="max-width:600px" id="tabs">
						<ul>
							{section name=tab start=0 loop=$slots}
								<li>
									<a href="#tabs-{$smarty.section.tab.index}">
										{$LNG.bs_acs_slot}{$smarty.section.tab.index + 1}
									</a>
								</li>
							{/section}
						</ul>
						{section name=content start=0 loop=$slots}
							<div style="max-width:600px" id="tabs-{$smarty.section.content.index}">
								<table class="table-gow table_full">
									<tr>
										<th>{$LNG.bs_techno}</th>
										<th>{$LNG.bs_atter}</th>
										<th>{$LNG.bs_deffer}</th>
									</tr>
									<tr>
										<td></td>
										<td><button class="reset">{$LNG.bs_reset}</button></td>
										<td><button class="reset">{$LNG.bs_reset}</button></td>
									</tr>
									<tr>
										<td>{$LNG.tech.109}:</td>
										<td>
											<input
												class=""
												type="text" size="10"
												value="{if isset($battle_input.{$smarty.section.content.index}.0.109)}{$battle_input.{$smarty.section.content.index}.0.109}{else}0{/if}"
												name="battle_input[{$smarty.section.content.index}][0][109]">
										</td>
										<td>
											<input
												class=""
												type="text" size="10"
												value="{if isset($battle_input.{$smarty.section.content.index}.1.109)}{$battle_input.{$smarty.section.content.index}.1.109}{else}0{/if}"
												name="battle_input[{$smarty.section.content.index}][1][109]">
										</td>
									</tr>
									<tr>
										<td>{$LNG.tech.110}:</td>
										<td><input
												class="text-center"
												type="text" size="10"
												value="{if isset($battle_input.{$smarty.section.content.index}.0.110)}{$battle_input.{$smarty.section.content.index}.0.110}{else}0{/if}"
												name="battle_input[{$smarty.section.content.index}][0][110]"></td>
										<td><input
												class="text-center"
												type="text" size="10"
												value="{if isset($battle_input.{$smarty.section.content.index}.1.110)}{$battle_input.{$smarty.section.content.index}.1.110}{else}0{/if}"
												name="battle_input[{$smarty.section.content.index}][1][110]"></td>
									</tr>
									<tr>
										<td>{$LNG.tech.111}:</td>
										<td><input
												class=""
												type="text" size="10"
												value="{if isset($battle_input.{$smarty.section.content.index}.0.111)}{$battle_input.{$smarty.section.content.index}.0.111}{else}0{/if}"
												name="battle_input[{$smarty.section.content.index}][0][111]"></td>
										<td><input
												class=""
												type="text" size="10"
												value="{if isset($battle_input.{$smarty.section.content.index}.1.111)}{$battle_input.{$smarty.section.content.index}.1.111}{else}0{/if}"
												name="battle_input[{$smarty.section.content.index}][1][111]"></td>
									</tr>
								</table>
								<br>
								<table class="table-gow table_full">
									<tr>
										<td style="width:50%;padding:0">
											<table>
												<tr>
													<th>{$LNG.bs_names}</th>
													<th>{$LNG.bs_atter}</th>
													<th>{$LNG.bs_deffer}</th>
												</tr>
												<tr>
													<td></td>
													<td>
														<button class="reset">{$LNG.bs_reset}</button>
													</td>
													<td>
														<button class="reset">{$LNG.bs_reset}</button>
													</td>
												</tr>
												{foreach $fleet_list as $id}
													<tr>
														<td>{$LNG.tech.$id}:</td>
														<td>
															<input
																type="text" size="10"
																value="{if isset($battle_input.{$smarty.section.content.index}.0.$id)}{$battle_input.{$smarty.section.content.index}.0.$id}{else}0{/if}"
																name="battle_input[{$smarty.section.content.index}][0][{$id}]"></td>
														<td>
															<input
																type="text" size="10"
																value="{if isset($battle_input.{$smarty.section.content.index}.1.$id)}{$battle_input.{$smarty.section.content.index}.1.$id}{else}0{/if}"
																name="battle_input[{$smarty.section.content.index}][1][{$id}]"></td>
													</tr>
												{/foreach}
											</table>
										</td>
										{if $smarty.section.content.index == 0}
											<td style="width:50%;padding:0">
												<table>
													<tr>
														<th>{$LNG.bs_names}
														<th>{$LNG.bs_atter}</th>
														<th>{$LNG.bs_deffer}</th>
													</tr>
													<tr>
														<td></td>
														<td></td>
														<td>
															<button class="reset">{$LNG.bs_reset}</button>
														</td>
													</tr>
												{foreach $defensive_list as $id}
													<tr>
														<td>{$LNG.tech.$id}:</td>
														<td>-</td>
														<td>
															<input type="text" size="10"
																value="{if isset($battle_input.{$smarty.section.content.index}.1.$id)}{$battle_input.{$smarty.section.content.index}.1.$id}{else}0{/if}"
																name="battle_input[{$smarty.section.content.index}][1][{$id}]"></td>
													</tr>
												{/foreach}
												</table>
											</td>
										{/if}
									</tr>
								</table>
							</div>
						{/section}
					</div>
				</td>
			</tr>
		</tbody>
			
	</table>
	<table class="table-gow table_full">
		<tr id="submit">
			<td>
				<input type="button" onClick="return check();" value="{$LNG.bs_send}">&nbsp;
				<input type="reset" value="{$LNG.bs_cancel}">
			</td>
		</tr>
		<tr id="wait" style="display:none;">
			<td style="height:20px">{$LNG.bs_wait}</td>
		</tr>
	</table>
</form>
{/block}