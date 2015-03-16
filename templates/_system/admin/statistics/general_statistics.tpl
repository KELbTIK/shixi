{capture name="trGenerateGraph"}[[Generate Graph]]{/capture}
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>

	google.load("visualization", "1", { packages:["corechart"]});
	
	function drawChart(lines, titles, section) {
	  var data = new google.visualization.DataTable();
	  data.addColumn('string', 'Period');
	  for (var i = 0; i < titles.length; i++) {
		  data.addColumn('number', titles[i]);
	  }
	  data.addRows(lines);
	  var options = {
	    title: section
	  };
	  $('#messageBox').html("<div style='width: 99%; height: 99%' id='messageBox1'></div>");
	  var chart = new google.visualization.LineChart(document.getElementById('messageBox1'));
	  chart.draw(data, options);
	  
	  return false
	}
	
	function GenerateGraph(formID, section)
	{
		var titles = new Array();
		var lines = new Array();
		var linesNum = 0;
		var counter = 0;
		var events = new Array();

		{foreach from=$statistics item=dateStatistic key=date}
			if ('{$date}' != 'total') {ldelim}
				lines[linesNum] = new Array();
				var countElements = 0;
				lines[linesNum][countElements] = '{$dateStatistic.date}';
				countElements++;
				{foreach from=$dateStatistic item=statistic key=event}
					{if $event != 'date'}
						for (var i = 0; i < document.forms[formID].length; i++) {ldelim}
							if (document.forms[formID].elements[i].checked) {ldelim}
								var eventName = document.forms[formID].elements[i].value;
								if (eventName == '{$event}') {ldelim}
									if (!in_array('{$statistic.title}', titles)) {ldelim}
										titles[countElements - 1] = '{$statistic.title}';
									{rdelim}
									lines[linesNum][countElements] = parseInt('{$statistic.statistic}');
									countElements++;
								{rdelim}
							{rdelim}
						{rdelim}
					{/if}
				{/foreach}
				linesNum++;
			{rdelim}
		{/foreach}
		windowMessage();
		drawChart(lines, titles, section);
		return false;
	 	
	}

	function in_array(what, where) {
	    for(var i=0, length_array=where.length; i<length_array; i++)
	        if(what == where[i]) 
	            return true;
	    return false;
	}
	
	function windowMessage(){
		$("#messageBox").dialog( 'destroy' ).html('{capture name="displayJobProgressBar"}<img style="vertical-align: middle;" src="{$GLOBALS.site_url}/../system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]{/capture}{$smarty.capture.displayJobProgressBar|escape:'quotes'}');
		$("#messageBox").dialog({
			width: 900,
			height: 600,
			title: 'Graph'
		}).dialog( 'open' );
		return false;
	}
</script>
{breadcrumbs}[[General Statistics]]{/breadcrumbs}
<h1><img src="{image}/icons/risegraph32.png" border="0" alt="" class="titleicon"/>[[General Statistics]]</h1>

