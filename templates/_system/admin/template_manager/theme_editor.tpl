{breadcrumbs}[[Themes]]{/breadcrumbs}

<script type="text/javascript">
	function not(){
		document.img1.src="{image}themes/{$theme}.png"
	}
	function imgOn(imgName) {
		if (document.images) {
			document.img1.src = imgName;
	}}
</script>

<h1><img src="{image}/icons/wand32.png" border="0" alt="" class="titleicon"/>[[Themes]]</h1>
	{if $ERROR eq "ALREADY_EXISTS"}
		<p class="error">[[Theme already exists]].</p>
	{elseif $ERROR eq "EMPTY_NAME"}
		<p class="error">[[Please enter a name of the New Theme]]</p>
	{elseif $ERROR eq "ACCESS_DENIED"}
		<p class="error">[[You don't have permissions for it. This is a Demo version of the software.]]</p>
	{/if}
	
	<form onsubmit="disableSubmitButton('submitCreate');">
		<fieldset>
			<legend>[[Create Theme]]</legend>
			<input type="hidden" name="action" value="copy_theme" />
			<table>
				<tr>
					<td>
						[[New Theme Name]]<br/>
						<input type="text" name="new_theme" value="" />
					</td>
					<td>
						[[Copy from]]<br/>
						<select name="copy_from_theme">
							{foreach from=$theme_list item="theme_name" key="theme_system_name"}
								<option value="{$theme_name|escape}"{if $theme eq $theme_name} SELECTED{/if}>{$theme_name|escape}</option>
							{/foreach}
						</select>
					</td>
					<td valign="bottom">
						<input type="submit" value="[[Create]]" class="greenButton" id="submitCreate" />
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
	<div class="clr"><br/></div>
	
	<div id="themes">
	<table>
		<thead>
			<tr>
				<th>[[Theme Name]]</th>
				<th>[[Current Theme]]</th>
				<th class="actions">[[Actions]]</th>
			</tr>
		</thead>
		<tbody>
			{assign var="counter" value=0}
			{foreach from=$theme_list item="theme_name" key="theme_system_name"}
				{assign var="counter" value=$counter+1}
				<tr class="{if $counter is odd}oddrow{else}evenrow{/if}" onmouseover="document.getElementById('pic').src='{$GLOBALS.user_site_url}/templates/{$theme_name}/main/images/{$theme_name}.png'" onmouseout="document.getElementById('pic').src='{$GLOBALS.user_site_url}/templates/{$theme}/main/images/{$theme}.png'">
					<td>{$theme_name}</td>
					<td>
						{if $theme ne $theme_name}
							<a href="?theme={$theme_name}" class="grayButton">[[Make current]]</a>
						{else}
							<strong>[[Current]]</strong>
						{/if}
					</td>
					<td>{if $theme ne $theme_name}<a href="?action=delete_theme&theme_name={$theme_name}" onclick="return confirm('[[Are you sure you want to delete this Theme?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a>{/if}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	</div>
	<div id="themesPic"><img src="{$GLOBALS.user_site_url}/templates/{$theme}/main/images/{$theme}.png" id="pic" /></div>