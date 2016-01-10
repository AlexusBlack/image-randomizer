<?php
/**
* FileManager - дает интерфейс для перемещения по папкам и выбора файлов
*/
class FileManager
{
	private $basePath;
	private $currentPath;
	private $lastError;
	function __construct($basePath=null)
	{
		if($basePath===null)
			$this->basePath="./";
		$this->currentPath="";
		$this->lastError=null;
	}
	function setCurrentPath($path) {
		$this->currentPath=$path;
	}
	function getCurrentPath() {
		return $this->currentPath;
	}
	function cd($path) {
		$this->currentPath.=$path;
	}
	function getFiles() {
		$path=$this->basePath.$this->currentPath;
		$files=array();

		if ($handle = opendir($path)) {

		    /* This is the correct way to loop over the directory. */
		    while (false !== ($entry = readdir($handle))) {
		    	if(in_array($entry, array(".",".."))) continue;
		        $files[]=array(
		        	"name"=>$entry,
		        	"is_dir"=>is_dir($path.$entry),
		        	"is_file"=>is_file($path.$entry),
		        	"is_readable"=>is_readable($path.$entry),
		        	"is_writable"=>is_writable($path.$entry)
		        );
		    }

		    closedir($handle);
		    return $files;
		} else {
			$this->lastError="Can't open dir ".$path;
			return false;
		}
	}
	function getLastError() {
		return $this->lastError;
	}
}
?>