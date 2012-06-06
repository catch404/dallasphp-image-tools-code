<?php

/* driver implementation.
 * see imgtool\drivers\image for method signature documentation.
 */

namespace imgtool\drivers\gd {
	use \imgtool as imgtool;

	class image extends imgtool\drivers\image {

		////////////////////////////////////////////////////////////////////////
		// driver required methods /////////////////////////////////////////////

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

		////////////////////////////////////////////////////////////////////////
		// visual filters //////////////////////////////////////////////////////

		public function alpha($percent) {
			$alpha = floor((127 * $percent) / 100);

			imagealphablending($this->img,false);
			for($y = 1; $y <= $this->height; $y++) {
				for($x = 1; $x <= $this->width; $x++) {
					$rgba = imagecolorsforindex($this->img,imagecolorat($this->img,$x,$y));
					$color = imagecolorallocatealpha(
						$this->img,
						$rgba['red'],
						$rgba['green'],
						$rgba['blue'],
						((($rgba['alpha'] + $alpha) < 127)?(($rgba['alpha'] + $alpha)):(127))
					);
					imagesetpixel($this->img,$x,$y,$color);
				}
			}
			imagealphablending($this->img,true);

			return;
		}

		public function desaturate() {
			imagefilter($this->img,IMG_FILTER_GRAYSCALE);
			return;
		}

		public function holga() {

			$shadow = imagecreatetruecolor($this->width,$this->height);
			imageantialias($shadow,true);
			imagealphablending($shadow,true);
			imagesavealpha($shadow,true);
			imagesetthickness($shadow,2);

			$clear = imagecolorallocatealpha($shadow,0,0,0,127);

			imagefill($shadow,0,0,$clear);

			$x = floor($this->width / 2);
			$y = floor($this->height / 2);
			$ringcount = (($x + $y) / 2);

			for($a = 0; $a < $ringcount; $a++) {
				$ringcolor = imagecolorallocatealpha(
					$shadow,
					0,0,0,
					(127 - floor(40 * ($a / $ringcount)))
				);

				imagearc(
					$shadow,
					$x,$y,
					($this->width+$a), ($this->height+$a),
					0, 359.9,
					$ringcolor
				);

				imageellipse(
					$shadow,
					$x,$y,
					($this->width+$a), ($this->height+$a),
					$ringcolor
				);

			}

			imagefilter($this->img,IMG_FILTER_CONTRAST,-20);
			imagefilter($this->img,IMG_FILTER_BRIGHTNESS,50);

			imagecopyresampled(
				$this->img,
				$shadow,
				0,0,
				0,0,
				$this->width,$this->height,
				$this->width,$this->height
			);

			imagefilter($this->img,IMG_FILTER_SMOOTH,5);

			imagedestroy($shadow);
		}

		public function sepia() {
			imagefilter($this->img,IMG_FILTER_GRAYSCALE);
			imagefilter($this->img,IMG_FILTER_COLORIZE,90,40,3);
			return;
		}

		public function text($x,$y,$font,$size,$color,$text) {
			preg_match('/#(.{2})(.{2})(.{2})(.{2})?/',$color,$hex);
			if(!array_key_exists(4,$hex)) $hex[4] = 'FF';
			$color = imagecolorallocatealpha(
				$this->img,
				hexdec($hex[1]),hexdec($hex[2]),hexdec($hex[3]),
				127 - floor((127 * hexdec($hex[4])) / 255)
			);

			$fontfile = sprintf(
				'%s/share/fonts/%s.ttf',
				dirname(dirname(dirname(__FILE__))),
				$font
			);

			imagettftext(
				$this->img,
				$size, 0,
				$x, ($y+$size),
				$color,
				$fontfile,
				$text
			);

			return;
		}

		public function watermark($filename) {
			$overlay = new imgtool\image($filename);
			$overlay->desaturate();
			$overlay->alpha(70);

			imagecopyresampled(
				$this->img,
				$overlay->img,
				($this->width-$overlay->width),($this->height-$overlay->height),
				0,0,
				$overlay->width,$overlay->height,
				$overlay->width,$overlay->height
			);

			return;
		}

	}
	
}

?>