<?php

/* driver implementation.
 * see imgtool\drivers\image for method signature documentation.
 */

namespace imgtool\drivers\imagick {
	use \imgtool as imgtool;

	class image extends imgtool\drivers\image {

		////////////////////////////////////////////////////////////////////////
		// driver required methods /////////////////////////////////////////////

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
			$this->img->setImageCompressionQuality($quality);

			return $this->img->writeImage($outfile);		
		}

		////////////////////////////////////////////////////////////////////////
		// visual filters //////////////////////////////////////////////////////

		public function alpha($percent) {
			$alpha = $percent / 100;

			/* we couldn't use setImageOpacity() because it lol all over the
			already transparent pixels (as it technically should) */

			$iter = $this->img->getPixelIterator();
			foreach($iter as $rowdata) {
				foreach($rowdata as $pixel) {
					$current = $pixel->getColorValue(\Imagick::COLOR_ALPHA);
					$pixel->setColorValue(
						\Imagick::COLOR_ALPHA,
						(($current - $alpha > 0)?($current - $alpha):(0))
					);
					$iter->syncIterator();
				}
			}

			return;
		}

		public function desaturate() {
			$this->img->modulateImage(100,0,100);
			return;
		}

		public function holga() {
			$width = $this->width;
			$height = $this->height;
			$vigx = $width / 4;
			$vigy = $height / 4;


			// adjust the image.
			$quant = $this->img->getQuantumRange()['quantumRangeLong'];
			$this->img->levelImage(
				floor((23 * $quant) / 255),
				2.35,
				floor((213 * $quant) / 255)
			);

			$this->img->contrastImage(-40);
			$this->img->blurImage(1,1);
		
			// fading out the edges.
			$this->img->setImageBackgroundColor('black');
			$this->img->vignetteImage(
				($vigx*1.1),
				($vigx*0.8),
				($vigx/5)*-1,
				($vity/5)*-1
			);

			return;
		}

		public function sepia() {
			$this->img->sepiaToneImage(80);
			return;
		}

		public function text($x,$y,$font,$size,$color,$text) {
			$color = new \ImagickPixel($color);
			$fontfile = sprintf(
				'%s/share/fonts/%s.ttf',
				dirname(dirname(dirname(__FILE__))),
				$font
			);

			$draw = new \ImagickDraw;
			$draw->setFont($fontfile);
			$draw->setFontSize($size);
			$draw->setFillColor($color);
			$draw->setStrokeColor(new \ImagickPixel('#000000'));
			$draw->setStrokeAntialias(true);
			$draw->setStrokeWidth(4);

			$this->img->annotateImage($draw,$x,($y+$size),0,$text);

			return;
		}

		public function watermark($filename) {
			$overlay = new imgtool\image($filename);
			$overlay->desaturate();
			$overlay->alpha(70);

			$this->img->compositeImage(
				$overlay->img,
				\Imagick::COMPOSITE_DEFAULT,
				($this->width-$overlay->width),($this->height-$overlay->height)
			);

			return;
		}

		////////////////////////////////////////////////////////////////////////
		// plotting ////////////////////////////////////////////////////////////

		public function dot($x,$y,$diam,$color) {
			$x -= floor($diam/2);
			$y -= floor($diam/2);

			$draw = new \ImagickDraw;
			$draw->setFillColor(new \ImagickPixel($color));
			$draw->ellipse($x,$y,($diam/2),($diam/2),0,360);

			$this->img->drawImage($draw);

			return;
		}

	}
	
}

?>