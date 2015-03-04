<fieldset>
	{if $theField.html || $theField.b_field_sid|substr:0:9 eq "htmlBlock"}
		<div class="inputField">{$theField.html}</div>
	{elseif $theField.id eq "City"}
		<div class="inputName">[[{$theField.caption}]]</div>
		<div class="inputField">{search property=$theField.id template="string.like.tpl"}</div>
	{elseif $theField.b_field_sid eq "keywords"}
		<div class="inputName">[[Keywords]]</div>
		<div class="inputField">{search property="keywords" type="bool" listingType=$listingTypeID}</div>
	{elseif $theField.b_field_sid eq "PostedWithin"}
		<div class="inputName">[[Posted Within]]</div>
		<div class="inputField">{search property=PostedWithin template="list.date.tpl"}</div>
	{elseif $theField.type eq "location"}
		{search property=$theField.id fields=$theField.fields}
	{else}
		<div class="inputName">
			{if $theField.id eq "ZipCode"}
				[[Search Within]]
			{else}
				[[{$theField.caption}]]
			{/if}
		</div>
		{if $complexParent}
			<div class="inputField">{search property=$theField.id complexParent=$complexParent}</div>
		{else}
			<div class="inputField">{search property=$theField.id}</div>
		{/if}
	{/if}
</fieldset>
