<?php

/* jake punched right in the iterator */

class MediaDirectoryIterator extends DirectoryIterator {
	public function __construct() {
		parent::__construct(sprintf('%s/media',m\webroot));
	}

	public function getMediaItem() {
		return new MediaItem($this->getFilename());
	}
}

class MediaIterator extends FilterIterator {
	public function __construct() {
		parent::__construct(new MediaDirectoryIterator);
		return;
	}

	public function accept() {
		if(preg_match('/(\.jpe?g|\.png)$/i',$this->getFilename())) return true;
		else return false;
	}
}

/* media item for file manipulation */

class MediaItem  {

	public $filename, $filepath;

	public function __construct($filename) {
		$this->filename = $filename;
		$this->filepath = sprintf('%s/media/%s',m\webroot,$filename);

		if(!file_exists($this->filepath))
		throw new \Exception('file not found');

		return;
	}

	public function getThumbnailURI() {
		$thumbfile = sprintf(
			'%s/media/thumbnails/%s',
			m\webroot,
			$this->filename
		);
	
		if(!file_exists($thumbfile)) {
			$imgtool = new imgtool\image($this->filepath);
			$imgtool->thumbnail(200,200);
			$imgtool->save($thumbfile);
			$imgtool->free();
		}
		
		return sprintf('/media/thumbnails/%s',basename($this->filename));
	}

	public function getViewURI() {
		$viewfile = sprintf(
			'%s/media/views/%s',
			m\webroot,
			$this->filename
		);
	
		if(!file_exists($viewfile)) {
			$imgtool = new imgtool\image($this->filepath);
			$imgtool->scale(680,-1);
			$imgtool->save($viewfile);
			$imgtool->free();
		}
		
		return sprintf('/media/views/%s',basename($this->filename));
	}

}

?>