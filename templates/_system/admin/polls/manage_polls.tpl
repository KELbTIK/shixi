{breadcrumbs}[[Manage Polls]]{/breadcrumbs}
<h1><img src="{image}/icons/bargraph32.png" border="0" alt="" class="titleicon"/>[[Manage Polls]]</h1>
<script language="JavaScript" type="text/javascript" src="{common_js}/pagination.js"></script>
{include file="field_errors.tpl"}
<p><a href="{$GLOBALS.site_url}/manage-polls/?action_name=new" class="grayButton">[[Add a New Poll]]</a></p>

<form action="">
	<input type="hidden" name="action_name" value="save_display_setting" />
	<table>
		<tr id="clearTable">
            <td>[[Polls Block Display On Front-end]]  &nbsp;</td>
            <td align=center>
                <select name="settings[show_polls_on_main_page]" onchange="javascript: form.submit();">
                    <option value="0"{if $show_polls_on_main_page == 0} selected="selected"{/if}>[[Disable]]</option>
                    <option value="1"{if $show_polls_on_main_page == 1} selected="selected"{/if}>[[Enable]]</option>
                </select>
            </td>
            <td></td>
		</tr>
	</table>
</form>


<form method="post" action="" name="resultsForm">
	<input type="hidden" name="action_name" id="action_name" value="" />
	<div class="box" id="displayResults">
		<div class="box-header">
			{include file="../pagination/pagination.tpl" layout="header"}
		</div>
		<div class="innerpadding">
			<div id="displayResultsTable">
				<table class="basetable" width="100%">
					<thead>
						{include file="../pagination/sort.tpl"}
					</thead>
					<tbody>
					{foreach from=$pollsInfo item=poll name=fields_block}
						<tr class="{cycle values = 'evenrow,oddrow' advance=false}" onmouseover="this.className='highlightrow'" onmouseout="this.className='{cycle values = 'evenrow,oddrow'}'">
							<td><input type="checkbox" name="polls[{$poll.sid}]" value="1" id="checkbox_{$smarty.foreach.fields_block.iteration}" /></td>
							<td>{$poll.sid}</td>
							<td>{$poll.question}</td>
							<td>[[{$poll.user_group}]]</td>
							<td>{$poll.start_date}</td>
							<td>{$poll.end_date}</td>
							<td>{if $poll.active == 1}[[Active]]{else}[[Not Active]]{/if}</td>
							<td>
								{foreach from=$frontendLanguages item=language}
									{if $language.id == $poll.language}{$language.caption}{/if}
								{/foreach}
							</td>
							<td nowrap="nowrap"><a href="{$GLOBALS.site_url}/manage-polls/?action_name=edit&amp;sid={$poll.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
							<td nowrap="nowrap"><a href="{$GLOBALS.site_url}/manage-polls/?action_name=delete&amp;sid={$poll.sid}" onclick="return confirm('{$paginationInfo.translatedText.delete|escape}')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="box-footer">
			{include file="../pagination/pagination.tpl" layout="footer"}
		</div>
	</div>
</form>
