<?php

namespace m {
	use \m as m;

	class platform {
		static $main;

		public $type = null;

		// these are actually output platforms that define how the actual
		// application intends to run.
		public $api = false;
		public $bin = false;
		public $cli = false;

		public function __construct() {
			$this->detect();
			return;
		}

		protected function detect() {

			// if the platform was forced to a certain type...
			if(defined('m\platform')) {
				switch(m\platform) {
					case 'api': { goto define_api; break; }
					case 'bin': { goto define_bin; break; }
					case 'cli': { goto define_cli; break; }
					default: { goto define_generic; }
				}
			}

			// else try to detect the platform.
			if(defined('STDIN')) goto define_cli;

			/*//
			//// ~ other auto detects like iphone hurr
			//*/
			
			// failing all auto detects assume the default.
			goto define_generic;

			define_api:
				$this->api = true;
				$this->type = 'api';
				return;

			define_bin:
				$this->bin = true;
				$this->type = 'bin';
				return;			

			define_cli:
				$this->cli = true;
				$this->type = 'cli';
				return;

			define_generic:
				$this->type = 'generic';
				return;

		}
	}

}

namespace {
	m\ki::queue('m-setup',function(){
		m\platform::$main = new m\platform;
		return;
	});
}

