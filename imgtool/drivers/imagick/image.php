<?php

/* driver implementation.
 * see imgtool\drivers\image for method signature documentation.
 */

namespace imgtool\drivers\imagick {
	use \imgtool as imgtool;

	class image extends imgtool\drivers\image {

		public function crop($x,$y,$w,$h) {
			$ok = $this->img->cropImage($w,$h,$x,$y);

			if($ok) {
				$this->width = $w;
				$this->height = $h;
				return true;
			} else {
				return false;
			}
		}

		public function free() {
			$this->img->destroy();
			foreach($this as $prop => $value) $this->{$prop} = null;
			return;
		}

		public function load($infile=null) {

			if($infile) {
				if(!file_exists($infile)) throw new \Exception('file not found');
				else $this->filename = $infile;
			}

			$this->img = new \Imagick($this->filename);

			$this->width = $this->img->getImageWidth();
			$this->height = $this->img->getImageHeight();

			return;
		}

		public function resize($w,$h) {
			$ok  = $this->img->resizeImage($w,$h,\Imagick::FILTER_LANCZOS,1);

			if($ok) {
				$this->width = $w;
				$this->height = $h;
				return true;
			} else {
				return false;
			}
		}

		public function save($outfile,$quality=80) {

			// set quality on filetypes that use it.
			if(preg_match('/\.jpe?g$/i',$outfile))
			$this->img->setCompressionQuality($quality);

			return $this->img->writeImage($outfile);		
		}

	}
	
}

?>