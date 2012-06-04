<?php

define('m\platform','bin');
require('../../../application.php');

setcookie('m_user','',-1,'/');

$go = new m\request\redirect('m://home');
$go->go();
