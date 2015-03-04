<div id="quickSearch">
	<form action="{$GLOBALS.site_url}/search-results-jobs/" id="quickSearchForm">
		<input type="hidden" name="action" value="search" /><input type="hidden" name="listing_type[equal]" value="Job" />
		<h1>[[Job Search]]</h1>
		<fieldset>
			<div class="quickSearchField">[[Keywords]]<br/>{search property=keywords}</div>
		</fieldset>
		<fieldset>
			<div class="quickSearchField">[[Location]]<br/>{search property=Location searchWithin=false fields=$locationFields template='location.like.tpl'}</div>
		</fieldset>
		<fieldset>
			<input type="submit" id="btn-search" class="button" value="[[Find]]"/>
			<div class="quickSearch-links">
				<a href="{$GLOBALS.site_url}/find-jobs/">[[Advanced search]]</a>
				{if $acl->isAllowed('open_search_by_company_form')}
					<br/><a href="{$GLOBALS.site_url}/browse-by-company/">[[Search by Company]]</a>
				{/if}
			</div>
		</fieldset>
		<fieldset id="stat">
			{module name="classifieds" function="count_listings"}
		</fieldset>
	</form>
</div>