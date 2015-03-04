{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-invoices/">[[Invoices]]</a>&nbsp;&#187;&nbsp;[[Edit Invoice]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit Invoice]]</h1>
{include file='errors.tpl'}
<fieldset>
	<legend>&nbsp;[[Edit Invoice]]</legend>
	<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="action" value="save" id="action">
	<input type="hidden" name="sid" value="{$invoice_sid}"/>
		<table>
			<tr>
				<td>[[Customer]]:</td>
				{if $userExists}
					{display property="subuser_sid" assign = subuser}
					<td><a href="{$GLOBALS.site_url}/edit-user/?user_sid={if $subuser > 0}{$subuser}{else}{display property="user_sid"}{/if}">{$username|escape:'html'}</a></td>
				{else}
					<td><span class="invoice-washy">[[User deleted]]</span></td>
				{/if}
				<td>[[Invoice Status]]:</td>
				<td>{input property="status"}</td>
			</tr>
			<tr>
				<td>[[Invoice Date]]:</td>
				<td>{input property="date"}</td>
				<td>[[Payment Method]]:</td>
				<td>{input property="payment_method"}</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>[[Include Tax]]</td>
				<td><input type="hidden" name="include_tax" value="0"/><input type="checkbox" name="include_tax" value="1" {if $include_tax}checked = checked{/if}/></td>
			</tr>
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
			<tr id="tax_info" {if !$include_tax || !$tax.sid}style = "display:none"{/if}>
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
				<td colspan="4">
					<div class="floatRight">
						{if $userExists}
							<a class="grayButton" href="{$GLOBALS.site_url}/edit-invoice/?sid={$invoice_sid}&amp;action=send_invoice" id="send_invoice">[[Send Invoice to Customer]]</a>
							<a class="grayButton" href="{$GLOBALS.site_url}/edit-invoice/?sid={$invoice_sid}&amp;action=download_pdf_version">[[Download PDF Version]]</a>
							<a class="grayButton" href="{$GLOBALS.site_url}/print-invoice/?sid={$invoice_sid}&amp;action=print" target="_blank">[[Print Invoice]]</a>
						{/if}
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<div class="floatRight">
						<input type="submit" class="grayButton" value="[[Apply]]" id="apply"/>
						<input type="submit" class="grayButton" value="[[Save]]" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
<br/><br/>
{include file="transactions_by_invoice.tpl"}
<div id="dialog" style="display: none"></div>

<script type="text/javascript">
	$(document).ready(function() {
		$('input[name^="items[amount]"]').attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
		$('input[name^="sub_total"]').attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
		$('input[name^="total"]').attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");
		$('input[name^="tax_info[tax_amount]"]').attr("readonly", "readonly").css("background-color","#F0F0F0").css("color","#A1A1A1");

		$('#apply').click(
			function(){
				$('#action').val('apply');
			}
		);
		var progbar = "<img src='{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif' />";
		$('#send_invoice').click( function() {
			$("#dialog").dialog('destroy');
			$("#dialog").attr({ title: "[[Loading]]"});
			$("#dialog").html(progbar).dialog({ width: 180});
			$.get($(this).attr('href'), function(data){
				$("#dialog").dialog('destroy');
				$("#dialog").attr({ title: "[[Sending Invoice To Customer]]"});
				$("#dialog").html(data).dialog({
					width: 560,
					close: function(event, ui) { }
				});
			});
			return false;
		});
	});

</script>
