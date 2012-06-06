<?php

require(sprintf(
	'%s/m/application.php',
	dirname(dirname(dirname(__FILE__)))
));

ini_set('memory_limit','2G');

$cli = new m\cli;
$mapfile = sprintf('%s/rsrc/earth.jpg',dirname(__FILE__));
$jsonfile = sprintf('%s/rsrc/geoip.json',dirname(__FILE__));
$outfile = sprintf('%s/geoip-%s.jpg',dirname(__FILE__),IMGTOOL_DRIVER);

if(!file_exists($mapfile) || !file_exists($jsonfile))
$cli->shutdown('need a map and datafile bro');

// load the data file.
$geodat = json_decode(file_get_contents($jsonfile));
if(!is_array($geodat)) $cli->shutdown('data file looks bish');

// load the image.
m_printfln('opening map...');
$imgtool = new imgtool\image($mapfile);
$imgtool->desaturate();

// plot all the data points on the image.
foreach($geodat as $iter => $point) {
	m_printfln('[%s] plotting %s (%d,%d)',($iter+1),$point->ip,$point->long,$point->lat);

	$plotx = ($imgtool->width / 2) + (($point->long * ($imgtool->width/2)) / 180);
	$ploty = ($imgtool->height / 2) + ((($point->lat*-1) * ($imgtool->height/2)) / 90);

	$imgtool->dot($plotx,$ploty,6,'#ff000077');
}

// save map to disk.
m_printfln('saving map to disk...');
$imgtool->save($outfile,100);
m_printfln('done (%s)',m_exec_time());

?>