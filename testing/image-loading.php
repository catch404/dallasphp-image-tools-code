<?php

require(sprintf(
	'%s/m/application.php',
	dirname(dirname(__FILE__))
));

$imagefile = sprintf('%s\rsrc\image.jpg',dirname(__FILE__));
$tool = new imgtool\image($imagefile);

echo "Loaded File: {$tool->filename}", PHP_EOL;
echo "File Size: ", number_format($tool->filesize), " bytes", PHP_EOL;
echo "Width: {$tool->width}, Height: {$tool->height}", PHP_EOL;

$tool->free();
unset($tool);

?>