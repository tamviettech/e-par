<?php
define ('DS', DIRECTORY_SEPARATOR);
define('SERVER_ROOT', __DIR__ . DS);
require('config.php');
echo "var SITE_ROOT = '" . SITE_ROOT . "';";