{breadcrumbs}<a href="{$GLOBALS.site_url}/listing-feeds/">[[XML Feeds]]</a> &#187; [[Add Feed]]{/breadcrumbs}
<h1><img src="{image}/icons/rss32.png" border="0" alt="" class="titleicon"/>[[Add New Feed]]</h1>

{foreach from=$errors item=error}
	<p class="error">[[{$error.message}]]</p>
{/foreach}

<form method="post" action="">
	<input type="hidden" name="action" value="add"/>
	<table>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Name]]</td>
			<td><input type="text" name="feed_name" value="{$feed.name}"/></td>
		</tr>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Template]]</td>
			<td><input type="text" name="feed_template" value="{$feed.template}"/></td>
		</tr>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Type]]</td>
			<td><select name="typeId">
				{foreach from=$listingTypes item=type}
					<option value="{$type.sid}"{if $feed.type eq $type.sid} selected="selected"{/if}>{$type.id}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Listings Limit]]</td>
			<td><input type="text" name="count_listings" value="{$feed.count_listings}"></td>
		</tr>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[MIME-type]]</td>
			<td><input type="text" name="mime_type" value="{$feed.mime_type|default:'application/rss+xml'}"></td>
		</tr>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td colspan="2">[[Feed Description]]</td>
		</tr>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td colspan="2"><textarea name="feed_desc" rows="5" cols="35">{$feed.description}</textarea></td>
		</tr>
		<tr id="clearTable">
			<td colspan="2">
                <div class="clr"><br/></div>
                <div class="floatRight"><input type="submit" name="addFeed" value="[[Add Feed]]" class="greenButton" /></div>
            </td>
		</tr>
	</table>
</form>