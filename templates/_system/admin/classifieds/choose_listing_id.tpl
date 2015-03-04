<div class="location">[[Choose Listing]]</div>
<p class="page_title">[[Choose Listing]]</p>
<form>
	[[Listing ID]]
	<select name="listing_id">
		{foreach from=$listing_ids item=listing}
			<option value="{$listing.id}">[[{$listing.id}]]</option>
		{/foreach}
	</select>
	<span class="greenButtonEnd"><input type="submit" value="[[Display]]" class="greenButton"/></span>
</form>