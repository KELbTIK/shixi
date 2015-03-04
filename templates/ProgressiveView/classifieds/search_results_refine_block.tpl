{if !empty($currentSearch)}
	<table cellpadding="0" cellspacing="0" id="currentSearch">
		<thead>
			<tr>
				<th class="tableLeft">&nbsp;</th>
				<th>&nbsp;[[Current Search]]:</th>
				<th class="tableRight">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="3">
					{capture name="urlParams"}searchId={$searchId}&amp;action=undo{if $show_brief_or_detailed}&amp;show_brief_or_detailed={$show_brief_or_detailed}{/if}&amp;view={$view}{/capture}
					{foreach from=$currentSearch item="fieldInfo" key="fieldID"}
						<div class="separate-div">
							<div class="currentSearch"><span class="strong">[[{$fieldInfo.name}]]</span></div>
							{foreach from=$fieldInfo.field item="fieldValue" key="fieldType"}
								{if $fieldType == 'monetary'}
									{foreach from=$fieldValue item="val" key="realVal" name="loopVal"}
										{if $smarty.foreach.loopVal.iteration%2 == 0}
											[[to]]
										{/if}
										<span class="curSearchItem">
											<a href="?{$smarty.capture.urlParams}&amp;param={$fieldID}&amp;type={$fieldType}&amp;value={$realVal|escape:'url'}">[[(undo)]]</a>
											{tr}{$val}{/tr|escape:'html'}
										</span>
									{/foreach}
								{else}
									{foreach from=$fieldValue item="val" key="realVal"}
										<span class="curSearchItem">
											<a href="?{$smarty.capture.urlParams}&amp;param={$fieldID}&amp;type={$fieldType}&amp;value={$realVal|escape:'url'}">[[(undo)]]</a>
											{tr}{$val}{/tr|escape:'html'}
										</span>
									{/foreach}
								{/if}
							{/foreach}
						</div>
					{/foreach}
					<br/>
				</td>
			</tr>
		</tbody>
	</table>
	<br/>
{/if}

{if !empty($refineFields)}
	<script language="JavaScript" type="text/javascript" src="{common_js}/refine_search.js"></script>
	{capture name="trLess"}&#60;&nbsp;<span>[[Less]]</span>{/capture}
	{capture name="trMore"}&#62;&nbsp;<span>[[More]]</span>{/capture}

	<table cellpadding="0" cellspacing="0" width="100%" id="refineResults">
		<thead>
		<tr>
			<th class="tableLeft">&nbsp;</th>
			<th>[[Refine Results]]</th>
			<th class="tableRight">&nbsp;</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td colspan="3">
				{capture name="urlParams"}searchId={$searchId}&amp;action=refine{if $show_brief_or_detailed}&amp;show_brief_or_detailed={$show_brief_or_detailed}{/if}&amp;view={$view}{/capture}
				{foreach from=$refineFields item=refineField}
					<div class="separate-div">
						{if $refineField.show && $refineField.count_results}
							<div class="refine_button" id="{$refineField.field_name}"><div class="refine_icon">[+]</div><strong>[[{$refineField.caption}]]</strong></div>
							<div class="refine_block" style="display: none">
								{foreach from=$refineField.search_result item=val name=fieldValue}
								{if $smarty.foreach.fieldValue.iteration == 6}
								<div class="block_values" style="display: none">
									{/if}
									{capture name="refineFieldCriteria"}{$refineField.field_name}{if in_array($refineField.type, array('string'))}[multi_like_and]{else}[multi_like]{/if}[]={if $val.sid}{$val.sid}{else}{$val.value|escape:'url'}{/if}{/capture}
									<div class="refineItem">
										<a href="?{$smarty.capture.urlParams}&amp;{$smarty.capture.refineFieldCriteria}">{tr}{$val.value}{/tr|escape:'html'}</a>{if empty($refineField.criteria)}&nbsp;({$val.count}){/if}
									</div>
									{/foreach}
									{if $smarty.foreach.fieldValue.total >= 6}
								</div><div class="block_values_button">{$smarty.capture.trMore}</div>
								{/if}
							</div>
							<script type="text/javascript">RefineSearchBlock.restore('{$refineField.field_name}',true); </script>
						{/if}
					</div>
				{/foreach}
				<br/>
			</td>
		</tr>
		</tbody>
	</table>
	<script type="text/javascript" language="JavaScript">
		refineBlockBinder("{$smarty.capture.trLess|escape:"quotes"}", "{$smarty.capture.trMore|escape:"quotes"}");
	</script>
{/if}
{if !$GLOBALS.is_ajax}
	<!-- preloader row here -->
	<div id="refine-block-preloader"></div>
{/if}

