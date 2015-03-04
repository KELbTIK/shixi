{literal}
<script type="text/javascript">
	$(document).ready(function() {
		var companyName = "{/literal}{$companyName}{literal}";
		$.ajax({
			dataType: "jsonp",
			crossDomain: true,
			url: "//www.linkedin.com/ta/federator",
			data: {types: 'company', query: companyName},
			success: function(data) {
				if (!$.isEmptyObject(data) && 'company' in data && 'resultList' in data.company) {
					var result = data.company.resultList;
					if ($.isArray(result)) {
						var company = result.shift();
						$(".in_ProfileWidget").append(
							'<script src="//platform.linkedin.com/in.js" type="text/javascript"><\/script>' +
							'<script type="IN/CompanyProfile" data-id="'+ company.id +'" data-format="hover"><\/script>'
						);
					}
				}
			}
		});
	});
</script>
{/literal}

<div class="in_ProfileWidget"></div>
<div class="clr"></div>