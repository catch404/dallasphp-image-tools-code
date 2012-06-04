<?php

/*//
Menagerie API Library.

Provides a universial response system for building JSON API
applications. Can be used standalone however using any of
the API Key methods will require the User and Database libraries
and the associated API Key database tables.

//*/

namespace m {
	use \m as m;
	
	class api {
	
		public $mode = 'json';

		public function __construct() {
		
			return;
		}
		
		public function shutdown() {
			$argv = func_get_args();
			$output = array(
				'errno'=>0,
				'errmsg'=>'',
				'mtime'=>0
			);
			
			switch(count($argv)) {
				case 1: { }
				case 2: { }
				case 3: {
				
					foreach($argv as $arg) {
						if(is_int($arg)) {
							$output['errno'] = $arg;
							continue;
						}
						
						if(is_string($arg)) {
							$output['errmsg'] = $arg;
							continue;
						}
						
						if(is_object($arg) || is_array($arg)) {
							$output = array_merge($output,(array)$arg);
							continue;
						}
					}
						
					break;
				}
				default: {
					break;
				}
			}
			
			$output['mtime'] = m_exec_time();
			echo json_encode($output);
			exit(0);
		}

	}
}

?>