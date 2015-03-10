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

{if $GLOBALS.user_page_uri == "/"}
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

{else}

	{include file="../users/errors.tpl" errors=$errors}

	{*<div class="soc_reg_form">{module name="social" function="social_plugins"}	</div>*}
    <!-- ================ -->
        <div class="form-block center-block">
            {if !$GLOBALS.is_ajax}<h2 class="title">[[Sign In]]</h2>{/if}
            <hr>
            <form class="form-horizontal" action="{$GLOBALS.site_url}/login/" method="post"  id="loginForm" {if $ajaxRelocate} onsubmit="return loginSubmit()" {/if}>
                <input type="hidden" name="return_url" value="{$return_url}" />
                <input type="hidden" name="action" value="login" />
                {if $ajaxRelocate}<input type="hidden" name="ajaxRelocate" value="1" />{/if}
                {if $proceedToPosting}<input type="hidden" name="proceed_to_posting" value="{$proceedToPosting}" />{/if}
                {if $productSID}<input type="hidden" name="productSID" value="{$productSID}" />{/if}
                {if $listingTypeID}<input type="hidden" name="listing_type_id" value="{$listingTypeID}" />{/if}
                {if $shopping_cart}<input type="hidden" name="shopping_cart" value="{$shopping_cart}" />{/if}
                <div class="form-group has-feedback">
                    <label for="inputUserName" class="col-sm-3 control-label">[[Username]]</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="username" id="inputUserName" placeholder="[[Username]]" required>
                        <i class="fa fa-user form-control-feedback"></i>
                    </div>
                </div>
                <div class="form-group has-feedback">
                    <label for="inputPassword" class="col-sm-3 control-label">[[Password]]</label>
                    <div class="col-sm-8">
                        <input type="password" name="password" class="form-control" id="inputPassword" placeholder="[[Password]]" required>
                        <i class="fa fa-lock form-control-feedback"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-8">
                        <div class="checkbox">
                            <label for="keep">
                                <input type="checkbox" name="keep" id="keep" /> [[Keep me signed in]]
                            </label>
                        </div>
                        {module name="miscellaneous" function="captcha_handle" displayMode="fieldset"}
                        <input type="submit" class="btn btn-group btn-default btn-sm" value="[[Login]]" class="button" />
                        <ul>
                            <li><a  href="{$GLOBALS.site_url}/password-recovery/">[[Forgot Your Password?]]</a></li>
                        </ul>
                        <span class="text-center text-muted">Login with</span>
                        <ul class="social-links colored circle clearfix">
                            <li class="facebook"><a target="_blank" href="http://www.facebook.com"><i class="fa fa-facebook"></i></a></li>
                            <li class="twitter"><a target="_blank" href="http://www.twitter.com"><i class="fa fa-twitter"></i></a></li>
                            <li class="googleplus"><a target="_blank" href="http://plus.google.com"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
        <p class="text-center space-top">Don't have an account yet? <a href="{$GLOBALS.site_url}/registration/{if $shopping_cart}?fromShoppingCart=1{/if}">[[Registration]]</a> now.</p>

{/if}



