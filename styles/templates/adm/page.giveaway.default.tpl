{block name="content"}

	<form method="post" action="?page=giveaway&mode=send" class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12">
		<!-- Zielplaneten definieren -->
		<table class="table table-dark table-striped table-sm fs-12 w-50 my-5 mx-auto">
			<tr>
				<th colspan="3">{$LNG.ga_definetarget}</th>
			</tr>
			<tr style="height:26px;">
				<td width="50%">{$LNG.ga_planettypes}:</td>
				<td width="50%">
					<table style="color:#FFFFFF">
						<tr>
							<td class="transparent"><input type="checkbox" name="planet" value="1" checked></td>
							<td class="transparent left">{$LNG.fcm_planet}</td>
						</tr>
						<tr>
							<td class="transparent"><input type="checkbox" name="moon" value="1"></td>
							<td class="transparent left">{$LNG.fcm_moon}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr style="height:26px;">
				<td width="50%">{$LNG.ga_homecoordinates}:</td>
				<td width="50%"><input type="checkbox" name="mainplanet" value="1"></td>
			</tr>
			<tr style="height:26px;">
				<td width="50%">{$LNG.ga_no_inactives}:</td>
				<td width="50%"><input type="checkbox" name="no_inactive" value="1"></td>
			</tr>
		</table>

		<ul class="nav nav-tabs" id="" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_1" type="button">
					{$LNG.tech.900}
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_2" type="button">
					{$LNG.tech.0}
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_3" type="button">
					{$LNG.tech.100}
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_4" type="button">
					{$LNG.tech.200}
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_5" type="button">
					{$LNG.tech.400}
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_6" type="button">
					{$LNG.tech.600}
				</button>
			</li>
		</ul>

		<div class="tab-content mt-3 w-100 mx-auto d-flex align-items-start justify-content-between h-100">
			<div class="tab-pane fade show active p-3" id="tab_1">
				<!-- resources -->
				<table class="p-3 mx-auto text-start">
					{foreach item=Element from=$reslist.resstype.1}
						<tr>
							<td class="">{$LNG.tech.{$Element}}:</td>
							<td><input type="text" name="element_{$Element}" value="0" pattern="[0-9]*"></td>
						</tr>
					{/foreach}
					{foreach item=Element from=$reslist.resstype.3}
						<tr>
							<td>{$LNG.tech.{$Element}}:</td>
							<td><input type="text" name="element_{$Element}" value="0" pattern="[0-9]*"></td>
						</tr>
					{/foreach}
				</table>
			</div>
			<div class="tab-pane fade p-3" id="tab_2">
				<!-- buildings -->
				<table class="p-3 mx-auto text-start">
					{foreach item=Element from=$reslist.build}
						<tr>
							<td>{$LNG.tech.{$Element}}:</td>
							<td><input type="text" name="element_{$Element}" value="0" pattern="[0-9]*"></td>
						</tr>
					{/foreach}
				</table>
			</div>
			<div class="tab-pane fade p-3" id="tab_3">
				<!-- tech -->
				<table class="p-3 mx-auto text-start">
					{foreach item=Element from=$reslist.tech}
						<tr>
							<td>{$LNG.tech.{$Element}}:</td>
							<td><input type="text" name="element_{$Element}" value="0" pattern="[0-9]*"></td>
						</tr>
					{/foreach}
				</table>
			</div>
			<div class="tab-pane fade p-3" id="tab_4">
				<!-- ships -->
				<table class="p-3 mx-auto text-start">
					{foreach item=Element from=$reslist.fleet}
						<tr>
							<td>{$LNG.tech.{$Element}}:</td>
							<td><input type="text" name="element_{$Element}" value="0" pattern="[0-9]*"></td>
						</tr>
					{/foreach}
				</table>
			</div>
			<div class="tab-pane fade p-3" id="tab_5">
				<!-- defenses -->
				<table class="p-3 mx-auto text-start">
					{foreach item=Element from=$reslist.defense}
						<tr>
							<td>{$LNG.tech.{$Element}}:</td>
							<td><input type="text" name="element_{$Element}" value="0" pattern="[0-9]*"></td>
						</tr>
					{/foreach}
				</table>
			</div>
			<div class="tab-pane fade p-3" id="tab_6">
				<!-- officers -->
				<table class="p-3 mx-auto text-start">
					{foreach item=Element from=$reslist.officers}
						<tr>
							<td>{$LNG.tech.{$Element}}:</td>
							<td><input type="text" name="element_{$Element}" value="0" pattern="[0-9]*"></td>
						</tr>
					{/foreach}
				</table>
			</div>
			<div class="d-flex justify-content-start align-items-start p-3">
				<input style="width: 148px;" class="btn btn-primary" type="submit" value="{$LNG.qe_send}">
			</div>
		</div>


	</form>

{/block}