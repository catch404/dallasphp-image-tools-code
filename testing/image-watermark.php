<?php

require(sprintf(
	'%s/m/application.php',
	dirname(dirname(__FILE__))
));

$imagefile = sprintf('%s\rsrc\image.jpg',dirname(__FILE__));
$overlayfile = sprintf('%s\rsrc\overlay.png',dirname(__FILE__));
$outfile = sprintf('%s/image-watermark-%s.jpg',dirname(__FILE__),IMGTOOL_DRIVER);

$tool = new imgtool\image($imagefile);
$tool->watermark($overlayfile);
$tool->save($outfile);

$tool->free();
unset($tool);

?>