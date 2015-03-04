
*****	{$smarty.now|date_format}	*****

notified Emails: {foreach from=$notified_emails item=email name=alerts}{$email}{if $smarty.foreach.alerts.iteration < $smarty.foreach.alerts.total}, {/if}{foreachelse}none{/foreach}

**************************
