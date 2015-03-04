<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>

	google.load("visualization", "1", { packages:["corechart"]});
	google.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
		var dataArr = new Array();
		var i = 0;
		data.addColumn('string', 'Title');
		data.addColumn('number', 'Percent');
        {foreach from=$statistics item=statistic key=key}
       		var total = parseInt('{$statistic.total}');
       		dataArr[i] = ['{$statistic.generalColumn}', total];
       		i++;
        {/foreach}


        data.addRows(dataArr);

        var options = {
          title: ''
        };
		if ('{$countResult}' > 0) {
	        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
	        chart.draw(data, options);
		}
    }

	var currentListingTypeID = '';
	function setListingType(listingTypeSID) {
		$('.userGroupFields').each(function() {
			$(this).css('display', 'none');
		});
		$('.commonFields').each(function() {
			$(this).css('display', 'none');
		});
		if (listingTypeSID != '') {
			{foreach from=$listingTypes item=listingType}
				if ('{$listingType.id}' == listingTypeSID) {ldelim}
					if ('{$listingType.key}' == 'Job' || '{$listingType.key}' == 'Resume')
						listingTypeID = '{$listingType.key}s';
					else
						listingTypeID = '"{$listingType.key}" Listings';

					$('.filterTitle').each(function() {ldelim}
						if (currentListingTypeID != '')
							var text = $(this).text().replace(currentListingTypeID, listingTypeID);
						else
							var text = $(this).text().replace('#listingType#', listingTypeID);
						$(this).text(text);
					{rdelim});
					currentListingTypeID = listingTypeID;
					$('.commonFields').each(function() {ldelim}
						$(this).css('display', 'table-row');
					{rdelim});
					var html = '';
					{foreach from=$listingType.userGroups item=userGroup}
						if ('{$userGroups.$userGroup}') {ldelim}
							$('#userGroup_{$userGroups.$userGroup.id}').css('display', 'table-row');
						{rdelim}
					{/foreach}
					$("#userGroupTable").html(html);
				{rdelim}
			{/foreach}

		}
	}

$(document).ready(function(){ldelim}
	if ('{$listingTypeSID}')
		setListingType('{$listingTypeSID}');
{rdelim});
</script>

{breadcrumbs}[[Listings]]{/breadcrumbs}
<h1><img src="{image}/icons/risegraph32.png" border="0" alt="" class="titleicon"/>[[Listings Reports]]</h1>

{if $errors }
	{foreach from=$errors item=error key=name}
		{if $error == EMPTY_PARAMETER}
			<p class="error">[[Report can not be generated. Select the report parameter]]</p>
		{elseif $error == SELECTED_PERIOD_IS_INCORRECT}
			<p class="error">[[Report can not be generated. Please set correct dates.]]</p>
		{else}
			<p class="error">[[{$error}]]</p>
		{/if}
	{/foreach}
{/if}

