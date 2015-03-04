{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-invoices/">[[Invoices]]</a>&nbsp;&#187;&nbsp;[[Create Invoice]]{/breadcrumbs}
<h1><img src="{image}/icons/paperstar32.png" border="0" alt="" class="titleicon"/>[[Create Invoice]]</h1>
{include file='errors.tpl'}
<fieldset>
	<legend>[[Create Invoice]]</legend>
	<form method="post" enctype="multipart/form-data" onsubmit="disableSubmitButton('submitCreate');" >
		<input type="hidden" name="action" value="save">
		<input type="hidden" name="username" value="{$username}"/>
		<input type="hidden" name="user_sid" value="{$user_sid}"/>
		<table>
			<tr>
				<td>[[Customer]]:</td>
				<td><a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user_sid}">{$username}</a></td>
				<td>[[Invoice Status]]:</td>
				<td>{input property="status"}</td>
			</tr>
			<tr>
				<td>[[Invoice Date]]:</td>
				<td>{input property="date"}</td>
				<td>[[Payment Method]]:</td>
				<td>{input property="payment_method"}</td>
			</tr>
			{if $GLOBALS.settings.enable_taxes}
			<tr>
				<td></td>
				<td></td>
				<td>[[Include Tax]]</td>
				<td><input type="hidden" name="include_tax" value="0"/><input type="checkbox" name="include_tax" value="1" {if $include_tax}checked = "checked"{/if}/></td>
			</tr>
			{/if}
			<tr>
				<td colspan="4">
					{input property='items' template="items_complex.tpl"}
				</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align:right;">
					[[Sub Total]]
				</td>
				<td>
					{input property='sub_total'}
				</td>
			</tr>
			<tr id="tax_info" {if (!$include_tax || $tax.tax_name eq null)}style = "display:none"{/if}>
				<td colspan="3" style="text-align:right;">
					{$tax.tax_name}
					<input type="hidden" name="tax_info[tax_name]" value="{$tax.tax_name}"/>
					<input type="hidden" name="tax_info[sid]" value="{$tax.sid}"/>
					<input type="hidden" name="tax_info[price_includes_tax]" value="{$tax.price_includes_tax}"/>
					<input type="hidden" name="tax_info[tax_rate]" value="{tr type="float"}{$tax.tax_rate}{/tr}"/>
				</td>
				<td >
					<input type="text" name="tax_info[tax_amount]" value="{tr type="float"}{$tax.tax_amount}{/tr}"/>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align:right;">
					[[Total]]
				</td>
				<td>
					{input property='total'}
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="checkbox" name="send_invoice" value="1">[[Send Invoice to Customer]]
				</td>
				<td colspan="2">
					<div class="floatRight">
						<input type="submit" class="grayButton" value="[[Create]]" id="submitCreate" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<script type="text/javascript">
	$('input[name^="items[amount]"]').attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
	$('input[name^="sub_total"]').attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
	$('input[name^="total"]').attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
	$('input[name^="tax_info[tax_amount]"]').attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
</script>