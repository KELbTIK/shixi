{if isset($countOfSuccessfulSent)}
	<p> [[$countOfSuccessfulSent activation letter(s) successful sent]]</p>
	<p> [[$countOfUnsuccessfulSent activation letter(s) not sent]]</p>
{elseif $error eq "USER_DOES_NOT_EXIST"}
	<p class="error"> [[There is no such user]] </p>
{elseif $error eq "CANNOT_SEND_EMAIL"}
	<p class="error"> [[Unable to send activation letter]] </p>
{else}
	<p> [[Activation letter was successfully sent]] </p>
{/if}