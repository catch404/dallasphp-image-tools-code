<?php

namespace m {
	ini_set('memory_limit','1G');

	////////////////////////////////////////////////////////////////////////////
	// config ki for cli access ////////////////////////////////////////////////

	if(php_sapi_name() === 'cli') {

		ki::queue('m-ready',function(){

			$cli = new cli;
			if($cli->gd) m_define('IMGTOOL_DRIVER','gd');
			if($cli->imagick) m_define('IMGTOOL_DRIVER','imagick');

			if(!defined('IMGTOOL_DRIVER')) {
				m_printfln('STOP BEING BAD, TELL ME --gd OR --imagick NUBKAKE');
				$cli->shutdown();
			}

			return;
		});
		
	}

	////////////////////////////////////////////////////////////////////////////
	// config ki for web access ////////////////////////////////////////////////

	if(php_sapi_name() !== 'cli') {

		ki::queue('m-init',function(){
			m_require('-lsurface');
		});

		ki::queue('m-config',function(){
			option::set('m-surface-autorun',true);
		});

	}

}

?>