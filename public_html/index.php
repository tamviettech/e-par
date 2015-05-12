<?php
ini_set('date.timezone', 'Asia/Ho_Chi_Minh');

define('DS', DIRECTORY_SEPARATOR);
define('SERVER_ROOT', __DIR__ . DS);

require_once ('config.php');
require_once ('const.php');

//library
require_once (SERVER_ROOT . 'libs' . DS . 'PEAR' . DS . 'PEAR.php');
require_once (SERVER_ROOT . 'libs' . DS . 'PEAR' . DS . 'Savant3.php');
require_once (SERVER_ROOT . 'libs' . DS . 'adodb5' . DS . 'adodb.inc.php');
require_once (SERVER_ROOT . 'libs' . DS . 'jwdate.class.php');
require_once (SERVER_ROOT . 'libs' . DS . 'functions.php');
require_once (SERVER_ROOT . 'libs' . DS . 'session.php');
require_once (SERVER_ROOT . 'libs' . DS . 'lang.php');

//TCPDF
require_once (SERVER_ROOT . 'libs' . DS . 'tcpdf' . DS . 'config' . DS . 'lang/vn.php');
require_once (SERVER_ROOT . 'libs' . DS . 'tcpdf' . DS . 'config' . DS . 'tcpdf_config_alt.php');
define("K_TCPDF_EXTERNAL_CONFIG", true);
require_once (SERVER_ROOT . 'libs' . DS . 'tcpdf' . DS . 'zreport.php');

//MVC
require_once (SERVER_ROOT . 'libs' . DS . 'model.php');
require_once (SERVER_ROOT . 'libs' . DS . 'view.php');
require_once (SERVER_ROOT . 'libs' . DS . 'controller.php');
require_once (SERVER_ROOT . 'libs' . DS . 'bootstrap.php');
//lang
//include_once SERVER_ROOT . 'languages/lang_vi.php';
//Kiem session ngon ngu
Lang::load_lang('lang_vi');

$mvc_xml = new Bootstrap();
$url     = get_request_var('url');