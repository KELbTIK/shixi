{if $ajaxRelocate}
	{literal}
	<script type="text/javascript">
		function loginSubmit() {
			var options = {
					  target: "#messageBox",
					  url:  $("#loginForm").attr("action")
					};
			$("#loginForm").ajaxSubmit(options);
			return false;
		}
	</script>
	{/literal}
{/if}
{if $shopping_cart && $logged_in}
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery-ui.js"></script>
	<script type="text/javascript">
		{literal}
			 $("#shoppingCartForm").click();
		{/literal}
	</script>
{/if}

{if !$GLOBALS.is_ajax}<h1>[[Sign In]]</h1>{/if}
{include file="../users/errors.tpl" errors=$errors}
<form action="{$GLOBALS.site_url}/login/" method="post" id="loginForm" {if $ajaxRelocate} onsubmit="return loginSubmit()" {/if}>
	<input type="hidden" name="return_url" value="{$return_url}" />
	<input type="hidden" name="action" value="login" />
	{if $shopping_cart}<input type="hidden" name="shopping_cart" value="{$shopping_cart}" />{/if}
	{if $proceedToPosting}<input type="hidden" name="proceed_to_posting" value="{$proceedToPosting}" />{/if}
	{if $productSID}<input type="hidden" name="productSID" value="{$productSID}" />{/if}
	{if $listingTypeID}<input type="hidden" name="listing_type_id" value="{$listingTypeID}" />{/if}
	{if $ajaxRelocate}<input type="hidden" name="ajaxRelocate" value="1" />{/if}
	<fieldset>
		<div class="inputName">[[Username]]</div>
		<div class="inputField"><input type="text" name="username" /></div>
	</fieldset>
	<fieldset>
		<div class="inputName">[[Password]]</div>
		<div class="inputField"><input type="password" name="password" /></div>
	</fieldset>
	<fieldset>
		<div class="inputName">&nbsp;</div>
		<div class="inputField"><input type="checkbox" name="keep" id="keep" /><label for="keep"> [[Keep me signed in]]</label></div>
	</fieldset>
	<fieldset>
		{module name="miscellaneous" function="captcha_handle" displayMode="fieldset"}
	</fieldset>
	<fieldset>
		<div class="inputName">&nbsp;</div>
		<div class="inputButton"><input type="submit" value="[[Login]]" class="button" /></div>
	</fieldset>
</form>

<br/><a  href="{$GLOBALS.site_url}/password-recovery/">[[Forgot Your Password?]]</a>&nbsp;|&nbsp; <a href="{$GLOBALS.site_url}/registration/{if $shopping_cart}?fromShoppingCart=1{/if}">[[Registration]]</a>
<div class="soc_reg_form">{module name="social" function="social_plugins"}</div>