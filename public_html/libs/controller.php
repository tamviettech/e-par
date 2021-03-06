<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed'); ?>
<?php

class Controller
{

    /** @var \View  */
    public $view;
    /** @var \Model */
    public $model;
    /** @var \Layout */
    
    protected $app_name    = '';
    protected $module_name = '';

    function __construct($app, $module)
    {
        @session_start();
        //Load app const
        $const_file = SERVER_ROOT . 'apps' . DS . $app . DS . "{$app}_const.php";
        if (file_exists($const_file))
        {
            require_once($const_file);
        }
        
        //Load custom functions
        $func = SERVER_ROOT . 'apps' . DS . $app . DS . 'functions.php';
        if (is_file($func))
        {
            @require_once($func);
        }

        $this->app_name    = $app;
        $this->module_name = $module;

        $v          = $app . '_View';
        $this->view = (class_exists($v)) ? new $v($app, $module) : new View($app, $module);

        $this->view->local_js = $this->view->template->local_js = SITE_ROOT . 'apps/' . $app . '/modules/' . $module . '/' . $module . '_views/js_' . $module . '.js';
        $this->view->view_url = SITE_ROOT . 'apps/' . $app . '/modules/' . $module . '/' . $module . '_views/';
        $this->_load_model($app, $module);
        
    }

    private function _load_model($app, $module)
    {
        $path = 'apps' . DS . $app . DS . 'modules' . DS . $module . DS . $module . '_Model.php';
        if (file_exists($path))
        {
            require $path;
            $model_name               = $this->module_name . '_Model';
            $this->model              = new $model_name;
            $this->model->db->debug   = DEBUG_MODE;
            $this->model->app_name    = $app;
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

    public function check_permission($function_code, $app_name = 'r3')
    {
        return check_permission($function_code, $app_name);
    }

    public static function get_post_var($html_object_name, $defaul = '', $is_replace_bad_char = TRUE)
    {
        $var = isset($_POST[$html_object_name]) ? $_POST[$html_object_name] : $defaul;

        if ($is_replace_bad_char)
        {
            return replace_bad_char($var);
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