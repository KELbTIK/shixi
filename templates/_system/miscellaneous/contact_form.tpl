{if $message_sent == false}
	<h1>{module name="static_content" function="show_static_content" pageid='Contact'}</h1>

	{foreach key="key" item="value" from=$field_errors}
		{if $key == 'EMAIL'}
			<div class="error alert alert-danger">[[Please specify a valid email address.]]</div>
		{elseif $key == 'NAME'}
            <div class="error alert alert-danger">[[Please provide your full name.]]</div>
		{elseif $key == 'COMMENTS'}
            <div class="error alert alert-danger">[[Please include your comments.]]</div>
		{elseif $key == 'EMPTY_VALUE'}
            <div class="error alert alert-danger">[[Enter Security code]]</div>
		{elseif $key == 'NOT_VALID'}
            <div class="error alert alert-danger">[[Security code is not valid]]</div>
		{/if}
	{/foreach}

	<div class="row">
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
						<textarea class="form-control" rows="8" id="message" name="message" style="position:static;">{$comments|escape:'html'}</textarea>
						<i class="fa fa-pencil form-control-feedback"></i>
					</div>
					<div>{module name="miscellaneous" function="captcha_handle" currentFunction="contact_form"}</div>
					<input type="submit" value="[[Submit]]" id="submitContact" class="btn btn-default btn-sm">

				</form>
			</div>
		</div>
	</div>

	{else}
		<div class="alert alert-success">[[Thank you very much for your message. We will respond to you as soon as possible.]]</div>
	{/if}