<h1>[[Job Search]]</h1>
<div id="quick-search">
	<form action="{$GLOBALS.site_url}/search-results-jobs/" id="quickSearchForm">
		<input type="hidden" name="action" value="search" />
		<input type="hidden" name="listing_type[equal]" value="Job" />
		<fieldset>
			<div class="quick-search-label">[[Keywords]]</div>
			<div class="quick-search-field">{search property=keywords}</div>
			<div class="quick-search-label">[[Category]]</div>
			<div class="quick-search-field">{search property=JobCategory  template='list.tpl'}</div>
		</fieldset>
		<fieldset>
			<div class="quick-search-label">[[City]]</div>
			<div class="quick-search-field">{search property=City parent=Location}</div>
			<div class="quick-search-label">[[State]]</div>
			<div class="quick-search-field">{assign var="name" value='State.Name'}{search property=$name parent=Location country=$GLOBALS.settings.default_country template='list.tpl'}</div>
		</fieldset>
		<fieldset>
			<div class="quick-search-label">&nbsp;</div>
			<div class="quick-search-field">
				<a href="{$GLOBALS.site_url}/find-jobs/">[[Advanced search]]</a>
				{if $acl->isAllowed('open_search_by_company_form')}
					<br/>
					<a href="{$GLOBALS.site_url}/browse-by-company/">[[Search by Company]]</a>
				{/if}
			</div>
			<div class="quick-search-label">&nbsp;</div>
			<div class="quick-search-field"><input type="submit" class="blueBtn" value="[[Find]]"/></div>
		</fieldset>
	</form>
</div>
<div id="count-listings">{module name="classifieds" function="count_listings"}</div>