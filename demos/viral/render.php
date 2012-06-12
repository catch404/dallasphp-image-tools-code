<?php

define('IMGTOOL_DRIVER','imagick');
require(sprintf(
	'%s/m/application.php',
	dirname(dirname(dirname(__FILE__)))
));

chdir(dirname(__FILE__));

$tool = new imgtool\image('rsrc/image.jpg');

$tool->text(
	45,0,
	'sans13black',90,
	'#ffffff',
	'DID YOU SAY'
);

$tool->text(
	20,($tool->height - 160),
	'sans13black',140,
	'#ffffff',
	'PONIES?'
);

$tool->save('output.png');

?>