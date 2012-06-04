<?php

namespace m {
	ki::queue('m-init',function(){
		ini_set('memory_limit','1G');
	});

	ki::queue('m-ready',function(){

		// imgtool ready check
		// make sure we said to use gd or imagick.

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

?>