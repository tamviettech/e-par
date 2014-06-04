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
<?php

defined('DS') or die('no direct access');
define('EXT_SESSION_KEY', 'media_extensions');

class advmedia_Controller extends Controller
{

    public function __construct()
    {
         //Kiem tra session
        @session::init();
        $login_name = session::get('login_name');
        
        if ($login_name == NULL)
        {
            session::destroy();
            header('location:' . SITE_ROOT . 'login.php');
            exit;
        }

        parent::__construct('public_service', 'advmedia');
        Session::init();
        
        if (DEBUG_MODE < 10)
        {
        	$this->model->db->debug = 0;
        }
        check_permission('XEM_DANH_SACH_MEDIA', $this->app_name) OR $this->access_denied();
        
        $this->model->app_name             = $this->app_name;
        $this->view->arr_count_article     = $this->model->gp_qry_count_article();
        $this->model->goback_url           = $this->view->get_controller_url();
        $this->view->dsp_side_bar = TRUE;
    }

    function main()
    {
        $this->view->title = __('meida');
        $data['current_dir'] = get_request_var('dir');
        $data['dir_path']    = SERVER_ROOT . 'uploads/public_service' . DS . $data['current_dir'];

        //folder exist
        if (is_dir($data['dir_path']) == false)
        {
            echo __('this directory has been moved or deleted');
            echo '<br/><a href="' . $this->view->get_controller_url() . '">' . __('click here to return to root') . '</a>';
            return;
        }
        if (!isset($this->extenstions))
        {
            $this->extenstions = strtoupper(EXT_ALL);
        }
        Session::set(EXT_SESSION_KEY, str_replace(' ', '', $this->extenstions));
        //Hien thi neu la dinh dang popwin
        $v_pop_win = isset($_GET['pop_win']) ? (int)$_GET['pop_win'] : '';
        if($v_pop_win == 1)
        {
            $this->view->layout_render('public_service/admin/dsp_layout_admin_pop_win','dsp_main', $data);
        }
        else
        {
            $this->view->layout_render('public_service/admin/dsp_layout_admin','dsp_main', $data);
        }
        
    }

    /**
     * 
     * @param string $type '', 'image', 'spreadsheet', 'data', 'text', 'video', 'audio', 'compressed'
     */
    function dsp_service($type='')
    {
        
        $type       = strtoupper($type);
        $const_name = "EXT_$type";
        if (defined($const_name))
        {
            $this->extenstions = strtoupper(constant($const_name));
        }
        $this->view->is_service = TRUE;
        $this->main();
    }

    function get_dir_content()
    {
        $dir_name = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : '';
//        if ($dir_name == '')
//        {
//            if (!is_dir(SERVER_ROOT . '/uploads/public_service/2014'))
//                mkdir(SERVER_ROOT . '/uploads/public_service/2014');
//            
//            if (!is_dir(SERVER_ROOT . '/uploads/public_service/2015'))
//                mkdir(SERVER_ROOT . '/uploads/public_service/2015');
//            echo json_encode(array(
//                'folder' => array(
//                    '2014' => array(
//                        'date' => date('d-m-Y H:i')
//                        , 'path' => DS . '2014'
//                        , 'type' => __('directory'))
//                    , '2015' => array(
//                        'date' => date('d-m-Y H:i')
//                        , 'path' => DS . '2015'
//                        , 'type' => __('directory')))
//                , 'file'   => array()
//            ));
//            return;
//        }
        if($dir_name == '')
        {
            $dir_path = SERVER_ROOT . 'uploads/public_service';
        }
        else
        {
            $dir_path = SERVER_ROOT . 'uploads/public_service' . DS . $dir_name;
        }

        if (!is_dir($dir_path) && file_exists($dir_path))
        {
            $dir_path = dir_name($dir_path, '\\/');
        }
        if (is_dir($dir_path) == false)
        {
            echo json_encode(array(
                'folder'  => array()
                , 'file'    => array()
                , 'dirpath' => $dir_path
            ));
            return;
        }
        $dir_path .= DS;
        $scan_result        = scandir($dir_path);
        $arr_folder         = array();
        $arr_file           = array();
        $allowed_extensions = explode(',', str_replace(' ', '', Session::get(EXT_SESSION_KEY)));
        
        foreach ($scan_result as $item)
        {
            $item_info = array();

            //chi xu ly folder va file
            if ($item != '.' && $item != '..')
            {
                $item_info['date'] = date("d/m/Y H:i", filemtime($dir_path . $item));
                $item_info['path'] = $dir_name . DS . $item;
                
                if (is_dir($dir_path . $item)) //neu la folder
                {
                    $item_info['type'] = (string) __('directory');
                    $arr_folder[$item] = $item_info;
                }
                else //neu la file
                {
                    $item_info['path'] = str_replace('\\', '/', $item_info['path']);
                    $item_info['type'] = strtoupper(substr($item, strrpos($item, '.') + 1));
                    $viewall   = (bool) empty($allowed_extensions);
                    $inallowed = (bool) in_array($item_info['type'], $allowed_extensions);
                    $isindex   = ($item == 'index.htm' or $item == 'index.html') ? true : false;
                    if (($viewall or $inallowed) && !$isindex)
                    {
                        $arr_file[$item] = $item_info;
                    }
                }
            }
        }
        echo json_encode(array('folder' => $arr_folder, 'file'   => $arr_file));
        return;
    }

