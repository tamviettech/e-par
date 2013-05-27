<?php
/**
// File name   : bootstrap.php
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

class Bootstrap {

    function __construct() {
        $url = (isset($_REQUEST['url'])) ? explode('/', rtrim($_REQUEST['url'], '/')) : array();

        $app = (isset($url[0])) ? trim($url[0]) : 'r3';
        if (isset($url[1]))
        {
            $module = trim($url[1]);
        }
        else
        {
            $module = ($app == 'r3') ? 'record' : '';
        }

        //Load Controller
        $file = SERVER_ROOT . 'apps' .DS . $app . DS . 'modules'. DS . $module . DS . $module . '_Controller.php';
        if (file_exists($file))
        {
            require $file;
            $controller_class = $module . '_Controller';

            //new instance
            $controller = new $controller_class($app, $module);
        }
        else
        {
            $this->_error(2);
            return false;
        }

        $function_args = (isset($url[3])) ? trim($url[3]) : '';
        $function = (isset($url[2])) ? trim($url[2]) : '';

        //Neu la yeu cau thuc hien method
        if ($function_args != '') //Goi ham co tham so
        {
            //echo '$function_args = ' . $function_args;
            if (method_exists($controller, $function))
            {
                $controller->{$function}($function_args);
            }
            else
            {
                $this->_error(1);
            }
        }
        elseif ($function != '') //Hay goi ham khong co tham so
        {
            if (method_exists($controller, $function))
            {
                $controller->{$function}();
            }
            else
            {
                $this->_error(1);
            }
        }
        else //neu khong co yeu cau gi, thuc hien method mac dinh
        {
            $controller->main();
        }
    }

    private function _error($code)
    {
        switch ($code)
        {
            case 1:
                die('Không tồn tại hàm!');
                break;

            case 2:
                die('Không tìm thấy file Controller!');
                break;
        }
    }

}