<?php
defined('DS')  OR define('DS', DIRECTORY_SEPARATOR);
defined('_PATH_TO_PEAR') OR define('_PATH_TO_PEAR',  __DIR__ . DS . 'libs' . DS . 'PEAR' . DS);

ini_set('include_path', _PATH_TO_PEAR);
require_once('Image/Barcode2.php');

Image_Barcode2::draw($_GET['bc'], 'code128', 'png');