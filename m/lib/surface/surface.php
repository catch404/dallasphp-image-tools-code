<?php

namespace m {
	use \m as m;

	// depends on the platform library for max automation.
	m_require('-lplatform');

	class surface {

		static $main = null;

		public $theme;
		public $style;

		private $storage = array();
		private $capturing = false;

		public function __construct($input=null) {
			$opt = new m\object($input,array(
				'autocapture' => true,
				'theme'       => option::get('m-surface-theme'),
				'style'       => option::get('m-surface-style')
			));

			$this->theme = $opt->theme;
			$this->style = $opt->style;

			if($opt->autocapture)
			$this->startCapture();

			return;
		}

		public function __destruct() {
			if($this->capturing) {
				$this->render();
			}

			return;
		}

		public function startCapture() {
			if($this->capturing) return;

			ob_start();
			$this->capturing = true;

			return;
		}

		public function stopCapture($append=true) {
			if(!$this->capturing) return;

			$output = ob_get_clean();
			$this->capturing = false;

			if($append)
			$this->append('stdout',$output);
			
			return;
		}

		public function render() {
			$themepath = $this->getThemePath();
			if(!$themepath) throw new \Exception("theme {$this->theme} not found");

			//. get stdout.
			if($this->capturing)
			$this->stopCapture(true);

			//. run theme.
			m_require($themepath);

			return;
		}

		private function getThemePath() {
			$path = sprintf(
				'%s%sthemes%s%s%sdesign.phtml',
				m\root,
				DIRECTORY_SEPARATOR,
				DIRECTORY_SEPARATOR,
				$this->theme,
				DIRECTORY_SEPARATOR
			);

			if(file_exists($path)) return $path;
			else return false;
		}

		public function area($area) {
			$path = dirname($this->getThemePath()).'/area/'.$area.'.phtml';
			m_require($path);
		}

		/*// Template Storage Engine API
		  // these methods will allow you to store data in the surface
		  // instance for use later when rendering the resulting page.
		  // they allow for both static and public access. static access
		  // is for working with a system managed instance, where public
		  // access is for working on a specific instance.
		  //*/

		public function append($key,$value) {
			if(!isset($this))
				if(self::$main) return self::$main->append($key,$value);
				else return;
			
			//////// ~~~ ////////
					
			if(!array_key_exists($key,$this->storage)) $this->storage[$key] = $value;
			else $this->storage[$key] .= $value;
		
			return;
		}

		public function get($key) {
			if(!isset($this))
				if(self::$main) return self::$main->get($key);
				else return;

			//////// ~~~ ////////

			if(array_key_exists($key,$this->storage)) return $this->storage[$key];
			else return null;
		}

		public function show($key) {
			if(!isset($this))
				if(self::$main) return self::$main->show($key);
				else return;

			//////// ~~~ ////////

			if(array_key_exists($key,$this->storage))
			echo $this->storage[$key].PHP_EOL;
			return;
		}

		public function set($key,$value) {
			if(!isset($this))
				if(self::$main) return self::$main->set($key,$value);
				else return;

			//////// ~~~ ////////

			$this->storage[$key] = $value;
			return;
		}

	}
}

namespace {

	//. define default configuration options.
	m\ki::queue('m-config',function(){
		m\option::define(array(
			'm-surface-theme' => 'default',
			'm-surface-style' => 'default'
		));

		return;
	});

	//. start up the auto instance if enabled.
	m\ki::queue('m-setup',function(){

		// do not automatically capture on output platforms that should by the
		// very definition of their nature be unsurfaced.
		switch(m\platform::$main->type) {
			case 'api': { }
			case 'bin': { }
			case 'cli': { return; }
		}

		if(m\option::get('m-surface-autorun'))
			m\surface::$main = new m\surface(array(
				'autocapture' => true
			));

		return;
	});

}

?>