{if $errors }
	{foreach from=$errors item=error key=name}
		{if $error == 'SELECTED_PERIOD_TOO_LONG'}
			<p class="error">[[The selected period is too long to display it by days. Please try to select a shorter period or change "Display by" option]]</p>
		{elseif $error == 'SELECTED_PERIOD_IS_INCORRECT'}
			<p class="error">[[Report can not be generated. Please set correct dates.]]</p>
		{elseif $error == 'EMPTY_PARAMETER'}
			<p class="error">[[Report can not be generated. Please select needed reports.]]</p>
		{else}
			<p class="error">[[{$error}]]</p>
		{/if}
	{/foreach}
{/if}
<form method="post">
<fieldset style="max-width: 800px;">
	<legend>[[Filter]]</legend>
	<table>
		<tbody>
			<tr>
				<td>[[Generate Stats for the Period from]]: </td>
				<td class="genStat">
					{capture name="input_text_field_from"}<input type="text" name="period[from]" value="{$period.from}" id="period_notless"/>{/capture}
					{capture name="input_text_field_to"}<input type="text" name="period[to]" value="{$period.to}" id="period_notmore"/>{/capture}
					{assign var="input_text_field_from" value="`$smarty.capture.input_text_field_from`"}
					{assign var="input_text_field_to" value="`$smarty.capture.input_text_field_to`"}
					[[$input_text_field_from to $input_text_field_to]]
				</td>
			</tr>
			<tr>
				<td>[[Display by]]:</td>
				<td>
					<select name="group_by">
						<option value="day" {if $groupBy == 'day'} selected = "selected" {/if} >[[Day]]</option>
						<option value="month" {if $groupBy == 'month'} selected = "selected" {/if}>[[Month]]</option>
						<option value="quarter" {if $groupBy == 'quarter'} selected = "selected" {/if}>[[Quarter]]</option>
						<option value="year" {if $groupBy == 'year'} selected = "selected" {/if}>[[Year]]</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					 [[Reports]]:&nbsp;&nbsp;
					 <input type="checkbox" name="filter[popularity]" value="1" {if $filter.popularity == 1 || !$filter} checked ="checked" {/if} /> [[Popularity]]
					 <input type="checkbox" name="filter[users]" value="1" {if $filter.users == 1 || !$filter} checked ="checked" {/if} /> [[Users]]
					 <input type="checkbox" name="filter[listings]" value="1" {if $filter.listings == 1 || !$filter} checked ="checked" {/if} />[[Listings]]
					 <input type="checkbox" name="filter[applications]" value="1" {if $filter.applications == 1 || !$filter} checked ="checked" {/if} /> [[Applications]]
					 <input type="checkbox" name="filter[alerts]" value="1" {if $filter.alerts == 1 || !$filter} checked ="checked" {/if} /> [[Alerts]]
					 <input type="checkbox" name="filter[sales]" value="1" {if $filter.sales == 1 || !$filter} checked ="checked" {/if} /> [[Sales]]
					 <input type="checkbox" name="filter[plugins]" value="1" {if $filter.plugins == 1 || !$filter} checked ="checked" {/if} /> [[Plugins]]
					 <input type="hidden" name="filter[general_statistics]" value="1" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
                    <div class="floatRight"><input  type="submit" name="search" value="[[Generate]]" class="grayButton" /></div>
                </td>
			</tr>
		</tbody>
	</table>
