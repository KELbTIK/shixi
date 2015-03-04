<tr>
	{if $paginationInfo.isCheckboxes == true}
		<th><input type="checkbox" id="all_checkboxes_control" onclick="setAllCheckboxes();"></th>
	{/if}
	{foreach  from=$paginationInfo.fields key=fieldKey item=fieldInfo}
		{if $fieldInfo.isVisible == true}
			<th>
				{if $fieldInfo.isSort == false}
					[[{$fieldInfo.name}]]
				{else}
					<a href="?{if $paginationInfo.restore == 1}restore=1&amp;{/if}sortingField={$fieldKey}&amp;sortingOrder={if $paginationInfo.sortingOrder == 'ASC' && $paginationInfo.sortingField == $fieldKey}DESC{else}ASC{/if}&amp;itemsPerPage={$paginationInfo.itemsPerPage}&amp;page=1{if $paginationInfo.uniqueUrlParams}{if is_array($paginationInfo.uniqueUrlParams)}{foreach from=$paginationInfo.uniqueUrlParams key=id item=param}&{$id}={if $param.escape}{$param.value|escape:"{$param.escape}"}{else}{$param.value}{/if}{/foreach}{else}&{$paginationInfo.uniqueUrlParams}{/if}{/if}">[[{$fieldInfo.name}]]</a>
					{if $paginationInfo.sortingField == $fieldKey}
						{if $paginationInfo.sortingOrder == 'DESC'}
							<img src="{image}b_down_arrow.gif" />
						{else}
							<img src="{image}b_up_arrow.gif" />
						{/if}
					{/if}
				{/if}
			</th>
		{/if}
	{/foreach}
	{if $paginationInfo.countActionsButtons}
		<th colspan="{$paginationInfo.countActionsButtons}" width="1%">
			[[Actions]]
		</th>
	{/if}
</tr>