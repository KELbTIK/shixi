{if !$display_error}
    <p><strong>[[To]]:</strong> {$email.email}</p>
    <p><strong>[[Subject]]:</strong> {$email.subject}</p>
    <br/>
    <br/>
    <p>{$email.message}</p>
{else}
    {if $email.error_msg}
        <p class="error">{$email.error_msg}</p>
    {else}
        [[Couldn't get error message]]
    {/if}
{/if}