{if $navCount == "0"}
{else}
    <div class="page-intro">
        <div class="col-xs-12">
            <ol class="breadcrumb">
                {foreach from=$navArray item=navItem name=navForeach}
                    {if $smarty.foreach.navForeach.iteration<$navCount }
                        <li>
                            <a href="{$GLOBALS.site_url}{$navItem.uri}">[[{$navItem.name}]]</a>
                        </li>
                    {else}
                        <li class="active">
                            [[{$navItem.name}]]
                        </li>
                    {/if}
                {/foreach}
            </ol>
        </div>
        <div class="clearfix"></div>
    </div>
{/if}