$cache_dir = WB_PATH.'/temp/';
$fileduration = "1 hour";
$fontfile = WB_PATH.'/media/fonts/'.$ttffile;
$fontangle = 0;
if (!isset($fontsize)) $fontsize = "12";
if (!isset($fontcolor)) $fontcolor = "000000";

$cache_file = 'text2png_'.md5($text.$fontfile.$fontsize.$fontcolor).'.png';

$cdir = opendir($cache_dir) or die ('Could not open '.$cache_dir);
while ($file = readdir($cdir)) {
	if ((preg_match('/text2png_/',$file)) && (filemtime($cache_dir.$file)) <  (strtotime('-'.$fileduration))) {
		unlink($cache_dir.$file);
	}
}

if (!file_exists($cache_dir.$cache_file))
	{$box = @imagettfbbox($fontsize, $fontangle, $fontfile, $text);
	$textwidth = abs($box[4] - $box[0]);
	$textheight = abs($box[5] - $box[1]);
	$imagewidth = $textwidth+10;
	$imageheight = $textheight+10;
	$xcord = ($imagewidth/2)-($textwidth/2)-2;
	$ycord = ($imageheight /1.33);
	
	$img = imagecreatetruecolor($imagewidth, $imageheight);
	imagealphablending($img, false);
	imagesavealpha($img, true);
	imageantialias($img, true);
	$opaque = imagecolorallocatealpha($img, 0, 0, 0, 0);
	$transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
	
	imagefilledrectangle($img, 0,0, $imagewidth, $imageheight, $transparent);

	if( eregi( "([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})", $fontcolor, $textrgb ) )
		{$textred = hexdec( $textrgb[1] );  $textgreen = hexdec( $textrgb[2] );  $textblue = hexdec( $textrgb[3] );}
	$opaque = imagecolorallocate($img, $textred, $textgreen, $textblue);

	imagettftext($img, $fontsize, $fontangle, $xcord, $ycord, $opaque , $fontfile, $text);

	// invert alpha channel
	for($i=0;$i<$xsize;$i++)
		{for($j=0;$j<$ysize;$j++)
		 	{$color = imagecolorat($img, $i, $j);
			$color = ($color & 0x00FFFFFF) | ((127-($color>>24))<<24);
			imagesetpixel($img, $i, $j, $color);
		 	}
		}
	imagepng($img, $cache_dir.$cache_file);
	imagedestroy($img);
	}
	
return '<img src="'.WB_URL.'/temp/'.$cache_file.'" style="border:0px;margin:0px;padding:0px;vertical-align:middle;" alt="'.$text.'"/>';
