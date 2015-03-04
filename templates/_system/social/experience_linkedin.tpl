{if $positions}
	<span class="strong">[[Experience]]</span>
	{foreach from=$positions item="position"}
		<p><span class="strong">{$position.company.name}</span></p>
		<p><span class="strong">{$position.title}</span></p>
		<p>{$position.company.industry} [[Industry]]</p>
		<p>{$position.start_date.month} {$position.start_date.year} - {$position.end_date} {if $position.present}[[Present]]{/if}</p>
		<p>{$position.summary}</p>
	{/foreach}
	<p>&nbsp;</p>
{/if}