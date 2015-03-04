<select name="{$id}[geo][radius]">
	<option value="any">[[Any Distance]]</option>
	<option value="10" {if $value.radius == 10}selected="selected"{/if}>[[Within]] 10 [[{$GLOBALS.radius_search_unit}]]</option>
	<option value="20" {if $value.radius == 20}selected="selected"{/if}>[[Within]] 20 [[{$GLOBALS.radius_search_unit}]]</option>
	<option value="30" {if $value.radius == 30}selected="selected"{/if}>[[Within]] 30 [[{$GLOBALS.radius_search_unit}]]</option>
	<option value="40" {if $value.radius == 40}selected="selected"{/if}>[[Within]] 40 [[{$GLOBALS.radius_search_unit}]]</option>
	<option value="50" {if $value.radius == 50}selected="selected"{/if}>[[Within]] 50 [[{$GLOBALS.radius_search_unit}]]</option>
</select>