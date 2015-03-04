{if $positions}
	<span class="strong">[[Experience]]</span><hr/>
	{foreach from=$positions item="position"}
		<p><span class="strong">{$position.employer}</span></p>
		<p><span class="strong">{$position.position}</span></p>
		<p>{$position.location}</p>
		<p>{$position.start_date} - {$position.end_date}</p>
		<p>{$position.description}</p>
	{/foreach}
	<p>&nbsp;</p>
{/if}