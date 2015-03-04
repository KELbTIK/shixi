<script language="javascript" type="text/javascript">
	{literal}
	$(document).ready(function() {
		var cssMarginTop =  $("body").css("margin-top");
		if (cssMarginTop == 'undefined') {
			cssMarginTop = 0;
		}
		$("body").css("margin-top", "30px");
		$("#demo-link,#demo-link-closed").click(function(){
			$("#demo-info").slideToggle("slow");
			if ($(this).attr('id') == 'demo-link') {
				$(this).attr('id', 'demo-link-closed');
				$("body").css("margin-top", cssMarginTop);
			} else {
				$(this).attr('id', 'demo-link');
				$("body").css("margin-top", "30px");
			}
		});
	});
	{/literal}
</script>

<div id="demo-fix">
	<div id="demo-link">
		<div id="demo-link-arrow">Demo Info</div>
	</div>
	<div id="demo-info">
		<ul>
			<li class="demo-info-sep"></li>
			<li>
				<form id="themeSwitcher" method="get" action="">
					<span class="strong">Theme:</span>
					<select name="theme" onchange="location.href='{$GLOBALS.site_url}{$url}?theme='+this.value+'&amp;{$params}'">
						<option disabled="disabled">Select Theme</option>
						<option value="ProgressiveView" {if $smarty.session.theme == "ProgressiveView"} selected="selected"{/if}>Progressive View</option>
						<option value="OfficeView" {if $smarty.session.theme == "OfficeView"} selected="selected"{/if}>Office View</option>
						<option value="IntelligentView" {if $smarty.session.theme == "IntelligentView"} selected="selected"{/if}>Intelligent View</option>
						<option value="GenerationX" {if $smarty.session.theme == "GenerationX"} selected="selected"{/if}>New Generation</option>
						<option value="CorporateView" {if $smarty.session.theme == "CorporateView"} selected="selected"{/if}>Corporate View</option>
						<option value="SmartJobBoard" {if $smarty.session.theme == "SmartJobBoard"} selected="selected"{/if}>Classic View</option>
						<option value="LightView" {if $smarty.session.theme == "LightView"} selected="selected"{/if}>Light View</option>
						<option disabled="disabled">- Paid Templates -</option>
						<option value="ElegantView" {if $smarty.session.theme == "ElegantView"} selected="selected"{/if}>Elegant View</option>
						<option value="BusinessView" {if $smarty.session.theme == "BusinessView"} selected="selected"{/if}>Business View</option>
						<option value="ClearView" {if $smarty.session.theme == "ClearView"} selected="selected"{/if}>Clear View</option>
						<option value="EnhancedView" {if $smarty.session.theme == "EnhancedView"} selected="selected"{/if}>Enhanced View</option>
					</select>
				</form>
			</li>
			<li class="demo-info-sep"></li>
			<li><span><span class="strong">Employer login / pass:</span> emp/emp</span></li>
			<li class="demo-info-sep"></li>
			<li><span><span class="strong">Job Seeker login / pass:</span> js/js</span></li>
			<li class="demo-info-sep"></li>
			<li><span><a href="{$GLOBALS.admin_site_url}/" target="_blank">Admin Demo</a></span></li>
			<li class="demo-info-sep"></li>
			<li><span><a href="http://www.smartjobboard.com/">Back to Site</a></span></li>
			<li class="demo-info-sep"></li>
			<li class="demo-order"><span><a href="https://www.smartjobboard.com/ca/cart.php">Buy Now</a></span></li>
			<li class="demo-info-sep"></li>
		</ul>
	</div>
</div>