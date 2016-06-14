$(document).ready(function(){
	$('.jump_to_competitor').click(function(){
		// alert($(this).attr('href'));
		//restore border color for all div:
		$('.jump_to_competitor').each(function(){
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
	});
});