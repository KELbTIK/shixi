{if $liNumResults > 0}
	<div class="clearfix"></div>
	<h1>[[LinkedIn People Search Results]]:</h1>
	{if $liKeywordEmpty}
		<div class="message alert alert-info"> [[To see people outside your network, please enter a search keyword.]]</div>
	{/if}
	<div class="table-responsive">
		<table class="table table-condensed">
		<thead>
			<tr>
				<th>[[Name]]</th>
				<th>[[Title]]</th>
				<th>[[Industry]]</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			{foreach item="liPerson" from=$liResults name="liPersons"}
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td>{$liPerson.firstName} {$liPerson.lastName}</td>
					<td>{$liPerson.headline}</td>
					<td>{$liPerson.industry}</td>
					<td nowrap="nowrap" align="right">{if $liPerson.url}<a href="{$liPerson.url}" target="_blank" class="linkedin-icon">[[LinkedIn Profile]]</a>{/if}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	</div>
{elseif isset($liNumResults)}
	<h1>[[LinkedIn People Search Results]]:</h1>
	{if $liKeywordEmpty}
		<div class="message alert alert-info">[[To see people outside your network, please enter a search keyword.]]</div>
	{/if}
	<div class="message alert alert-info">[[no results from linkedin]]</div>
{/if}