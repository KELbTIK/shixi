{breadcrumbs}[[Update to new version]]{/breadcrumbs}

<h1>[[Update control]]</h1>
{if $zip_extension_loaded == true}

	<p class="note">
	[[Please enter your Client Area access details. You got these access details to our system when you purchased your license.]]
	</p>

	{foreach from=$errors item=error key=error_code}
		<p class="error">
			[[{$error}]]
		</p>
	{/foreach}

	<form method="post" action="{$GLOBALS.site_url}/update-to-new-version/">
		<table>
			<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
				<td width="150px">[[SJB Client Area Login]]</td>
				<td width="210px"><input type="text" name="auth_username"></td>
			</tr>

			<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
				<td>[[Password]]</td>
				<td><input type="password" name="auth_password"></td>
			</tr>

			<tr>
				<td>
					[[Update Type]]
				</td>
				<td>
					<input type="radio" id="way_to_updateAuto" name="way_to_update" value="autoUpdate" {if !isset($wayToUpdate) or $wayToUpdate eq 'autoUpdate'}checked="checked"{/if} /><label for="way_to_updateAuto">[[Auto]]</label><br/>
					<input type="radio" id="way_to_updateArch" name="way_to_update" value="makeArchive" {if $wayToUpdate eq 'makeArchive'}checked="checked"{/if} /><label for="way_to_updateArch">[[Get Archived Files]]</label><br/>
				</td>
			</tr>
			<tr>
				<td style="text-align: right;" colspan="2">
					{capture assign="trContinue"}[[Continue]]{/capture}
					<input class="grayButton" type="submit" name="update_to_version" value="[[{$trContinue|escape:'html'}]]" />
				</td>
			</tr>
		</table>
	</form>

{else}
	{foreach from=$errors item=error key=error_code}
		<p class="error">
			[[{$error}]]
		</p>
	{/foreach}
{/if}