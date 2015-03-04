// Aurora Menu v1.2
// Design and conception by Aurora Studio http://www.aurora-studio.co.uk
// Plugin development by Invent Partners http://www.inventpartners.com
// Copyright Invent Partners & Aurora Studio 2009

var auroraMenuSpeed = 150;
var auroraDontSlide = false;

$(document).ready(function(){
	if (jQuery.browser.msie) {
		if(parseInt(jQuery.browser.version) < 8){
			auroraDontSlide = true;
		} else {
			auroraDontSlide = false;
		}
	} else {
		auroraDontSlide = false;
	}
	var auroramenucount = 0;
	$('.auroramenu').each(function(){
		var auroramenuitemcount = 0;
		$(this).children('li').children('ul').each(function(){
			if($.cookie('arMenu_' + auroramenucount + '_arItem_' + auroramenuitemcount) == 1){
				$(this).addClass('auroraopen');
				$(this).parent().children('.aurorahide').css("display","inline");
				$(this).parent().children('.aurorashow').css("display","none");
			} else {
				$(this).css("display","none");
				$(this).removeClass('auroraopen');
				$(this).parent().children('.aurorahide').css("display","none");
				$(this).parent().children('.aurorashow').css("display","inline");
			}
			$(this).attr('auroramenuitem' , 'arMenu_' + auroramenucount + '_arItem_' + auroramenuitemcount);
			$(this).siblings('a').click(function(){
				$('.auroramenu').children('li').children('a').removeClass('auroraclicked');
				$(this).siblings('a').addClass('auroraclicked');
				$('.auroramenu').children('li').children('ul').each(function(){
					if($(this).siblings('a').hasClass('auroraclicked')){
						//$(this).slideToggle(auroraMenuSpeed);
						var arshow = 0;
						if($(this).hasClass('auroraopen')){
							arshow = 0;
							$(this).removeClass('auroraopen');
							if (auroraDontSlide == true) {
							  	$(this).css("display","none");
							} else {
								$(this).slideUp(auroraMenuSpeed);
							}
							$(this).parent().children('.aurorahide').css("display","none");
							$(this).parent().children('.aurorashow').css("display","inline");
						} else {
							arshow = 1;
							$(this).addClass('auroraopen');
							if (auroraDontSlide == true) {
							  	$(this).css("display","block");
							} else {
								$(this).slideDown(auroraMenuSpeed);
							}
							$(this).parent().children('.aurorahide').css("display","inline");
							$(this).parent().children('.aurorashow').css("display","none");
						}
						$.cookie($(this).attr('auroramenuitem') , arshow);
					}
				});
				return false;

			});
			auroramenuitemcount ++;
		});
		auroramenucount ++;
	});
});