<?php
/**
* RandImg - класс рандомизации изображений
*/
class RandImg
{
	public static function Mirror($image, $outtype="png", $raw=false, $vertical=false) {
		if(!$raw) {
			list($size["x"], $size["y"])=getimagesize($image);
		} else {
			list($size["x"], $size["y"])=getimagesizefromstring($image);
		}
		$data=RandImg::loadImage($image, $raw);
		imageflip($data, $vertical?IMG_FLIP_VERTICAL:IMG_FLIP_HORIZONTAL);
		return RandImg::makeImageString($data, $outtype);
	}
	public static function Invert($image, $outtype="png", $raw=false) {
		if(!$raw) {
			list($size["x"], $size["y"])=getimagesize($image);
		} else {
			list($size["x"], $size["y"])=getimagesizefromstring($image);
		}
		$data=RandImg::loadImage($image, $raw);
		imagefilter($data, IMG_FILTER_NEGATE);
		return RandImg::makeImageString($data, $outtype);
	}
	public static function Grayscale($image, $outtype="png", $raw=false) {
		if(!$raw) {
			list($size["x"], $size["y"])=getimagesize($image);
		} else {
			list($size["x"], $size["y"])=getimagesizefromstring($image);
		}
		$data=RandImg::loadImage($image, $raw);
		imagefilter($data, IMG_FILTER_GRAYSCALE);
		return RandImg::makeImageString($data, $outtype);
	}
	public static function Sharp($image, $outtype="png", $raw=false) {
		if(!$raw) {
			list($size["x"], $size["y"])=getimagesize($image);
		} else {
			list($size["x"], $size["y"])=getimagesizefromstring($image);
		}
		$data=RandImg::loadImage($image, $raw);
		$contrast=rand(0,60)-30;
		imagefilter($data, IMG_FILTER_CONTRAST,$contrast);
		return RandImg::makeImageString($data, $outtype);
	}
	public static function Blur($image, $outtype="png", $raw=false) {
		if(!$raw) {
			list($size["x"], $size["y"])=getimagesize($image);
		} else {
			list($size["x"], $size["y"])=getimagesizefromstring($image);
		}
		$data=RandImg::loadImage($image, $raw);
		$blurs=array(IMG_FILTER_GAUSSIAN_BLUR, IMG_FILTER_SELECTIVE_BLUR);
		$blur_type=array_rand($blurs);
		$blur_type=$blurs[$blur_type];
		imagefilter($data, $blur_type);
		return RandImg::makeImageString($data, $outtype);
	}
	public static function Eskiz($image, $outtype="png", $raw=false) {
		if(!$raw) {
			list($size["x"], $size["y"])=getimagesize($image);
		} else {
			list($size["x"], $size["y"])=getimagesizefromstring($image);
		}
		$data=RandImg::loadImage($image, $raw);
		imagefilter($data, IMG_FILTER_MEAN_REMOVAL);
		return RandImg::makeImageString($data, $outtype);
	}
	public static function Pixelization($image, $outtype="png", $raw=false) {
		if(!$raw) {
			list($size["x"], $size["y"])=getimagesize($image);
		} else {
			list($size["x"], $size["y"])=getimagesizefromstring($image);
		}
		$data=RandImg::loadImage($image, $raw);
		$block_size=rand(3,7);
		$pix_modern=true;
		imagefilter($data, IMG_FILTER_PIXELATE, $block_size, $pix_modern);
		return RandImg::makeImageString($data, $outtype);
	}
	public static function Crop($image, $outtype="png", $raw=false) {
		if(!$raw) {
			list($size["x"], $size["y"])=getimagesize($image);
		} else {
			list($size["x"], $size["y"])=getimagesizefromstring($image);
		}
		$data=RandImg::loadImage($image, $raw);
		$cropTop=rand(5,15)*($size["y"]/100);
		$cropBottom=rand(5,15)*($size["y"]/100);
		$cropLeft=rand(5,15)*($size["x"]/100);
		$cropRight=rand(5,15)*($size["x"]/100);
		$new_size=array(
			"x" => $size["x"]-$cropLeft-$cropRight,
			"y" => $size["y"]-$cropTop-$cropBottom,
		);
		$new_data=imagecreatetruecolor($new_size["x"], $new_size["y"]);
		imagecopy($new_data, $data, 0, 0, $cropLeft, $cropTop, $new_size["x"], $new_size["y"]);

		return array(
			"image"=>RandImg::makeImageString($new_data, $outtype),
			"width"=>$new_size["x"],
			"height"=>$new_size["y"]
		);
	}

	public static function Resize($image, $outtype="png", $raw=false, $fix=true) {
		if(!$raw) {
			list($size["x"], $size["y"])=getimagesize($image);
		} else {
			list($size["x"], $size["y"])=getimagesizefromstring($image);
		}
		$data=RandImg::loadImage($image, $raw);
		$resizex=rand(75,115);
		$resizey=rand(75,115);
		if($fix) $resizey=$resizex;

		$new_size=array(
			"x" => $size["x"]/100*$resizex,
			"y" => $size["y"]/100*$resizey,
		);
		$new_data=imagecreatetruecolor($new_size["x"], $new_size["y"]);
		imagecopyresized($new_data, $data, 0, 0, 0, 0, $new_size["x"], $new_size["y"], $size["x"], $size["y"]);

		return array(
			"image"=>RandImg::makeImageString($new_data, $outtype),
			"width"=>$new_size["x"],
			"height"=>$new_size["y"],
			"owidth"=>$size["x"],
			"oheight"=>$size["y"]
		);
	}

