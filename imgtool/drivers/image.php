<?php

namespace imgtool\drivers {
	abstract class image {

		// physical, meta, and metaphysical properties.
		public $filename;
		public $width,$height;

		// a resource holder.
		protected $img;

		public function __construct($filename) {

			// file exists?
			if(!$filename || !file_exists($filename))
			throw new \Exception('file not found');

			// try to load it
			$this->filename = $filename;
			$this->filesize = filesize($filename);
			$this->load();

			return;
		}

		////////////////////////////////////////////////////////////////////////
		// resource management /////////////////////////////////////////////////

		/* void free(void);
		 * destroy all image data in ram, and blank out the object properties.
		 */

		abstract public function free();

		////////////////////////////////////////////////////////////////////////
		// file handling ///////////////////////////////////////////////////////

		/* void load([string filename]);
		 * if filename provided, object is updated to open specified file, else
		 * it will open what was already set into the filename property.
		 */

		abstract public function load($infile);

		/* boolean save(string filename, int quality default 80);
		 * write a file to disk with the specified quality value.
		 */
		
		abstract public function save($outfile,$quality);

		////////////////////////////////////////////////////////////////////////
		// image manipulation (abstracts) //////////////////////////////////////

		// these methods must be implemented by the driver to provide support
		// for the rest of the suite.

		/* boolean crop(int x, int y, int width, int height);
		 * crop the image to the selection.
		 */

		abstract public function crop($x,$y,$w,$h);

		/* boolean resize(int width, int height)
		 * resize the image to these dimentions without a care in the world as
		 * to how stupid the image may look out of proportion.
		 */

		abstract public function resize($w,$h);

		////////////////////////////////////////////////////////////////////////
		// image manipulation (stacked) ////////////////////////////////////////

		// these methods are extrapolated by using the basic implementation to
		// do more neato things. they can be overwritten by a driver if needed
		// but there should be no reason to.

		/* void scale(int width, int height);
		 * scale an image down to fit inside the dimentions required, keeping
		 * the aspect ratio intact.
		 */

		public function scale($w,$h) {
			list($w,$h) = $this->calcScaleSize($w,$h);
			$this->resize($w,$h);
			return;
		}

		/* void thumbnail(int width, int height);
		 * scale an image and crop it to produce a perfect cut thumbnail of the
		 * size asked for.
		 */

		public function thumbnail($size) {

			// fill fit the image to the size we need
			if($this->width > $this->height) $this->scale(-1,$size);
			else $this->scale($size,-1);

			// then crop the excess off.
			$x = ($this->width / 2) - ($size / 2);
			$y = ($this->height / 2) - ($size / 2);
			$this->crop($x,$y,$size,$size);
			
			return;
		}

		////////////////////////////////////////////////////////////////////////
		// utilities ///////////////////////////////////////////////////////////

		/* array calcScaleSize(int width, int height);
		 * given max values, calculate the values we really want to use if we
		 * wish to keep the image aspect ratio in check. if one of the values is
		 * less than 0 (i.e. standard -1) then that value will be fill fit to
		 * by the other value.
		 */

		public function calcScaleSize($w,$h) {
			if($w < 0 && $h < 0)
			throw new \Exception('durr cant has both autofit');	

			if($w < 0 || $h < 0) {
				// handle fill fit
				if($w < 0) goto calc_scale_size_do_w;
				if($h < 0) goto calc_scale_size_do_h;
			} else {
				// handle best fit
				if($this->width > $this->height) goto calc_scale_size_do_h;
				else goto calc_scale_size_do_w;
			}

			calc_scale_size_do_h:
				$width = $w;
				$height = ceil(($w * $this->height) / $this->width);
				return array($width,$height);

			calc_scale_size_do_w:
				$height = $h;
				$width = ceil(($h * $this->width) / $this->height);
				return array($width,$height);	

		}

	}
}

?>