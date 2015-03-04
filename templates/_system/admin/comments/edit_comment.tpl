{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-{$listingType.link}/?restore=1">
		[[Manage {$listingType.name}s]]
	</a>
	&#187; <a href="{$GLOBALS.site_url}/edit-listing/?listing_id={$comment.listing_id}">[[Edit Listing]]</a>
	&#187; <a href="{$GLOBALS.site_url}/listing-comments/?listing_id={$comment.listing_id}">[[Listing Comments]]</a> &#187;  [[Edit Comment]]
{/breadcrumbs}
<h1>[[Edit Comment]]</h1>

<form method="post">
	<input type="hidden" name="comment_id" value="{$comment.sid}" />
	<input type="hidden" name="listing_id" value="{$comment.listing_id}" />
	<input type="hidden" name="action" value="edit" />
	<textarea name="message" style="width:500px" rows="6">{$comment.message}</textarea><br /><br/>
	<span class="greenButtonInEnd"><input type="submit" class="greenButtonIn" name="" value="[[Save]]" /></span>
</form>
