<form id="formPayment" method="post" action="{$form_submit_url}">
	<input type="hidden" name="item_number" value="{$form_data_source.item_number}" />
	<input type="hidden" name="item_name" value="{$form_data_source.item_name}" />
	<input type="hidden" name="x_description" value="{$form_data_source.x_description}" />
	<input type="hidden" name="x_currency_code" value="{$form_data_source.x_currency_code}" />
	<input type="hidden" name="x_amount" value="{$form_data_source.x_amount}" />
	<input type="hidden" name="gw" value="{$form_data_source.gw}" />
	<input type="hidden" name="submit" value="1" />

	{if $errors}
		<b>[[Errors]]:</b>
		<ul>
			{foreach from=$errors item=error}
				<p class="error"">[[{$error}]]</p>
			{/foreach}
		</ul>
		<hr/>
		<br/>
	{/if}

	<table>
		<tr>
			<td colspan="2"><b>[[Order Information]]</b></td>
		</tr>
		<tr>
			<td colspan="2">[[Invoice Number]]: {$form_data_source.item_number}</td>
		</tr>
		<tr>
			<td colspan="2">[[Description]]: {$form_data_source.x_description}</td>
		</tr>
		<tr>
			<td colspan="2">[[Total]]: {capture assign="amount"}{tr type="float"}{$form_data_source.x_amount}{/tr}{/capture}{currencyFormat amount=$amount}</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2"><b>[[Credit Card Information]]</b></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<img src="{image}/creditcards/visa.gif" width="43" height="26" title="Visa" alt="Visa" />
				<img src="{image}/creditcards/mastercard.gif" width="41" height="26" title="MasterCard" alt="MasterCard" />
				<img src="{image}/creditcards/amex.gif" width="40" height="26" title="American Express" alt="American Express" />
				<img src="{image}/creditcards/discovery.gif" width="40" height="26" title="Discover" alt="Discover" />
				<img src="{image}/creditcards/dclub.gif" width="35" height="26" title="DinersClub" alt="DinersClub" />
				<img src="{image}/creditcards/jcb.gif" width="21" height="26" title="JCB" alt="JCB" />
			</td>
		</tr>
		<tr>
			<td>[[Card Number]] <span style="color:#ff0000;">*</span>:</td>
			<td>
				<input type="text" class="input_text" id="x_card_num" name="x_card_num" maxLength="16" value="{$form_data_source.x_card_num}" />&nbsp;([[enter number without spaces or dashes]])
			</td>
		</tr>
		<tr>
			<td>[[Expiration Date]] <span style="color:#ff0000;">*</span>:</td>
			<td>
				<input type="text" class="input_text" id="x_exp_date" name="x_exp_date" maxLength="20" value="{$form_data_source.x_exp_date}" />&nbsp;(mmyy)
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2"><b>[[Billing Information]]</b></td>
		</tr>
		<tr>
			<td>[[First Name]] <span style="color:#ff0000;">*</span>:</td>
			<td>
				<input type="text" class="input_text" id="x_first_name" name="x_first_name" maxLength="50" value="{$form_data_source.x_first_name}"/>
			</td>
		</tr>
		<tr>
			<td>[[Last Name]] <span style="color:#ff0000;">*</span>:</td>
			<td>
				<input type="text" class="input_text" id="x_last_name" name="x_last_name" maxLength="50" value="{$form_data_source.x_last_name}"/>
			</td>
		</tr>
		<tr>
			<td>[[Company]]:</td>
			<td>
				<input type="text" class="input_text" id="x_company" name="x_company" maxLength="50" value="{$form_data_source.x_company}"/>
			</td>
		</tr>
		<tr>
			<td>[[Address]]:</td>
			<td>
				<input type="text" class="input_text" id="x_address" name="x_address" maxLength="60" value="{$form_data_source.x_address}"/>
			</td>
		</tr>
		<tr>
			<td>[[City]]:</td>
			<td>
				<input type="text" class="input_text" id="x_city" name="x_city" maxLength="40" value="{$form_data_source.x_city}"/>
			</td>
		</tr>
		<tr>
			<td>[[State]]:</td>
			<td>
				<input type="text" class="input_text" id="x_state" name="x_state" maxLength="40" value="{$form_data_source.x_state}"/>
			</td>
		</tr>
		<tr>
			<td>[[Zip Code]]:</td>
			<td>
				<input type="text" class="input_text" id="x_zip" name="x_zip" maxLength="20" value="{$form_data_source.x_zip}"/>
			</td>
		</tr>
		<tr>
			<td>[[Country]]:</td>
			<td>
				<input type="text" class="input_text" id="x_country" name="x_country" maxLength="60" value="{$form_data_source.x_country}"/>
			</td>
		</tr>
		<tr>
			<td>[[Email]]:</td>
			<td>
				<input type="text" class="input_text" id="x_email" name="x_email" maxLength="255" value="{$form_data_source.x_email}"/>
			</td>
		</tr>
		<tr>
			<td>[[Phone]]:</td>
			<td>
				<input type="text" class="input_text" id="x_phone" name="x_phone" maxLength="25" value="{$form_data_source.x_phone}"/>
			</td>
		</tr>
		<tr>
			<td>[[Fax]]:</td>
			<td>
				<input type="text" class="input_text" id="x_fax" name="x_fax" maxLength="25" value="{$form_data_source.x_fax}"/>
			</td>
		</tr>
		<tr>
			<td colSpan="2">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" value="[[Submit]]" />
				<input type="reset" value="[[Reset]]" />
			</td>
		</tr>
	</table>
</form>