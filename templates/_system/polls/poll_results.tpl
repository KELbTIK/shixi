<h1>[[Poll Results]]</h1>
{foreach from=$errors item=error}
	<p class="error">{$error}</p>
{/foreach}
<h3>[[{$pollInfo.question}]]</h3>
<table style="width:70%">
<tbody>
{foreach from=$result item=poll}
	<tr  class="progress">
		<td style="width:30%" >[[{$poll.value}]]</td>
		<td style="width:60%">{if $poll.vote != 0}<div class="progress-bar" style="width:{$poll.vote}%;  background-color: #{$poll.color}; border: 1px solid #000;">&nbsp;</div>{/if}</td>
		<td>{$poll.vote}%</td>

	</tr>
	<div class="progress">
		<div class="col-md-4">[[{$poll.value}]]</div>
		<div class="col-md-8"></div>
		<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: {$poll.vote}%">
			<span class="sr-only">{$poll.vote}% {$poll.value}</span>
		</div>
	</div>
{/foreach}
</tbody>
</table>
{if $show_total_votes}<br/><div>[[Total votes]]:&nbsp;{$count_vote}</div>{/if}
