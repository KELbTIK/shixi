{breadcrumbs}[[Plugins]]{/breadcrumbs}
<h1><img src="{image}/icons/plug32.png" border="0" alt="" class="titleicon"/>[[Plugins]]</h1>


{foreach from=$errors item="error"}
	<p class="error">[[{$error}]]</p>
{foreachelse}
	{if $saved}
		<p class="message">[[Saved Successfully]]</p>
	{/if}
{/foreach}

<form method="post">
	{foreach from=$groups item=plugins key=group}
	<h3>[[{$group} Plugins]]</h3>
		<table>
			<thead>
				<tr>
					<th>[[Plugin Name]]</th>
					<th>[[Status]]</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$plugins item=plugin key=key}
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td>{$plugin.name}</td>
						<td>
							<input type="hidden" name="path[{$key}]" value="{$plugin.config_file}" />
							<input type="hidden" name="active[{$key}]" value="{if $plugin.socialMedia}1{else}0{/if}" />
							<input type="checkbox" name="active[{$key}]" value="1" {if $plugin.active == 1}checked="checked"{/if} {if $plugin.group}id="{$plugin.group_id}_{$plugin.name}" onchange="pluginsGroup('{$plugin.group_id}', '{$plugin.name}')" {/if} {if $plugin.socialMedia}disabled="disabled"{/if} />
						</td>
						<td>
							{if $plugin.socialMedia}
								<a href="{$GLOBALS.site_url}/social-media/{$plugin.socialMedia}" class="editbutton">[[Settings]]</a>
							{else}
								{if $plugin.active == 1 && $plugin.settings == 1}<a href="?action=settings&amp;plugin={$plugin.name}" class="editbutton">[[Settings]]</a>{/if}
							{/if}
						</td>
					</tr>
				{/foreach}
		</tbody>
			<tr id="clearTable">
				<td colspan="3">
					<div class="floatRight">
						<input type="hidden" name="action" value="save" />
						<input type="submit" class="grayButton" value="[[Save]]" />
					</div>
				</td>
			</tr>
		</table>
	{/foreach}
</form>

<script>
    function pluginsGroup(group, pluginName)
    {ldelim}
        {foreach from=$plugins item=plugin key=key}
            if ("{$plugin.group_id}" && "{$plugin.group_id}" == group ){ldelim}
                    if ($("#"+group+"_"+pluginName).attr("checked") == true && "{$plugin.name}" != pluginName){ldelim}
                        $("#"+group+"_{$plugin.name}").removeAttr("checked");
                    {rdelim}
            {rdelim}
        {/foreach}
    {rdelim}
</script>