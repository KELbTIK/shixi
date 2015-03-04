{if $ajaxRelocate}
	{literal}
	<script>
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
	<script>
		{literal}
			 $("#shoppingCartForm").click();
		{/literal}
	</script>
{/if}
{if $GLOBALS.user_page_uri == "/"}
	<form action="{$GLOBALS.site_url}/login/" method="post" id="loginForm" {if $ajaxRelocate} onsubmit="return loginSubmit()" {/if}>
		<input type="hidden" name="return_url" value="{$return_url}" />
		<input type="hidden" name="action" value="login" />
		{if $ajaxRelocate}<input type="hidden" name="ajaxRelocate" value="1" />{/if}
		<fieldset>
			<div class="inputFieldLogin">
				[[Username]]<br/>
				<input type="text" class="logInNameInput" name="username" />
			</div>
		</fieldset>
		<fieldset>
			<div class="inputFieldLogin">
				[[Password]]<br/>
				<input class="logInPassInput" type="password" name="password" /> <input type="submit" value="[[GO]]" id="buttonLogin" />
			</div>
		</fieldset>
		<br/>
		<input type="checkbox" name="keep" id="keep" /><label for="keep"> [[Keep me signed in]]</label><br/>
		<a href="{$GLOBALS.site_url}/password-recovery/">[[Forgot Your Password?]]</a><br/>
		<a href="{$GLOBALS.site_url}/registration/">[[Registration]]</a><br/>
	</form>
{else}
	{if !$GLOBALS.is_ajax}<h1>[[Sign In]]</h1>{/if}
    {include file="../users/errors.tpl" errors=$errors}
	<form action="{$GLOBALS.site_url}/login/" method="post" id="loginForm" {if $ajaxRelocate} onsubmit="return loginSubmit()" {/if}>
		<input type="hidden" name="return_url" value="{$return_url}" />
		<input type="hidden" name="action" value="login" />
		{if $ajaxRelocate}<input type="hidden" name="ajaxRelocate" value="1" />{/if}
		{if $proceedToPosting}<input type="hidden" name="proceed_to_posting" value="{$proceedToPosting}" />{/if}
		{if $productSID}<input type="hidden" name="productSID" value="{$productSID}" />{/if}
		{if $listingTypeID}<input type="hidden" name="listing_type_id" value="{$listingTypeID}" />{/if}
		{if $shopping_cart}<input type="hidden" name="shopping_cart" value="{$shopping_cart}" />{/if}
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
	<div class="soc_reg_form">{module name="social" function="social_plugins"}	</div>
{/if}