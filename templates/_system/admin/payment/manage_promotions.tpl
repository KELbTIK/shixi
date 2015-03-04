{breadcrumbs}[[Promotions]]{/breadcrumbs}
<h1><img src="{image}/icons/paperstar32.png" border="0" alt="" class="titleicon"/>[[Promotions]]</h1>
<p>[[Promotions allow you to offer discounts to users for certain products and services]]</p>
<div class="clr"></div>
<form>
	<input type="hidden" name="action" value="setting" />
	<table>
		<thead>
			<tr>
				<th>[[Enable Promotion Codes]]&nbsp;</th>
				<th align=center><input type="checkbox" name="enable_promotion_codes" value="1" {if $GLOBALS.settings.enable_promotion_codes == 1}checked = checked{/if} onChange="javascript: form.submit();" /></th>
			</tr>
		</thead>
	</table>
</form>
<p><a href="{$GLOBALS.site_url}/add-promotion-code/" class="grayButton">[[Add a New Promotion Code]]</a></p>
{foreach from=$errors item="error_message" key="error"}
	{if $error eq "DATE_IS_NOT_VALID"}
		<p class="error">[[Please change the expiration date first]]</p>
	{elseif $error eq "MAX_USES_ACHIEVED"}
		<p class="error">[[Please change the 'Maximum Uses' first]]</p>
	{/if}
{/foreach}
<table>
	<thead>
	<tr>
		<th>[[Promotion Code]]</th>
		<th>[[Discount]]</th>
		<th>[[Uses]]</th>
		<th>[[Start Date]]</th>
		<th>[[Expiry Date]]</th>
		<th>[[Status]]</th>
		<th colspan="4" class="actions">[[Actions]]</th>
	</tr>
	</thead>
	<tbody>
	{foreach from=$promotions item=promotion name=promotion_block}
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>{$promotion.code}</td>
			{capture assign="discount"}{tr type="float"}{$promotion.discount}{/tr}{/capture}
			<td>{if $promotion.type == 'percentage'}{$discount}%{else}{currencyFormat amount=$discount sign=$currency.currency_sign}{/if}</td>
			<td>{$promotion.uses}{if $promotion.maximum_uses != 0}/{$promotion.maximum_uses}{/if}</td>
			<td>{tr type="date"}{$promotion.start_date}{/tr}</td>
			<td>{tr type="date"}{$promotion.end_date}{/tr}</td>
			<td>{if $promotion.active == 'used'}[[Used]]{elseif $promotion.active == 1}[[Active]]{elseif $promotion.active == 2}[[Expired]]{else}[[Not Active]]{/if}</td>
			<td>
				{if $promotion.active == 1}
					<input type="button" value="[[Deactivate]]" class="deletebutton" onclick="location.href='{$GLOBALS.site_url}/promotions/?action=deactivate&sid={$promotion.sid}'"/>
				{else}
					<input type="button" value="[[Activate]]" class="editbutton" onclick="location.href='{$GLOBALS.site_url}/promotions/?action=activate&sid={$promotion.sid}'"/>
				{/if}
			</td>
			<td><input type="button" value="[[Edit]]" class="editbutton" onclick="location.href='{$GLOBALS.site_url}/edit-promotion-code/?sid={$promotion.sid}'"/></td>
			<td><a href="{$GLOBALS.site_url}/promotions/?action=delete&sid={$promotion.sid}" onClick="return confirm('[[Are you sure you want to delete this code?]]');" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
			{capture name="trViewLog"}[[View Log]]{/capture}
			<td><input type="button" value="{$smarty.capture.trViewLog|escape:'html'}" class="editbutton" onclick="location.href='{$GLOBALS.site_url}/promotions/log/{$promotion.sid}/'"/></td>
		</tr>
	{/foreach}
	</tbody>
</table>