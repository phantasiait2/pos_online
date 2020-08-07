<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/geo.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script>
getCoordinate('新北市新店區寶安街60號之1');
function getCoordinate(locations)
{
	id= 5;
	 geocoder = new google.maps.Geocoder();
	geocoder.geocode( { 'address': locations}, function(results, status) {
		
			 if (status == google.maps.GeocoderStatus.OK) {
				//$('#report').append(results[0].formatted_address)
				//insertCoord(results[0].geometry.location.lat(),results[0].geometry.location.lng())
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

</script>
</head>

<body>
</body>
</html>