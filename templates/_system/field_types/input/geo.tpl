<input id="{if $parentID}{$parentID}_{$id}{else}{$id}{/if}" type="text" class="inputGeo form-control {if $complexField}complexField{/if}" value="{$value}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{elseif $parentID}{$parentID}[{$id}]{else}{$id}{/if}" />

{if $useAutocomplete == 1}
	{include file='../field_types/search/autocomplete.tpl'}
{/if}