<?php

chdir(dirname(__FILE__));
foreach(glob('*.jpg') as $img) unlink($img);

?>