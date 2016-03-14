<?php
class Img_utils{
	# public var (class default values)
	var $config = array(
		'max_width'   => 80,
		'max_height'  => 80,
		'scale_by'    => 'auto',  // posible values: width, height, auto
		);
	#private source image var...
	var $img_source    = '';
	var $img_handler   = false;
	var $img_width     = null;
	var $img_height    = null;
	#private thumbs var...
	var $thumb_handler = false;
	var $thumb_width   = null;
	var $thumb_height  = null;
	var $thumb_x       = 0;
	var $thumb_y       = 0;
	
	/**
	 * Open a source image file and calculate thumbmail size
	 *
	 * @param string $file
	 * @return boolean
	 */
	function open($file){
		//intitialize private var...
		$this->img_source    = $file;
		$this->img_handler   = false;
		$this->thumb_handler = false;
		$this->thumb_width   = null;
		$this->thumb_height  = null;
		$this->thumb_x       = 0;
		$this->thumb_y       = 0;
		//initialize img_handler
		$arr_pathinfo = pathinfo($file);
		$extension = strtolower($arr_pathinfo['extension']);
		if($extension == 'jpg' || $extension == 'jpeg')
			$this->img_handler = imagecreatefromjpeg($file);
		elseif($extension == 'png')
			$this->img_handler = imagecreatefrompng($file);
		elseif($extension == 'gif')
			$this->img_handler = imagecreatefromgif($file);
		elseif($extension == 'bmp')
			$this->img_handler = imagecreatefromwbmp($file);
		if($this->img_handler){
			//image original size
			list($this->img_width, $this->img_height) = getimagesize($this->img_source);
			return true;
		}
		return false;
	}

	/**
	 * Save the thumbmail or image
	 *
	 * @param string $file
	 * @param string $ext
	 * @return booblean
	 */
	function save($file, $ext = 'png'){
		ini_set ( "memory_limit", "48M"); 
		if(is_null($this->thumb_width) or is_null($this->thumb_height)){
			$this->thumb_width  = $this->img_width;
			$this->thumb_height = $this->img_height;
		}
		$this->thumb_handler = imagecreatetruecolor($this->thumb_width, $this->thumb_height);
		@imagecopyresampled($this->thumb_handler, $this->img_handler, 0 , 0, $this->thumb_x, $this->thumb_y, $this->thumb_width, $this->thumb_height, $this->img_width, $this->img_height);
		@unlink($file);
		switch ($ext) {
			case 'bmp':
				imagewbmp ($this->thumb_handler, "$file.bmp");
				break;
			case 'gif':
				imagegif ($this->thumb_handler, "$file.gif");
				break;
			case 'jpg':
			case 'jpeg':
				imagejpeg ($this->thumb_handler, "$file.jpg", 80);
				break;
			case 'png':
			default:
				imagepng ($this->thumb_handler, "$file.png", 8);
				break;
		}
		return imagedestroy($this->thumb_handler);
	}
	
	/**
	 * Create a thumbmail with a portion (subarea) of the source image
	 *
	 * @param string $file
	 * @param int $x thumb x position
	 * @param int $y thumb y position
	 * @param int $w thumb width
	 * @param int $h thumb height
	 */

	function crop($x, $y, $w, $h){
		$this->thumb_x    = intval($x);
		$this->thumb_y    = intval($y);
		$this->img_width  = intval($w);
		$this->img_height = intval($h);
	}
	
	/**
	 * Create thumbmail...
	 *
	 * @param int $max_width
	 * @param int $max_height
	 * @param string  $scale_by
	 */
	function resize($max_width, $max_height, $scale_by = 'auto'){
		$this->config['max_width']  = intval($max_width);
		$this->config['max_height'] = intval($max_height);
		$this->config['scale_by']   = ($scale_by == 'height')? 'height' : ($scale_by == 'width')? 'width' : 'auto';
		$this->auto_resize();
	}
	
	/**
	 * Auto resize the thumb using the default 'scale_by' value of class constructor...
	 *
	 */
	function auto_resize(){
		switch ($this->config['scale_by']) {
			case 'width':
				$this->resize_by_width($this->config['max_width']);
				break;
			case 'height':
				$this->resize_by_height($this->config['max_height']);
				break;
			case 'auto':
			default:
				//is it landscape or portrait image?
				if($this->img_width > $this->img_height){
					//it is landscape, resize by with
					$this->resize_by_width($this->config['max_width']);
					if($this->thumb_height > $this->config['max_height'])
						$this->resize_by_height($this->config['max_height']);
				}elseif($this->img_width < $this->img_height){
					//it is portrait, resize by height
					$this->resize_by_height($this->config['max_height']);
					if($this->thumb_width > $this->config['max_width'])
						$this->resize_by_width($this->config['max_width']);
				}else{
					//is a square
					$this->thumb_width  = $this->config['max_width'];
					$this->thumb_height = $this->config['max_height'];
				}
				break;
		}
	}
	
	/**
	 * Rezise thumb by height
	 *
	 * @param int $height
	 */
	function resize_by_height($height){
		$this->thumb_height = $height;
		$ratio              = $height / $this->img_width;
		$this->thumb_width  = intval($this->img_height * $ratio);
	}
	/**
	 * Rezise thumb by width
	 *
	 * @param int $width
	 */
	function resize_by_width($width){
		$this->thumb_width  = $width;
		$ratio              = $width / $this->img_width;
		$this->thumb_height = intval($this->img_height * $ratio);
	}

}
?>