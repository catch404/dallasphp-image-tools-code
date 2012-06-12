<?php

define('IMGTOOL_DRIVER','imagick');
require(sprintf(
	'%s/m/application.php',
	dirname(dirname(dirname(__FILE__)))
));

ini_set('memory_limit','3G');

$cli = new m\cli;
$mapfile = sprintf('%s/rsrc/earth.jpg',dirname(__FILE__));
$jsonfile = sprintf('%s/rsrc/geoip.json',dirname(__FILE__));
$outfile = sprintf('%s/geoip-imagickk.jpg',dirname(__FILE__));

if(!file_exists($mapfile) || !file_exists($jsonfile))
$cli->shutdown('need a map and datafile bro');

if($cli->format) $format = $cli->format;
else $format = 'jpg';

// load the data file.
$geodat = json_decode(file_get_contents($jsonfile));
if(!is_array($geodat)) $cli->shutdown('data file looks bish');

// load the image.
m_printfln('opening map...');
$imgtool = new imgtool\image($mapfile);
m_printfln('>> opened at %s',m_exec_time());

// desaturate map.
$imgtool->desaturate();
m_printfln('>> desaturated at %s',m_exec_time());


////////////////////////////////////////////////////////////////////////////////
// imagick plotting. ///////////////////////////////////////////////////////////

$draw = new ImagickDraw;
$draw->setFillColor(new ImagickPixel('#ff000077'));

foreach($geodat as $iter => $point) {
	m_printfln(
		'[%s] plotting %s (%d,%d)',
		($iter+1),
		$point->ip,
		$point->long,$point->lat
	);

	$plotx = ($imgtool->width / 2) + (($point->long * ($imgtool->width/2)) / 180);
	$ploty = ($imgtool->height / 2) + ((($point->lat*-1) * ($imgtool->height/2)) / 90);

	$draw->ellipse($plotx,$ploty,3,3,0,360);
}

$imgtool->img->drawImage($draw);
$draw->destroy();
m_printfln('>> plotted at %s',m_exec_time());

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


// save map to disk.
if($format !== 'jpg')
$outfile = str_replace('.jpg',".{$format}",$outfile);

m_printfln('saving map to disk %s...',basename($outfile));
$imgtool->save($outfile,90);

m_printfln(
	'done (%s, %s MB RAM)',
	m_exec_time(),
	number_format((memory_get_peak_usage(true) / 1024) / 1024)
);

?>