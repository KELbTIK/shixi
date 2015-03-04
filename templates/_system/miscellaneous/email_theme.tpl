{fromName}{$emailData.fromName}{/fromName}
{subject}{$emailData.subject}{/subject}
{message}
	<p><img src="{image}logo.png" /></p>
	<p style="display:block; background-color: #dddddd; height: 20px; width: 100%;"></p>
	{$emailData.message}
	<hr/>
	{$emailData.signature}
	<p style="display:block; background-color: #dddddd; height: 20px; width: 100%;"></p>
{/message}
