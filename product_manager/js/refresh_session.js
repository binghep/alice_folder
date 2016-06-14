	var refreshTime = 600000; // every 10 minutes in milliseconds
	// var refreshTime = 5000; // every 5 seconds in milliseconds
	window.setInterval( function() {
	    $.ajax({
	        cache: false,
	        type: "GET",
	        url: "refreshSession.php",
	        success: function(data) {
	        	console.log('refreshed session');
	        }
	    });
	}, refreshTime );