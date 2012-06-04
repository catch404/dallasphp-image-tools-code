<?php

namespace m\request {
	use \m as m;

	/* TODO

	 * Add redirect types (301, 302, etc)

	 * Add URL completion. As in, by the spec if we want to go to /place/ we
	need to really say http://domain/place/ to be by the spec. browsers
	are smarter than the spec though so i just laid this class down to
	get rolling. The URL completion will probably be done by another function
	in a utility library that can be reused here.

	 */

	class redirect {

		public $location;

		public function __construct($location = null) {
			$this->location = $location;
			$this->parse();
			return;
		}

		public function go() {
			header("Location: {$this->location}");
			exit(0); // *wave*
		}

		protected function parse() {

			// TODO - instead of redirecting to the root, they should
			// redirect to the set site root which may not be the actual
			// domain root.

			if(!$this->location || !is_string($this->location))
				goto DoHome;

			switch($this->location) {
				case 'm://home': {
					goto DoHome;
					break;
				}

				case 'm://back':
				case 'm://referer': 
				case 'm://referrer': {
					goto DoBack;
					break;
				}

				case 'm://current':
				case 'm://refresh': 
				case 'm://reload':
				case 'm://self': {
					goto DoSelf;
					break;
				}

				default: {
					return;
				}
			}

			DoHome:
				$this->location = '/';
				return;

			DoBack:
				if(array_key_exists('HTTP_REFERER',$_SERVER)) {
					$this->location = $_SERVER['HTTP_REFERER'];
				} else { goto DoHome; }

			DoSelf:
				if(array_key_exists('REQUEST_URI',$_SERVER)) {
					$this->location = $_SERVER['REQUEST_URI'];
				} else { goto DoHome; }

		}

	}
}

?>