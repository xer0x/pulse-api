// ==========================================================================
// Project:   Pulse
// Copyright: ©2010 My Company, Inc.
// ==========================================================================
/*globals Pulse */

// This is the function that will start your app running.  The default
// implementation will load any fixtures you have created then instantiate
// your controllers and awake the elements on your page.
//
// As you develop your application you will probably want to override this.
// See comments for some pointers on what to do next.
//
Pulse.main = function main() {
  
  // Step 1: Instantiate Your Views
  // The default code here will make the mainPane for your application visible
  // on screen.  If you app gets any level of complexity, you will probably 
  // create multiple pages and panes.  
  Pulse.getPath('mainPage.mainPane').append() ;

  // Step 2. Set the content property on your primary controller.
  // This will make your app come alive!

  // Fixture datasource:
  //var query = SC.Query.local(Pulse.Building);
  //var buildings = Pulse.store.find(query);

  // Live HTTP datasource:
  var buildings = Pulse.store.find(Pulse.BUILDINGS_QUERY);
  // (fixture and http are identical... at this stage?)

  // TODO: Set the content property on your primary controller
  // ex: Pulse.contactsController.set('content',Pulse.contacts);
  Pulse.buildingsController.set('content', buildings);

  // it would be nice to have this guarantee'd to happen after SproutCore loads
  Pulse.addGMapScriptCode(); 

} ;

function main() { Pulse.main(); }

// this is probably not the best way to add google maps, 
// but it works.. a sproutcore plugin would handy
Pulse.initializeGMap = function initializeGMap() {
	
  var initialLatLng = new google.maps.LatLng(49.271296584780345, -123.11454373016358); // view vancouver on map
  var myOptions = {
	  zoom: 14,
  	center: initialLatLng,
  	mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  var mapDiv = document.getElementById("map_canvas");
  mapDiv.style.width='100%';
  mapDiv.style.height='100%';

  //var map = Pulse.map = new google.maps.Map(mapDiv, myOptions);
  Pulse.map = new google.maps.Map(mapDiv, myOptions);
  
  Pulse.GMapLoaded = true;
  Pulse.invokeLater(Pulse.setMarkers);
};

/**
 * After building.json is loaded,
 * this should be called to place map markers
 *
 * 1. called via data-source/building.js didFetchBuildings()
 * 2. called via main.js Pulse.initializeGmap()
 */
Pulse.setMarkers = function() {
  
  // skip this if google map is not yet loaded
  if (!Pulse.GMapLoaded) {
    // timing is difficult.
    return;
  }
  
  // popup overlay window for map
  var infoWindow = new google.maps.InfoWindow ({
      content: 'Building', 
      size: new google.maps.Size(150,150),
      disableAutoPan: false
      });

  // building data via sproutcore controller -> ajax/fitting
  var buildings = Pulse.buildingsController.get('content');
  
  buildings.forEach(function(building) {
	
	  var marker = new google.maps.Marker({
  	  position: new google.maps.LatLng( building.get('latitude'), building.get('longitude') ),
  	  map: Pulse.map,
  	  title: building.get('name')
  	});
	
  	var contentString = '<div id="content" style="color: black">'+
      '<h1 id="firstHeading" class="firstHeading">'+building.get('name')+'</h1>'+
      '<p>Used '+building.get('energy') +' energy '+
      'over the last day.</p>'+
      '</div>';
    
    var selectMarker = function(event) {
      console.log('selectMarker called for a building');

      infoWindow.setContent(contentString);
      infoWindow.open(Pulse.map, marker);
      
      // focus in on the selected spot
      Pulse.map.panTo( new google.maps.LatLng(building.get('latitude'), building.get('longitude')) );
      
    };
    
    if (!Pulse.GMapSelectors) {
      Pulse.GMapSelectors = {};
    }
    Pulse.GMapSelectors[building.get('guid')] = selectMarker;

    // Marker's click action	
	  google.maps.event.addListener(marker, 'click', selectMarker);
	  
	  //console.log('building : ' + building.get('name') );
  
  });

  google.maps.event.addListener(Pulse.map, 'click', function(event) {
    infoWindow.close();
    //console.log('saw click on map');
    //console.log(SC.inspect(event));
  });

};


Pulse.addGMapScriptCode = function addGMapScriptCode() {
  var script = document.createElement("script");
  script.type = "text/javascript";
  script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=Pulse.initializeGMap";
  document.body.appendChild(script);
  //console.log("adding GMap script tag");
};
