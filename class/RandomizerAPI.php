<?php
/**
* RandomizerAPI - апи для интерфейса и сторонних скриптов, например йаПосылалки
*/
class RandomizerAPI
{
	private $REQUEST_METHOD;
	private $QUERY_STRING;
	private $POST;
	private $GET;
	private $URL;
	private $translation;
	private $imagesPath="images/";
	private $cachePath="cache/";
	private $format;

	function __construct($REQUEST_METHOD, $POST, $GET, $QUERY_STRING, $translation)
	{
		$this->REQUEST_METHOD=$REQUEST_METHOD;
		$this->QUERY_STRING=$QUERY_STRING;
		$this->POST=$POST;
		$this->GET=$GET;
		$this->URL=$_SERVER['PHP_SELF'];
		$this->translation=$translation;
	}
	function processRequest() {
		if($this->REQUEST_METHOD=="POST") {
			if(isset($this->GET['req']) && $this->GET['req']!="") {
				$req=$this->GET['req'];
			} else {
				return "Bad request!";
			}
		} else {
			if(isset($this->GET['req']) && $this->GET['req']!="") {
				$req=$this->GET['req'];
				if($req=="selfDiagnostics") {
					return $this->getSelfDiagnosticts();
				} elseif ($req="randomizeImage") {
					$this->format=isset($this->GET['format'])?$this->GET['format']:"image";
					if($this->format=="base64") {
						$this->format="image";
						$encodeBase64=true;
					}
					$path=iconv("UTF-8", "CP1251", $this->GET['path']);
					$opath=$path;
					$path=$this->imagesPath.$path;

					if(DEMO && $this->format=="convert") {
						header('Content-type: text/html; charset=utf-8;'); 
						return "<h1>Доступ запрещен</h1><p>Массовая обработка запрещена в демонстрационном режиме</p>";
					}
					//Проверяем что файл существует
					if(!file_exists($path)) {
						if($this->format=="image") {
							header('Content-type: text/html; charset=utf-8;'); 
							return "<h1>Файл не найден</h1><p>Файл ".htmlentities($path)." - не существует</p>";
						} else {
							header('Content-Type: application/json');
							return json_encode(array(
								"convert"=>false,
								"reason"=>"not_exists",
								"file"=>$path
							));
						}
					}
					//Проверяем, что запрошена не папка
					if(is_dir($path)) {
						$files=array_filter(scandir($path), function($var) {
							return !in_array($var, array(".",".."));
						});
						foreach ($files as $index => $filename) {
							$files[$index]=iconv("CP1251", "UTF-8", $opath.$filename);
						}
						header('Content-Type: application/json');
						return json_encode(array(
							"convert"=>false,
							"reason"=>"is_dir",
							"files"=>array_values($files)
						));
					}
					$filename=basename($path);
					/*$options=array(
						'hmirror'=>isset($_GET['hmirror']) && $_GET['hmirror']=="y",
						'vmirror'=>isset($_GET['vmirror']) && $_GET['vmirror']=="y",
						'invert'=>isset($_GET['invert']) && $_GET['invert']=="y",
						'grayscale'=>isset($_GET['grayscale']) && $_GET['grayscale']=="y",
						'crop'=>isset($_GET['crop']) && $_GET['crop']=="y",
						'fixresize'=>isset($_GET['fixresize']) && $_GET['fixresize']=="y",
						'resize'=>isset($_GET['resize']) && $_GET['resize']=="y",
						'rotate'=>isset($_GET['rotate']) && $_GET['rotate']=="y",
						'border'=>isset($_GET['border']) && $_GET['border']=="y",
						'sharp'=>isset($_GET['sharp']) && $_GET['sharp']=="y",	
						'blur'=>isset($_GET['blur']) && $_GET['blur']=="y",	
						'eskiz'=>isset($_GET['eskiz']) && $_GET['eskiz']=="y",	
						'pixelization'=>isset($_GET['pixelization']) && $_GET['pixelization']=="y",
						'move'=>isset($_GET['move']) && $_GET['move']=="y"	
					);*/
					$options=array();
					$querys=explode("&", $this->QUERY_STRING);
					foreach ($querys as $variable) {
						list($name, $value)=explode("=", $variable);
						if($name!="o" && $value=="y")
							$options[]=array("name"=>$name);
						//else
						//тут будут опции методов
					}
					
					if(isset($_GET['download']) && $_GET['download']=="y") {
						$response=$this->randomizeImage($path, $options, false);
						header('Content-Description: File Transfer');
						header('Content-Type: application/octet-stream');
						header('Content-Disposition: attachment; filename="'.$filename.'"');
						header('Content-Transfer-Encoding: binary');
						header('Connection: Keep-Alive');
						header('Expires: 0');
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						header('Pragma: public');
						header('Content-Length: ' . filesize($path));
						return $response;
					} else {
						$response=$this->randomizeImage($path, $options);
						if($this->format=="convert") {
							$outdir=$_GET['outdir'];
							if(!file_exists($this->imagesPath.$outdir))
								mkdir($this->imagesPath.$outdir);
							$outpath=$this->imagesPath.$outdir.$filename;
							//$i=0;
							while(file_exists($outpath)) {
								//$i++;
								preg_match("/^(.+?)([\.\d]*)\.(.+?)$/", $outpath, $arr);
								$num=$arr[2]==""?0:intval(str_replace(".", "", $arr[2]));
								$num++;
								$outpath="{$arr[1]}.{$num}.{$arr[3]}";
							}
							file_put_contents($outpath, $response);
							header('Content-Type: application/json');
							return json_encode(array(
								"convert"=>true
							));
						}
					}
					if(isset($encodeBase64) && $encodeBase64) {
						header('Content-Type: text/plain; charset=UTF-8');
						return base64_encode($response);
					}
					return $response;
				}
			} else {
				return false;
			}
		}
	}
	//POST REQUESTS
	private function randomizeImage($path, $options, $header=true) {
		$type=RandImg::getImageType($path);
		$image=file_get_contents($path);
		list($size["x"], $size["y"])=getimagesizefromstring($image);
		$atributes=array();
		$css_rules=array();
		$json_options=array();

		if($this->format=="img") {
			$atributes["width"]=$size["x"];
			$atributes["height"]=$size["y"];
		} elseif ($this->format=="bg") {	
			$css_rules["width"]=$size["x"]."px";
			$css_rules["height"]=$size["y"]."px";
		}

		foreach ($options as $option) {
			if($option['name']=='hmirror') {
				$image=RandImg::Mirror($image, $type, true, false);
			} else if($option['name']=='vmirror') {
				$image=RandImg::Mirror($image, $type, true, true);
			} else if($option['name']=='invert') {
				$image=RandImg::Invert($image, $type, true);
				if($this->format=="img") {
					$atributes["style"]="-webkit-filter: invert(100%);";
				} elseif ($this->format=="bg") {
					$css_rules["-webkit-filter"]="invert(100%)";
				}
			} else if($option['name']=='grayscale') {
				$image=RandImg::Grayscale($image, $type, true);
			} else if($option['name']=='crop') {
				$image=RandImg::Crop($image, $type, true);
				if($this->format=="image" || $this->format=="convert")
					$image=$image["image"];
				elseif ($this->format=="img") {
					$atributes["width"]=intval($image["width"]);
					$atributes["height"]=intval($image["height"]);
					$image=$image["image"];
				} elseif ($this->format=="bg") {
					$css_rules["width"]=intval($image["width"])."px";
					$css_rules["height"]=intval($image["height"])."px";
					$image=$image["image"];
				}
			} else if($option['name']=='fixresize') {
				$image=RandImg::Resize($image, $type, true, true);
				if($this->format=="image" || $this->format=="convert")
					$image=$image["image"];
				elseif ($this->format=="img") {
					$atributes["width"]=intval($image["width"]);
					$atributes["height"]=intval($image["height"]);
					$image=$image["image"];
				} elseif ($this->format=="bg") {
					$css_rules["width"]=intval($image["width"])."px";
					$css_rules["height"]=intval($image["height"])."px";
					$image=$image["image"];
				}
			} else if($option['name']=='resize') {
				$image=RandImg::Resize($image, $type, true, false);
				if($this->format=="image" || $this->format=="convert")
					$image=$image["image"];
				elseif ($this->format=="img") {
					$atributes["width"]=intval($image["owidth"]);
					$atributes["height"]=intval($image["oheight"]);
					$image=$image["image"];
				} elseif ($this->format=="bg") {
					$css_rules["width"]=intval($image["owidth"])."px";
					$css_rules["height"]=intval($image["oheight"])."px";
					$css_rules["background-size"]=$image["owidth"]."px ".$image["oheight"]."px";
					$image=$image["image"];
				}
			} else if($option['name']=='rotate') {
				$image=RandImg::Rotate($image, $type, true);
				if($this->format=="image" || $this->format=="convert")
					$image=$image["image"];
				elseif ($this->format=="img") {
					$atributes["width"]=intval($image["width"]);
					$atributes["height"]=intval($image["height"]);
					$image=$image["image"];
				} elseif ($this->format=="bg") {
					//$css_rules["transform"]="rotate(".(-1*$image["angle"])."deg)";
					//$css_rules["-webkit-transform"]="rotate(".(-1*$image["angle"])."deg)";
					//коррекцио оставим на будущее
					$image=$image["image"];
				}
			} else if($option['name']=='border') {
				$image=RandImg::Border($image, $type, true);
				if($this->format=="image" || $this->format=="convert")
					$image=$image["image"];
				elseif ($this->format=="img") {
					$atributes["width"]=intval($image["width"]);
					$atributes["height"]=intval($image["height"]);
					$image=$image["image"];
				} elseif ($this->format=="bg") {
					$css_rules["width"]=intval($image["width"])."px";
					$css_rules["height"]=intval($image["height"])."px";
					$image=$image["image"];
				}
			} else if($option['name']=='sharp')
				$image=RandImg::Sharp($image, $type, true);
			else if($option['name']=='blur')
				$image=RandImg::Blur($image, $type, true);
			else if($option['name']=='eskiz')
				$image=RandImg::Eskiz($image, $type, true);
			else if($option['name']=='pixelization')
				$image=RandImg::Pixelization($image, $type, true);
			else if($option['name']=='move') {
				$image=RandImg::Move($image, $type, true);
				if($this->format=="image" || $this->format=="img" || $this->format=="convert")
					$image=$image["image"];
				elseif ($this->format=="bg") {
					$css_rules["background-position-x"]="-".intval($image["move_x"])."px";
					$css_rules["background-position-y"]="-".intval($image["move_y"])."px";

					$image=$image["image"];
				}
			}
		}

		if($this->format=="convert") {
			return $image;
		}
		if($header && $this->format=="image") {
			header('Content-Type: image/'.$type);
			return $image;
		} 
		if($this->format=="img") {
			//сохраним картинку в папку кэша
			$filename=time().".".$type;
			$dir='http://'.$_SERVER['HTTP_HOST'].pathinfo($this->URL, PATHINFO_DIRNAME)."/".$this->cachePath;
			$dir=str_replace('\/', '/', $dir);
			file_put_contents($this->cachePath.$filename, $image);
			$html="<img src='".$dir.$filename."' ";
			foreach ($atributes as $atribute => $value) 
				$html.=$atribute."='".$value."' ";
			$html.=">";
			return $html;
		}
		if($this->format=="bg") {
			//сохраним картинку в папку кэша
			$filename=time().".".$type;
			$dir='http://'.$_SERVER['HTTP_HOST'].pathinfo($this->URL, PATHINFO_DIRNAME)."/".$this->cachePath;
			$dir=str_replace('\/', '/', $dir);
			file_put_contents($this->cachePath.$filename, $image);
			$html="background='".$dir.$filename."' style='";
			foreach ($css_rules as $rule => $value) 
				$html.=$rule.":".$value.";";
			$html.="'";
			return $html;
		}
		return $image;
	}

	//GET REQUESTS
	private function getSelfDiagnosticts() {
		header('Content-Type: application/json');
		$data=json_encode(RandomizerAPI::selfDiagnostics());
		return $data;
	}
	public static function selfDiagnostics($cache=true) {
		global $RandomizerAPI_selfDiagnostics;
		if($cache && $RandomizerAPI_selfDiagnostics!=null)
			return $RandomizerAPI_selfDiagnostics;

		$self=__FILE__;
		$path="./";
		$info=array(
			"is_writable"=>is_writable($self),
			"cache_is_writable"=>is_writable($path."cache"),
			"images_is_readable"=>is_readable($path."images"),
			"images_is_writable"=>is_writable($path."images"),
			"phpgd_installed"=>extension_loaded('gd') && function_exists('gd_info'),
			"php_version_is_55"=>version_compare(PHP_VERSION, '5.5.0', '>='),
			"php_version_is_54"=>version_compare(PHP_VERSION, '5.4.0', '>='),
			"php_version_is_53"=>version_compare(PHP_VERSION, '5.3.0', '>='),
		);

		$RandomizerAPI_selfDiagnostics=$info;
		return $info;
	}
}

?>