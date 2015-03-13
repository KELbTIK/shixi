<h1 class="col-xs-12">[[Find Jobs]] {if $acl->isAllowed('open_search_by_company_form')}<span class="RightLink pull-right small"><a href="{$GLOBALS.site_url}/browse-by-company/">[[Search by Company]]</a></span>{/if}</h1>
<div class="clearfix"></div>
<div class="col-sm-8">
{if $id_saved}
	<form action="{$GLOBALS.site_url}/saved-searches/" method="get" id="search_form" class="form-horizontal">
		<input type="hidden" name="action" value="{$action}" />
		<input type="hidden" name="id_saved" value="{$id_saved}" />
{else}
	<form action="{$GLOBALS.site_url}/search-results-jobs/" method="get" id="search_form" class="form-horizontal">
		<input type="hidden" name="action" value="search" />
{/if}
	<input type="hidden" name="listing_type[equal]" value="Job" />
	<div id="adMargin">
		{if $id_saved}
			<div class="form-group">
				<label class="inputName control-label col-sm-3">[[Search Name]]</label>
				<div class="inputField col-sm-8 has-feedback">{search property=name template='string.tpl'}</div>
			</div>
		{/if}

		{include file="../builder/bf_searchform_fieldsholders.tpl"}
        <div class="form-group">
            <div class="inputField col-sm-8 col-sm-offset-3">
                {if $id_saved}
                    <input class="button btn btn-default" type="submit" name="submit" value="[[Save]]"  id="search_button" />
                {else}
                    <input class="button btn btn-default" type="submit" value="[[Search]]"  id="search_button" />
                {/if}
            </div>
        </div>
	</div>
</form>
</div>
<div id="adSpace">{module name="static_content" function="show_static_content" pageid="FindJobsAdSpace"}</div>
