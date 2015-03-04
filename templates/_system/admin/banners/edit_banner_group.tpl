{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-banner-groups/">[[Banners]]</a> &#187; '{$bannerGroup.id}' [[Group]]{/breadcrumbs}
<h1><img src="{image}/icons/slide32.png" border="0" alt="" class="titleicon"/>'{$bannerGroup.id}' [[Group]]</h1>
{if $errors }
	{foreach from=$errors item=error}
		<p class="error">[[{$error}]]</p>
	{/foreach} 
{/if}

<fieldset>
	<legend>[[Edit Banner Group]]</legend>

	<form method="post" enctype="multipart/form-data">
		<table>
			<input type="hidden" name="action" value="edit" />
			<input type="hidden" id="submit" name="submit" value="save_banner" />
			<input type="hidden" name="groupSID" value="{$bannerGroup.sid}" />
			<tr>
				<td valign="top">[[Group ID]]</td>
				<td><input type="text" name="groupID" maxlength="20" value="{$bannerGroup.id}" /></td>
			</tr>
			<tr>
				<td valign="top">[[Number of Banners to Display At Once]]</td>
				<td><input type="text" name="number_banners_display_at_once" maxlength="20" value="{$bannerGroup.number_banners_display_at_once}" /></td>
			</tr>
			<tr>
				<td colspan="2" align="right">
					<div class="floatRight">
						<input type="submit" id="apply" value="[[Apply]]" class="grayButton"/>
						<input type="submit" name="send" value="[[Save]]" class="grayButton" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
<div class="banner-clue">
	[[Please insert the following code to the templates where you want this banner group to appear]]:<br>
	<span>{ldelim}module name="banners" function="show_banners" group="{$bannerGroup.id}"{rdelim}</span>
</div>

<script>
	$('#apply').click(
		function(){
			$('#submit').attr('value', 'apply');
		}
	);
</script>