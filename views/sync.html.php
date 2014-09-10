<!DOCTYPE html>
<html>
  <head>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <script src="https://code.jquery.com/jquery-2.1.1.js"></script>
    <script>
    	(function($) {
    		data = false;
    		$.when( function() { $.get("http://data.sfgov.org/resource/yitu-d5am.json", function( data ) {
    				$.data = data;
    			}
    		) } ).done( function() {
    			console.log($.data);
    			for( i = 0; i < 10 ; i++ ) {
    				setTimeout( 'console.log( "wait" )', 1000 ).lookup(data[i].locations);
    				$("#output").append("<div>Checking ... "+ data[i].title +"</div>" );
    				
    			}

    		});	
    		
    		
			geocoder = new google.maps.Geocoder();
    		
    		function lookup( address ) {
			// add SF if the address doesn't already have it. 
				if ( ! address.indexOf('San Francisco') > -1  ) {
					address = address + ', San Francisco, CA';
				}
				
    			geocoder.geocode( { 'address': address  }, function( answer, status ) {
					if( status == google.maps.GeocoderStatus.OK ) {
							console.log(answer[0].geometry.location.k);
					}
				});
			
			}

    		
    	})(jQuery);
    </script>
</head>
<body>
	<div id="output"></div>
</body>
</html>