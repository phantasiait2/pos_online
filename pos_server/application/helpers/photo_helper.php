<?php

function ImageResize($from_filename, $save_filename, $in_width, $in_height, $quality){
	
	$allow_format = array('jpeg', 'png', 'gif');
	$sub_name = $t = '';

	// Get new dimensions
	$img_info = getimagesize($from_filename);
	$width    = $img_info['0'];
	$height   = $img_info['1'];
	$imgtype  = $img_info['2'];
	$imgtag   = $img_info['3'];
	$bits     = $img_info['bits'];
	$channels = $img_info['channels'];
	$mime     = $img_info['mime'];

	list($t, $sub_name) = explode('/', $mime);
	if ($sub_name == 'jpg') {
		$sub_name = 'jpeg';
	}

	if (!in_array($sub_name, $allow_format)) {
		return false;
	}

	// 取得縮在此範圍內的比例
	$percent = getResizePercent($width, $height, $in_width, $in_height);
	$new_width  = $width * $percent;
	$new_height = $height * $percent;

	// Resample
	$image_new = imagecreatetruecolor($new_width, $new_height);
		
	// $function_name: set function name
	//   => imagecreatefromjpeg, imagecreatefrompng, imagecreatefromgif
	/*
	// $sub_name = jpeg, png, gif
	$function_name = 'imagecreatefrom'.$sub_name;
	$image = $function_name($filename); //$image = imagecreatefromjpeg($filename);
	*/
	$image = imagecreatefromjpeg($from_filename);

	imagecopyresampled($image_new, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

	return imagejpeg($image_new, $save_filename, $quality);
}
	
function getResizePercent($source_w, $source_h, $inside_w, $inside_h)
{
	if ($source_w < $inside_w && $source_h < $inside_h) {
		return 1; // Percent = 1, 如果都比預計縮圖的小就不用縮
	}

	$w_percent = $inside_w / $source_w;
	$h_percent = $inside_h / $source_h;

	return ($w_percent > $h_percent) ? $h_percent : $w_percent;
}	

?>