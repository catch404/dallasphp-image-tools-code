<?php

require('m/application.php');
require('demos/webapp.php');

$get = new m\request\input('get');

if(!$get->file) {
	echo 'no file selected';
	exit(0);
} 

try { $image = new MediaItem($get->file); }
catch(Exception $e) {
	echo 'file not found';
	exit(0);
}

?>

<div class="post">
	<h2><a href=""><?php echo $image->filename ?></a></h2>
	<div class="view"><img src="<?php echo $image->getViewURI() ?>" alt="" /></div>
</div>	