</fieldset>
</form>
<br/>
{if !$errors  && $statistics}
{assign var=listingCount value=$listingTypes|@count}
{assign var=userCount value=$userGroups|@count}
<table>
	<tr style="font-weight: bold;">
		<td nowrap="nowrap"></td>
		{foreach from=$statistics item=statistic key=key}
			<td style="text-align: center"  nowrap="nowrap">
			{if $key == 'total'}
				[[Total]]
			{elseif $groupBy == 'day'}
				[[{$key}]]
			{elseif $groupBy == 'month'}
				{$statistic.month}, {$statistic.year} 
			{elseif $groupBy == 'year'}
				{$statistic.year} 
			{else}
				{$statistic.quarter}, {$statistic.year} 
			{/if}
			</td>
		{/foreach}
		<td colspan="2" style="text-align: center">[[Graphs]]</td>
	</tr>
	
	{if $filter.popularity == 1}
	<tr><td style="text-align: center; font-weight: bold">[[Popularity]]</td><td colspan="{$countItems}"></td></tr>
	<form method="post" id="formPopularity" action="{$GLOBALS.site_url}/general-statistics/" onSubmit="return GenerateGraph('formPopularity', 'Popularity')" >
	<tr>
		<td nowrap="nowrap">[[Number of Website Views]]</td>
		{foreach from=$statistics item=statistic key=key}
			<td>{$statistic.siteView.statistic}</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="siteView" /></td>
		{assign var=rowspan value=$listingCount+1}
		<td rowspan="{$rowspan}"><input type="submit" name="submit" value="[[{$smarty.capture.trGenerateGraph|escape:"html"}]]" class="grayButton" /></td>
	</tr>
	{foreach from=$listingTypes item=listingType}
	<tr>
		<td nowrap="nowrap">[[Number of {$listingType.caption} Views]]</td>
		{assign var=listingTypeID value=$listingType.id}
		{foreach from=$statistics item=statistic key=key}
			{assign var="event" value="viewListing$listingTypeID"}
			<td>{$test}{$statistic.$event.statistic}</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="viewListing{$listingTypeID}" /></td>
	</tr>
	{/foreach}
	</form>
	{/if}
	
	{if $filter.users == 1}
	<form method="post" id="formUsers" action="{$GLOBALS.site_url}/general-statistics/" onSubmit="return GenerateGraph('formUsers', 'Users')" >
	<tr><td style="text-align: center; font-weight: bold">[[Users]]</td><td colspan="{$countItems}"></td></tr>
	{foreach from=$userGroups item=userGroup name=users_block}
	<tr>
		{assign var=userGroupID value=$userGroup.id}
		<td  nowrap="nowrap">[[Number of {if $userGroup.key == 'Employer'}Employers{elseif $userGroup.key == 'JobSeeker'}Job Seekers{else}"{$userGroup.caption}" Users{/if} Registered]]</td>
		{foreach from=$statistics item=statistic key=key}
			{assign var="event" value="addUser$userGroupID"}
			<td>{$statistic.$event.statistic}</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="addUser{$userGroup.id}" /></td>
		{if $smarty.foreach.users_block.iteration == 1}
			{assign var=rowspan value=$userCount+2}
			<td rowspan="{$rowspan}"><input type="submit" name="submit" value="[[{$smarty.capture.trGenerateGraph|escape:"html"}]]" class="grayButton" /></td>
		{/if}
	</tr>
	{/foreach}
	<tr>
		<td nowrap="nowrap">[[Number of Sub-Employers Registered]]</td>
		{foreach from=$statistics item=statistic key=key}
			<td>{$statistic.addSubAccount.statistic}</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="addSubAccount" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap">[[Number of Profiles Deleted]]</td>
		{foreach from=$statistics item=statistic key=key}
			<td>{$statistic.deleteUser.statistic}</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="deleteUser" /></td>
	</tr>
	</form>
	{/if}
	
	{if $filter.listings == 1}
	<form method="post" id="formListings" action="{$GLOBALS.site_url}/general-statistics/" onSubmit="return GenerateGraph('formListings', 'Listings')">
	<tr><td style="text-align: center; font-weight: bold">[[Listings]]</td><td colspan="{$countItems}"></td></tr>
	{foreach from=$listingTypes item=listingType name=listings_block}
	<tr>
		<td nowrap="nowrap">[[Number of {if $listingType.key == 'Job' || $listingType.key == 'Resume'}{$listingType.key}s{else}"{$listingType.caption}" Listings{/if} Posted]]</td>
		{assign var=listingTypeID value=$listingType.id}
		{foreach from=$statistics item=statistic key=key}
			{assign var="event" value="addListing$listingTypeID"}
			<td>{$statistic.$event.statistic}</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="addListing{$listingTypeID}" /></td>
		{if $smarty.foreach.listings_block.iteration == 1}
			{assign var=rowspan value=$listingCount*3+1}
			<td rowspan="{$rowspan}"><input type="submit" name="submit" value="[[{$smarty.capture.trGenerateGraph|escape:"html"}]]" class="grayButton" /></td>
		{/if}
	</tr>
	{if $listingType.id == 6}
		<tr>
			<td nowrap="nowrap">[[Number of Featured {if $listingType.key == 'Job' || $listingType.key == 'Resume'}{$listingType.key}s{else}"{$listingType.caption}" Listings{/if} Posted]]</td>
			{assign var=listingTypeID value=$listingType.id}
			{foreach from=$statistics item=statistic key=key}
				{assign var="event" value="addListingFeatured$listingTypeID"}
				<td>{$statistic.$event.statistic}</td>
			{/foreach}
			<td><input type="checkbox"  checked = "checked" name="itemName[]" value="addListingFeatured{$listingTypeID}" /></td>
		</tr>
	{/if}
	<tr>
		<td nowrap="nowrap">[[Number of Priority {if $listingType.key == 'Job' || $listingType.key == 'Resume'}{$listingType.key}s{else}"{$listingType.caption}" Listings{/if} Posted]]</td>
		{assign var=listingTypeID value=$listingType.id}
		{foreach from=$statistics item=statistic key=key}
			{assign var='event' value="addListingPriority$listingTypeID"}
			<td>{$statistic.$event.statistic}</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="addListingPriority{$listingTypeID}" /></td>
	</tr>
	{/foreach}
	{foreach from=$listingTypes item=listingType}
	<tr>
		<td nowrap="nowrap">[[Number of {if $listingType.key == 'Job' || $listingType.key == 'Resume'}{$listingType.key}s{else}"{$listingType.caption}" Listings{/if} Deleted]]</td>
		{assign var=listingTypeID value=$listingType.id}
		{foreach from=$statistics item=statistic key=key}
			{assign var="event" value="deleteListing$listingTypeID"}
			<td>{$statistic.$event.statistic}</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="deleteListing{$listingTypeID}" /></td>
	</tr>
	{/foreach}
	</form>
	{/if}
	
	{if $filter.applications == 1}
	<form method="post" id="formApplications" action="{$GLOBALS.site_url}/general-statistics/" onSubmit="return GenerateGraph('formApplications', 'Applications')" >
	<tr><td style="text-align: center; font-weight: bold">[[Applications]]</td><td colspan="{$countItems}"></td></tr>
	<tr>
		<td nowrap="nowrap">[[Number of Applications Made]]</td>
		{foreach from=$statistics item=statistic key=key}
			<td>{$statistic.apply.statistic}</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="apply" /></td>
		<td rowspan="3"><input type="submit" name="submit" value="[[{$smarty.capture.trGenerateGraph|escape:"html"}]]" class="grayButton" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap">[[Number of Applications Approved]]</td>
		{foreach from=$statistics item=statistic key=key}
			<td>{$statistic.applyApproved.statistic}</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="applyApproved" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap">[[Number of Applications Rejected]]</td>
		{foreach from=$statistics item=statistic key=key}
			<td>{$statistic.applyRejected.statistic}</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="applyRejected" /></td>
	</tr>
	</form>
	{/if}
	
	{if $filter.alerts == 1}
	<form method="post" id="formAlerts" action="{$GLOBALS.site_url}/general-statistics/" onSubmit="return GenerateGraph('formAlerts', 'Alerts')" >
	<tr><td style="text-align: center; font-weight: bold">[[Alerts]]</td><td colspan="{$countItems}"></td></tr>
	{foreach from=$listingTypes item=listingType name=alert_block}
	<tr>
		<td nowrap="nowrap">[[Number of {$listingType.caption} Alerts Subscribed for]]</td>
		{assign var=listingTypeID value=$listingType.id}
		{foreach from=$statistics item=statistic key=key}
			{assign var="event" value="addAlert$listingTypeID"}
			<td>{$statistic.$event.statistic}</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="addAlert{$listingTypeID}" /></td>
		{if $smarty.foreach.alert_block.iteration == 1}
			{assign var=rowspan value=$listingCount*4}
			<td rowspan="{$rowspan}"><input type="submit" name="submit" value="[[{$smarty.capture.trGenerateGraph|escape:"html"}]]" class="grayButton" /></td>
		{/if}
	</tr>
	{/foreach}
	{foreach from=$listingTypes item=listingType}
	<tr>
		<td nowrap="nowrap">[[Number of {$listingType.caption} Alerts Sent]]</td>
		{assign var=listingTypeID value=$listingType.id}
		{foreach from=$statistics item=statistic key=key}
			{assign var="event" value="sentAlert$listingTypeID"}
			<td>{$statistic.$event.statistic}</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="sentAlert{$listingTypeID}" /></td>
	</tr>
	{/foreach}
	{foreach from=$listingTypes item=listingType}
		<tr>
			<td nowrap="nowrap">[[Number of Guest{$listingType.caption} Alerts Sent]]</td>
			{assign var=listingTypeID value=$listingType.id}
			{foreach from=$statistics item=statistic key=key}
				{assign var="event" value="GuestAlertsSent$listingTypeID"}
				<td>{$statistic.$event.statistic}</td>
			{/foreach}
			<td><input type="checkbox"  checked = "checked" name="itemName[]" value="GuestAlertsSent{$listingTypeID}" /></td>
		</tr>
	{/foreach}
	{foreach from=$listingTypes item=listingType}
		<tr>
			<td nowrap="nowrap">[[Number of Guest {$listingType.caption} Alerts subscribed for]]</td>
			{assign var=listingTypeID value=$listingType.id}
			{foreach from=$statistics item=statistic key=key}
				{assign var="event" value="GuestAlertSubscribed$listingTypeID"}
				<td>{$statistic.$event.statistic}</td>
			{/foreach}
			<td><input type="checkbox"  checked = "checked" name="itemName[]" value="GuestAlertSubscribed{$listingTypeID}" /></td>
		</tr>
	{/foreach}
	</form>
	{/if}
	
	{if $filter.sales == 1}
	<form method="post" id="formSales" action="{$GLOBALS.site_url}/general-statistics/" onSubmit="return GenerateGraph('formSales', 'Sales')" >
	<tr><td style="text-align: center; font-weight: bold">[[Sales]]</td><td colspan="{$countItems}"></td></tr>
	<tr>
		<td nowrap="nowrap">[[Total Sales]]</td>
		{foreach from=$statistics item=statistic key=key}
			<td>
				{capture assign="statisticTotalAmount"}{tr type="float"}{$statistic.totalAmount.statistic}{/tr}{/capture}
				{currencyFormat amount=$statisticTotalAmount}
			</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="totalAmount" /></td>
		{assign var=rowspan value=$userCount+1}
		<td rowspan="{$rowspan}"><input type="submit" name="submit" value="[[{$smarty.capture.trGenerateGraph|escape:"html"}]]" class="grayButton" /></td>
	</tr>
	{foreach from=$userGroups item=userGroup}
	<tr>
		<td nowrap="nowrap">[[Earnings from {if $userGroup.key == 'Employer'}Employers{elseif $userGroup.key == 'JobSeeker'}Job Seekers{else}"{$userGroup.caption}" Users{/if}]]</td>
		{assign var="group_sid" value=$userGroup.id}
		{assign var="event" value="amount_$group_sid"}
		{foreach from=$statistics item=statistic key=key}
			<td>
				{capture assign="earningsFromUsers"}{tr type="float"}{$statistic.$event.statistic}{/tr}{/capture}
				{currencyFormat amount=$earningsFromUsers}
			</td>
		{/foreach}
		<td><input type="checkbox"  checked = "checked" name="itemName[]" value="{$event}" /></td>
	</tr>
	{/foreach}
		<tr>
			<td nowrap="nowrap">[[Promotion Discount]]</td>
			{foreach from=$statistics item="statistic" key="key"}
				<td>
					{capture assign="promotionDiscount"}{tr type="float"}{$statistic.promotionUsed.statistic}{/tr}{/capture}
					{currencyFormat amount=$promotionDiscount}
				</td>
			{/foreach}
			<td><input type="checkbox"  checked="checked" name="itemName[]" value="promotionUsed" /></td>
		</tr>
	</form>
	{/if}
	
	{if $filter.plugins == 1}
		<form method="post" id="formPlugins" action="{$GLOBALS.site_url}/general-statistics/" onSubmit="return GenerateGraph('formPlugins', 'Plugins')" >
			<tr>
				<td style="text-align: center; font-weight: bold">[[Plugins]]</td>
				<td colspan="{$countItems}"></td>
			</tr>
			<tr {if !$listPlugins.MobilePlugin.active}style="color: #A1A1A1"{/if}>
				<td nowrap="nowrap">[[Number of Mobile Version Views]]</td>
				{foreach from=$statistics item=statistic key=key}
					<td>{$statistic.viewMobileVersion.statistic}</td>
				{/foreach}
				<td><input type="checkbox"  name="itemName[]" value="viewMobileVersion" {if !$listPlugins.MobilePlugin.active} disabled = "disabled"{else} checked = "checked" {/if} /></td>
				<td rowspan="9"><input type="submit" name="submit" value="{$smarty.capture.trGenerateGraph|escape:"html"}" class="grayButton" /></td>
			</tr>
			<tr {if !$listPlugins.IndeedPlugin.active && !$listPlugins.BeyondPlugin.active && !$listPlugins.SimplyHiredPlugin.active}style="color: #A1A1A1"{/if}>
				<td nowrap="nowrap">[[Number of Redirects to Partnering Sites]]</td>
				{foreach from=$statistics item=statistic key=key}
					<td>{$statistic.partneringSites.statistic}</td>
				{/foreach}
				<td><input type="checkbox"  name="itemName[]" value="partneringSites" {if !$listPlugins.IndeedPlugin.active && !$listPlugins.BeyondPlugin.active && !$listPlugins.SimplyHiredPlugin.active} disabled = "disabled"{else} checked = "checked" {/if} /></td>
			</tr>

			<tr>
				<td style="text-align: center; font-weight: bold">[[Social Plugins]]</td>
				<td colspan="{$countItems-1}"></td>
			</tr>
			{foreach from=$userGroups item="userGroup"}
				<tr {if !$listPlugins.LinkedinSocialPlugin.active}style="color: #A1A1A1"{/if}>
					{assign var=userGroupID value=$userGroup.id}
					<td  nowrap="nowrap">[[Number of "{$userGroup.key}" Users registered through LinkedIn]]</td>
					{foreach from=$statistics item="statistic" key="key"}
						{assign var="event" value="addUserlinkedin$userGroupID"}
						<td>{$statistic.$event.statistic}</td>
					{/foreach}
					<td><input type="checkbox" checked = "checked" name="itemName[]" value="addUserlinkedin{$userGroup.id}"
							   {if !$listPlugins.LinkedinSocialPlugin.active} disabled = "disabled"{else} checked = "checked" {/if} /></td>
				</tr>
			{/foreach}
			{foreach from=$userGroups item="userGroup"}
				<tr {if !$listPlugins.FacebookSocialPlugin.active}style="color: #A1A1A1"{/if}>
					{assign var="userGroupID" value=$userGroup.id}
					<td  nowrap="nowrap">[[Number of "{$userGroup.key}" Users registered through Facebook]]</td>
					{foreach from=$statistics item="statistic" key="key"}
						{assign var="event" value="addUserfacebook$userGroupID"}
						<td>{$statistic.$event.statistic}</td>
					{/foreach}
					<td><input type="checkbox" checked = "checked" name="itemName[]" value="addUserfacebook{$userGroup.id}"
							   {if !$listPlugins.FacebookSocialPlugin.active} disabled = "disabled"{else} checked = "checked" {/if} /></td>
				</tr>
			{/foreach}
			{foreach from=$userGroups item="userGroup"}
				<tr {if !$listPlugins.GoogleSocialPlugin.active}style="color: #A1A1A1"{/if}>
					{assign var="userGroupID" value=$userGroup.id}
					<td  nowrap="nowrap">[[Number of "{$userGroup.key}" Users registered through Google]]</td>
					{foreach from=$statistics item="statistic" key="key"}
						{assign var="event" value="addUsergoogle$userGroupID"}
						<td>{$statistic.$event.statistic}</td>
					{/foreach}
					<td><input type="checkbox" checked = "checked" name="itemName[]" value="addUsergoogle{$userGroup.id}"
							   {if !$listPlugins.GoogleSocialPlugin.active} disabled = "disabled"{else} checked = "checked" {/if} /></td>
				</tr>
			{/foreach}
		</form>
	{/if}
