<ul>
{foreach from=$GLOBALS.user_groups item=user_group}
    <li><a href="{$GLOBALS.site_url}/{$user_group.id|lower}-products/">[[{$user_group.name}]]</a></li>
{/foreach}
</ul>