    function dsp_item_details()
    {
        $path  = isset($_REQUEST['path']) ? $_REQUEST['path'] : '';
        $fname = SERVER_ROOT . 'uploads/public_service' . DS . $path;
        $furl  = SITE_ROOT . 'uploads/public_service/' . str_replace('\\', '/', $path);

        if (file_exists($fname) && check_permission('XEM_DANH_SACH_MEDIA', false))
        {
            $data['fname'] = $fname;
            $data['furl']  = $furl;
            $this->view->render('dsp_item_details', $data);
        }
    }

    function create_dir()
     {
        check_permission('THEM_MOI_MEDIA', $this->app_name) or $this->access_denied();
        $parent_dir = SERVER_ROOT . 'uploads/public_service' . get_post_var('path', DS, false);
        $dirname    = auto_slug(get_post_var('dirname', 'new folder'));
        $msg        = $this->model->create_dir($parent_dir, $dirname);

        echo json_encode($msg);
    }

    public function delete_items()
    {
        check_permission('XOA_MEDIA', $this->app_name) or $this->access_denied();
        $items      = get_post_var('items', array(), false);
        $parent_dir = SERVER_ROOT . 'uploads/public_service' . DS . get_post_var('parent_dir', '', false);
        $msg = $this->model->delete_items($parent_dir, $items);
        
        echo json_encode($msg);
    }

    function upload()
    {
        $error              = "";
        $fileElementName    = $_POST['file_element'];
        $folder             = $_POST['folder'];
        $fnam               = $_FILES[$fileElementName]['name'];
        $file_extension     = substr($fnam, strrpos($fnam, '.') + 1);
        $fname_without_ext  = current(explode(".", $fnam));
        $allowed_extensions = explode(',', str_replace(' ', '', Session::get(EXT_SESSION_KEY)));
        $error_code         = 0;
        if (!empty($allowed_extensions) && !in_array(strtoupper($file_extension), $allowed_extensions))
        {
            $error_code = 8;
        }

        if (file_exists($folder) && !is_dir($folder))
        {
            $folder = dirname($folder);
        }

        if (check_permission('THEM_MOI_MEDIA', false) == false)
        {
            $error_code = 7;
        }

        if ($error_code)
        {
            $_FILES[$fileElementName]['error'] = $error_code;
        }

        if (!empty($_FILES[$fileElementName]['error']))
        {
            switch ($_FILES[$fileElementName]['error'])
            {

                case '1':
                    $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                    break;
                case '2':
                    $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                    break;
                case '3':
                    $error = 'The uploaded file was only partially uploaded';
                    break;
                case '4':
                    $error = 'No file was uploaded.';
                    break;

                case '6':
                    $error = 'Missing a temporary folder';
                    break;
                case '7':
                    $error = 'Failed to write file to disk';
                    break;
                case '8':
                    $error = 'File upload stopped by extension';
                    break;
                case '999':
                default:
                    $error = 'No error code avaiable';
            }
        }
        elseif (empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
        {
            $error = 'No file was uploaded..';
        }
        else
        {

            $fnam = auto_slug($fname_without_ext) . '.' . $file_extension;
            $path = SERVER_ROOT . 'uploads/public_service' . DS . trim($folder, '/\\') . DS;
            $size = @filesize($_FILES[$fileElementName]['tmp_name']);

            //for security reason, we force to remove uploaded file
            //Use move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $destination) instead.
            //@unlink($_FILES[$fileElementName]['tmp_name']);
            if (file_exists($path . $fnam))
            {
                $fnam = $fname_without_ext . '-' . date('Y.m.d.H.i.s') . '.' . $file_extension;
            }
            move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $path . $fnam);
        }

        $res = new stdClass();

        $res->error    = $error;
        $res->filename = $fnam;
        $res->path     = $path;
        $res->size     = sprintf("%.2fMB", $size / 1048576);
        $res->dt       = date('Y-m-d H:i:s');
        die(json_encode($res));
    }

    function update_db()
    {
        $this->model->update_db();
    }

}
