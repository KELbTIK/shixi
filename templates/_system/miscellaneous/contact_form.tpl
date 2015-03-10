{if $message_sent == false}
	{module name="static_content" function="show_static_content" pageid='Contact'}

	{foreach key="key" item="value" from=$field_errors}
		{if $key == 'EMAIL'}
			<p class="error">[[Please specify a valid email address.]]</p>
		{elseif $key == 'NAME'}
			<p class="error">[[Please provide your full name.]]</p>
		{elseif $key == 'COMMENTS'}
			<p class="error">[[Please include your comments.]]</p>
		{elseif $key == 'EMPTY_VALUE'}
			<p class="error">[[Enter Security code]]</p>
		{elseif $key == 'NOT_VALID'}
			<p class="error">[[Security code is not valid]]</p>
		{/if}
	{/foreach}


		<div class="main col-md-8">
			<div class="contact-form">
				<form id="contact-form" role="form" novalidate="novalidate" method="post" action="" onsubmit="disableSubmitButton('submitContact');">

					<div class="form-group has-feedback">
						<label for="name">Salutation First and Last Name*</label>
						<input type="text" class="form-control fix" name="name" id="name" placeholder="" value="{if $GLOBALS.current_user.logged_in}{$name|default:"`$GLOBALS.current_user.FirstName` `$GLOBALS.current_user.LastName`"|escape:'html'}{else}{$name|escape:'html'}{/if}">
						<i class="fa fa-user form-control-feedback"></i>
					</div>
					<div class="form-group has-feedback">
						<label for="email">Enter email*</label>
						<input type="email" class="form-control" id="email" name="email" placeholder="" value="{if $GLOBALS.current_user.logged_in}{$email|default:$GLOBALS.current_user.email|escape:'html'}{else}{$email|escape:'html'}{/if}">
						<i class="fa fa-envelope form-control-feedback"></i>
					</div>

					<div class="form-group has-feedback">
						<label for="message">Comments*</label>
						<textarea class="form-control" rows="6" id="message" name="message" placeholder="">{$comments|escape:'html'}</textarea>
						<i class="fa fa-pencil form-control-feedback"></i>
					</div>
					<div>{module name="miscellaneous" function="captcha_handle" currentFunction="contact_form"}</div>
					<input type="submit" value="[[Submit]]" id="submitContact" class="btn btn-default btn-sm">
				</form>
			</div>
		</div>


	{else}
		<br />
		<p>[[Thank you very much for your message. We will respond to you as soon as possible.]]</p>
		<br />
	{/if}