</table>

<div id="stat-footer">
	<form method="post">
		<input type="hidden" name="action" value="export" />
		{foreach from=$period key=key item=value}
			<input type="hidden" name="period[{$key}]" value="{$value}" />
		{/foreach}
		{foreach from=$filter key=key item=value}
			<input type="hidden" name="filter[{$key}]" value="{$value}" />
		{/foreach}
		<input type="hidden" name="group_by" value="{$groupBy}" />
		<input type="submit" name="export" value="[[Export in]]" class="grayButton" /> &nbsp;
		<select name="type" class="export-select">
			<option value="csv">CSV</option>
			<option value="xls">XLS</option>
		</select>
	</form>
	<div class="clr"></div>
	<form method="post" action="{$GLOBALS.site_url}/print-general-statistics/" target="_blank">
		{foreach from=$period key=key item=value}
			<input type="hidden" name="period[{$key}]" value="{$value}" />
		{/foreach}
		{foreach from=$filter key=key item=value}
			<input type="hidden" name="filter[{$key}]" value="{$value}" />
		{/foreach}
		<input type="hidden" name="search" value="1" />
		<input type="hidden" name="group_by" value="{$groupBy}" />
		<input type="submit" name="print" value="[[Print]]" class="grayButton" />
	</form>
</div>
{/if}
<script >
$(function() {

	var dFormat = '{$GLOBALS.current_language_data.date_format}';
	dFormat = dFormat.replace('%m', "mm");
	dFormat = dFormat.replace('%d', "dd");
	dFormat = dFormat.replace('%Y', "yy");
	
	$("#period_notless, #period_notmore").datepicker({
		dateFormat: dFormat,
		showOn: 'both',
		yearRange: '-99:+99',
		buttonImage: '{image}icons/icon-calendar.png'
	});
});
</script>