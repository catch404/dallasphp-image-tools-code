<?php

namespace imgtool {
	class image {
		protected $driver;

		public function __construct($filename,$drivername=null) {

			if(!$filename || !file_exists($filename))
			throw new \Exception("file ({$filename}) not found.");

			// choose a default driver if none was specified.
			if(!$drivername) {
				if(!defined('IMGTOOL_DRIVER'))
				throw new \Exception('no image driver selected.');

				else
				$drivername = IMGTOOL_DRIVER;
			}

			// see if the requested driver is available.
			$driverpath = sprintf('imgtool\drivers\%s\image',$drivername);
			if(!class_exists($driverpath,true))
			throw new \Exception("requested driver ({$driverpath}) not found");

			// go ahead and load up the driver.
			$this->driver = new $driverpath($filename);

			return;
		}

		/* if we asked for a property that does not exist, pass the query on to
		the image driver. */

		public function __get($key) {
			if(is_object($this->driver) and property_exists($this->driver,$key))
			return $this->driver->{$key};

			else return null;
		}

		/* if we wanted to perform an action that is not defined, pass the
		request on to the image driver. */

		public function __call($func,$argv) {
			if(is_object($this->driver) && method_exists($this->driver,$func))
			return call_user_func_array(array($this->driver,$func),$argv);

			else throw new \Exception("the method you requested ($func) does not exist");
		}

	}
}

?>