	public static function Rotate($image, $outtype="png", $raw=false) {
		if(!$raw) {
			list($size["x"], $size["y"])=getimagesize($image);
		} else {
			list($size["x"], $size["y"])=getimagesizefromstring($image);
		}
		$data=RandImg::loadImage($image, $raw);
		$angle=rand(0,30)-15;
		$color=imagecolorallocate($data, 255, 255, 255);
		$new_data=imagerotate($data, $angle, $color);

		$new_image=RandImg::makeImageString($new_data, $outtype);
		list($new_size["x"], $new_size["y"])=getimagesizefromstring($new_image);
		return array(
			"image"=>RandImg::makeImageString($new_data, $outtype),
			"width"=>$new_size["x"],
			"height"=>$new_size["y"],
			"owidth"=>$size["x"],
			"oheight"=>$size["y"],
			"angle"=>$angle
		);
	}

	public static function Border($image, $outtype="png", $raw=false) {
		if(!$raw) {
			list($size["x"], $size["y"])=getimagesize($image);
		} else {
			list($size["x"], $size["y"])=getimagesizefromstring($image);
		}
		$data=RandImg::loadImage($image, $raw);
		$border_size=rand(5,15);
		$new_size=array(
			"x" => $size["x"]+$border_size*2,
			"y" => $size["y"]+$border_size*2,
		);
		$new_data = imagecreatetruecolor($new_size["x"], $new_size["y"]);
		$color=imagecolorallocate($new_data, rand(0,255), rand(0,255), rand(0,255));
		imagefill($new_data, 0, 0, $color);
		imagecopy($new_data, $data, $border_size, $border_size, 0, 0, $size["x"], $size["y"]);

		return array(
			"image"=>RandImg::makeImageString($new_data, $outtype),
			"width"=>$new_size["x"],
			"height"=>$new_size["y"],
			"border"=>$border_size
		);
	}
	public static function Move($image, $outtype="png", $raw=false) {
		if(!$raw) {
			list($size["x"], $size["y"])=getimagesize($image);
		} else {
			list($size["x"], $size["y"])=getimagesizefromstring($image);
		}
		$data=RandImg::loadImage($image, $raw);
		$move_x=rand(0,$size["x"]);
		$move_y=rand(0,$size["y"]);
		$new_data = imagecreatetruecolor($size["x"], $size["y"]);
		imagecopy($new_data, $data, 0, 0, $size["x"]-$move_x, $size["y"]-$move_y, $move_x, $move_y);
		imagecopy($new_data, $data, $move_x, 0, 0, $size["y"]-$move_y, $size["x"]-$move_x, $move_y);
		imagecopy($new_data, $data, 0, $move_y, $size["x"]-$move_x, 0, $move_x, $size["y"]-$move_y);
		imagecopy($new_data, $data, $move_x, $move_y, 0, 0, $size["x"]-$move_x, $size["y"]-$move_y);

		return array(
			"image"=>RandImg::makeImageString($new_data, $outtype),
			"move_x"=>$move_x,
			"move_y"=>$move_y
		);
	}

	public static function getImageType($name) {
		$types=array(
			"png" => "/\.png$/i",
			"gif" => "/\.gif$/i",
			"bmp" => "/\.bmp$/i",
			"jpeg" => "/\.(jpg|jpeg)$/i"
		);
		foreach ($types as $type => $regexp) {
			if(preg_match($regexp, $name))
				return $type;
		}
	}
	public static function loadImage($image, $raw=false) {
		$data=$raw?$image:file_get_contents($image);
		$image=imagecreatefromstring($data);
		return $image;
	}
	public static function makeImageString($data, $type) {
		ob_start();
		switch ($type) {
			case 'bmp':
				imagewbmp($data);
				break;
			case 'gif':
				imagegif($data);
				break;
			case 'jpeg':
				imagejpeg($data);
				break;
			
			default:
				imagepng($data);
				break;
		}
		$content = ob_get_contents();
	  	ob_end_clean();
	  	return $content;
	}
}
if(!function_exists('getimagesizefromstring')) {
	function getimagesizefromstring($string_data)
	{
		$uri = 'data://application/octet-stream;base64,'  . base64_encode($string_data);
		return getimagesize($uri);
	}
}
if(!function_exists('imageflip')) {
	function imageflip(&$image, $orientation)
	{
	    $x = 0;
	    $y = 0;
	    $width = null;
	    $height = null;
	    if ($width  < 1) $width  = imagesx($image);
	    if ($height < 1) $height = imagesy($image);
	    // Truecolor provides better results, if possible.
	    if (function_exists('imageistruecolor') && imageistruecolor($image))
	    {
	        $tmp = imagecreatetruecolor(1, $height);
	    }
	    else
	    {
	        $tmp = imagecreate(1, $height);
	    }
	    $x2 = $x + $width - 1;
	    for ($i = (int) floor(($width - 1) / 2); $i >= 0; $i--)
	    {
	        // Backup right stripe.
	        imagecopy($tmp,   $image, 0,        0,  $x2 - $i, $y, 1, $height);
	        // Copy left stripe to the right.
	        imagecopy($image, $image, $x2 - $i, $y, $x + $i,  $y, 1, $height);
	        // Copy backuped right stripe to the left.
	        imagecopy($image, $tmp,   $x + $i,  $y, 0,        0,  1, $height);
	    }
	    imagedestroy($tmp);
	    return true;
	}
}
?>