<div class="fleet_events {if $show_fleets_active}hidden{/if}">
    {foreach $fleets as $index => $fleet}
        <div class="fleetRow">
            <span id="fleettime_{$index}" class="fleets" data-fleet-end-time="{$fleet.returntime}"
                data-fleet-time="{$fleet.resttime}">
                {pretty_fly_time({$fleet.resttime})}
            </span>
            <span id="fleettime_{$index}">{$fleet.text}</span>
        </div>
    {/foreach}
</div>