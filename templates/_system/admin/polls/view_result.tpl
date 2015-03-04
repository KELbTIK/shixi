<h3>[[{$pollInfo.question}]]</h3>
<table>
<tbody>
{foreach from=$result item=poll}
	<tr><td>[[{$poll.value}]]</td><td>{if $poll.vote != 0}<div style="width:{$poll.width}px;  background-color: #{$poll.color}; border: 1px solid #000;">&nbsp;</div>{/if}</td><td>{$poll.vote}%</td></tr>
{/foreach}
</tbody>
</table>
<br/>
<div>[[Total votes]]:&nbsp;{$count_vote}</div>
