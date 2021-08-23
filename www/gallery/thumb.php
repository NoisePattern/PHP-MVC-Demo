<?php

class Thumb {

	private $path;
	private $path_thumb;
	private $width;
	private $height;
	private $mode;
	private $aspect;

	private $destWidth;
	private $destHeight;
	private $destOffsetX;
	private $destOffsetY;

	private $sourceWidth;
	private $sourceHeight;
	private $sourceOffsetX;
	private $sourceOffsetY;

	public function __construct(){
		$this->path = filter_input(INPUT_GET, 'path', FILTER_SANITIZE_STRING);
		$this->width = filter_input(INPUT_GET, 'width', FILTER_SANITIZE_NUMBER_INT);
		$this->height = filter_input(INPUT_GET, 'height', FILTER_SANITIZE_NUMBER_INT);
		$this->mode = filter_input(INPUT_GET, 'mode', FILTER_SANITIZE_STRING);
		if(empty($this->mode)) $this->mode = 'fit';
		$this->aspect = $this->width / $this->height;
		$parts = explode('/', $this->path);
		$this->path_thumb = $parts[0] . '/_thumb' . $this->mode .'w' . $this->width . 'h' . $this->width . '_' . $parts[1];
	}

	/**
	 * Create thumbnail image.
	*/
	public function create(){
		// Get the type of image.
		$type = exif_imagetype($this->path);
		// Load to memory according to type.
		if($type == IMAGETYPE_JPEG){
			$source = imagecreatefromjpeg($this->path);
		}
		else if($type == IMAGETYPE_PNG){
			$source = imagecreatefrompng($this->path);
		}
		else if($type == IMAGETYPE_GIF){
			$source = imagecreatefromgif($this->path);
		}
		else if($type == IMAGETYPE_WEBP){
			$source = imagecreatefromwebp($this->path);
		}
		// Get image size and aspect.
		$this->sourceWidth = imagesx($source);
		$this->sourceHeight = imagesy($source);
		$this->sourceAspect = $this->sourceWidth / $this->sourceHeight;

		// If mode is 'shrink', original is shrunk to fit inside given dimensions and resulting size becomes thumbnail size.
		if($this->mode == 'shrink'){
			$this->fitWithAspect($this->sourceAspect);
			$thumb = imagecreatetruecolor($this->destWidth, $this->destHeight);
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $this->destWidth, $this->destHeight, $this->sourceWidth, $this->sourceHeight);
		}
		// If mode is 'fit', thumbnail is set to given dimensions and original image is shrunk to fit inside it and centered.
		else if($this->mode == 'fit'){
			$this->fitWithAspect($this->sourceAspect);
			$thumb = imagecreatetruecolor($this->width, $this->height);
			imagefilledrectangle($thumb, 0, 0, $this->width - 1, $this->height - 1, imagecolorallocate($thumb, 245, 245, 245));
			imagecopyresampled($thumb, $source, $this->destOffsetX, $this->destOffsetY, 0, 0, $this->destWidth, $this->destHeight, $this->sourceWidth, $this->sourceHeight);
		}
		// If mode is 'clip', original is shrunk until width or height fits within given dimensions, and center of the original is clipped as thumbnail.
		else if($this->mode == 'clip'){
			$this->clipWithAspect();
			$thumb = imagecreatetruecolor($this->width, $this->height);
			imagecopyresampled($thumb, $source, 0, 0, $this->sourceOffsetX, $this->sourceOffsetY, $this->width, $this->height, $this->sourceWidth, $this->sourceHeight);
		}

		// Save image.
		imagejpeg($thumb, $this->path_thumb);
		imagedestroy($source);
		imagedestroy($thumb);
	}

	/**
	 * Fits original image do thumb, preserving aspect.
	 */
	private function fitWithAspect(){
		// If original's aspect ratio is larger than thumbnail's, it is wider and must be vertically centered.
		if($this->sourceAspect >= $this->aspect){
			$this->destWidth = $this->width;
			$this->destHeight = $this->height / $this->sourceAspect;
			$this->destOffsetX = 0;
			$this->destOffsetY = ($this->height - $this->destHeight) / 2;
		}
		// Otherwise source aspect ratio is less than thumbnail aspect ratio, it is taller and must be horizontally centered.
		else {
			$this->destWidth = $this->width / $this->sourceAspect;
			$this->destHeight = $this->height;
			$this->destOffsetX = ($this->width - $this->destWidth) / 2;
			$this->destOffsetY = 0;
		}
	}

	private function clipWithAspect(){
		// If original's aspect ratio is larger than thumbnail's, it is wider and clip area must be horizontally centered.
		if($this->sourceAspect >= $this->aspect){
			$new = $this->sourceHeight * $this->aspect;
			$this->sourceOffsetX = ($this->sourceWidth - $new) / 2;
			$this->sourceOffsetY = 0;
			$this->sourceWidth = $new;
		}
		else {
			$new = $this->sourceWidth * $this->aspect;
			$this->sourceOffsetX = 0;
			$this->sourceOffsetY = ($this->sourceHeight - $new) / 2;
			$this->sourceHeight = $new;
		}
	}

	public function show(){
		if(!file_exists($this->path_thumb)){
			$this->create();
		}
		header('Content-type: image/jpeg');
		header('Content-Length: ' . filesize($this->path_thumb));
		readfile($this->path_thumb);
	}
}

$image = new Thumb();
$image->show();

?>