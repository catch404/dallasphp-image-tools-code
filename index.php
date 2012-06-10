<?php

require('m/application.php');
require('demos/webapp.php');

$media = new MediaIterator;

?>

<div class="post">
	<h2><a href="/">Images All Up In Here</a></h2>

	<?php foreach($media as $image) { $obj = $image->getMediaItem(); ?>
	<div class="thumbnail"><a href="view.php?file=<?php echo $obj->filename ?>"><img src="<?php echo $obj->getThumbnailURI() ?>" alt="" /></a></div>
	<?php } ?>

</div>	
