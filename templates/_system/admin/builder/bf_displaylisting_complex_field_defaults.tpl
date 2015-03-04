{if !empty($theField.html)}{$theField.html}{else}
	{if $theField.b_field_sid eq "Education" || $theField.id eq "Education"}
		{literal}
		<div class="leftDisplaySIde">
			<strong>{EntranceDate} - {GraduationDate}</strong>
		</div>
		<div class="rightDisplaySIde">
			{InstitutionName}<br>
			{Major}<br>
			{DegreeLevel}
		</div>
		<div class="clrBorder"><br></div>
		<div class="clr"><br></div>
		{/literal}

	{elseif $theField.b_field_sid eq "WorkExperience" || $theField.id eq "WorkExperience"}
		{literal}
		<div class="leftDisplaySIde">
			<strong>{StartDate} - 	{EndDate}</strong>
		</div>
		<div class="rightDisplaySIde">
			{JobTitle}<br>
			{CompanyName} | {Industry}<br>
			{Description}
		</div>
		<div class="clrBorder"><br></div>
		<div class="clr"><br></div>
		{/literal}
	{else}
		{foreach from=$theField.fields item="complexChildField"}
			{literal}{{/literal}{$complexChildField.id}{literal}},{/literal}
		{/foreach}
	{/if}
{/if}