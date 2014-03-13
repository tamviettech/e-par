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
?>
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');?>
<?php

/******************** Hang so ket noi CSDL ***********************************/
define('CONST_DB_SERVER_ADDRESS', '10.10.1.200');
define('CONST_DB_DATABASE_NAME', 'epar-github');
define('CONST_DB_USER_NAME', 'root');
define('CONST_DB_USER_PASSWORD', 'root');
define('DATABASE_TYPE','MYSQL');//Be MSSQL or ORACLE or MYSQL
/******************************************************************************/

//Che do Debug:
//          o   FALSE: Chay that
//          o   TRUE:  Bat che do debug 
define('DEBUG_MODE', 0);

//Duong dan dia chi goc
//          o   /virtual-directory/: Chay bang thu muc ao (Virtual Directory)
//          o   /                  : Chay bang domain     
define('SITE_ROOT','/epar-github/');

//Turn on/off chat module
//          o   0: Khong su dung chat
//          o   1: Co su dung chat
define('CHAT_MODULE', 0);

//Dung Cache tao report
define('CONST_USE_ADODB_CACHE_FOR_REPORT',0);

//Database tren cung server voi webserver
//Neu khac, thoi gian duoc lay theo dong ho cua Database server
define('CONST_DATABASE_IS_SAME_SERVER',0);

define('CONST_IDC_INTERGRATED',0);

//Xac thuc qua Windows AD
define('AUTH_MODE', 'AD');
define('AD_DOMAIN_NAME', 'zinxu.net');
define('AD_ACCOUNT_SUFFIX', '@zinxu.net');
define('AD_BASEDN', 'DC=zinxu,DC=net');
define('AD_DC_LIST', 'dc01.zinxu.net');
define('AD_ADMIN_USER','Administrator');
define('AD_ADMIN_PASSWORD','P@ssw0rd');

//Khong can dieu chinh tham so duoi day
if (!preg_match('/^http:/', SITE_ROOT) OR !preg_match('/^https:/', SITE_ROOT))
{
    if (empty($_SERVER['HTTPS']))
    {
        define('FULL_SITE_ROOT','http://' . $_SERVER['HTTP_HOST'] . SITE_ROOT);
    }
    else 
    {
        define('FULL_SITE_ROOT','https://' . $_SERVER['HTTP_HOST'] . SITE_ROOT);
    }
}
else
{
	define('FULL_SITE_ROOT', SITE_ROOT);
}

//Oracle Setting
define('CONST_ORACLE_DSN', 'oci8://mvcxml:mvcxml@172.16.1.252/XE');

//SQL Server Setting
define('CONST_MSSQL_DSN', 'PROVIDER=SQLOLEDB;DRIVER={SQL Server};SERVER=' . CONST_DB_SERVER_ADDRESS . ';DATABASE=' . CONST_DB_DATABASE_NAME . ';UID='.CONST_DB_USER_NAME . ';PWD=' . CONST_DB_USER_PASSWORD . ';');

//MySQL Setting
define('CONST_MYSQL_DSN','mysql://' . CONST_DB_USER_NAME . ':' . CONST_DB_USER_PASSWORD . '@' .CONST_DB_SERVER_ADDRESS . '/' .  CONST_DB_DATABASE_NAME);

//Pear libs
define('_PATH_TO_PEAR', SERVER_ROOT . 'libs/PEAR/');
@ini_set('include_path', _PATH_TO_PEAR);

//apps dir
define('CONST_APPS_DIR', SERVER_ROOT . 'apps' . DS);

//DOC upload folder
define('CONST_SERVER_DOC_FILE_UPLOAD_DIR', SERVER_ROOT . 'uploads/');
define('CONST_SITE_DOC_FILE_UPLOAD_DIR', SITE_ROOT . 'uploads/');

@require_once _PATH_TO_PEAR . 'Var_Dump.php';
if (DEBUG_MODE > 0)
{
    error_reporting(E_ALL);
    ini_set('display_errors',1);
}
else
{
    error_reporting(E_ALL);
    ini_set('display_errors',0);
}