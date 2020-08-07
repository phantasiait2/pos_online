<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDEw75OoWnRMDjow1BC7_KvEH2Wr53iFmw&callback=initMap"
  type="text/javascript"></script><title>Untitled Document</title>

<script type="text/javascript" src="http://shipment.phantasia.com.tw/javascript/geo.js"></script>


<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

<script type="application/javascript">
function showAddress(address) {

	
	 geocoder = new google.maps.Geocoder();

		geocoder.geocode( { 'address': address}, function(results, status) {
    var myOptions = {
      zoom: 15,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };			
			ret = initialize(results[0].geometry.location.lat(),results[0].geometry.location.lng());
			showAllmember(ret,<?=$coord?>);	 
	
		
      });

}

var geocoder;
  function initialize(latitude,longitude) {
     var latlng = new google.maps.LatLng(latitude, longitude);
    var myOptions = {
      zoom: 15,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	//setMarkers(map, beaches);
	return map;
  }


function setMarkers(map, locations) {
  // Add markers to the map

  // Marker sizes are expressed as a Size of X,Y
  // where the origin of the image (0,0) is located
  // in the top left of the image.

  // Origins, anchor positions and coordinates of the marker
  // increase in the X direction to the right and in
  // the Y direction down.
  
  var image = new google.maps.MarkerImage('http://shipment.phantasia.com.tw/images/bb.jpg',
      // This marker is 20 pixels wide by 32 pixels tall.
      new google.maps.Size(8, 8),
      // The origin for this image is 0,0.
      new google.maps.Point(0,0),
      // The anchor for this image is the base of the flagpole at 0,32.
      new google.maps.Point(0, 32));
  var shadow = new google.maps.MarkerImage('http://shipment.phantasia.com.tw/images/bb.jpg',
      // The shadow image is larger in the horizontal dimension
      // while the position and offset are the same as for the main image.
      new google.maps.Size(8, 8),
      new google.maps.Point(0,0),
      new google.maps.Point(0, 32));
      // Shapes define the clickable region of the icon.
      // The type defines an HTML <area> element 'poly' which
      // traces out a polygon as a series of X,Y points. The final
      // coordinate closes the poly by connecting to the first
      // coordinate.
	  var star = new google.maps.MarkerImage('http://shipment.phantasia.com.tw/images/star.png',
	       // This marker is 20 pixels wide by 32 pixels tall.
      new google.maps.Size(16, 16),
      // The origin for this image is 0,0.
      new google.maps.Point(0,0),
      // The anchor for this image is the base of the flagpole at 0,32.
      new google.maps.Point(0, 32));
 
  
  var shape = {
      coord: [1, 1, 1, 20, 18, 20, 18 , 1],
      type: 'poly'
  };

	var infowindow = new google.maps.InfoWindow();	


  for (var i = 0; i < locations.length; i++) {
    var beach = new Array();
	beach = locations[i];

    var myLatLng = new google.maps.LatLng(beach['latitude'], beach['longitude']);
     marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        shadow: shadow,
        icon: image,
        shape: shape,
        title: beach[0],
        zIndex: i+1
    });
		//addInfWindow(marker,map,infowindow,beach);
  }
	    var myLatLng = new google.maps.LatLng(24.983089,121.540932);
     marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        shadow: shadow,
        icon: star,
        shape: shape,
        title: beach[0],
        zIndex: i+1
    });
	
	
	
	//var infowindow = new google.maps.InfoWindow();	
/*
  for (var i = 0; i < locations.length; i++) {
	  setTimeout(	"getCoordinate('"+locations[i].address+"')",5000*i);
  }
	  alert(i);
	*/  
   
		//addInfWindow(marker,map,infowindow,beach);
 
	
}

function getCoordinate(locations)
{
	id= 5;
	 geocoder = new google.maps.Geocoder();
	geocoder.geocode( { 'address': locations}, function(results, status,id) {
		
			 if (status == google.maps.GeocoderStatus.OK) {
				$('#report').append(results[0].formatted_address)
				insertCoord(results[0].geometry.location.lat(),results[0].geometry.location.lng())
				alert(id)		;
			
			 }
        
      });
	
}



function insertCoord(latitude,longitude)
{
	$.post('/member/insert_coord',{latitude:latitude,longitude:longitude},function(data)
	{
		
		if(data.result==true)
		{
				
			
		}
		
	},'json')	
	
	
}






function showAllmember(map,locations)
{
	
//	var data = eval("(" + locations + ")");
	setMarkers(map, locations);
	
	
}

showAddress('<?=$address?>');

/*
if(geo_position_js.init()){
			geo_position_js.getCurrentPosition(success_callback,error_callback,{enableHighAccuracy:true});
		}
		else{
			alert("Functionality not available");
		}

		function success_callback(p)
		{
			
			 
				

			//getLocation(p.coords.latitude,p.coords.longitude,100000000,ret);
		}
		
		function error_callback(p)
		{
			alert('error='+p.code);
		}		

*/
</script>

</head>

<body>
<div id="map_canvas" style="width:1200px; height:800px;"></div>
</body>
</html>