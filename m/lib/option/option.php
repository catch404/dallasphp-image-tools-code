<?php

namespace m {

	class option {
		
		static $storage = array();
		
		static function get($key) {
			if(array_key_exists($key,self::$storage)) return self::$storage[$key];
			else return null;				
		}
		
		static function define() {
			//. set the values requested, but only if they do not exist
			//. already in the storage array. this would mostly be prefered
			//. by libraries to set default values, since they will not know
			//. if they have loaded before or after the application config.

			$argv = func_get_args();
			if(!count($argv)) throw new Exception('expected [string,mixed] or [array(string=>mixed,...)]');
			
			if(is_array($argv[0])) {
				foreach($argv[0] as $key => $value) {
					if(!array_key_exists($key,self::$storage))
					self::$storage[$key] = $value;
				}
			} else {
				if(!array_key_exists($key,self::$storage))
				self::$storage[$argv[0]] = $argv[1];
			}
			
			return;
		}

		static function set() {
			//. set the values requested, overwriting any values that may have
			//. previously been set.
					
			$argv = func_get_args();
			if(!count($argv)) throw new Exception('expected [string,mixed] or [array(string=>mixed,...)]');
			
			if(is_array($argv[0])) {
				foreach($argv[0] as $key => $value) {
					self::$storage[$key] = $value;
				}
			} else {
				self::$storage[$argv[0]] = $argv[1];
			}
			
			return;
		}

	}

}

?>