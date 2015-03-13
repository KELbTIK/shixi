<div class="form-group">
	{if $theField.html || $theField.b_field_sid|substr:0:9 eq "htmlBlock"}
		<div class="inputField col-sm-8">{$theField.html}</div>
	{elseif $theField.id eq "City"}
		<label class="inputName col-sm-3 control-label">[[{$theField.caption}]]</label>
		<div class="inputField col-sm-8">{search property=$theField.id template="string.like.tpl"}</div>
	{elseif $theField.b_field_sid eq "keywords"}
		<label class="inputName col-sm-3 control-label">[[Keywords]]</label>
		<div class="inputField col-sm-8">{search property="keywords" type="bool" listingType=$listingTypeID}</div>
	{elseif $theField.b_field_sid eq "PostedWithin"}
		<label class="inputName col-sm-3 control-label">[[Posted Within]]</label>
		<div class="inputField col-sm-8">{search property=PostedWithin template="list.date.tpl"}</div>
	{elseif $theField.type eq "location"}
		{search property=$theField.id fields=$theField.fields}
	{else}
		<label class="inputName col-sm-3 control-label">
			{if $theField.id eq "ZipCode"}
				[[Search Within]]
			{else}
				[[{$theField.caption}]]
			{/if}
		</label>
		{if $complexParent}
			<div class="inputField col-sm-8">{search property=$theField.id complexParent=$complexParent}</div>
		{else}
			<div class="inputField col-sm-8">{search property=$theField.id}</div>
		{/if}
	{/if}
</div>
