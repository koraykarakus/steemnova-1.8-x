{block name="content"}

<div class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12 d-flex flex-column">
	<div class="form-group d-flex w-100">
		<span class="text-yellow fw-bold">{$LNG.ti_header}</span>
	</div>
	<div class="form-group d-flex my-2 py-2 border border-1 border-secondary">
		<span class="col text-center text-yellow fw-bold">{$LNG.ti_id}</span>
		<span class="col text-center text-yellow fw-bold">{$LNG.ti_username}</span>
		<span class="col text-center text-yellow fw-bold">{$LNG.ti_subject}</span>
		<span class="col text-center text-yellow fw-bold">{$LNG.ti_answers}</span>
		<span class="col text-center text-yellow fw-bold">{$LNG.ti_date}</span>
		<span class="col text-center text-yellow fw-bold">{$LNG.ti_status}</span>
	</div>
	{foreach $ticketList as $TicketID => $TicketInfo}
	{if $TicketInfo.status < 2}
	<div class="form-group d-flex my-2 py-2 border border-1 border-secondary align-items-center">
			<span class="col text-center">
				<a class="text-white" href="admin.php?page=support&amp;mode=view&amp;id={$TicketID}">#{$TicketID}</a>
			</span>
			<span class="col">
				<a class="text-white" href="admin.php?page=support&amp;mode=view&amp;id={$TicketID}">{$TicketInfo.username}</a>
			</span>
			<span class="col">
				<a class="text-white" href="admin.php?page=support&amp;mode=view&amp;id={$TicketID}">{$TicketInfo.subject}</a>
			</span>
			<span class="col">{$TicketInfo.answer - 1}</span>
			<span class="col">{$TicketInfo.time}</span>
			<span class="col">
				{if $TicketInfo.status == 0}
				<span style="color:green">{$LNG.ti_status_open}</span>
				{elseif $TicketInfo.status == 1}
				<span style="color:orange">{$LNG.ti_status_answer}</span>
				{/if}
			</span>
	</div>
	{/if}
	{/foreach}
	{foreach $ticketList as $TicketID => $TicketInfo}
	{if $TicketInfo.status == 2}
	<div class="form-group d-flex">
		<span class="col"><a href="admin.php?page=support&amp;mode=view&amp;id={$TicketID}">#{$TicketID}</a></span>
		<span class="col"><a href="admin.php?page=support&amp;mode=view&amp;id={$TicketID}">{$TicketInfo.username}</a></span>
		<span class="col"><a href="admin.php?page=support&amp;mode=view&amp;id={$TicketID}">{$TicketInfo.subject}</a></span>
		<span class="col">{$TicketInfo.answer - 1}</span>
		<span class="col">{$TicketInfo.time}</span>
		<span class="col"><span style="color:red">{$LNG.ti_status_closed}</span></span>
	</div>
	{/if}
	{/foreach}
	<div class="form-group d-flex w-100">
		<span class="text-center fw-bold text-yellow d-flex w-100">{$LNG.ti_status_closed}</span>
	</div>
	<div class="form-group d-flex">
		<span class="col">{$LNG.ti_id}</span>
		<span class="col">{$LNG.ti_username}</span>
		<span class="col">{$LNG.ti_subject}</span>
		<span class="col">{$LNG.ti_answers}</span>
		<span class="col">{$LNG.ti_date}</span>
		<span class="col">{$LNG.ti_status}</span>
	</div>
</div>

{/block}
