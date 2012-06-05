<?php

/* driver implementation.
 * see imgtool\drivers\image for method signature documentation.
 */

namespace imgtool\drivers\gd {
	use \imgtool as imgtool;

	class image extends imgtool\drivers\image {

		public function crop($x,$y,$w,$h) {
			$new = imagecreatetruecolor($w,$h);

			$ok = imagecopyresampled(
				$new,       // destination
				$this->img, // source
				0, 0,       // destination cords
				$x, $y,     // source coords
				$w, $h,     // destination size
				$w, $h      // source size
			);

			if($ok) {
				// update properties.
				$this->width = $w;
				$this->height = $h;

				// resource swap.
				imagedestroy($this->img);
				$this->img = $new;

				return true;
			} else {
				return false;
			}
		}

		public function free() {
			if($this->img) imagedestroy($this->img);
			foreach($this as $prop => $value) $this->{$prop} = null;
			return;
		}

		public function load($infile=null) {

			if($infile) {
				if(!file_exists($infile)) throw new \Exception('file not found');
				else $this->filename = $infile;
			}

			// let gd decide if its an image it can handle.
			$info = getimagesize($this->filename);

			if(!is_array($info))
			throw new \Exception('getimagesize failed to test image');

			// decide how to open it.
			// for the sake of brevity i am only going to care about jpeg and
			// png files. anything else and the terrorists win anyway.
			switch($info['mime']) {
				case 'image/jpeg':
					$this->img = imagecreatefromjpeg($this->filename);
					break;
			
				case 'image/png':
					$this->img = imagecreatefrompng($this->filename);
					break;
		
				default:
					throw new \Exception('come back when you have a real file format');
			}

			// make sure it sort of loaded.
			if(!$this->img) throw new \Exception('the image did not appear to load');

			// take note of the properties i decided to care about now that we
			// are fairly sure we have an image sitting in RAM.
			$this->width = $info[0];
			$this->height = $info[1];

			imagealphablending($this->img,true);
			imageantialias($this->img,true);
			imagesavealpha($this->img,true);

			return;
		}

		public function resize($w,$h) {
			$new = imagecreatetruecolor($w,$h);

			$ok = imagecopyresampled(
				$new,       // destination
				$this->img, // source
				0, 0,       // destination coords
				0, 0,       // source coords
				$w, $h,     // destination size
				$this->width, $this->height // source size
			);

			if($ok) {
				// update properties.
				$this->width = $w;
				$this->height = $h;

				// resource swap.
				imagedestroy($this->img);
				$this->img = $new;

				return true;
			} else {
				return false;
			}
		}

		public function save($outfile,$quality=80) {

			if(preg_match('/\.jpe?g$/i',$outfile)) {
				// jpeg quality is 1 to 100
				return imagejpeg($this->img,$outfile,$quality);
			}

			if(preg_match('/\.png$/i',$outfile)) {
				// png quality is actually compression level 0 to 9
				$quality = floor($quality/10)-1;
				return imagepng($this->img,$outfile,$quality);
			}	

			return false;		
		}

	}
	
}

?>