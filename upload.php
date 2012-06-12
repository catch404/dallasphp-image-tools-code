<?php

require('m/application.php');
require('demos/webapp.php');


if(array_key_exists('derp',$_FILES)) {
	
//	echo '<pre>';
//	print_r($_FILES['derp']);
//	echo '</pre>';
//	die();

	$files = (object)$_FILES['derp'];
	$filecount = count($files->tmp_name);

	for($a = 0; $a < $filecount; $a++) {
		if(!preg_match('/\.(jpe?g|png)$/i',$files->name[$a]))
		continue;

		rename($files->tmp_name[$a],sprintf(
			'%s/media/%s',
			m\webroot,
			$files->name[$a]
		));
	}

	$bye = new m\request\redirect('/');
	$bye->go();
}

?>

<script type="text/javascript">
function AddUploadForm(fade) {
	jQuery('#uploadarea').append(
		'<div class="upload" style="display:none;"><input type="file" name="derp[]" /></div>'
	);

	if(fade) jQuery('.upload:hidden').fadeIn();
	else jQuery('.upload:hidden').show();

	return;
}

jQuery(document).ready(function(){
	AddUploadForm(false);
});
</script>

<div class="post">
	<h2><a href="">Upload Stuff</a></h2>
	<form method="post" enctype="multipart/form-data">
		<div id="uploadarea"></div>
		<div style="text-align:center;margin:10px;">
				<input type="button" value="Add File" onclick="javascript:AddUploadForm(true);" />
				<input type="submit" value="Upload Now" />
		</div>
	</form>
</div>	
