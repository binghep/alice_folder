/**
* This file:
*
When setting a date, without specifying the time zone, JavaScript will use the browser's time zone.

When getting a date, without specifying the time zone, the result is converted to the browser's time zone.

In other words: If a date/time is created in GMT (Greenwich Mean Time), the date/time will be converted to CDT (Central US Daylight Time) if a user browses from central US.
*/

//============added by Alice========================
jQuery.noConflict();
function addMinutes(date, minutes) {
	return new Date(date.getTime() + minutes*60*1000);
}
function minusHours(date, hours) {
    return new Date(date.getTime() - hours*60*60*1000);
}
// jQuery(function(){

// (function(jQuery){
jQuery(document).ready(function($) {

		var date_of_next_holiday;
		// console.log('here');
		var note = $('#note');
		// ts = new Date(2016, 4, 1);//may 1st
		// ts = new Date(2016, 2, 8);//3.8

		//--------------------------------------------------------------
		//Date.UTC() accept a UTC date. Return the number of milliseconds between a specified date and midnight January 1 1970: e.g. 1333065600000
		//d = new Date(Date.UTC(2016, 1, 27, 16, 0, 0));//2.28 12am (+8 GMT) convert to UTC time(+0) is 2.27 16pm    [I just subtract 8 hours from beijing time]
		// d = new Date(Date.UTC(2016, 1, 27, 23, 0, 0));//2.28 7am (+8 GMT) convert to UTC time(+0) is 2.27 23pm    [I just subtract 8 hours from beijing time]
		// d = new Date(Date.UTC(2016, 1, 27, 23, 30, 0));//2.28 7:30am (+8 GMT) convert to UTC time(+0) is 2.27 23:30pm    [I just subtract 8 hours from beijing time]
		//d = new Date(Date.UTC(2016, 2, 7, 16, 0, 0));//3.8 12am (+8 GMT) convert to UTC time(+0) is 3.7 16:00pm    [I just subtract 8 hours from beijing time]
		// d = new Date(Date.UTC(2016, 2, 27, 15, 0, 0));//3.27 8am (+8 GMT)
		//d = new Date(Date.UTC(2016, 4, 6, 1, 0, 0));//working- 5.6 9am (+8 GMT) convert to UTC time(+0) is 5.6 1am    [I just subtract 8 hours from beijing time]
		var d = new Date(Date.UTC(2016, 5, 18, 16, 0, 0));//6.19 0am (+8) convert to UTC is 6.18 16pm
		// d.setUTCHours(d.getUTCHours());//set the time to 8 hours later.
		//--------------------------------------------------------------
		//[verified] 9am (+8 GMT)= 1am (UTC +0)
		// beijing time is 8 hours + UTC time
		//--------------------------------------------------------------
		//so in the future if you want to set the beijing time to a value. just subtract 8 hours from beijing time, then put it in the parenthesis
		//--------------------------------------------------------------
		//month expect 0-11
		//day expect 1-31
		//--------------------------------------------------------------

		// d.setUTCHours(d.getUTCHours() + 8);

		//see if the d is the correct chinese holiday:
		/* alert(d.getUTCFullYear() + '-' + z(d.getUTCMonth() + 1) + '-' +
		      z(d.getUTCDate()) + 'T' + z(d.getUTCHours()) + ':' +
		      z(d.getUTCMinutes()) + ':' + z(d.getUTCSeconds()) + '+08:00'
		);
		*/
		// return;
		// 2012-12-30T05:00:00-07:00

		// ts = new Date(2016, 1, 28);//2.28
		date_of_next_holiday=d;


		// ts = new Date(2016, 1, 14); ==>2.14 valentine's day
		Xmas = true;



		var element = this;
		$.ajax({
	   		url: "https://www.1661usa.com/alice/countdown/ajax_api.php",
	   		type:"GET",
	   		dataType: 'json', // jQuery will parse the response as JSON
	   		data: {"secret_key":"uiweui35s653kjsd923*2w"},
	    	success: function(result){
	    		if (result['status']===false){
	    			// $(element).css('background-color', 'rgb(255, 152, 145)');//red
	    			// alert(result['error_details']);
	    		}else{
	    			// $(element).css('background-color', '#D8FFDA');//green
	    			var time=result['data']['beijing_datetime'];
	    			var holiday_chinese_name=result['data']['holiday_chinese_name'];
	    			var holiday_english_name=result['data']['holiday_english_name'];
	    			if ($("#language").text()!=="cn"){
		    			$('.holiday_name').html(holiday_english_name);
		    		}else{
		    			$('.holiday_name').html(holiday_chinese_name);
		    		}
		    		//time is beijing time:
		    		var t=time.split(/[- :]/);
		    		var exact_time_plus_8_hours=Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
		    		//2016-8-9 08:00
		    		var exact_time=minusHours(new Date(exact_time_plus_8_hours),8);
		    		//2016-8-9 00:00
					date_of_next_holiday=exact_time;

					/*Define a function which is used in jquery.countdown.js to render/refresh the timer based on alarm.*/
					$('#countdown').countdown({//must put inside this ajax result block, otherwise, countdown will count down to a value before ajax returns and data_of_next_holiday was set correctly.
						timestamp	: date_of_next_holiday,
						callback	: function(days, hours, minutes, seconds){
							/* save unnecessary CPU
							 var message = "";

							 message += days + " day" + ( days==1 ? '':'s' ) + ", ";
							 message += hours + " hour" + ( hours==1 ? '':'s' ) + ", ";
							 message += minutes + " minute" + ( minutes==1 ? '':'s' ) + " and ";
							 message += seconds + " second" + ( seconds==1 ? '':'s' ) + " <br />";

							 if(Xmas){
							 message += "left until the 1st!";
							 }
							 else {
							 message += "left until the 2nd!";
							 }

							 note.html(message);
							 */
						}
					});
	    		}
	    	},
	    	error: function(jqXHR, textStatus, errorThrown) {
        		// report error
    			console.log('ajax request failed: pls check your php file ');
    			var error = $.parseJSON(jqXHR.responseText);
				console.log(error.errors.message);
				// $(element).css('background-color', 'rgb(255, 152, 145)');//red
    		}
		});
		//============added by Alice========================
	//console.log(date_of_next_holiday);
	if((new Date()) > date_of_next_holiday){
		// The new year is here! Count towards something else.
		// Notice the *1000 at the end - time must be in milliseconds
		// ts = new Date(2015, 11, 25);
		// ts = new Date(2016, 4, 1);//Y-M-D h   note: month should count from 0
//		ts = new Date(Date.UTC(2016, 5, 1));//Y-M-D h   note: month should count from 0
		//ts = new Date("2, 7, 2016 09:00:00");
		//ts = new Date("2016,2,7,9:00");
//Xmas = false;
	}



// })(jQuery);
});


