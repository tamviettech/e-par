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
            $current_model = new Model();
            $module = $current_model->get_default_module($app);
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