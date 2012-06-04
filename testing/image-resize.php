<?php

require(sprintf(
	'%s/m/application.php',
	dirname(dirname(__FILE__))
));

$imagefile = sprintf('%s\rsrc\image.jpg',dirname(__FILE__));
$outfile = sprintf('%s/image-resize-%s.jpg',dirname(__FILE__),IMGTOOL_DRIVER);

$tool = new imgtool\image($imagefile);
echo "Loaded File: {$tool->filename}", PHP_EOL;
echo "File Size: ", number_format($tool->filesize), " bytes", PHP_EOL;
echo "Width: {$tool->width}, Height: {$tool->height}", PHP_EOL;

$tool->resize(100,200);
$tool->save($outfile);

echo "Resized Width: {$tool->width}, Height: {$tool->height}", PHP_EOL;

$tool->free();
unset($tool);

?>