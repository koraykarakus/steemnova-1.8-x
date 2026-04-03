//topnav.js
//RealTimeRessisanzeige for 2Moons
// @version 1.0
// @copyright 2010 by ShadoX

function resourceTicker(config, init) {
	if(typeof init !== "undefined" && init === true)
		window.setInterval(function(){resourceTicker(config)}, 1000);

	var element	= $('#'+config.valueElem);
	var element_tooltip = $('#' + config.valueTooltip);

	if(element.hasClass('res_current_max'))
	{
		return false;
	}

	var nrResource = Math.max(0, Math.floor(parseFloat(config.available) + parseFloat(config.production) / 3600 * (serverTime.getTime() - startTime) / 1000));
	nrResource = Math.min(nrResource, config.limit[1]);

	if (nrResource < config.limit[1])
	{
		if (!element.hasClass('res_current_warn') 
			&& nrResource >= config.limit[1] * 0.75)
		{
			element.addClass('res_current_warn');
		}
	} 
	else 
	{
		element.removeClass('res_current_warn');
		element.addClass('res_current_max');
	}

	if (view_shortly_number) {
		element.attr('data-tooltip-content', NumberGetHumanReadable(nrResource));
		element.html(shortly_number(nrResource));
		element_tooltip.html(shortly_number(nrResource));
	} 
	else 
	{
		element.html(NumberGetHumanReadable(nrResource));
		element_tooltip.html(NumberGetHumanReadable(nrResource));
	}
}

function getRessource(name) {
	return parseInt($('#current_'+name).data('real'));
}
