<div class="QuickSearch">
	<div class="loop"></div>
	<h1>[[Job Search]]</h1>
	<form action="{$GLOBALS.site_url}/search-results-jobs/" id="quickSearchForm">
		<input type="hidden" name="action" value="search" />
		<input type="hidden" name="listing_type[equal]" value="Job" />
		<fieldset>
			<div class="quickSearchInputName">[[Keywords]]</div>
			<div class="quickSearchInputField">{search property=keywords}</div>
			<div class="quickSearchInputName">[[Category]]</div>
			<div class="quickSearchInputField">{search property=JobCategory  template='list.tpl'}</div>
			<div class="quickSearchInputField"><input type="submit" class="button" value="[[Find]]" /></div>
		</fieldset>
		<fieldset>
			<div class="quickSearchInputName">[[City]]</div>
			<div class="quickSearchInputField">{search property=City  parent=Location}</div>
			<div class="quickSearchInputName">[[State]]</div>
			<div class="quickSearchInputField">{assign var="name" value='State.Name'}{search property=$name parent=Location country=$GLOBALS.settings.default_country template="list.tpl"}</div>
			<div class="quickSearchInputField">
				<a href="{$GLOBALS.site_url}/find-jobs/">[[Advanced search]]</a>
				{if $acl->isAllowed('open_search_by_company_form')}
    				<br/>
    				<a href="{$GLOBALS.site_url}/browse-by-company/">[[Search by Company]]</a>
				{/if}
			</div>
		</fieldset>
	</form>
</div>
<div class="clr"></div>
<div class="InputStat">{module name="classifieds" function="count_listings"}</div>
<div class="clr"><br/></div>