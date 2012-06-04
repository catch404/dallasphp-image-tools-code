<?php

namespace m {

	class object {
		static $PropertyMap = array();
		
		public function __construct($input=null,$defaults=null) {
			
			//. initialize the object with the input data, running the input
			//. by the property map first if need be.
			if(is_array($input)) $input = (object)$input;
			if(is_object($input)) {
				if(count(static::$PropertyMap))	
				$this->inputApplyMap($input);				

				$this->inputProperties($input,true);
			}
			
			//. set any default properties that may have been missing from
			//. the original input data.
			if(is_array($defaults)) $defaults = (object)$defaults;
			if(is_object($defaults)) {
				$this->inputProperties($defaults,false);
			}
			
			if(method_exists($this,'__ready')) $this->__ready();
				
			return;
		}

		private function inputApplyMap($input) {
			//. given a source object, rename properties as we want. this is
			//. mostly used for taking a database dump and converting it into
			//. a readable object.
				
			foreach(static::$PropertyMap as $old => $new) {
				if(property_exists($input,$old)) {
					$input->{$new} = $input->{$old};
					unset($input->{$old});
				}
			}
			
			return;		
		}
		
		private function inputProperties($source,$overwrite=false) {
			//. given an source object, copy the source properties into this
			//. object instance. by default it will not overwrite properties
			//. that already exist.
		
			foreach($source as $property => $value) {
				if(!property_exists($this,$property) || $overwrite) {
					$this->{$property} = $value;
				}
			}
		
			return;
		}
		
	}

}

?>