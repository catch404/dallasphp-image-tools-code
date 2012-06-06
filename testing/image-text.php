<?php

require(sprintf(
	'%s/m/application.php',
	dirname(dirname(__FILE__))
));

$imagefile = sprintf('%s\rsrc\image.jpg',dirname(__FILE__));
$outfile = sprintf('%s/image-text-%s.jpg',dirname(__FILE__),IMGTOOL_DRIVER);

$tool = new imgtool\image($imagefile);

$tool->text(
	10,10,
	'jedi',12,'#000000',
	sprintf('HERP A DERP - %s',date('Y/m/d g:ia'))
);

$tool->text(
	9,9,
	'jedi',12,'#ff8800',
	sprintf('HERP A DERP - %s',date('Y/m/d g:ia'))
);

$tool->save($outfile,100);

$tool->free();
unset($tool);

?>