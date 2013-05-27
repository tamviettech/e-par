<?php 
/**
 // File name   : controller.php
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

class Controller {
    public $view;
    public $model;

    protected $app_name ='';
    protected $module_name = '';

    function __construct($app,$module)
    {
        @session_start();
        //Load custom functions
        $func = SERVER_ROOT . 'apps' . DS . $app . DS . 'functions.php';
        if (is_file($func))
        {
            @require_once($func);
        }

        $this->app_name =$app;
        $this->module_name = $module;

        $v = $app . '_View';
        $this->view = (class_exists($v)) ? new $v($app, $module) : new View($app,$module);

        $this->_load_model($app, $module);
    }

    private function _load_model($app, $module)
    {
        $path = 'apps'.DS . $app . DS .'modules'.DS . $module . DS . $module. '_Model.php';
        if (file_exists($path))
        {
            require $path;
            $model_name = $this->module_name . '_Model';
            $this->model = new $model_name;
            $this->model->db->debug = DEBUG_MODE;
            $this->model->app_name = $app;
            $this->model->module_name = $module;
        }
        else
        {
            $this->error(1);
        }
    }

    public static function check_login()
    {
        session::init();
        $login_name = session::get('login_name');
        if ($login_name == NULL)
        {
            session::destroy();
            header('location:' . SITE_ROOT . 'login.php');
            return FALSE;
        }

        return TRUE;
    }

    public function check_permission($function_code, $app_name='')
    {
        @Session::init();
        $app_name = ($app_name != '') ? strtoupper($app_name) : strtoupper($this->app_name);
        $function_code =  $app_name . '::' . $function_code;
        return in_array($function_code, Session::get('arr_function_code'));
    }

    public static function get_post_var($html_object_name, $defaul='', $is_replace_bad_char=TRUE)
    {
        $var = isset($_POST[$html_object_name]) ? $_POST[$html_object_name] : $defaul;

        if ($is_replace_bad_char)
        {
            return replace_bad_char ($var);
        }

        return $var;
    }

    public function error($error_code)
    {
        switch ($error_code)
        {
            case 1: //Ma loi 1: Không thấy file Model!
                die('Không thấy file Model!');
                break;
            case 2:
                die('xxx');
                break;
        }
    }

    public function access_denied()
    {
        die('Bạn không có quyền thực hiện chức năng này!');
    }
}