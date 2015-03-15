{breadcrumbs}<a href="{$GLOBALS.site_url}/system/payment/gateways/">[[Payment Gateways]]</a> &#187; {$gateway.caption}{/breadcrumbs}
<h1>[[Configure]] {$gateway.caption}</h1>
<p>
	{if $gateway}
		{if $gateway.active}
			[[This gateway is currently active. Click here to <a href="?action=deactivate&gateway=$gateway.id">deactivate </a>it.]]
		{else}
			[[This gateway is currently inactive. Click here to <a href="?action=activate&gateway=$gateway.id">activate </a>it.]]
		{/if}
	{/if}
</p>
{if $gateway.template}
	<p><a href="{$GLOBALS.site_url}/edit-templates/?module_name=payment&template_name={$gateway.template}">[[Edit the instructions page]]</a></p>
{/if}
{foreach from=$errors key=error item=error_data}
	{if $error == "NOT_IMPLEMENTED"}<p class="error">[[There is something not yet implemented in the system]]</p>{/if}
	{if $error == "API_LOGIN_ID_IS_NOT_SET"}<p class="error">[[API Login ID is not set]]</p>{/if}
	{if $error == "TRANSACTION_KEY_IS_NOT_SET"}<p class="error">[[Transaction Key is not set]]</p>{/if}
	{if $error == "MD5_HASH_IS_NOT_SET"}<p class="error">[[MD5-Hash is not set]]</p>{/if}
	{if $error == "CURRENCY_CODE_IS_NOT_SET"}<p class="error">[[Currency Code is not set]]</p>{/if}
	{if $error == "GATEWAY_NOT_FOUND"}<p class="error">[[Gateway not found]]</p>{/if}
	{if $error == "PAYMENT_ID_IS_NOT_SET"}<p class="error">[[Payment ID is not set]]</p>{/if}
	{if $error == "NONEXISTED_PAYMENT_ID_SPECIFIED"}<p class="error">[[Specified payment ID does not exist]]</p>{/if}
	{if $error == "PAYMENT_IS_NOT_PENDING"}<p class="error">[[Payment status is not pending]]</p>{/if}
	{if $error == "EMAIL_IS_NOT_SET"}<p class="error">[[Email address is not set]]</p>{/if}
	{if $error == "NOT_VERIFIED"}<p class="error">[[Payment procedure is not verified]]</p>{/if}
	{if $error == "USER_NAME_IS_NOT_SET"}<p class="error">[[User name is not set]]</p>{/if}
	{if $error == "USER_PASSWORD_IS_NOT_SET"}<p class="error">[[User password is not set]]</p>{/if}
	{if $error == "USER_SIGNATURE_IS_NOT_SET"}<p class="error">[[User signature is not set]]</p>{/if}
	{if $error == 'SETTINGS_SAVED_WITH_PROBLEMS'}<p class="error">[[Gateway settings saved with problems, please try again]]</p>{/if}

{/foreach}
{if $gatewaySaved}<p class="message">[[Your changes were successfully saved]]<p>{/if}
{if $form_fields}
	<form method=post>
		<table>
			<thead>
				<tr>
					<th>[[Parameter]]</th>
					<th colspan="2">[[Current Value]]</th>
				</tr>
			</thead>
			{foreach from=$form_fields key=field_id item=field_info}
				<tr class="{cycle values='oddrow,evenrow' }">
					<td valign="top">[[{$field_info.caption}]]</td>
					<td valign="top" class="required">{if $field_info.is_required}*{/if}</td>
					<td style="border-left:0;">{if $field_id == "country"}{input property=$field_id template="list_empty.tpl"}{else}{input property=$field_id}{/if}</td>
				</tr>
			{/foreach}
			<tr id="clearTable">
				<td colspan="2">
					<input type="hidden" name="gateway" value="{$gateway.id}" />
                    <input type="hidden" id="submit" name="submit" value="save_gateway"/>
					<span class="greenButtonEnd"><input type="submit" value="[[Save]]" class="greenButton" /></span>
                    <span class="greenButtonEnd"><input type="submit" id="apply" value="[[Apply]]" class="greenButton"/></span>
				</td>
			</tr>
		</table>
	</form>
{/if}

{*if $gateway_info.ConfigurationInstructions}
	<a href="{$gateway_info.ConfigurationInstructions}">Edit the instructions page</a>
{/if*}

{if $gateway.id == "2checkout"}
<span class="productTypeComment">
	[[To set up recurring billing in 2Checkout you need to set an email address in 2Checkout for subscription notifications to be sent to (you'll need to do this only once);]]<br/>
	<br/>
	[[Please see the detailed description below:]]<br/>
	&nbsp; &nbsp; <strong>a)</strong> [[Log in to your Admin Vendor area in 2Checkout;]]<br/>
	&nbsp; &nbsp; <strong>b)</strong> [[Go to Notification &#187; Settings section;]]<br/>
	&nbsp; &nbsp; <strong>c)</strong> [[Enter the following URL to the Global URL field:]] {$GLOBALS.user_site_url}/system/payment/notifications/2checkout/<br/>
	&nbsp; &nbsp; <strong>d)</strong> [[Press the Apply button.]]<br/>
</span>
{elseif $gateway.id == "authnet_sim"}
<span class="productTypeComment">
	[[To work with recurring billing in Authorize.Net you need to set the Silent Post URL. It is the URL where the subscription notifications will be sent.]]<br/>
	<br/>
	[[Silent Post URL description]]:<br/>
	[[The Silent Post URL is a location on your Web server where the payment gateway can "carbon copy" the transaction response. This allows you to use transaction response information for other purposes separately without affecting the amount of time it takes to respond to the payment gateway with a custom receipt page from the Relay Response URL.]]<br/>
	<br/>
	[[To configure the Silent Post URL]]:<br/>
	<strong>1)</strong> [[Log on to the Merchant Interface at https://account.authorize.net]]<br/>
	<strong>2)</strong> [[Click Settings under Account in the main menu on the left]]<br/>
	<strong>3)</strong> [[Click Silent Post URL in the Transaction Format Settings section]]<br/>
	<strong>4)</strong> [[Enter the secondary URL to which you would like the payment gateway to copy the transaction response]] {$GLOBALS.user_site_url}/system/payment/notifications/authnet_sim/<br/>
	<strong>5)</strong> [[Click Submit]]<br/>
	<strong>6)</strong> [[Go to Settings &#187; API Login ID and Transaction Key. There you will get the 'Current API Login ID' and the 'Current Transaction Key' (copy them).]]<br/>
	<strong>7)</strong> [[Also go to Settings &#187; MD5-Hash. And enter any descired value for the MD5-Hash. (copy it).]]<br/>
	<strong>8)</strong> [[Then you need to go to your Shixi.com Admin Panel &#187; Payments &#187; Authorize.Net SIM (edit) and specify there the API Login ID, Transaction Key and MD5-Hash.]]<br/>
</span>
{/if}

<script>
	$('#apply').click(
		function(){
			$('#submit').attr('value', 'apply_gateway');
		}
	);
</script>