<?php

namespace m\request {
	use \m as m;

	class input {

		protected $var = '';
		protected $which;
		protected $opt;

		public function __construct($which,$opt=null) {
			$this->opt = new m\object($opt,array(
				'trim' => false
			));

			if(!is_array($which)) {
				$this->which = strtolower($which);
				switch($this->which) {
					case 'cookie':  { $this->var =& $_COOKIE;  break; }
					case 'files':   { $this->var =& $_FILES;   break; }
					case 'get':     { $this->var =& $_GET;     break; }
					case 'global':  { $this->var =& $_GLOBALS; break; }
					case 'post':    { $this->var =& $_POST;    break; }
					case 'request': { $this->var =& $_REQUEST; break; }
					case 'server':  { $this->var =& $_SERVER;  break; }
					case 'session': { $this->var =& $_SESSION; break; }
					default:        { $this->var =& $_REQUEST; break; }
				}
			} else {
				$this->which = 'custom';
				$this->var = $which;
			}

			return;
		}

		public function __toString() {
			return json_encode($this->var);
		}

		public function __set($key,$value) {
			$return = null;

			switch($this->which) {
				case 'custom':  { }
				case 'global':  { }
				case 'session': { $this->var[$key] = $return = $value; break; }
			}

			return $return;
		}

		public function __unset($key) {

			switch($this->which) {
				case 'custom':  { }
				case 'global':  { }
				case 'session': { if($this->exists($key)) unset($this->var[$key]); break; }
			}

			return;
		}

		public function __get($key) {
			if($this->exists($key)) return $this->filter($this->var[$key]);
			else return null;
		}

		public function exists($key) {
			if(is_array($this->var) AND array_key_exists($key,$this->var)) return true;
			else	return false;
		}
		
		public function itemize() {
			$list = func_get_args();
			$output = array();
			
			foreach($list as $key)
				if(!is_array($key))
					$output[$key] = $this->__get($key);
				else foreach($key as $kkey)
					$output[$kkey] = $this->__get($kkey);
	
			return $output;
		}

		protected function filter($input) {
			if($this->opt->trim) $input = trim($input);
			return $input;
		}
	}

}

?>