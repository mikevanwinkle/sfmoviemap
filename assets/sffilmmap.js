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
			self.titles = [];
			self.filter = false;
			
			
			self.init = function() {
				// Set the starting position
				var mapOptions = {
					zoom: 12,
					center: self.sf
				};
			
				// Instantiate the map
				self.map = new google.maps.Map(document.getElementById('sfmap'), mapOptions);

				// Add some animation
	  			$("#search").on('mousein', function() { self.searchIn(); } );
	  			$("#search").on('focus', function() { self.searchIn(); } );
	  			$("#search").focusout( function() { self.searchOut(); } );			  	
	  			
	  			// Load the data and then create the initial markers and menu
			  	$.when( $.get("/movies.txt", function(data) {
					self.data = $.parseJSON(data);					  
				})).done( function() {
			  		self.setMarkers();
			  	}).done( function() {
			  		self.makeMenu();
			  	});
			  	
				// The search function
				$('#search').on("keydown",function(e) {
					console.log(e);
					key = e.which || e.keyCode || e.charCode;
					if ( key == 8 ||  $('#search').val().length === 0 ) {
							self.filter = false;
							self.clearMarkers();
							self.setMarkers();
							$('ul#menu li').filter( function() { $(this).show(); } );
					} else if ( key == 13 && $('#search').val().length === 0 ) {
							self.filter = false;
							self.clearMarkers();
							self.setMarkers();
							$('ul#menu li').filter( function() { $(this).show(); } );
					} else {
						 match = 0;
						 $('ul#menu li').filter( function() { 
						 	if( $(".title", this).text().indexOf( $('#search').val() ) > -1 ) {
						 		match = 1;
							 	return this;
						 	} else {
						 		$(this).hide();
						 	}
						 });						 
					}
				});	
			}
			
			/** 
			 * Sets the markers for the specified films
			 */
			self.setMarkers = function() {
				if( "object" === typeof(self.data) ) { 
		  			for( i = 0; i < self.data.length; i++ ) {
		  				if ( !self.filter || 'All' == self.filter || self.filter == self.data[i].title ) {
			  				self.markerMaker(self.data[i]);
			  			}
		  				if ( self.titles.indexOf( self.data[i].title ) < 0 ) {
			  				self.titles.push( self.data[i].title );				  			
		  				} 
		  			}
		  		}
			}
			
			/** 
			 * Build the sidebar menu
			 */
			self.makeMenu = function() {
				$('#search').after('<ul id="menu"><li class="active"><span class="title">All</span></li></ul>');
				menu = $("ul#menu");
				films = [];
				if( "object" === typeof(self.data) ) { 
		  			for( i = 0; i < self.data.length; i++ ) {
		  				if ( !self.filter || self.filter == self.data[i].title ) {		
							if ( films.indexOf(self.data[i].title) < 0 ) {
								menu.append("<li><span class='title'>"+self.data[i].title+"</span> <span class='small italic'>"+self.data[i].release_year+"</span> </li>");
								films.push(self.data[i].title);					
							}
			  			}
		  			}
		  		}
				
				// after menu is created bind to the click event 
		  		$("ul#menu li").on( 'click', function(event) { 
					console.log( $(this).text());
					self.filter = $(this).find('span.title').text();
					self.clearMarkers();
					self.setMarkers();
					$('#search').val(self.filter);
				});
				
			}
			
			/**
			 * Factory function for markers
			 */
			self.markerMaker = function(obj) {
				var marker, i;
				if ( "object" == typeof(obj.geometry) ) {
					var marker = new google.maps.Marker({
							map: self.map, 
							position: new  google.maps.LatLng( obj.geometry.lat, 	obj.geometry.lng ),
							title: obj.title,
							zIndex: 10000
					});
					self.filmmarkers.push(marker);
					google.maps.event.addListener(marker, 'click', (function(marker, i) {
						self.buildHTML(obj);
						self.infowindow.open(self.map, this);
  					}));
				}
			}
			
			/**
			 * Factory for Info Windows on Map
			 * @todo this should be more dynamic
			 */
			self.buildHTML = function(obj) {
				content = '<img src="http://ia.media-imdb.com/images/M/MV5BMjA2NjI1Mzg2MV5BMl5BanBnXkFtZTgwMTI2MzE1MDE@._V1_SX214_AL_.jpg" class="img"/>';
				$.each( obj, function(i,obj) {
					content += "<div><strong>"+i+": </strong>"+obj+"</div>";				
				});

				self.infowindow.setContent( content );
				return content;
				
			}
			
			/**
			 * Adds animation for the search field
			 */
			self.searchIn = function() {
				$('#search').animate({ opacity:0.9, height:35}, 250);
			}
			
			/**
			 * Animation out for the search field
			 */
			self.searchOut = function() {
				$('#search').animate({ height:30}, 250);
			}
			
			/**
			 * Clear all markers from the map
			 */
			self.clearMarkers = function( title ) {
				for ( i = 0 ; i < self.filmmarkers.length; i++ ) {
					self.filmmarkers[i].setMap(null);
				}
			}
		} // end FilmMap()
		
	/**
	 * Initialize google map object
	 */
	function initialize() {
		map = new FilmMap();
		map.init();
	}
		
	/** 
	 * Standard listener for google maps api
	 */
	google.maps.event.addDomListener(window, 'load', initialize);
});