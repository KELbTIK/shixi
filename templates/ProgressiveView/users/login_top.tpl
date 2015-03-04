{if $shopping_cart && $logged_in}
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery-ui.js"></script>
	<script type="text/javascript">
		$("#shoppingCartForm").click();
	</script>
{/if}

<form action="{$GLOBALS.site_url}/login/" method="post" class="loginForm">
	<input type="hidden" name="return_url" value="{$return_url}" />
	<input type="hidden" name="action" value="login" />
	{if $shopping_cart}<input type="hidden" name="shopping_cart" value="{$shopping_cart}" />{/if}
	<fieldset>
		<input type="text" name="username" id="username" />
		<input type="password" name="password" id="password" />
		<input type="submit" value="[[Login]]" class="button" />
	</fieldset>
</form>
<div class="clr"><br/></div>

<a href="{$GLOBALS.site_url}/registration/{if $shopping_cart}?fromShoppingCart=1{/if}">[[Registration]]</a>
<div class="soc_reg_form">{module name="social" function="social_plugins"}</div>

<script type="text/javascript">
	$(function() {
		//*** Placeholder for Username ***//
		$("#username").val('[[Username]]');

		$("#loginForm").submit(function(){
			if ($("#username").val()=='[[Username]]')
			{
				$("#username").val('');
			}
		});

		$("#username").focus(function(){
			if ($("#username").val()=='[[Username]]')
			{
				$("#username").val('');
			}
		});

		$("#username").blur(function(){
			if ($("#username").val()=='')
			{
				$("#username").val('[[Username]]');
			}
		});
		//*** Placeholder for Password ***//
		$("#password").val('[[Password]]');

		$("#loginForm").submit(function(){
			if ($("#password").val()=='[[Password]]')
			{
				$("#password").val('');
			}
		});

		$("#password").focus(function(){
			if ($("#password").val()=='[[Password]]')
			{
				$("#password").val('');
				$("#password").get(0).type='password';
			}
		});

		$("#password").blur(function(){
			if ($("#password").val()=='')
			{
				$("#password").val('[[Password]]');
				$("#password").get(0).type='text';
			}
		});
	});
</script>