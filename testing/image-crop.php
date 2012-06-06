<?php

require(sprintf(
	'%s/m/application.php',
	dirname(dirname(__FILE__))
));

$imagefile = sprintf('%s\rsrc\image.jpg',dirname(__FILE__));
$outfile = sprintf('%s/image-crop-%s.jpg',dirname(__FILE__),IMGTOOL_DRIVER);

$tool = new imgtool\image($imagefile);
$tool->crop(278,119,105,76);
$tool->save($outfile);
$tool->free();
unset($tool);

?>