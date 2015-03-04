{capture name="edit_phrase"}[[Edit Phrase]]{/capture}
{capture name="loading"}[[Loading]]{/capture}
<script language="JavaScript" type="text/javascript" src="{common_js}/pagination.js"></script>
<script type="text/javascript">
	$.ui.dialog.prototype.options.bgiframe = true;
	$(function() {
		var content = '<img src="{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif" />';
		
		$(".goEditPhrase").click(function(){
			$("#editPhraseDialog").dialog('destroy');
			$("#editPhraseDialog").attr({ title: "{$smarty.capture.loading|escape:"javascript"}"});
			$("#editPhraseDialog").html(content).dialog({ width: 200});
			var link = $(this).attr("href");
			$.get(link, function(data){
				$("#editPhraseDialog").dialog('destroy');
				$("#editPhraseDialog").attr({ title: "{$smarty.capture.edit_phrase|escape:"javascript"}"});
				$("#editPhraseDialog").html(data).dialog({ width: 650});
			});
			return false;
		});
	});
	</script>

<div id="editPhraseDialog" style="display: none"></div>

{breadcrumbs}[[Manage Phrases]]{/breadcrumbs}
<h1><img src="{image}/icons/exchange32.png" border="0" alt="" class="titleicon"/>[[Manage Phrases]]</h1>
<p><a href="{$GLOBALS.site_url}/add-phrase/" class="grayButton">[[Add a New Phrase]]</a></p>

{if $errors}
	{include file="errors.tpl" errors=$errors}
{/if}

<div id="result">
	{if $result == 'added'}
		<p class="message">[[The new phrase was successfully added]]</p>
	{elseif $result == 'deleted'}
		<p class="message">[[The phrase was deleted]]</p>
	{/if}
</div>

<form method="post" action="{$GLOBALS.site_url}/manage-phrases/">
	<input type="hidden" name="curr_lang" id="curr_lang" value="{$criteria.language}" />
	<table>
		<tr>
			<td>[[Phrase ID]]:</td>
			<td><input type="text" name="phrase_id" value="{$criteria.phrase_id|escape}" /></td>
		</tr>
		<tr>
			<td>[[Domain]]:</td>
			<td>
				<select name="domain">
					<option value="">[[Any]]</option>
					{foreach from=$domains item=domain}
					<option value="{$domain}"{if $criteria.domain == $domain} selected="selected"{/if}>{$domain}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td>[[Languages]]:</td>
			<td>
				<select name="language">
					{foreach from=$languages item=language}
						<option value="{$language.id}"
						{if $criteria.language == $language.id}
							selected="selected"
							{assign var='chosen_language_id' value=$language.id}
							{assign var='chosen_language_caption' value=$language.caption}
						{/if}>{$language.caption}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
                <div class="floatRight">
                    <input type="hidden" name="action" value="search_phrases" />
                    <input type="submit" name="show" value="[[Show]]" class="grayButton" />
                </div>
			</td>
		</tr>
	</table>
</form>
<div class="clr"><br/></div>
<div class="box" id="displayResults">
	<div class="box-header">
		{include file="../pagination/pagination.tpl" layout="header"}
	</div>
	<div class="innerpadding">
		<div id="displayResultsTable">
			<table width="100%">
				<thead>
					<tr>
						<th>[[Phrase ID]]</th>
						<th>{$chosen_language_caption}</th>
						<th colspan="2" class="actions" width="1%">[[Actions]]</th>
					</tr>
				</thead>
				{if !empty($found_phrases)}
					{foreach from=$found_phrases item=phrase}
						{if $phrase.domain != $domain}
							<tr>
								<th colspan="4">{$phrase.domain}</th>
							</tr>
						{/if}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td><a href="{$GLOBALS.site_url}/edit-phrase/?phrase={$phrase.id|escape:"url"}&amp;domain={$phrase.domain|escape:"url"}&amp;current_lang={$chosen_language_id|escape:"url"}" title="[[Edit]]" class="goEditPhrase">{$phrase.id|escape}</a></td>
							<td class="translated"><a href="{$GLOBALS.site_url}/edit-phrase/?phrase={$phrase.id|escape:"url"}&amp;domain={$phrase.domain|escape:"url"}&amp;current_lang={$chosen_language_id|escape:"url"}" class="goEditPhrase">{$phrase.translations.$chosen_language_id|escape}</a></td>
							<td><a href="{$GLOBALS.site_url}/edit-phrase/?phrase={$phrase.id|escape:"url"}&amp;domain={$phrase.domain|escape:"url"}&amp;current_lang={$chosen_language_id|escape:"url"}" title="[[Edit]]" class="goEditPhrase editbutton">[[Edit]]</a></td>
							<td nowrap="nowrap">
								{capture name="delete_message"}[[Do you want to delete]] `{$phrase.id|escape:"javascript"}` [[phrase]]?{/capture}
								{capture name="delete_confirm_script"}return confirm('{$smarty.capture.delete_message|escape:"javascript"}');{/capture}
								<a href="?action=delete_phrase&amp;phrase={$phrase.id|escape:"url"}&amp;phrases_per_page={$phrases_per_page}&amp;domain={$phrase.domain|escape:"url"}" onclick="{$smarty.capture.delete_confirm_script|escape:"html"}" title="[[Delete]]" class="deletebutton">[[Delete]]</a>
							</td>
						</tr>
						{assign var=domain value=$phrase.domain}
					{/foreach}
				{/if}
			</table>
		</div>
	</div>
	<div class="box-footer">
		{include file="../pagination/pagination.tpl" layout="footer"}
	</div>
</div>