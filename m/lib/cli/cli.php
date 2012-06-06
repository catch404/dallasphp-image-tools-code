<?php

namespace m {

	class cli {
	
		public $argv = array();
		public $args = array();
	
		public function __construct() {
			$this->parseArguments();
			
			return;
		}
		
		public function __get($key) {
			if(array_key_exists($key,$this->argv)) return $this->argv[$key];
			else return null;
		}
		
		public function exists($key) {
			return array_key_exists($key,$this->argv);
		}
		
		public function shutdown() {
			$argv = func_get_args();
			$errno = 0;
			$errmsg = '';
			
			// this version of the function is an experiment in
			// pseudopolymorphism. e.g., will anybody care that it does not
			// really care about the order of its arguments.
			// - cli->shutdown(void)
			// - cli->shutdown(int errno)
			// - cli->shutdown(string errmsg)
			// - cli->shutdown(int errno, string errmsg)
			// - cli->shutdown(string errmsg, int errno)
			
			switch(count($argv)) {
				case 1: { }
				case 2: {
				
					foreach($argv as $arg)
						if(is_int($arg)) $errno = $arg;
						else if(is_string($arg)) $errmsg = $arg;
						
					break;
				}
				default: {
					// derp.
					break;
				}
			}
			
			//. output message.
			if($errmsg) fwrite(
				($errno)?(STDERR):(STDOUT),
				$errmsg.PHP_EOL
			);
			
			//. goodbye.
			exit($errno);
		}
		
		protected function parseArguments() {
			if(!array_key_exists('argv',$_SERVER)) return;
			if(!is_array($_SERVER['argv'])) return;
			
			$input = $_SERVER['argv'];
			unset($input[0]);

			foreach($input as $argv) {
				if(preg_match('/--([a-z0-9-]+)(?:(=)(.*))?/i',$argv,$match)) {
					if($match[2] == '=') $this->argv[$match[1]] = $match[3];
					else $this->argv[$match[1]] = true;
				} else {
					$this->args[] = $argv;
				}
			}
			
			return;
		}
	
	}

}

?>