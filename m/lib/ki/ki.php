<?php

namespace m {

	class ki {

		static $queue = array();
		
		public $call;
		public $argv;
		public $persist;
	
		public function __construct($call,$argv,$persist) {
		
			if(!is_callable($call))
			throw new Exception('specified value not callable');
			
			if(!is_array($argv) || !is_object($argv))
			$argv = array($argv);
			
			$this->call = $call;
			$this->argv = $argv;
			$this->persist = $persist;			

			return;
		}
		
		public function exec() {
			call_user_func_array($this->call,$this->argv);
			return;
		}
		
		static function flow($key) {
			if(!array_key_exists($key,self::$queue)) return 0;
			
			$count = 0;
			foreach(self::$queue[$key] as $iter => $ki) {
				$ki->exec();
				if(!$ki->persist) unset(self::$queue[$key][$iter]);
				++$count;
			}
		
			return $count;
		}
		
		static function queue($key,$call,$argv=array(),$persist=false) {
			if(!array_key_exists($key,self::$queue))
			self::$queue[$key] = array();
			
			self::$queue[$key][] = new self($call,$argv,$persist);
			return;
		}
	
	}

}

?>