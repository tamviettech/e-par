<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');?>
<?php
function mvc_url_parse(&$app, &$module, &$function, &$function_args)
{
    $url           = (isset($_REQUEST['url'])) ? explode('/', rtrim($_REQUEST['url'], '/')) : array();

    $app           = (isset($url[0])) ? trim($url[0]) : 'main';
    $module        = (isset($url[1])) ? trim($url[1]) : '';
    $function      = (isset($url[2])) ? trim($url[2]) : '';
    $function_args = (isset($url[3])) ? trim($url[3]) : '';
}
class Bootstrap {

    function __construct() {
        
        mvc_url_parse($app, $module, $function, $function_args);
        if ($module == '')
        {
            if ($app == 'main')
            {
               $module = 'main'; 
            }
            else
            {
                $current_model = new Model();
                $module = $current_model->get_default_module($app);  
            }
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

        //Neu la yeu cau thuc hien method
        if ($function_args != '') //Goi ham co tham so
        {
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

    private function _error($code=0)
    {
        if (DEBUG_MODE > 0)
        {
            switch ($code)
            {
                case 1:
                    die('Không tìm thấy Method!');
                    break;
    
                case 2:
                    die('Không tìm thấy Controller!');
                    break;
            }
        }
        else 
        {
            require_once (SERVER_ROOT . '404.php');
        }
    }

}