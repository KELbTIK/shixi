<h1>[[Find Jobs]]{if $acl->isAllowed('open_search_by_company_form')}<span class="RightLink"><a href="{$GLOBALS.site_url}/browse-by-company/">[[Search by Company]]</a></span>{/if}</h1>
<div class="clr"></div>
{if $id_saved}
	<form action="{$GLOBALS.site_url}/saved-searches/" method="get" id="search_form">
		<input type="hidden" name="action" value="{$action}" />
		<input type="hidden" name="id_saved" value="{$id_saved}" />
{else}
	<form action="{$GLOBALS.site_url}/search-results-jobs/" method="get" id="search_form">
		<input type="hidden" name="action" value="search" />
{/if}
	<input type="hidden" name="listing_type[equal]" value="Job" />
	<div id="adMargin">
		{if $id_saved}
			<fieldset>
				<div class="inputName">[[Search Name]]</div>
				<div class="inputField">{search property=name template='string.tpl'}</div>
			</fieldset>
		{/if}

		{include file="../builder/bf_searchform_fieldsholders.tpl"}

		<fieldset>
			<div class="inputName">&nbsp;</div>
			<div class="inputField">
				{if $id_saved}
					<input class="button" type="submit" name="submit" value="[[Save]]"  id="search_button" />
				{else}
					<input class="button" type="submit" value="[[Search]]"  id="search_button" />
				{/if}
			</div>
		</fieldset>
	</div>
</form>
<div id="adSpace">{module name="static_content" function="show_static_content" pageid="FindJobsAdSpace"}</div>
