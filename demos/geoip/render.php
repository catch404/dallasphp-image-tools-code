<?php

require(sprintf(
	'%s/m/application.php',
	dirname(dirname(dirname(__FILE__)))
));

$mapfile = sprintf('%s/rsrc/earth.jpg',dirname(__FILE__));

$img = new imgtool\gd\image($mapfile);

?>