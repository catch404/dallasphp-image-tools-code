<?php

error_reporting(E_ALL & ~E_STRICT);
// surface library is doing a public/static trick that has been depreciated in
// 5.4 - while i ponder a replacement im shutting off the strict messages.

define('m\root',dirname(__FILE__));
define('m\timeinit',gettimeofday(true));

/*// core library
  // the lib that goes right round like a record baby right round
  // round round.
  //*/

require(sprintf('%s/application.so.php',m\root));


/*// autoload step one
  // allow subdirectories to be used for the class name to help keep
  // the project folder organized.
  //*/

set_include_path(sprintf(
	// adding the m directory to the include search.
	'%s%s%s',
	get_include_path(),
	PATH_SEPARATOR,
	dirname(dirname(__FILE__))
));

spl_autoload_register('m_autoloader');
function m_autoloader($classname){

	// custom menagerie autoloader.

	// example: requesting class m\database.
	// 1) if exists m/lib/database/database.php (Found)
	// 2) if exists m/lib/database.php          (Secondary)
	// 3) if exists m/database.php              (PSR-0 Fallback)

	// example: requesting class m\database\driver\mysqli.
	// 1) if exists m/lib/database/drivers/mysqli/mysqli.php (Not Found)
	// 2) if exists m/lib/database/drivers/mysqli.php        (Found)
	// 3) if exists m/database/drivers/mysqli.php            (PSR-0 Fallback)

	// had the mysqli driver been made up of multiple files, then format
	// #1 would have been the preferred directory structure, but because
	// it is able to add all its support via one file, the second format
	// is preferred in this case.

	$classname = str_replace('\\','/',$classname);
	// ^^^^^ https://bugs.php.net/bug.php?id=60996

	// convert the requested classname m/library
	// into file path m/lib/library/library.php
	$filepath = sprintf(
		'%s%s%s',
		dirname(m\root),
		DIRECTORY_SEPARATOR,
		preg_replace('/^m\//','m/lib/',sprintf(
			'%s/%s.php',
			$classname,
			basename($classname)
		))
	);

	// no m/lib/library/library.php?
	// try m/lib/library.php
	if(!file_exists($filepath))
	$filepath = sprintf('%s.php',dirname($filepath));

	// no m/lib/library.php? sadface.
	if(!file_exists($filepath)) return false;
	else {
		require($filepath);

		if(defined('m\ready')) {
			m\ki::flow('m-config');
			m\ki::flow('m-setup');
			m\ki::flow('m-ready');
		}

		return true;
	}

}


/*// autoload step two
  // if the custom autoloader fails allow php to continue on and
  // attempt the default autoloader that is PSR-0 compliant. may
  // your deity of choice have mercy on you.
  //*/

spl_autoload_register(function($classname){
	spl_autoload($classname);

	if(defined('m\ready') and class_exists($classname)) {
		m\ki::flow('m-config');
		m\ki::flow('m-setup');
		m\ki::flow('m-ready');
	}

	return;
});


/*// load configuration
  // the application.conf.php file stores all the application specific
  // options and settings.
  //*/

$configfile = sprintf('%s/application.conf.php',m\root);
if(file_exists($configfile))
require($configfile);

/*// when ready...
  // some things to do once the framework decides it is ready to
  // proceed with the rest of the application.
  //*/

m\ki::queue('m-ready',function(){
	define('m\ready',gettimeofday(true));
	return;
});

/*// init train
  // flow some ki to allow libraries to setup as they need.
  //*/

// init. things defined in an m-init ki block should be designed for
// setting or loading values core to the operation of the framework,
// and are designed to change how it behaves from the ground up.
m\ki::flow('m-init');

// config. things defined in an m-config ki block are for setting
// values that could be used at any point during an application, but
// primarily get used by libraries when they...
m\ki::flow('m-config');

// setup. things defined in an m-setup ki block are for initializing
// states and setting up any instances that need to be done for the
// rest of the application.
m\ki::flow('m-setup');

// ready. once the framework is ready this ki flows, setting any last
// late minute values before handing the process over to the
// application using the framework.
m\ki::flow('m-ready');

?>