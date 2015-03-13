<h1>[[Poll Results]]</h1>
{foreach from=$errors item=error}
	<div class="error alert alert-danger" role="alert">{$error}</div>
{/foreach}
<h3>[[{$pollInfo.question}]]</h3>
{foreach from=$result item=poll}
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width:{$poll.vote}%;  background-color: #{$poll.color};">
			<span class="sr-only">{$poll.vote}% [[{$poll.value}]] </span>
		</div>
	</div>
{/foreach}
{if $show_total_votes}<br/><div>[[Total votes]]:&nbsp;{$count_vote}</div>{/if}
