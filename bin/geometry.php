<?php 

if(php_sapi_name() !== 'cli') exit(1);
require(sprintf(
	'%s/m/application.php',
	dirname(dirname(__FILE__))
));

function lol_tag_filename($file,$tag) {
	return preg_replace('/\.([^\.]+)$/',"-{$tag}.\\1",$file);
}

function lol_crop($string,$file,$overwrite) {
	preg_match('/(\d+)x(\d+)\+(\d+)x(\d+)/',$string,$crop);

	m_printfln(
		'>> cropping %s at %dx%d size %dx%d...',
		basename($file),
		$crop[1], $crop[2],
		$crop[3], $crop[4]
	);

	$tool = new imgtool\image($file);
	$tool->crop($crop[1],$crop[2],$crop[3],$crop[4]);

	if($overwrite) $tool->save($file);
	else $tool->save(lol_tag_filename($file,'crop'));
}

function lol_resize($string,$file,$overwrite) {
	preg_match('/(\d+)x(\d+)/',$string,$size);

	m_printfln(
		'>> resizing %s to %dx%d...',
		basename($file),
		$size[1], $size[2]
	);

	$tool = new imgtool\image($file);
	$tool->resize($size[1],$size[2]);

	if($overwrite) $tool->save($file);
	else $tool->save(lol_tag_filename($file,'resize'));
}

function lol_scale($string,$file,$overwrite) {
	preg_match('/(\d+)x(\d+)/',$string,$size);

	m_printfln(
		'>> scaling %s to %dx%d...',
		basename($file),
		$size[1], $size[2]
	);

	$tool = new imgtool\image($file);
	$tool->scale($size[1],$size[2]);

	if($overwrite) $tool->save($file);
	else $tool->save(lol_tag_filename($file,'scale'));
}

function lol_thumbnail($string,$file,$overwrite) {
	m_printfln(
		'>> thumbnailing %s to %d...',
		basename($file),
		$string
	);

	$tool = new imgtool\image($file);
	$tool->thumbnail((int)$string);

	if($overwrite) $tool->save($file);
	else $tool->save(lol_tag_filename($file,'thumb'));
}

$cli = new m\cli;
if(!count($cli->args)) $cli->shutdown('tell me what files eh');

foreach($cli->args as $arg) {
	$filelist = glob($arg);
	foreach($filelist as $file) {
		if(!file_exists($file)) continue;
		if($cli->overwrite && !is_writable($file)) continue;
		if(!$cli->overwrite && !is_writable(dirname(__FILE__))) continue;
		
		// go ahead and do them in order if you really tried multiple ops even
		// though i did not really plan on allowing that originally.
		foreach($cli->argv as $arg => $val) {
			switch($arg) {
				case 'crop': { lol_crop($cli->crop,$file,$cli->overwrite); break; }
				case 'resize': { lol_resize($cli->resize,$file,$cli->overwrite); break; }
				case 'scale': { lol_scale($cli->scale,$file,$cli->overwrite); break; }
				case 'thumbnail': { lol_thumbnail($cli->thumbnail,$file,$cli->overwrite); break; }
			}
		}
	}
}

?>