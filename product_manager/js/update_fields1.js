$(document).ready(function() {
	$('#search_sku_button').click(function(){
		var sku=$('input#search_sku').val();
		if (!sku) {
			alert('sku should not be empty. ');
		}else{
			var search_page_url="search.php?sku="+sku;
			window.open(search_page_url,'_blank');
		}
	});

	// $('#filter_toggle').click(function(){
 //    	$('#float_filter_block').css("display","inline");
 //    });

	// $('#filter_close_button').click(function(){
 //    	$('#float_filter_block').css("display","none");
	// });
	// $('#filter_toggle').mouseexit(function(){
	// 	$('#float_filter_block').css("display","none");
	// });



$('input[id^="price_"]').blur(function(){
	console.log($(this).attr('id'));
	// console.log($(this).val());
	var id=$(this).attr('id');//e.g. price_amazon
	var competitor=id.substring(6);// amazon
	console.log('the competitor is: '+competitor);
	var currency_id="currency_"+competitor;//e.g. currency_amazon
 	var selector="#"+currency_id+" option:selected";

 	var currency_selected=$(selector).text();
 	console.log('currency selected: '+currency_selected);
    var element = this;
    $(element).css('background-color', 'white');//remove any color
	$.ajax(
	   	{
	   		url: "api.php",
	   		type:"GET",
	   		dataType: 'json', // jQuery will parse the response as JSON
	   		data: {"class":id,"sku":$(element).parents('form').find('#sku').text(),"new_value":currency_selected+$(this).val(),"worker_id":$('#session_user_id').text()},
	    	success: function(result){
	    		console.log(result);
	    		if (result['status']=="error"){
	    			$(element).css('background-color', 'rgb(255, 152, 145)');//red
	    			alert(result['error_details']);
	    		}else{
	    			$(element).css('background-color', '#D8FFDA');//green
	    		}
	    	},
	    	error: function(jqXHR, textStatus, errorThrown) {
        		// report error
    			console.log('ajax request failed: pls check your php file ');
    			var error = jQuery.parseJSON(jqXHR.responseText);
				alert(error.errors.message);
				$(element).css('background-color', 'rgb(255, 152, 145)');//red
    		}
		}
	);
});



$('input[id^="url_"]').blur(function(){
	console.log($(this).attr('id'));
	// console.log($(this).val());
	var id=$(this).attr('id');//e.g. url_amazon
	var competitor=id.substring(4);// amazon
	console.log('the competitor is: '+competitor);
 	var attribute_to_update="url_"+competitor;//e.g. url_amazon

    var element = this;
    $(element).css('background-color', 'white');//remove any color
	$.ajax(
		   	{
		   		url: "api.php",
		   		type:"GET",
		   		dataType: 'json', // jQuery will parse the response as JSON
		   		data: {"class":attribute_to_update,"sku":$(element).parents('form').find('#sku').text(),"new_value":$(this).val(),"worker_id":$('#session_user_id').text()},
		    	success: function(result){
		    		console.log(result);
		    		if (result['status']=="error"){
		    			$(element).css('background-color', 'rgb(255, 152, 145)');//red
		    			alert(result['error_details']);
		    		}else{
		    			$(element).css('background-color', '#D8FFDA');//green
		    		}
		    	},
		    	error: function(jqXHR, textStatus, errorThrown) {
	        		// report error
    				console.log('ajax request failed: pls check your php file ');
	    			var error = jQuery.parseJSON(jqXHR.responseText);
					alert(error.errors.message);
					$(element).css('background-color', 'rgb(255, 152, 145)');//red
	    		}
			}
	);
});


//==================I recommend=====================
$('#i_recommend').change(function(){
	// alert($(this).parents('form').find('#sku').text());
    var element = this;
    if (this.checked){
    	// alert('checked');
    	$(element).parent('div').css('background-color', 'white');//remove any color
		$.ajax(
		   	{
		   		url: "api.php",
		   		type:"GET",
		   		dataType: 'json', // jQuery will parse the response as JSON
		   		data: {"class":"i_recommend","sku":$(element).parents('form').find('#sku').text(),"new_value":1,"worker_id":$('#session_user_id').text()},
		    	success: function(result){
		    		console.log(result);
		    		if (result['status']=="error"){
		    			$(element).parent('div').css('background-color', 'rgb(255, 152, 145)');//red
		    			alert(result['error_details']);
		    		}else{
		    			$(element).parent('div').css('background-color', '#D8FFDA');//green
		    		}
		    	},
		    	error: function(jqXHR, textStatus, errorThrown) {
	        		// report error
    				console.log('ajax request failed: pls check your php file ');
	    			var error = jQuery.parseJSON(jqXHR.responseText);
					alert(error.errors.message);
					$(element).parent('div').css('background-color', 'rgb(255, 152, 145)');//red
	    		}
			}
		);
    }else{
    	// alert('unchecked');
    	$(element).parent('div').css('background-color', 'white');//remove any color
		$.ajax(
		   	{
		   		url: "api.php",
		   		type:"GET",
		   		dataType: 'json', // jQuery will parse the response as JSON
		   		data: {"class":"i_recommend","sku":$(element).parents('form').find('#sku').text(),"new_value":0,"worker_id":$('#session_user_id').text()},
		    	success: function(result){
		    		console.log(result);
		    		if (result['status']=="error"){
		    			$(element).parent('div').css('background-color', 'rgb(255, 152, 145)');//red
		    			alert(result['error_details']);
		    		}else{
		    			$(element).parent('div').css('background-color', '#D8FFDA');//green
		    		}
		    	},
		    	error: function(jqXHR, textStatus, errorThrown) {
	        		// report error
    				console.log('ajax request failed: pls check your php file ');
	    			var error = jQuery.parseJSON(jqXHR.responseText);
					alert(error.errors.message);
					$(element).parent('div').css('background-color', 'rgb(255, 152, 145)');//red
	    		}
			}
		);
    }
	
});


$('#research_note').blur(function(){
	var attribute_to_update="research_note";
    var element = this;
    $(element).css('background-color', 'white');//remove any color
	$.ajax(
		   	{
		   		url: "api.php",
		   		type:"GET",
		   		dataType: 'json', // jQuery will parse the response as JSON
		   		data: {"class":attribute_to_update,"sku":$(element).parents('form').find('#sku').text(),"new_value":$(this).val(),"worker_id":$('#session_user_id').text()},
		    	success: function(result){
		    		console.log(result);
		    		if (result['status']=="error"){
		    			$(element).css('background-color', 'rgb(255, 152, 145)');//red
		    			alert(result['error_details']);
		    		}else{
		    			$(element).css('background-color', '#D8FFDA');//green
		    		}
		    	},
		    	error: function(jqXHR, textStatus, errorThrown) {
	        		// report error
    				console.log('ajax request failed: pls check your php file ');
	    			var error = jQuery.parseJSON(jqXHR.responseText);
					alert(error.errors.message);
					$(element).css('background-color', 'rgb(255, 152, 145)');//red
	    		}
			}
	);
});


});