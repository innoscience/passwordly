<?php

error_reporting(E_ALL | E_STRICT);

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
	require_once __DIR__ . '/../vendor/autoload.php';
}
else if (file_exists(__DIR__ . '/../../../autoload.php')) {
	require_once __DIR__ . '/../../../autoload.php';
}
else {
	throw new \Exception('Can\'t find autoload.php. Did you install dependencies via composer?');
}