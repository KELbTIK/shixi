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
			<label for="inputUserName" class="control-label">User Name</label>
            <input type="text" class="form-control" id="inputUserName" placeholder="User Name" required="">
            <i class="fa fa-user form-control-feedback"></i>
		</div>
		<div class="form-group has-feedback">
			<label for="inputPassword" class="control-label">Password</label>
            <input type="password" class="form-control" id="inputPassword" placeholder="Password" required="">
            <i class="fa fa-lock form-control-feedback"></i>
		</div>
        <button type="submit" class="btn btn-group btn-dark btn-sm">Log In</button>
        <span>or</span>
        <a href="{$GLOBALS.site_url}/registration/" class="btn btn-group btn-default btn-sm">[[Sign Up]]</a><br/>
        <div class="checkbox">
            <label for="keep">
                <input type="checkbox" name="keep" id="keep" required=""> [[Keep me signed in]]
            </label>
        </div>
        <ul class="list-unstyled">
            <li><a href="{$GLOBALS.site_url}/password-recovery/">[[Forgot Your Password?]]</a><br/></li>
        </ul>

        <div class="divider"></div>
        <span class="text-center text-muted">Login with</span>
        <ul class="social-links clearfix">
            <li class="facebook"><a target="_blank" href="http://www.facebook.com"><i class="fa fa-facebook"></i></a></li>
            <li class="twitter"><a target="_blank" href="http://www.twitter.com"><i class="fa fa-twitter"></i></a></li>
            <li class="googleplus"><a target="_blank" href="http://plus.google.com"><i class="fa fa-google-plus"></i></a></li>
        </ul>
	</form>


