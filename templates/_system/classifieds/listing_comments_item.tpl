<div style="padding: 5px; margin: 0; {if $iteration is even};background-color: #efefef;border: 1px solid #eee{else};background-color: #fefefe;border: 1px solid #ccc{/if}" class="comment_item">
	<table>
		<tr>
			<td rowspan="2">
			</td>
			<td>
				<a href="#comment_{$comment.id}">#</a>
				[[Author]]: <span class="strong">{$comment.user.username}</span>
				<br/>
				<span class="small">{$comment.added|date_format:"%d.%m.%Y %H:%M"}</span>
			</td>
		</tr>
		<tr>
			<td>
			{$comment.message}
			</td>
		</tr>
	</table>
</div>