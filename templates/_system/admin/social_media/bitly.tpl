{breadcrumbs}
	<a href="{$GLOBALS.site_url}/social-media/">[[Social Media]]</a> &#187; [[Bitly Settings]]
{/breadcrumbs}
<h1><img src="{image}/icons/gear32.png" border="0" alt="" class="titleicon"/>[[Bitly Settings]]</h1>

{foreach from=$errors item=error key=field}
	{if $error == 'EMPTY_VALUE'}
		<p class="error">'{$field}' [[is empty]]</p>
	{/if}
{/foreach}

<div id="social-media">
	<form method="post" action="{$GLOBALS.site_url}/social-media/bitly/" id="settingsPane">
		<input type="hidden" id="action" name="action" value="saveSettings" />
		<input type="hidden" id="page" name="page" value="#generalTab"/>
		<table>
			<thead>
				<tr>
					<th>[[Name]]</th>
					<th>[[Value]]</th>
				</tr>
			</thead>
			<tbody>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>Bitly Token ID <span class="color-red">*</span></td>
					<td><input type="text" name="bitlyTokenId" value="{$settings.bitlyTokenId}" /></td>
				</tr>
				<tr>
					<td colspan="2"><small>[[* Please register at <a href="http://www.bitly.com">www.bitly.com</a> and create a new <a href="https://bitly.com/a/create_oauth_app">application</a>. After your application is created insert generated <a href="https://bitly.com/a/oauth_apps">TokenID</a> in this field]]</small></td>
				</tr>
				<tr id="clearTable">
					<td colspan="2" align="right">
						<div class="floatRight">
							<input type="submit" class="grayButton" value="[[Save]]" />
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$("#settingsPane").tabs();
	});
</script>
