<div class="form-group">
	{if $theField.html || $theField.b_field_sid|substr:0:9 eq "htmlBlock"}
		<div class="inputField">{$theField.html}</div>
	{elseif $theField.id eq "City"}
		<label class="inputName">[[{$theField.caption}]]</label>
		<div class="inputField">{search property=$theField.id template="string.like.tpl"}</div>
	{elseif $theField.b_field_sid eq "keywords"}
		<label class="inputName">[[Keywords]]</label>
		<div class="inputField">{search property="keywords" type="bool" listingType=$listingTypeID}</div>
	{elseif $theField.b_field_sid eq "PostedWithin"}
		<label class="inputName">[[Posted Within]]</label>
		<div class="inputField">{search property=PostedWithin template="list.date.tpl"}</div>
	{elseif $theField.type eq "location"}
		{search property=$theField.id fields=$theField.fields}
	{else}
		<label class="inputName">
			{if $theField.id eq "ZipCode"}
				[[Search Within]]
			{else}
				[[{$theField.caption}]]
			{/if}
		</label>
		{if $complexParent}
			<div class="inputField">{search property=$theField.id complexParent=$complexParent}</div>
		{else}
			<div class="inputField">{search property=$theField.id}</div>
		{/if}
	{/if}
</div>
