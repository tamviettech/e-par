<?php 
/**
// File name   : config.php
// Version     : 1.0.0.1
// Begin       : 2012-12-01
// Last Update : 2010-12-25
// Author      : TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
//Copyright (C) 2012-2013  TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn

// E-PAR is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// E-PAR is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// See LICENSE.TXT file for more information.
*/

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

define('DEBUG_MODE', 1);
define('SITE_ROOT','/lang-giang/');

define('DATABASE_TYPE','MYSQL');//Be MSSQL or ORACLE or MYSQL

//Oracle Setting
define('CONST_ORACLE_DSN','oci8://mvcxml:mvcxml@172.16.1.252/XE');

//SQL Server Setting
define('CONST_MSSQL_DSN','PROVIDER=SQLOLEDB;DRIVER={SQL Server};SERVER=172.16.1.252;DATABASE=Go-Office;UID=sa;PWD=P@ssw0rd;');

//MySQL Server Setting
define('CONST_MYSQL_DSN','mysql://go-office:123456@172.16.1.200/lang-giang');
//define('CONST_MYSQL_DSN','mysql://root:root@192.168.1.206/lang-giang');

//Dung Cache
define('CONST_USE_ADODB_CACHE_FOR_REPORT',0);

//DB tren cung server
define('CONST_DATABASE_IS_SAME_SERVER',1);
//Pear libs
define('_PATH_TO_PEAR', SERVER_ROOT . 'libs/PEAR/');
@ini_set('include_path', _PATH_TO_PEAR);

//apps dir
define('CONST_APPS_DIR', SERVER_ROOT . 'apps' . DS);

//DOC upload folder
define('CONST_SERVER_DOC_FILE_UPLOAD_DIR', SERVER_ROOT . 'uploads/');
define('CONST_SITE_DOC_FILE_UPLOAD_DIR', SITE_ROOT . 'uploads/');

//ADLDAP
define('AUTH_MODE', 'AD');
define('AD_DOMAIN_NAME', 'zinxu.net');
define('AD_ACCOUNT_SUFFIX', '@zinxu.net');
define('AD_BASEDN', 'DC=zinxu,DC=net');
define('AD_DC_LIST', 'dc01.zinxu.net');
define('AD_ADMIN_USER','Administrator');
define('AD_ADMIN_PASSWORD','P@ssw0rd');

/******************************************************************************/
@require_once _PATH_TO_PEAR . 'Var_Dump.php';
if (DEBUG_MODE > 0)
{
    error_reporting(E_ALL);
    ini_set('display_errors',1);
}
else
{
    error_reporting(0);
    ini_set('display_errors',0);
}