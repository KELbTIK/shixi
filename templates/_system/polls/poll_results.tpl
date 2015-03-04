<h1>[[Poll Results]]</h1>
{foreach from=$errors item=error}
	<p class="error">{$error}</p>
{/foreach}
<h3>[[{$pollInfo.question}]]</h3>
<table style="width:70%">
<tbody>
{foreach from=$result item=poll}
	<tr>
		<td style="width:30%" >[[{$poll.value}]]</td>
		<td style="width:60%">{if $poll.vote != 0}<div style="width:{$poll.vote}%;  background-color: #{$poll.color}; border: 1px solid #000;">&nbsp;</div>{/if}</td>
		<td>{$poll.vote}%</td>
	</tr>
{/foreach}
</tbody>
</table>
{if $show_total_votes}<br/><div>[[Total votes]]:&nbsp;{$count_vote}</div>{/if}
