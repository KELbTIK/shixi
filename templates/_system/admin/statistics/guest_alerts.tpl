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
</script>

{breadcrumbs}[[Guest Alerts Reports]]{/breadcrumbs}
<h1><img src="{image}/icons/risegraph32.png" border="0" alt="" class="titleicon"/>[[Guest Alerts Reports]]</h1>

{if $errors }
	{foreach from=$errors item=error key=name}
		{if $error == EMPTY_PARAMETER}
        <p class="error">[[Report can not be generated. Select the report parameter.]]</p>
		{elseif $error == SELECTED_PERIOD_IS_INCORRECT}
        <p class="error">[[Report can not be generated. Please set correct dates]].</p>
		{else}
        <p class="error">{$error}</p>
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
					<label for="listingTypeID">[[Select Listing Type]]:</label>
                    <select name="listingTypeID" id="listingTypeID">
						{foreach from=$listingTypes item="listingType"}
							<option value="{$listingType.key}" {if $listingTypeID == $listingType.key} selected ="selected"{/if} >[[{$listingType.caption}]]</option>
						{/foreach}
                    </select>
                </td>
            </tr>
            <tr class="commonFields">
                <td><input type="radio" id="filterSubscribed" name="filter" value="subscribed" {if $filter == 'subscribed' || empty($filter)} checked="checked" {/if} /></td>
                <td class="filterTitle"><label for="filterSubscribed">[[Number of Guest Alerts subscribed for]]</label></td>
            </tr>
            <tr class="commonFields">
                <td><input type="radio" id="filterSent" name="filter" value="sent" {if $filter == 'sent'} checked="checked" {/if} /></td>
                <td class="filterTitle"><label for="filterSent">[[Number of Guest Alerts sent]]</label></td>
            </tr>
            <tr class="commonFields">
                <td colspan="2">
                    [[from]]:
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
		{if $filter == 'subscribed'}
			[[Number of Guest "$listingTypeID" Alerts subscribed per Period for]]
		{elseif $filter == 'sent'}
			[[Number of Guest "$listingTypeID" Alerts sent per Period]]
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
				<a href="?search=search&amp;filter={$filter}&amp;listingTypeID={$listingTypeID}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=email&amp;sorting_order={if $sorting_order == 'ASC'}DESC{else}ASC{/if}">[[{$columnTitle}]]</a>
				{if $sorting_field == 'email'}
					{if $sorting_order == 'ASC'}
						<img src="{image}b_up_arrow.gif" alt="Up" />
					{else}
						<img src="{image}b_down_arrow.gif" alt="Down" />
					{/if}
				{/if}
			</th>
			<th>
				<a href="?search=search&amp;filter={$filter}&amp;listingTypeID={$listingTypeID}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=total&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'total'}DESC{else}ASC{/if}">[[Total]]</a>
				{if $sorting_field == 'total'}
					{if $sorting_order == 'ASC'}
						<img src="{image}b_up_arrow.gif" alt="Up" />
					{else}
						<img src="{image}b_down_arrow.gif" alt="Down" />
					{/if}
				{/if}
			</th>
			<th>
				<a href="?search=search&amp;filter={$filter}&amp;listingTypeID={$listingTypeID}&amp;period[from]={$period.from}&amp;period[to]={$period.to}&amp;sorting_field=percent&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'percent'}DESC{else}ASC{/if}">%</a>
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
			{foreach from=$statistics item="statistic" key="key"}
			<tr>
				<td><strong>{$statistic.generalColumn}</strong></td>
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
			<input type="hidden" name="listingTypeID" value="{$listingTypeID}" />
			<input type="hidden" name="sorting_field" value="{$sorting_field}" />
			<input type="hidden" name="sorting_order" value="{$sorting_order}" />
			<span class="greenButtonEnd"><input type="submit" name="export" value="[[Export]]" class="greenButton" /></span>
			<select name="type" class="export-select">
				<option value="csv">CSV</option>
				<option value="xls">XLS</option>
			</select>
		</form>
		<div class="clr"></div>
		<form method="post" action="{$GLOBALS.site_url}/statistics/guest-alerts/print/" target="_blank">
			{foreach from=$period key=key item=value}
				<input type="hidden" name="period[{$key}]" value="{$value}" />
			{/foreach}
			<input type="hidden" name="filter" value="{$filter}" />
			<input type="hidden" name="listingTypeID" value="{$listingTypeID}" />
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

<script >
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