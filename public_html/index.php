<?php
/**


This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

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