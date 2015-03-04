{breadcrumbs}
	<a href="{$GLOBALS.site_url}/social-media/">[[Social Media]]</a> &#187; <a href="{$GLOBALS.site_url}/social-media/{$network}">[[$networkName Settings]]</a> &#187;
	{if !$feed_sid}
		[[Add New Feed]]
	{else}
		[[Edit Feed]]
	{/if}
{/breadcrumbs}
{if !$feed_sid}
	<h1><img src="{image}/icons/linedpaperplus32.png" border="0" alt="" class="titleicon"/>[[Add New Feed]]</h1>
{else}
	<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit Feed]]</h1>
{/if}

{include file="errors.tpl"}
{foreach from=$errors item="error"}
	<p class="error">
		{if $error == 'APPROVE_ACCOUNT'}
			[[To edit the feed you need to refresh the token or change account by clicking "Change / Grant permission" button.]]
		{elseif $error == 'TOKEN_EXPIRED'}
			[[The access token for this feed is expired. To activate the feed you need to refresh the token or change account by clicking "Change / Grant permission" button.]]
		{else}
			[[{$error}]]
		{/if}
	</p>
{/foreach}
{* include network settings block *}
<div id="social-media">
	<form method="POST" id="addForm" onsubmit="disableSubmitButton('submitFeed');">
		<input type="hidden" name="action" value="save_feed" />
		<input type="hidden" name="soc_network" value="{$network}" />
		<input type="hidden" id="submit" name="submit" value="save"/>
		<input type="hidden" name="action_feed" value="{$action}" />
		<input type="hidden" name="authorized" value="{$authorized}" />
		{if $feed_sid}<input type="hidden" name="sid" value="{$feed_sid}" />{/if}

		<table id="social-feed-settings">
			{* include system block *}
			{include file="system_block.tpl"}

			{* include search block *}
			{include file="search_block.tpl"}

			{* Posting Settings Block *}
			{include file="posting_settings_block.tpl"}

			<tr id="clearTable">
				<td colspan="4">
					<div class="floatRight">
						{if $feed_sid}
							<input type="submit" id="apply" value="[[Apply]]" id="submitFeed" class="grayButton"/>
						{/if}
						<input type="submit" value="{if !$feed_sid}[[Add]]{else}[[Save]]{/if}" id="submitFeed" class="grayButton"/>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
	$('#apply').click(
		function(){
			$('input[name="submit"]').attr('value', 'apply');
		}
	);
</script>
