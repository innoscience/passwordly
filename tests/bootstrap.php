<?php

error_reporting(E_ALL | E_STRICT);

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
	require __DIR__ . '/../vendor/autoload.php';
}
elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
	require __DIR__ . '/../../../autoload.php';
}
else {
	throw new \Exception('Can\'t find autoload.php. Did you install dependencies via composer?');
}