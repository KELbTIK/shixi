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
	<script  type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery-ui.js"></script>
	<script>
		{literal}
			 $("#shoppingCartForm").click();
		{/literal}
	</script>
{/if}

<form class="login-form" action="{$GLOBALS.site_url}/login/" method="post" id="loginForm" {if $ajaxRelocate} onsubmit="return loginSubmit()" {/if}>
    <input type="hidden" name="return_url" value="{$return_url}" />
    <input type="hidden" name="action" value="login" />
    {if $ajaxRelocate}<input type="hidden" name="ajaxRelocate" value="1" />{/if}
    <div class="form-group has-feedback">
        <label class="control-label">[[Username]]</label>
        <input type="text" class="form-control logInNameInput" name="username" />
        <i class="fa fa-user form-control-feedback"></i>
    </div>

    <div class="form-group has-feedback">
        <label class="control-label">[[Password]]</label>
        <input class="logInPassInput form-control" type="password" name="password" />
        <i class="fa fa-lock form-control-feedback"></i>
    </div>

    <button type="submit" class="btn btn-group btn-dark btn-sm" id="buttonLogin">Log In</button>
    <span>or</span>
    <a href="{$GLOBALS.site_url}/registration/" class="btn btn-group btn-default btn-sm">[[Sign Up]]</a><br/>
    <div class="checkbox">
        <label for="keep">
            <input type="checkbox" name="keep" id="keep"> [[Keep me signed in]]
        </label>
    </div>
    <ul class="list-unstyled">
        <li><a href="{$GLOBALS.site_url}/password-recovery/">[[Forgot Your Password?]]</a><br/></li>
    </ul>

    <div class="divider"></div>
    <div class="soc_reg_form">{module name="social" function="social_plugins" shoppingCart=$shopping_cart}</div>

</form>