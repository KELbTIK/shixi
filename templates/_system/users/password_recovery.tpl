<div class="password-recovery form-block center-block">
    <h2 class="title">[[Password Recovery]]</h2>
    <hr/>
	{foreach from=$errors key=error_code item=error_message}
		{if $error_code == 'WRONG_EMAIL'}
			<div class="error alert alert-danger">[[Please specify a valid email address.]]</div>
		{/if}
	{/foreach}
    <div class="alert alert-info">
        [[Please, enter your email in the field below and we'll send you a link to a page where you can change your password]]:
    </div>
    <form method="post" action="">
        <div class="row">
            <div class="col-sm-7">
                <input type="text" name="email" value="{$email|escape:'html'}" class="text form-control" />
            </div>
            <div class="col-sm-5">
                <input type="submit" name="submit" value="[[Submit]]" class="button btn btn-default btn-recovery" />
            </div>
        </div>
        <div class="clearfix"></div>
	</form>
</div>