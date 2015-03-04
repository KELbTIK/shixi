{breadcrumbs}
	{foreach name="foreach" item="item" from=$navigation}
		{if $item.reference eq ""}
			[[{$item.name}]]
		{else}
			<a href="{$item.reference}">[[{$item.name}]]</a>
		{/if}
		{if not $smarty.foreach.foreach.last} &#187; {/if}
	{/foreach}
{/breadcrumbs}

<h1><img src="{image}/icons/article32.png" border="0" alt="" class="titleicon"/> [[{$title}]]</h1>

{if !empty($errors.CANT_DELETE_FILES)}
	<p class="error">[[The following files could not be removed]]:</p>
	<p class="errorList">
	{foreach from=$errors.CANT_DELETE_FILES key=key item=file}
		-{$file};<br />
	{/foreach}
	</p>
{elseif !empty($result)}
	<p class="message">[[{$result}]]</p>
{/if}

{foreach from=$errors item=error}
	{if $error == 'NOT_ALLOWED_IN_DEMO'}
		<p class="error">[[This action is not allowed in Demo mode]]</p>
	{else}
	{/if}
{/foreach}

<p>[[Active theme]]: <b>{$GLOBALS.settings.CURRENT_THEME}</b></p>
{if $show_highlight_setting}
	<div class="clr"></div>
	<form>
		<table>
			<thead>
				<tr>
					<th>[[Highlight Templates On Frontend]]&nbsp;</th>
					<th align=center>
						<select name="highlight_templates">
							<option value="0"{if $highlight_templates == 0} selected="selected"{/if}>[[disable]]</option>
							<option value="1"{if $highlight_templates == 1} selected="selected"{/if}>[[enable]]</option>
						</select>
					</th>
					<th><input type="submit" name="highlight_submit" value="[[Save]]" class="grayButton"/></th>
				</tr>
			</thead>
		</table>
	</form>
{/if}
{if $show_clear_cache_setting}
<div class="clr"></div>
<form>
	<table>
		<thead>
			<tr>
				<th colspan="2">[[Clear Smarty Cache]]&nbsp;</th>
				<th><input type="submit" name="clear_cache_submit" value="[[Go]]" class="grayButton" /></th>
			</tr>
		</thead>
	</table>
</form>
{/if}