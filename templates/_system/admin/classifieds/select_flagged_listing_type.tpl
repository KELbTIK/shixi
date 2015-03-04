<p class="page_title">[[Flagged Listings]]</p>
<form method="post">
	<table>
		<tr>
			<td>[[Select Listing Type]]</td>
			<td>
				<select name="listing_type">
					<option value="">[[All]]</option>
				{foreach from=$listing_types item=type key=key}
					<option value="{$type.sid}">[[{$type.id}]]</option>
				{/foreach}
				</select>
			</td>
			<td><span class="greenButtonEnd"><input type="submit" name="sendForm" value="[[Select]]" class="greenButton" /></span></td>
		</tr>
	</table>
</form>