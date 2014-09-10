<!DOCTYPE html>
<html>
  <head>
    <title>Overlay map types</title>
    <style>
		html, body, #sfmap {
			height: 100%;
			margin: 0px;
			padding: 0px;
		}
		
		#search {
			position:absolute;
			height: 30px;
			width:100%;
			background: #f7f7f7;
			z-index: 1000;
			opacity: 0.5;
		}
	 
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <script src="https://code.jquery.com/jquery-2.1.1.js"></script>
    <script>
    jQuery(document).ready(function($) {	
		
		// map object
		function FilmMap() {
			var self = this;
			
			self.map;
			self.sf = new google.maps.LatLng(37.774929500000000000, -122.419415500000010000);
			self.geocoder = new google.maps.Geocoder();
			self.filmmarkers = [];
			self.data;
			self.instance = false;
			self.filmmarkers = [];
			self.infowindow = new google.maps.InfoWindow({content:'loading...'});
			
			
			self.init = function() {
				
				var mapOptions = {
					zoom: 12,
					center: self.sf
				};

				self.map = new google.maps.Map(document.getElementById('sfmap'), mapOptions);

				var marker = new google.maps.Marker({
				    position: self.map.getCenter(),
				    map: self.map,
				    title: 'Click to zoom'
			  	});
			  	
			  	self.loadMarkers();
			
			}
			
			
			
			self.loadMarkers = function( ) {
				// load marker data
				$.when( $.get("/movies", function(data) {
				  		self.data = $.parseJSON(data);
					}) ).done( function() { 
						if( "object" === typeof(self.data) ) { 
				  			for( i = 0; i < self.data.length; i++ ) {
				  				self.markerMaker(self.data[i]);
				  			}
				  		}
			  	});
			 }
			
			self.markerMaker = function(obj) {
				var marker, i;
				if ( "object" == typeof(obj.geometry) ) {
					var marker = new google.maps.Marker({
							map: self.map, 
							position: new  google.maps.LatLng( obj.geometry.lat, 	obj.geometry.lng ),
							title: obj.title
					});
					self.filmmarkers.push(marker);
					google.maps.event.addListener(marker, 'click', (function(marker, i) {
						self.buildHTML(obj);
						self.infowindow.open(self.map, this);
  					}));
				}
			}
			
			self.buildHTML = function(obj) {
				console.log(obj);
				content = '<h1>'+obj.title+'</h1>';
				self.infowindow.setContent( content );
				return content;
			}
		}
		
		
		
		function initialize() {
			map = new FilmMap();
			map.init();
		}

		google.maps.event.addDomListener(window, 'load', initialize);
	});
    </script>
  </head>
  <body>
  	<div id="search"></div>
    <div id="sfmap"></div>
  </body>
</html>