<form method="post">
<input type="hidden" name="action" value="search" />
<fieldset style="width: 600px;">
	<legend>[[Filter]]</legend>
	<table id="filterForm" border="1">
		<tbody>
			<tr>
				<td colspan="2">
					<select name="listingTypeSID" onChange = "setListingType(this.value)">
					<option value="">[[Select Listing Type]]</option>
					{foreach from=$listingTypes item=listingType}
						<option value="{$listingType.id}" {if $listingTypeSID == $listingType.id} selected ="selected"{/if} >[[{$listingType.caption}]]</option>
					{/foreach}
					</select>
				</td>
			</tr>
			{foreach from=$userGroups item=userGroup}
			<tr id="userGroup_{$userGroup.id}" style="display: none;" class="userGroupFields">
				{assign var=userGroupId value=$userGroup.id}
				{assign var=filterValue value="userGroup_$userGroupId"}
				<td><input type="radio" name="filter" value="{$filterValue}" {if $filter == $filterValue} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Number of #listingType# posted per Period by $userGroup.caption]]</td>
			</tr>
			{/foreach}
			<tr class="commonFields"  style="display: none;">
				<td><input type="radio" name="filter" value="JobCategory" {if $filter == 'JobCategory'} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Number of #listingType# posted per Period by Category]]</td>
			</tr>
			<tr class="commonFields" style="display: none;">
				<td><input type="radio" name="filter" value="Location_Country" {if $filter == 'Location_Country'} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Number of #listingType# posted per Period by Country]]</td>
			</tr>
			<tr class="commonFields" style="display: none;">
				<td><input type="radio" name="filter" value="Location_State" {if $filter == 'Location_State'} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Number of #listingType# posted per Period by State]]</td>
			</tr>
			<tr class="commonFields" style="display: none;">
				<td><input type="radio" name="filter" value="Location_City" {if $filter == 'Location_City'} checked="checked" {/if} /></td>
				<td class="filterTitle">[[Number of #listingType# posted per Period by City]]</td>
			</tr>
			<tr class="commonFields"  style="display: none;">
				<td colspan="2">
					[[From]]:
					{capture name="input_text_field_from"}<input type="text" name="period[from]" value="{$period.from}" id="period_notless"/>{/capture}
					{capture name="input_text_field_to"}<input type="text" name="period[to]" value="{$period.to}" id="period_notmore"/>{/capture}
					{assign var="input_text_field_from" value="`$smarty.capture.input_text_field_from`"}
					{assign var="input_text_field_to" value="`$smarty.capture.input_text_field_to`"}
					[[$input_text_field_from to $input_text_field_to]]
				</td>
			</tr>
			<tr>
				<td colspan = "2" align="right"><div class="floatRight"><input  type="submit" name="search" value="[[Generate]]" class="grayButton" /></div></td>
			</tr>
		</tbody>
	</table>
</fieldset>
</form>

{if !$errors  && $statistics}
<div class="clr"><br/><br/></div>
<h3>
	{if $filter == 'JobCategory'}
		[[Number of {if $listingTypes.$listingTypeSID.key == 'Job' || $listingTypes.$listingTypeSID.key == 'Resume'}{$listingTypes.$listingTypeSID.key}s{else}"{$listingTypes.$listingTypeSID.caption}" Listings{/if} posted per Period by Category]]
	{elseif $filter == 'Location_Country'}
		[[Number of {if $listingTypes.$listingTypeSID.key == 'Job' || $listingTypes.$listingTypeSID.key == 'Resume'}{$listingTypes.$listingTypeSID.key}s{else}"{$listingTypes.$listingTypeSID.caption}" Listings{/if} posted per Period by Country]]
	{elseif $filter == 'Location_State'}
		[[Number of {if $listingTypes.$listingTypeSID.key == 'Job' || $listingTypes.$listingTypeSID.key == 'Resume'}{$listingTypes.$listingTypeSID.key}s{else}"{$listingTypes.$listingTypeSID.caption}" Listings{/if} posted per Period by State]]
	{elseif $filter == 'Location_City'}
		[[Number of {if $listingTypes.$listingTypeSID.key == 'Job' || $listingTypes.$listingTypeSID.key == 'Resume'}{$listingTypes.$listingTypeSID.key}s{else}"{$listingTypes.$listingTypeSID.caption}" Listings{/if} posted per Period by City]]
	{else}
		{foreach from=$userGroups item=userGroup}
			{assign var=userGroupId value=$userGroup.id}
			{assign var=filterValue value="userGroup_$userGroupId"}
			{if $filter == $filterValue}
				[[Number of {if $listingTypes.$listingTypeSID.key == 'Job' || $listingTypes.$listingTypeSID.key == 'Resume'}{$listingTypes.$listingTypeSID.key}s{else}"{$listingTypes.$listingTypeSID.caption}" Listings{/if} posted per Period by {$userGroup.caption}]]
			{/if}
		{/foreach}
	{/if}
</h3>
<div>
	{if $periodView.from}
		[[from]]  {tr type="date"}{$periodView.from}{/tr}
	{/if}
	{if $periodView.to}
		[[to]]  {tr type="date"}{$periodView.to}{/tr}
	{/if}
</div>

<table>
	<thead>
		<tr>
			<th>
				{if $filter == 'JobCategory' || $filter == 'Location_Country' || $filter == 'Location_State' || $filter == 'Location_City'}
					<a href="?search=search&amp;filter={$filter}&amp;listingTypeSID={$listingTypeSID}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field={$filter}&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == $filter}DESC{else}ASC{/if}">[[{$columnTitle}]]</a>
					{if $sorting_field == $filter}
						{if $sorting_order == 'ASC'}
							<img src="{image}b_up_arrow.gif" alt="Up" />
						{else}
							<img src="{image}b_down_arrow.gif" alt="Down" />
						{/if}
					{/if}
				{else}
					<a href="?search=search&amp;filter={$filter}&amp;listingTypeSID={$listingTypeSID}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=username&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'username'}DESC{else}ASC{/if}">[[{$columnTitle}]]</a>
					{if $sorting_field == 'username'}
						{if $sorting_order == 'ASC'}
							<img src="{image}b_up_arrow.gif" alt="Up" />
						{else}
							<img src="{image}b_down_arrow.gif" alt="Down" />
						{/if}
					{/if}
				{/if}
			</th>
			<th>
				<a href="?search=search&amp;filter={$filter}&amp;listingTypeSID={$listingTypeSID}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=regular&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'regular'}DESC{else}ASC{/if}">[[Number of Regular {if $listingTypes.$listingTypeSID.key == 'Job' || $listingTypes.$listingTypeSID.key == 'Resume'}{$listingTypes.$listingTypeSID.key}s{else}"{$listingTypes.$listingTypeSID.caption}" Listings{/if} Posted]]</a>
				{if $sorting_field == 'regular'}
					{if $sorting_order == 'ASC'}
						<img src="{image}b_up_arrow.gif" alt="Up" />
					{else}
						<img src="{image}b_down_arrow.gif" alt="Down" />
					{/if}
				{/if}
			</th>
			{if $listingTypes.$listingTypeSID.key == 'Job'}
			<th>
				<a href="?search=search&amp;filter={$filter}&amp;listingTypeSID={$listingTypeSID}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=FeaturedListings&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'FeaturedListings'}DESC{else}ASC{/if}">[[Number of Featured Jobs Posted]]</a>
				{if $sorting_field == 'FeaturedListings'}
					{if $sorting_order == 'ASC'}
						<img src="{image}b_up_arrow.gif" alt="Up" />
					{else}
						<img src="{image}b_down_arrow.gif" alt="Down" />
					{/if}
				{/if}
			</th>
			{/if}
			<th>
				<a href="?search=search&amp;filter={$filter}&amp;listingTypeSID={$listingTypeSID}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=PriorityListings&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'PriorityListings'}DESC{else}ASC{/if}">[[Number of Priority {if $listingTypes.$listingTypeSID.key == 'Job' || $listingTypes.$listingTypeSID.key == 'Resume'}{$listingTypes.$listingTypeSID.key}s{else}"{$listingTypes.$listingTypeSID.caption}" Listings{/if} Posted]]</a>
				{if $sorting_field == 'PriorityListings'}
					{if $sorting_order == 'ASC'}
						<img src="{image}b_up_arrow.gif" alt="Up" />
					{else}
						<img src="{image}b_down_arrow.gif" alt="Down" />
					{/if}
				{/if}
			</th>
			<th>
				<a href="?search=search&amp;filter={$filter}&amp;listingTypeSID={$listingTypeSID}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=total&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'total'}DESC{else}ASC{/if}">[[Total]]</a>
				{if $sorting_field == 'total'}
					{if $sorting_order == 'ASC'}
						<img src="{image}b_up_arrow.gif" alt="Up" />
					{else}
						<img src="{image}b_down_arrow.gif" alt="Down" />
					{/if}
				{/if}
			</th>
			<th>
				<a href="?search=search&amp;filter={$filter}&amp;listingTypeSID={$listingTypeSID}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=percent&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'percent'}DESC{else}ASC{/if}">%</a>
				{if $sorting_field == 'percent'}
					{if $sorting_order == 'ASC'}
						<img src="{image}b_up_arrow.gif" alt="Up" />
					{else}
						<img src="{image}b_down_arrow.gif" alt="Down" />
					{/if}
				{/if}
			</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$statistics item=statistic key=key}
			<tr>
				<td>
					<strong>
					{if $link == 'user'}
						{if $statistic.generalColumn == 'Other'}
							{$statistic.generalColumn}
						{else}
							<a href="{$GLOBALS.site_url}/edit-user/?user_sid={$statistic.user_sid}">[[{$statistic.generalColumn}]]</a>
						{/if}
					{else}
						[[{$statistic.generalColumn}]]
					{/if}
					</strong>
				</td>
				<td>{$statistic.regular}</td>
				{if $listingTypes.$listingTypeSID.key == 'Job'}
					<td>{$statistic.FeaturedListings}</td>
				{/if}
				<td>{$statistic.PriorityListings}</td>
				<td>{$statistic.total}</td>
				<td>{$statistic.percent}%</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<div id="stat-footer">
	<form method="post">
		<input type="hidden" name="action" value="export" />
		{foreach from=$period key=key item=value}
			<input type="hidden" name="period[{$key}]" value="{$value}" />
		{/foreach}
		<input type="hidden" name="filter" value="{$filter}" />
		<input type="hidden" name="listingTypeSID" value="{$listingTypeSID}" />
		<input type="hidden" name="sorting_field" value="{$sorting_field}" />
		<input type="hidden" name="sorting_order" value="{$sorting_order}" />
		<span class="greenButtonEnd"><input type="submit" name="export" value="[[Export]]" class="greenButton" /></span>
		<select name="type" class="export-select">
			<option value="csv">CSV</option>
			<option value="xls">XLS</option>
		</select>
	</form>
	<div class="clr"></div>
	<form method="post" action="{$GLOBALS.site_url}/print-listings-statistics/" target="_blank">
		{foreach from=$period key=key item=value}
			<input type="hidden" name="period[{$key}]" value="{$value}" />
		{/foreach}
		<input type="hidden" name="filter" value="{$filter}" />
		<input type="hidden" name="listingTypeSID" value="{$listingTypeSID}" />
		<input type="hidden" name="sorting_field" value="{$sorting_field}" />
		<input type="hidden" name="sorting_order" value="{$sorting_order}" />
		<span class="greenButtonEnd printButton"><input type="submit" name="search" value="[[Print]]" class="greenButton"  /></span>
	</form>
</div>
{elseif !$errors  && !$statistics && $search}
	<br/><br/><p class="error" style="width: 600px;">[[Report can not be generated. There is no statistics for this period.]]</p>
{/if}
<div class="clr"><br/><br/></div>
<div id="chart_div" style="width: 900px; height: 500px;"></div>

<script language="Javascript">
$(function(){ldelim}

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