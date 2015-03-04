<div class="QuickSearchBg">
	<div class="QuickSearch"> 
		<form action="{$GLOBALS.site_url}/search-results-jobs/" id="quickSearchForm">
			<input type="hidden" name="action" value="search" />
			<input type="hidden" name="listing_type[equal]" value="Job" />
			<h1>[[Job Search]]</h1><br/>
			<fieldset>
				<div class="quickSearchInputName">[[Keywords]]</div>
				<div class="quickSearchInputField">[[Category]]</div>
			</fieldset>
			<fieldset>
				<div class="quickSearchInputName">{search property=keywords}</div>
				<div class="quickSearchInputField">{search property=JobCategory  template='list.tpl'}</div>
			</fieldset>
			<fieldset>
				<div class="quickSearchInputName">[[City]]</div>
				<div class="quickSearchInputField">[[State]]</div>
			</fieldset>
			<fieldset>
				<div class="quickSearchInputName">{search property=City parent=Location}</div>
				<div class="quickSearchInputField">{assign var="name" value='State.Name'}{search property=$name parent=Location country=$GLOBALS.settings.default_country template='list.tpl'}</div>
			</fieldset>
			<fieldset>
				<div class="quickSearchInputName"><br/><input type="submit" id="btn-search" value="[[Find]]"/></div>
				<div class="quickSearchInputField">
					<br/><a href="{$GLOBALS.site_url}/find-jobs/">[[Advanced search]]</a>
					{if $acl->isAllowed('open_search_by_company_form')}
						<br/><a href="{$GLOBALS.site_url}/browse-by-company/">[[Search by Company]]</a>
					{/if}
				</div>
			</fieldset>
		</form>
	</div>
	<div class="QuickSearchBottom"> </div>
	<div class="InputStat">{module name="classifieds" function="count_listings"}</div>
</div>