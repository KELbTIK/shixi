<div id="mailchimp-newsletter">
	<div id="mailchimp-nl-cont">
		{if $error}
			<p class="error">
				{if $error eq 'EMPTY_FIELD'}
					[[All fields are required!]]
				{else}
					[[{$error}]]
				{/if}
			</p>
		{/if}
		{if $message}
			<p class="message">[[{$message}]]</p>
		{/if}
		<form action="{$GLOBALS.site_url}/system/miscellaneous/mailchimp/" method="get" id="mailchimp-form">
			<p class="mailchimp-nl-desc">[[Fill the form to subscribe]]</p>
			<fieldset><label for="mch_name">[[Your name]]:</label><input type="text" name="mch_name" id="mch_name"/></fieldset>
			<fieldset><label for="mch_email">[[Email]]:</label><input type="text" name="mch_email" id="mch_email"/></fieldset>
			<fieldset><input type="submit" name="subscribe" value="[[Subscribe]]" id="mch_subscribe"/></fieldset>
		</form>
	</div>
</div>

<script type="text/javascript">
	{literal}
	$(document).ready(function(){
		$("#mch_subscribe").live("click", function(){
			var oEmail = $("#mch_email");
			var oName = $("#mch_name");
			var email = oEmail.val();
			var name = oName.val();
			var error = false;
			if (!email || !name){
				error = true;
			}
			if (!error) {
				var content = $("#mailchimp-nl-cont");
				content.html("<img src=\"{/literal}{$GLOBALS.site_url}{literal}/templates/_system/main/images/ajax_preloader_circular_32.gif\" />")
					.css("text-align", "center");
				$.ajax({
					url: '{/literal}{$GLOBALS.site_url}{literal}/system/miscellaneous/mailchimp/',
					type: "GET",
					data: "mch_name="+name+"&mch_email="+email+"&subscribe=1",
					success: function(data) {
						content.html($(data).find("#mailchimp-nl-cont"));
					}
				});
			}
			return false;
		});
	});
	{/literal}
</script>

