<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php

/* * ****************** Hang so ket noi CSDL ********************************** */
define('CONST_DB_SERVER_ADDRESS', 'localhost');
define('CONST_DB_DATABASE_NAME', 'e-par');
define('CONST_DB_USER_NAME', 'root');
define('CONST_DB_USER_PASSWORD', 'root');
define('DATABASE_TYPE', 'MYSQL'); //Be MSSQL or ORACLE or MYSQL
/* * *************************************************************************** */

//Che do Debug:
//          o   FALSE: Chay that
//          o   TRUE:  Bat che do debug 

define('DEBUG_MODE', 0);

//Duong dan dia chi goc
//          o   /virtual-directory/: Chay bang thu muc ao (Virtual Directory)
//          o   /                  : Chay bang domain     
define('SITE_ROOT', '/mot-cua-yen-bai/');

define('SITE_THEME_ROOT', SITE_ROOT . 'public/themes/bootstrap/');

//Turn on/off chat module
//          o   0: Khong su dung chat
//          o   1: Co su dung chat
define('CHAT_MODULE', 1);

//Dung Cache tao report
define('CONST_USE_ADODB_CACHE_FOR_REPORT', 0);

//Database tren cung server voi webserver
//Neu khac, thoi gian duoc lay theo dong ho cua Database server
define('CONST_DATABASE_IS_SAME_SERVER', 0);

define('CONST_IDC_INTERGRATED', 0);

//Xac thuc qua Windows AD
define('AUTH_MODE', '');
define('AD_DOMAIN_NAME', 'your_domain');
define('AD_ACCOUNT_SUFFIX', '');
define('AD_BASEDN', '');
define('AD_DC_LIST', '');
define('AD_ADMIN_USER', '');
define('AD_ADMIN_PASSWORD', '');

//Khong can dieu chinh tham so duoi day
isset($_SERVER['HTTP_HOST']) OR die('403-No ServerHost Info');

if (!preg_match('/^http:/', SITE_ROOT) OR ! preg_match('/^https:/', SITE_ROOT))
{
    if (empty($_SERVER['HTTPS']))
    {
        define('FULL_SITE_ROOT', 'http://' . $_SERVER['HTTP_HOST'] . SITE_ROOT);
    }
    else
    {
        define('FULL_SITE_ROOT', 'https://' . $_SERVER['HTTP_HOST'] . SITE_ROOT);
    }
}
else
{
    define('FULL_SITE_ROOT', SITE_ROOT);
}

//Cookie root path
if (empty($_SERVER['HTTPS']))
{
    define('COOKIE_ROOT', str_replace('http://' . $_SERVER['HTTP_HOST'], '', FULL_SITE_ROOT));
}
else
{
    define('COOKIE_ROOT', str_replace('https://' . $_SERVER['HTTP_HOST'], '', FULL_SITE_ROOT));
}

//Oracle Setting
define('CONST_ORACLE_DSN', 'oci8://mvcxml:mvcxml@172.16.1.252/XE');

//SQL Server Setting
define('CONST_MSSQL_DSN', 'PROVIDER=SQLOLEDB;DRIVER={SQL Server};SERVER=' . CONST_DB_SERVER_ADDRESS . ';DATABASE=' . CONST_DB_DATABASE_NAME . ';UID=' . CONST_DB_USER_NAME . ';PWD=' . CONST_DB_USER_PASSWORD . ';');

//MySQL Setting
define('CONST_MYSQL_DSN', 'mysqli://' . CONST_DB_USER_NAME . ':' . CONST_DB_USER_PASSWORD . '@' . CONST_DB_SERVER_ADDRESS . '/' . CONST_DB_DATABASE_NAME);

//Pear libs
define('_PATH_TO_PEAR', SERVER_ROOT . 'libs/PEAR/');
@ini_set('include_path', _PATH_TO_PEAR);

//apps dir
define('CONST_APPS_DIR', SERVER_ROOT . 'apps' . DS);

//DOC upload folder
define('CONST_SERVER_DOC_FILE_UPLOAD_DIR', SERVER_ROOT . 'uploads/');
define('CONST_SITE_DOC_FILE_UPLOAD_DIR', SITE_ROOT . 'uploads/');

require_once _PATH_TO_PEAR . 'Var_Dump.php';

if (DEBUG_MODE > 0 || isset($_REQUEST['debug']))
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
else
{
    error_reporting(0);
    ini_set('display_errors', 0);
}


if (!function_exists('http_response_code'))
{

    function http_response_code($code = NULL)
    {

        if ($code !== NULL)
        {

            switch ($code) {
                case 100: $text = 'Continue';
                    break;
                case 101: $text = 'Switching Protocols';
                    break;
                case 200: $text = 'OK';
                    break;
                case 201: $text = 'Created';
                    break;
                case 202: $text = 'Accepted';
                    break;
                case 203: $text = 'Non-Authoritative Information';
                    break;
                case 204: $text = 'No Content';
                    break;
                case 205: $text = 'Reset Content';
                    break;
                case 206: $text = 'Partial Content';
                    break;
                case 300: $text = 'Multiple Choices';
                    break;
                case 301: $text = 'Moved Permanently';
                    break;
                case 302: $text = 'Moved Temporarily';
                    break;
                case 303: $text = 'See Other';
                    break;
                case 304: $text = 'Not Modified';
                    break;
                case 305: $text = 'Use Proxy';
                    break;
                case 400: $text = 'Bad Request';
                    break;
                case 401: $text = 'Unauthorized';
                    break;
                case 402: $text = 'Payment Required';
                    break;
                case 403: $text = 'Forbidden';
                    break;
                case 404: $text = 'Not Found';
                    break;
                case 405: $text = 'Method Not Allowed';
                    break;
                case 406: $text = 'Not Acceptable';
                    break;
                case 407: $text = 'Proxy Authentication Required';
                    break;
                case 408: $text = 'Request Time-out';
                    break;
                case 409: $text = 'Conflict';
                    break;
                case 410: $text = 'Gone';
                    break;
                case 411: $text = 'Length Required';
                    break;
                case 412: $text = 'Precondition Failed';
                    break;
                case 413: $text = 'Request Entity Too Large';
                    break;
                case 414: $text = 'Request-URI Too Large';
                    break;
                case 415: $text = 'Unsupported Media Type';
                    break;
                case 500: $text = 'Internal Server Error';
                    break;
                case 501: $text = 'Not Implemented';
                    break;
                case 502: $text = 'Bad Gateway';
                    break;
                case 503: $text = 'Service Unavailable';
                    break;
                case 504: $text = 'Gateway Time-out';
                    break;
                case 505: $text = 'HTTP Version not supported';
                    break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;
        }
        else
        {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
        }

        return $code;
    }

}//end if function_exists('http_response_code')
