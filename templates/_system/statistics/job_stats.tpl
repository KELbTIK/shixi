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
					<a href="{$GLOBALS.site_url}/general-stats/" class="middle-item">[[General Stats]]</a>
					<a href="{$GLOBALS.site_url}/job-stats/" class="last-item current">[[Job Stats]]</a>
				</div>
			</div>
		</div>

		<h2>[[Job Stats]]</h2>
		<div id="job-stats">
			<form name="filterForJobsStat" id="filterForJobsStat" method="post" action="{$GLOBALS.site_url}/job-stats/">
				[[Display]]:
				<select name="active" onchange="submitForm()">
					<option value="1" {if $active}selected="selected"{/if}>[[Active Jobs]]</option>
					<option value="0" {if not $active}selected="selected"{/if}>[[All jobs]]</option>
				</select>
			</form>

			{if $jobsStat|count > 0}
				<table>
					<thead>
					<tr>
						<th class="tableLeft"> </th>
						<th width="26%">
							<a href="?active={$active}&amp;sortingField=Title&amp;sortingOrder={if $sortingOrder == 'ASC' && $sortingField == 'Title'}DESC{else}ASC{/if}">[[Job Title]]</a>
							{if $sortingField == 'Title'}
								{if $sortingOrder == 'ASC'}
									<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />
								{/if}
							{/if}
						</th>
						<th width="19%">
							<a href="?active={$active}&amp;sortingField=postedDate&amp;sortingOrder={if $sortingOrder == 'ASC' && $sortingField == 'postedDate'}DESC{else}ASC{/if}">[[Posted]]</a>
							{if $sortingField == 'postedDate'}
								{if $sortingOrder == 'ASC'}
									<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />
								{/if}
							{/if}
						</th>
						<th width="19%">
							<a href="?active={$active}&amp;sortingField=expDate&amp;sortingOrder={if $sortingOrder == 'ASC' && $sortingField == 'expDate'}DESC{else}ASC{/if}">[[Expiration Date]]</a>
							{if $sortingField == 'expDate'}
								{if $sortingOrder == 'ASC'}
									<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />
								{/if}
							{/if}
						</th>
						<th width="9%">
							<a href="?active={$active}&amp;sortingField=countSearches&amp;sortingOrder={if $sortingOrder == 'ASC' && $sortingField == 'countSearches'}DESC{else}ASC{/if}">[[Searches]]</a>
							{if $sortingField == 'countSearches'}
								{if $sortingOrder == 'ASC'}
									<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />
								{/if}
							{/if}
						</th>
						<th width="9%">
							<a href="?active={$active}&amp;sortingField=countSavings&amp;sortingOrder={if $sortingOrder == 'ASC' && $sortingField == 'countSavings'}DESC{else}ASC{/if}">[[Job Saves]]</a>
							{if $sortingField == 'countSavings'}
								{if $sortingOrder == 'ASC'}
									<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />
								{/if}
							{/if}
						</th>
						<th width="9%">
							<a href="?active={$active}&amp;sortingField=countViewedListings&amp;sortingOrder={if $sortingOrder == 'ASC' && $sortingField == 'countViewedListings'}DESC{else}ASC{/if}">[[Views]]</a>
							{if $sortingField == 'countViewedListings'}
								{if $sortingOrder == 'ASC'}
									<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />
								{/if}
							{/if}
						</th>
						<th width="9%">
							<a href="?active={$active}&amp;sortingField=countApplications&amp;sortingOrder={if $sortingOrder == 'ASC' && $sortingField == 'countApplications'}DESC{else}ASC{/if}">[[Applications]]</a>
							{if $sortingField == 'countApplications'}
								{if $sortingOrder == 'ASC'}
									<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />
								{/if}
							{/if}
						</th>
						<th class="tableRight"> </th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$jobsStat item=value}
						<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
							<td> </td>
							<td><a href="{$GLOBALS.site_url}/my-job-details/{$value.listingSID}/{$value.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html"><span class="strong">{$value.Title}</span></a></td>
							<td>{tr type="date"}{$value.postedDate}{/tr}</td>
							<td>{tr type="date"}{$value.expDate}{/tr}</td>
							<td>{$value.countSearches}</td>
							<td>{$value.countSavings}</td>
							<td>{$value.countViewedListings}</td>
							<td>{if $value.countApplications == "0"}{$value.countApplications}{else}<a href="{$GLOBALS.site_url}/system/applications/view/?appJobId={$value.listingSID}">{$value.countApplications}</a>{/if}</td>
							<td> </td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			{else}
				<p>[[You have no listings now]] [[Click]] <a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job">[[here]]</a> [[to add a new listing.]]</p>
			{/if}
		</div>
	</div>
{/if}
<script type="text/javascript">
	function submitForm() {
		form = document.getElementById("filterForJobsStat");
		form.submit();
	}
</script>