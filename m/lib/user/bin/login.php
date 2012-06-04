<?php

define('m\platform','bin');
require('../../../application.php');

$post = new m\request\input('post');

// quit if they did not even send us the data.
if(!$post->username || !$post->password) {
	die('error: try sending a username and password yo');
}

// see if that user exists.
$user = m\user::get($post->username,array('KeepHashes'=>true));
if(!$user) {
	die('error: try sending a valid username');
}

// see if the password is a win.
if(hash('sha512',$post->password) !== $user->PHash) {
	die('error: try sending a valid password');
}

// user authenticated.
$user->sessionUpdate();

// bye.
$go = new m\request\redirect('m://home');
$go->go();
