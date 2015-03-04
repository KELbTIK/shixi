<h1>[[View Questionnaire]]</h1>
<table border="0" cellpadding="0" cellspacing="0" class="tableSearchResultApplications" width="100%">
<thead>
	<tr>
		<th class="tableLeft"> </th>
		<th class="pointedInListingInfo2">[[Question]]</th>
		<th class="pointedInListingInfo2">[[Answer]]</th>
		<th class="tableRight"> </th>
	</tr>
</thead>
<tbody>
{foreach from=$questions key=question item=answer}
<tr class="{cycle values = 'evenrow,oddrow'}">
	<td>&nbsp;</td>
	<td>{$question}</td>
	<td>
		{if is_array($answer)}
			{foreach from=$answer item=answr}
				{$answr}<br/>
			{/foreach}
		{else}
			{$answer}
		{/if}
	</td>
	<td>&nbsp;</td>
</tr>
{/foreach}
</tbody>
</table>