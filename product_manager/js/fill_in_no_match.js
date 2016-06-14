$(document).ready(function(){
	$('.no_match').click(function(){
		// alert($(this).attr('href'));
		//restore border color for all div:
		$('.no_match').each(function(){
			var competitor_name=$(this).attr('href');
			// console.log(competitor_name);
			if (competitor_name){
				$(competitor_name).css('border-color','#dedede');
			}
		});
		// highlight only selected competitor div:
		var competitor_name=$(this).attr('href');//#amazon
		// console.log(competitor_name);
		if (competitor_name){
			$(competitor_name).css('border-color','yellow');
		}
		//=================================================================
		// var className=$(this).attr('class');
		// console.log(className);//undefined or no_match
		//-----fill in No Match to this competitor fields-----
			var text_field_1_selector='#url_'+competitor_name.substring(1);//#url_amaozn
			var text_field_2_selector='#price_'+competitor_name.substring(1);//#[price_amaozn
			// console.log(text_field_2_selector);
			$(text_field_1_selector).val("No Match");
			$(text_field_2_selector).val("No Match");
			//---------------unhide confirm button-----------------
			$(this).next('.confirm_no_match').css('display','inline');
			$(this).css('display','none');
	});
	$('.confirm_no_match').click(function(){
		//--------restore border color for all competitor blocks-----------
		$('.confirm_no_match').each(function(){
			var competitor_name=$(this).attr('href');
			// console.log(competitor_name);
			if (competitor_name){
				$(competitor_name).css('border-color','#dedede');
			}
		});
		// -----highlight only selected competitor div:---------
		var competitor_name=$(this).attr('href');//#amazon
		// console.log(competitor_name);
		if (competitor_name){
			$(competitor_name).css('border-color','yellow');
		}
		//=================================================================
		//if the class of this element is confirm_no_match, move cursor in and out fields, to make input fields update and turn green:
		var className=$(this).attr('class');
		// console.log(className);//undefined or no_match
		if (className=="confirm_no_match"){//not undefined
			//-----fill in No Match to this competitor fields-----
			//console.log('no_match');
			var text_field_1_selector='#url_'+competitor_name.substring(1);//#url_amaozn
			var text_field_2_selector='#price_'+competitor_name.substring(1);//#[price_amaozn
			// console.log(text_field_2_selector);
			$(text_field_1_selector).blur();//unfocus
			$(text_field_2_selector).blur();//unfocus
			//---------------add a confirm button-----------------
			$(this).css('display','none');
			$(this).prev('no_match').css('display','inline');
		}
	});
});