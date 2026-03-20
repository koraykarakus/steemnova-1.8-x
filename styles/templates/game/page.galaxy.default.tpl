{block name="title" prepend}{$LNG.lm_galaxy}{/block}
{block name="content"}
	
	{if $action == 'sendMissle'}
		<form action="?page=fleetMissile" method="post">
			<input type="hidden" name="galaxy" value="{$galaxy}">
			<input type="hidden" name="system" value="{$system}">
			<input type="hidden" name="planet" value="{$planet}">
			<input type="hidden" name="type" value="{$type}">
			<table class="table-gow table_full">
				<tr>
					<th colspan="2">{$LNG.gl_missil_launch} [{$galaxy}:{$system}:{$planet}]</th>
				</tr>
				<tr>
					<td>{$missile_count} <input type="text" name="SendMI" size="2" maxlength="7"></td>
					<td>{$LNG.gl_objective}:
						{html_options name=Target options=$missileSelector}
					</td>
				</tr>
				<tr>
					<th colspan="2" style="text-align:center;"><input type="submit" value="{$LNG.gl_missil_launch_action}"></th>
				</tr>
			</table>
		</form>
	{/if}
	
	<div class="galaxy_wrapper">
		<div class="galaxy_header">
			<form action="?page=galaxy" method="post" id="galaxy_form">
				<input type="hidden" id="auto" value="dr">
					<input class="galaxy_button" type="button" name="galaxyLeft"
									value="&lt;" onclick="galaxy_submit('galaxyLeft')">
					<input class="galaxy_input" type="text" name="galaxy"
									value="{$galaxy}" size="5" maxlength="3" tabindex="1">
					<input class="galaxy_button" type="button" name="galaxyRight"
									value="&gt;" onclick="galaxy_submit('galaxyRight')">
					<input class="galaxy_button" type="button" name="systemLeft"
									value="&lt;" onclick="galaxy_submit('systemLeft')">
					<input class="galaxy_input" type="text" name="system"
									value="{$system}" size="5" maxlength="3" tabindex="2">
					<input class="galaxy_button" type="button" name="systemRight"
									value="&gt;" onclick="galaxy_submit('systemRight')">
					<input class="galaxy_button" id="galaxySubmit"
									type="submit" value="{$LNG.gl_show}">
			</form>
		</div>
		<div class="galaxy_header info_available_res">
			{$LNG.gl_avaible_missiles}&nbsp;:&nbsp;<span id="missiles">{$currentmip|number}</span>&nbsp; 
			{$LNG.gl_avaible_spyprobes}&nbsp;:&nbsp;<span id="elementID210">{$spyprobes|number}</span>&nbsp;
			{$LNG.gl_avaible_recyclers}&nbsp;:&nbsp;<span id="elementID209">{$recyclers|number}</span>&nbsp;
			{$LNG.gl_fleets}&nbsp;({$fleetmax}/<span id="slots">{$maxfleetcount}</span>)
		</div>
		<div class="galaxy_header">
			<div class="num">#</div>
			<div class="planet_name">{$LNG.gl_name_activity}</div>
			<div class="planet_pic">{$LNG.gl_planet_short}</div>
			<div class="moon_pic">{$LNG.gl_moon_short}</div>
			<div class="debris_pic">{$LNG.gl_debris_short}</div>
			<div class="state">{$LNG.gl_player_estate}</div>
			<div class="alliance">{$LNG.gl_alliance}</div>
			<div class="actions">{$LNG.gl_actions}</div>
		</div>
		{for $planet=1 to $max_planets}
			<div class="galaxy_row">
				{if !isset($GalaxyRows[$planet])}
					<div class="num">
						{$planet}
					</div>
					<div class="planet_name"></div>
					<div class="planet_picture"></div>
					<div class="moon_picture"></div>
					<div class="debris_picture"></div>
					<div class="player_name"></div>
					<div class="alliance_name"></div>
					<div class="actions">
						<a class="icon_colony" href="?page=fleetTable&amp;galaxy={$galaxy}&amp;system={$system}&amp;planet={$planet}&amp;planettype=1&amp;target_mission=7">
							<div class="tooltip tooltip_top">{$LNG.gl_mission_colonize}</div>
						</a>
					</div>
				{elseif $GalaxyRows[$planet] === false}
					<div class="num">
						{$planet}
					</div>
					<div class="planet_name">
						{$LNG.gl_planet_destroyed}
					</div>
					<div class="planet_picture"></div>
					<div class="moon_picture"></div>
					<div class="debris_picture"></div>
					<div class="player_name"></div>
					<div class="alliance_name"></div>
					<div class="actions"></div>
				{else}
					{$currentPlanet = $GalaxyRows[$planet]}
					<div class="num">{$planet}</div>
					<div class="planet_name">
					{if mb_strlen($currentPlanet.planet.name, 'UTF-8') > 12}
						<div class="tooltip tooltip_bottom">
							{$currentPlanet.planet.name}
						</div>
						{limitText($currentPlanet.planet.name, 12)}
					{else}
						{$currentPlanet.planet.name}
					{/if}
						&nbsp;<span class="color-red">{$currentPlanet.lastActivity}</span>
					</div>
					<div class="planet_picture">
						<div class="tooltip tooltip_right">
							<table class='table-gow'>
								<tr>
									<th colspan='2'>
										<span>{$LNG.gl_planet} {$currentPlanet.planet.name} [{$galaxy}:{$system}:{$planet}]</span>
									</th>
								</tr>
								{if $userAuthLevel == 3}
								<tr>
									<td>{$LNG.gl_admin_user_id}</td>
									<td>{$currentPlanet['user']['id']}</td>
								</tr>
								<tr>
									<td>{$LNG.gl_admin_planet_id}</td>
									<td>{$currentPlanet['planet']['id']}</td>
								</tr>
								{/if}
								<tr>
									<td>
										<table class="table-gow table_full">
											<thead>
												<tr>
													<th colspan='2'>{$LNG.gl_actions}</th>
												</tr>
											</thead>
											<tbody>
												{if $currentPlanet.missions.6}
												<tr>
													<td>
														<a onclick='doit(6,{$currentPlanet.planet.id});'>
															{$LNG.type_mission_6}
														</a>
													</td>
												</tr>
												{/if}
												{foreach $currentPlanet.user.class as $class}
												{if $class != 'vacation' && $currentPlanet.planet.phalanx}
												<tr>
													<td>
														<a onclick='OpenPopup(&quot;?page=phalanx&amp;galaxy={$galaxy}&amp;system={$system}&amp;planet={$planet}&amp;planettype=1&quot;, &quot;&quot;, 640, 510);'>
															{$LNG.gl_phalanx}
														</a>
													</td>
												</tr>
												{/if}
												{foreachelse}
												{if $currentPlanet.planet.phalanx}
												<tr>
													<td>
														<a onclick='OpenPopup(&quot;?page=phalanx&amp;galaxy={$galaxy}&amp;system={$system}&amp;planet={$planet}&amp;planettype=1&quot;, &quot;&quot;, 640, 510);'>{$LNG.gl_phalanx}</a>
													</td>
												</tr>
												{/if}
												{/foreach}
												{if $currentPlanet.missions.1}
												<tr>
													<td>
														<a href='?page=fleetTable&amp;galaxy={$galaxy}&amp;system={$system}&amp;planet={$planet}&amp;planettype=1&amp;target_mission=1'>
															{$LNG.type_mission_1}
														</a>
													</td>
												</tr>
												{/if}
												{if $currentPlanet.missions.5}
												<tr>
													<td>
														<a href='?page=fleetTable&amp;galaxy={$galaxy}&amp;system={$system}&amp;planet={$planet}&amp;planettype=1&amp;target_mission=5'>
														{$LNG.type_mission_5}
														</a>
													</td>
												</tr>
												{/if}
												{if $currentPlanet.missions.4}
												<tr>
													<td>
														<a href='?page=fleetTable&amp;galaxy={$galaxy}&amp;system={$system}&amp;planet={$planet}&amp;planettype=1&amp;target_mission=4'>
														{$LNG.type_mission_4}
														</a>
													</td>
												</tr>
												{/if}
												{if $currentPlanet.missions.3}
												<tr>
													<td>
														<a href='?page=fleetTable&amp;galaxy={$galaxy}&amp;system={$system}&amp;planet={$planet}&amp;planettype=1&amp;target_mission=3'>
														{$LNG.type_mission_3}
														</a>
													</td>
												</tr>
												{/if}
												<tr>
													<td>
														<a href='?page=fleetTable&amp;galaxy={$galaxy}&amp;system={$system}&amp;planet={$planet}&amp;planettype=1&amp;target_mission=17'>
														{$LNG.type_mission_17}
														</a>
													</td>
												</tr>
												{if $currentPlanet.missions.10}
												<tr>
													<td>
														<a href='?page=galaxy&amp;action=sendMissle&amp;galaxy={$galaxy}&amp;system={$system}&amp;planet={$planet}'>{$LNG["type_mission_10"]}</a>
													</td>
												</tr>
												{/if}
											</tbody>
										</table>
									</td>
								<td>
									<img src='{$dpath}planets/small/s_{$currentPlanet.planet.image}.jpg' height='75' width='75'>
								</td>
								</tr>
							</table>
						</div>
						<img src="{$dpath}planets/small/s_{$currentPlanet.planet.image}.jpg" height="30"
							width="30" alt="">
					</div>
					<div class="moon_picture">
						{if $currentPlanet.moon}
							<div class="tooltip tooltip_right">
								<table class='table-gow'>
									<tr>
										<th colspan="2">{$LNG.gl_moon} {$currentPlanet.moon.name} [{$galaxy}:{$system}:{$planet}]
										</th>
									</tr>
									{if $userAuthLevel == 3}
									<tr>
										<td>{$LNG.gl_admin_user_id}</td>
										<td>{$currentPlanet['user']['id']}</td>
									</tr>
									<tr>
										<td>{$LNG.gl_admin_planet_id}</td>
										<td>{$currentPlanet['moon']['id']}</td>
									</tr>
									{/if}
									<tr>
										<td style='width:80px'>
											<img src='{$dpath}planets/small/s_mond.jpg' height='75' width='75'>
										</td>
										<td>
											<table class='table-gow' style='width:100%'>
												<tr>
													<th colspan='2'>{$LNG.gl_features}</th>
												</tr>
												<tr>
													<td>{$LNG.gl_diameter}</td>
													<td>{$currentPlanet.moon.diameter|number}</td>
												</tr>
												<tr>
													<td>{$LNG.gl_temperature}</td>
													<td>{$currentPlanet.moon.temp_min}</td>
												</tr>
												<tr>
													<th colspan='2'>{$LNG.gl_actions}</th>
												</tr>
												<tr>
													<td colspan='2'>
														<table class="table-gow">
															<tbody>
																{if $currentPlanet.missions.1}
																<tr>
																	<td>
																		<a class='hover-underline hover-pointer' href='?page=fleetTable&galaxy={$galaxy}&system={$system}&planet={$planet}&planettype=3&target_mission=1'>
																			{$LNG.type_mission_1}
																		</a>
																	</td>
																</tr>
																{/if}
																{if $currentPlanet.missions.3}
																<tr>
																	<td>
																		
																		<a class='hover-underline hover-pointer' href='?page=fleetTable&galaxy={$galaxy}&system={$system}&planet={$planet}&planettype=3&target_mission=3'>
																			{$LNG.type_mission_3}
																		</a>
																	</td>
																</tr>
																{/if}
																{if $currentPlanet.missions.3}
																<tr>
																	<td>
																		
																		<a class='hover-underline hover-pointer' href='?page=fleetTable&galaxy={$galaxy}&system={$system}&planet={$planet}&planettype=3&target_mission=4'>
																			{$LNG.type_mission_4}
																		</a>
																	</td>
																</tr>
																{/if}
																{if $currentPlanet.missions.5}
																<tr>
																	<td>
																		
																		<a class='hover-underline hover-pointer' href='?page=fleetTable&galaxy={$galaxy}&system={$system}&planet={$planet}&planettype=3&target_mission=5'>
																			{$LNG.type_mission_5}
																		</a>
																	</td>
																</tr>
																{/if}
																{if $currentPlanet.missions.6}
																<tr>
																	<td>
																		<a class='hover-underline hover-pointer' onclick='doit(6,{$currentPlanet.moon.id});'>
																			{$LNG.type_mission_6}
																		</a>
																	</td>
																</tr>
																{/if}
																{if $currentPlanet.missions.9}
																<tr>
																	<td>
																		<a class='hover-underline hover-pointer' href='?page=fleetTable&galaxy={$galaxy}&system={$system}&planet={$planet}&planettype=3&target_mission=9'>
																			{$LNG.type_mission_9}
																		</a>
																	</td>
																</tr>
																{/if}
																{if $currentPlanet.missions.10}
																<tr>
																	<td>
																		<a class='hover-underline hover-pointer' href='?page=galaxy&action=sendMissle&galaxy={$galaxy}&system={$system}&planet={$planet}&type=3'>
																			{$LNG.type_mission_10}
																		</a>
																	</td>
																</tr>
																{/if}
															</tbody>
														</table>	
													</td>
												</tr>
											</table>
										</td>
										</tr>
									</table>
							</div>
							<img src="{$dpath}planets/small/s_mond.jpg" height="22" width="22"
								alt="{$currentPlanet.moon.name}">
						{/if}
					</div>
					<div class="debris_picture">
						{if $currentPlanet.debris}
							<div class="tooltip tooltip_right">
								<table class='table-gow'>
									<tr>
										<th colspan='2'>
											{$LNG.gl_debris_field} [{$galaxy}:{$system}:{$planet}]
										</th>
									</tr>
									<tr>
										<td style='width:80px'><img src='{$dpath}planets/debris.png' height="22" width="22"></td>
										<td>
											<table style='width:100%'>
												<tr>
													<th colspan='2'>{$LNG.gl_resources}:</th>
												</tr>
												<tr>
													<td>{$LNG.tech.901}: </td>
													<td>{$currentPlanet.debris.metal|number}</td>
												</tr>
												<tr>
													<td>{$LNG.tech.902}: </td>
													<td>{$currentPlanet.debris.crystal|number}</td>
												</tr>{if $currentPlanet.missions.8 and $recyclers|number > 0}<tr>
													<th colspan='2'>{$LNG.gl_actions}</th></tr><tr><td colspan='2'>
														<a class='hover-underline my-1 hover-pointer' onclick='doit(8, {$currentPlanet.planet.id});'>{$LNG["type_mission_8"]}</a>
													</td>
												</tr>
												{/if}
											</table>
										</td>
									</tr>
								</table>
							</div>
							<img src="{$dpath}planets/debris.png" height="22" width="22" alt="">
						{/if}
					</div>
					<div class="player_name">
							<div class="tooltip tooltip_bottom">
								<table class='table-gow'>
									<tr>
										<th colspan='2'>
											{$currentPlanet.user.playerrank}
										</th>
									</tr>
				 					{if !$currentPlanet.ownPlanet}
				 					{if $currentPlanet.user.isBuddy}
									<tr>
										<td class="text_center">
											<a class='hover-underline hover-pointer' href='#' onclick='return Dialog.Buddy({$currentPlanet.user.id})'>{$LNG.gl_buddy_request}</a>
										</td>
									</tr>
					 				{/if}
									<tr>
										<td class="text_center">
											<a class='hover-underline hover-pointer' href='#' onclick='return Dialog.Playercard({$currentPlanet.user.id});'>{$LNG.gl_playercard}</a>
										</td>
									</tr>
									{/if}
									<tr>
										<td class="text_center">
											<a class='hover-underline hover-pointer' href='?page=statistics&amp;who=1&amp;start={$currentPlanet.user.rank}'>{$LNG.gl_see_on_stats}</a>
										</td>
									</tr>
				 				</table>
							</div>
							<span class="{foreach $currentPlanet.user.class as $class}{if !$class@first}{/if}galaxy-username-{$class}{/foreach} galaxy-username">
								{limitText($currentPlanet.user.username, 15)}
							</span>
							{if !empty($currentPlanet.user.class)}
								<span>(</span>
								{foreach $currentPlanet.user.class as $class}
									{if !$class@first}&nbsp;
									{/if}
									<span
									class="galaxy-short-{$class} galaxy-short">{$ShortStatus.$class}</span>
								{/foreach}
								<span>)</span>
							{/if}
					</div>
					<div class="alliance_name">
						{if $currentPlanet.alliance}
							<div class="tooltip tooltip_bottom">
								<table class='table-gow'>
									<tr>
										<th>
											{$LNG.gl_alliance} {$currentPlanet.alliance.name} {$currentPlanet.alliance.member}
										</th>
									</tr>
									<tr>
										<td class="text_center">
											<a class='hover-underline hover-pointer' href='?page=alliance&amp;mode=info&amp;id={$currentPlanet.alliance.id}'>{$LNG.gl_alliance_page}</a>
										</td>
									</tr>
									<tr>
										<td class="text_center">
											<a class='hover-underline hover-pointer' href='?page=statistics&amp;start={$currentPlanet.alliance.rank}&amp;who=2'>{$LNG.gl_see_on_stats}</a>
										</td>
									</tr>
									{if $currentPlanet.alliance.web}
									<tr>
										<td class="text_center">
											<a class='hover-underline hover-pointer' href='{$currentPlanet.alliance.web}' target='allyweb'>{$LNG.gl_alliance_web_page}</a>
										</td>
									</tr>
									{/if}
								</table>
							</div>
							<span class="{foreach $currentPlanet.alliance.class as $class}{if !$class@first}{/if}galaxy-alliance-{$class}{/foreach} galaxy-alliance">
								[{limitText($currentPlanet.alliance.tag,10)}]
								</span>
						{else}
							-
						{/if}
					</div>
					<div class="actions">
						{if $currentPlanet.action}
							{if $currentPlanet.action.esp}
								<a class="action_link icon_spy" onclick="doit(6,{$currentPlanet.planet.id},{$spyShips|json|escape:'html'})">
									<div class="tooltip tooltip_top">
										{$LNG.gl_spy}
									</div>
								</a>
							{/if}
							{if $currentPlanet.action.message}
								<a class="action_link icon_message" onclick="return Dialog.PM({$currentPlanet.user.id})">
									<div class="tooltip tooltip_top">
										{$LNG.write_message}
									</div>
								</a>
							{/if}
							{if $currentPlanet.action.buddy}
								<a class="action_link icon_buddy" onclick="return Dialog.Buddy({$currentPlanet.user.id})">
									<div class="tooltip tooltip_top">
										{$LNG.gl_buddy_request}
									</div>
								</a>
							{/if}
							{if $currentPlanet.action.missle}
								<a class="action_link icon_missile" href="?page=galaxy&amp;action=sendMissle&amp;galaxy={$galaxy}&amp;system={$system}&amp;planet={$planet}&amp;type=1">
									<div class="tooltip tooltip_top">
										{$LNG.gl_missile_attack}
									</div>
								</a>
							{/if}
						{/if}
						{if $currentPlanet.planet.phalanx}
							<a onclick="OpenPopup('?page=phalanx&amp;galaxy={$galaxy}&amp;system={$system}&amp;planet={$planet}&amp;planettype=1','',640,510);return false;">
								<div class="tooltip tooltip_top">
									{$LNG.gl_phalanx}
								</div>
								<img width="18" height="18" src="{$dpath}img/r.gif" title="{$LNG.gl_phalanx}" alt="">
							</a>
						{/if}
					</div>
				{/if}
			</div>
		{/for}
	</div>

	<table class="table-gow table_full">
		{for $planet=1 to $max_planets}
			<tr>
				
			</tr>
		{/for}
		<tr>
			<td>{$max_planets + 1}</td>
			<td colspan="7">
				<a href="?page=fleetTable&amp;galaxy={$galaxy}&amp;system={$system}&amp;planet={$max_planets + 1}&amp;planettype=1&amp;target_mission=15">{$LNG.gl_out_space}</a>
			</td>
		</tr>
		<tr>
			<td>Trade</td>
			<td colspan="7"><a
					href="?page=fleetTable&amp;galaxy={$galaxy}&amp;system={$system}&amp;planet={$max_planets + 2}&amp;planettype=1&amp;target_mission=16">{$LNG.gl_trade_space}</a>
			</td>
		</tr>

		<tr>
			<td colspan="6">({$planetcount})</td>
			<td class="galaxy_legend" colspan="2">
				<div class="tooltip tooltip_top">
					<table style='width:240px'>
						<tr>
							<th colspan='2'>{$LNG.gl_legend}</th>
						</tr>
						<tr>
							<td>{$LNG.gl_strong_player}</td>
							<td>
								<span class='galaxy-short-strong'>{$LNG.gl_short_strong}</span>
							</td>
						</tr>
						<tr>
							<td>{$LNG.gl_week_player}</td>
							<td><span class='galaxy-short-noob'>{$LNG.gl_short_newbie}</span></td>
						</tr>
						<tr>
							<td>{$LNG.gl_vacation}</td>
							<td><span class='galaxy-short-vacation'>{$LNG.gl_short_vacation}</span></td>
						</tr>
						<tr>
							<td>{$LNG.gl_banned}</td>
							<td><span class='galaxy-short-banned'>{$LNG.gl_short_ban}</span></td>
						</tr>
						<tr>
							<td>{$LNG.gl_inactive_seven}</td>
							<td><span class='galaxy-short-inactive'>{$LNG.gl_short_inactive}</span></td>
						</tr>
						<tr>
							<td>{$LNG.gl_inactive_twentyeight}</td>
							<td><span class='galaxy-short-longinactive'>{$LNG.gl_short_long_inactive}</span></td>
						</tr>
					</table>
				</div>
					{$LNG.gl_legend}
			</td>
		</tr>
		<tr style="display:none;" id="fleetstatusrow">
			<th colspan="8">{$LNG.cff_fleet_target}</th>
		</tr>
	</table>
	<script type="text/javascript">
		status_ok		= '{$LNG.gl_ajax_status_ok}';
		status_fail		= '{$LNG.gl_ajax_status_fail}';
		MaxFleetSetting = {$settings_fleetactions};
	</script>
{/block}