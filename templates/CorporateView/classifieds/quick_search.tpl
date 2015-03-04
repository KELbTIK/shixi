<div id="quicksearch">
	<h1>[[Job Search]]</h1>
	<form action="{$GLOBALS.site_url}/search-results-jobs/" id="quickSearchForm">
		<input type="hidden" name="action" value="search" />
		<input type="hidden" name="listing_type[equal]" value="Job" />
		<fieldset>
			<div class="quickSearchInputName">[[Keywords]]<br/>{search property=keywords}</div>
			<div class="quickSearchInputName">[[Category]]<br/>{search property=JobCategory  template='list.tpl'}</div>
		</fieldset>
		<fieldset>
			<div class="quickSearchInputName">[[City]]<br/>{search property=City parent=Location}</div>
			<div class="quickSearchInputName">[[State]]<br/>{assign var="name" value='State.Name'}{search property=$name parent=Location country=$GLOBALS.settings.default_country template='list.tpl'}</div>
		</fieldset>
		<fieldset>
			<div class="quickSearchInputName">
				<a href="{$GLOBALS.site_url}/find-jobs/">* [[Advanced search]]</a>
				{if $acl->isAllowed('open_search_by_company_form')}
    				<br/>
    				<a href="{$GLOBALS.site_url}/browse-by-company/">* [[Search by Company]]</a>
				{/if}
			</div>
			<div class="quickSearchInputName"><input type="submit" value="[[Find]]"/></div>
		</fieldset>
	</form>
	<fieldset>
		<div class="InputStat">{module name="classifieds" function="count_listings"}</div>
	</fieldset>
</div>