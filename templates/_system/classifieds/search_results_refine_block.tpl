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
						<div class="currentSearch"><span class="strong">[[{$fieldInfo.name}]]</span></div>
						{foreach from=$fieldInfo.field item="fieldValue" key="fieldType"}
							{if $fieldType == 'monetary'}
								{foreach from=$fieldValue item="val" key="realVal" name="loopVal"}
									{if $smarty.foreach.loopVal.iteration%2 == 0}
										[[to]]
									{/if}
									<span class="curSearchItem">
										<a href="?{$smarty.capture.urlParams}&amp;param={$fieldID}&amp;type={$fieldType}&amp;value={$realVal|escape:'url'}">[[(undo)]]</a>
										&nbsp;{tr}{$val}{/tr|escape:'html'}
									</span>
								{/foreach}
							{else}
								{foreach from=$fieldValue item="val" key="realVal"}
									<span class="curSearchItem">
										<a href="?{$smarty.capture.urlParams}&amp;param={$fieldID}&amp;type={$fieldType}&amp;value={$realVal|escape:'url'}">[[(undo)]]</a>
										&nbsp;{tr}{$val}{/tr|escape:'html'}
									</span><br/>
								{/foreach}
							{/if}
						{/foreach}
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

	{capture name="trLess"}&nbsp;&nbsp;&#171;&nbsp;[[less]]{/capture}
	{capture name="trMore"}&nbsp;&nbsp;&#187;&nbsp;[[more]]{/capture}

	<div id="refineResults">
        <h3>[[Refine Results]]:</h3>
        <div class="separator"></div>
        {capture name="urlParams"}searchId={$searchId}&amp;action=refine{if $show_brief_or_detailed}&amp;show_brief_or_detailed={$show_brief_or_detailed}{/if}&amp;view={$view}{/capture}
        {foreach from=$refineFields item=refineField}
            {if $refineField.show && $refineField.count_results}
                <!-- accordion start -->
                <div class="panel-group panel-dark" id="accordion-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion-2" href="#{$refineField.field_name}" class="collapsed">
                                    [[{$refineField.caption}]]
                                </a>
                            </h4>
                        </div>
                        <div id="{$refineField.field_name}" class="panel-collapse collapse">
                            <div class="panel-body">
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
                        </div>
                    </div>
                </div>
                <!-- accordion end -->
                <script type="text/javascript">RefineSearchBlock.restore('{$refineField.field_name}', true); </script>
            {/if}
        {/foreach}
        <br/>
	</div>
	<script type="text/javascript" language="JavaScript">
		refineBlockBinder("{$smarty.capture.trLess|escape:"quotes"}", "{$smarty.capture.trMore|escape:"quotes"}");
	</script>
{/if}
{if !$GLOBALS.is_ajax}
	<!-- preloader row here -->
	<div id="refine-block-preloader"></div>
{/if}

