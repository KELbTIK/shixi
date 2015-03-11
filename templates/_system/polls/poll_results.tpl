<h1>[[Poll Results]]</h1>
{foreach from=$errors item=error}
	<p class="error">{$error}</p>
{/foreach}
<h3>[[{$pollInfo.question}]]</h3>

{foreach from=$result item=poll}
	<div class="progress">
		<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width:{$poll.vote}%;  background-color: #{$poll.color};">
			<span class="sr-only">[[{$poll.value}]] {$poll.vote}% </span>
		</div>
	</div>
{/foreach}
{if $show_total_votes}<br/><div>[[Total votes]]:&nbsp;{$count_vote}</div>{/if}
