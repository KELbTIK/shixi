<h1>[[My Reports]]</h1>
{if $errors}
	{foreach from=$errors item="errorData" key="errorId"}
		{if $errorId == 'NOT_EMPLOYER'}
			<p class="error">[[You are not employer]]</p>
		{/if}
	{/foreach}
{else}
	<div id="reports">
		<div id="reports-navigation">
			<div id="reports-navigation-in">
				<div id="reports-navigation-in-border">
					<a href="{$GLOBALS.site_url}/my-reports/" class="first-item">[[Quick Stats]]</a>
					<a href="{$GLOBALS.site_url}/general-stats/" class="middle-item current">[[General Stats]]</a>
					<a href="{$GLOBALS.site_url}/job-stats/" class="last-item">[[Job Stats]]</a>
				</div>
			</div>
		</div>

		<h2>[[General Stats]]</h2>
		<table id="general-stats">
			<thead>
			<tr>
				<th class="tableLeft"> </th>
				<th></th>
				<th>[[Whole Period]]</th>
				<th>[[This Month]]</th>
				<th>[[This Week]]</th>
				<th>[[Today]]</th>
				<th class="tableRight"> </th>
			</tr>
			</thead>
			<tbody>
			{foreach from=$generalStat key=columnTitle item=values}
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td> </td>
					<td>[[{$columnTitle}]]</td>
					{foreach from=$values item=value}
						<td class="general-stats-item">{$value}</td>
					{/foreach}
					<td> </td>
				</tr>
			{/foreach}
			</tbody>
		</table>
		<div class="clr"><br/></div>
	</div>
{/if}