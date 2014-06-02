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
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class guidance_Controller extends Controller
{
  
    public  $get_controller_url;
    function __construct() 
    {
        parent:: __construct('guidance', 'guidance');
        $this->get_controller_url  = $this->view->get_controller_url();
        
         
        $this->view->get_apps_url                           = $this->view->template->get_apps_url   = SITE_ROOT.'apps/guidance/';
        $this->view->get_apps_url_bootstrap                 = $this->view->template->get_apps_url_bootstrap   = SITE_ROOT.'apps/guidance/bootstrap/';
        
        $this->view->template->get_apps_url_plugin          = $this->view->get_apps_url_plugin = $this->view->template->get_apps_url.'bootstrap/plugins/';
        $this->view->template->get_images_url               = $this->view->get_images_url = $this->view->template->get_apps_url.'images/';
        
        $this->view->get_apps_directory                     = $this->view->template->get_apps_directory   = SERVER_ROOT . 'apps'.DS.'guidance'.DS;
        $this->view->get_images_directory                   = $this->view->template->get_images_directory   = SERVER_ROOT . 'apps'.DS.'guidance'.DS;
        $this->view->get_url_image_directory                = SERVER_ROOT. DS . 'apps' . DS . 'guidance'. DS . 'images' .DS;
        $this->view->template->get_url_image_directory      = $this->view->get_url_image_directory;
        $this->view->get_url_image                         = SITE_ROOT . 'apps/guidance/images/';
        $this->view->template->get_url_image               = $this->view->get_url_image;
        //check mobile
        $v_file = SERVER_ROOT . 'apps'.DS.'guidance'.DS. 'lib'.DS.'mobile_device.php';          
       
        $detect = 0;
        if(file_exists($v_file))
        {
            require_once $v_file;
            $detect = (isMobile())? 1 : 0;
        }
        $this->view->template->detect = $this->view->detect = $detect;
        
        unset($detect);
        
        
    }

    public function main()
    {
        $VIEW_DATA['arr_all_guidance'] = $this->model->qry_all_linh_vuc();
        $this->view->render('dsp_all_guidance',$VIEW_DATA);
    }
    public function dsp_all_list_guidance($v_id)
    {
        $v_id       = intval(replace_bad_char($v_id));
        $v_keyword = isset($_REQUEST['keyword']) ? strval(replace_bad_char($_REQUEST['keyword'])) : '';
        if(!trim($v_keyword))
        {
            //neu khong co tu khoa tim kiem 
            $VIEW_DATA['arr_all_list_guidance'] = $this->model->qry_all_list_thu_tuc($v_id);
        }
        else 
        {
            // Ton tai tu khoa tim kiem
            $VIEW_DATA['arr_all_list_guidance'] = $this->model->qry_all_list_thu_tuc($v_id,$v_keyword);
            
        }
    
        $this->view->render('dsp_all_list_guidance',$VIEW_DATA);
    }
    public function dsp_single_guidance($v_id)
    {
        $v_id       = intval(replace_bad_char($v_id));
        $VIEW_DATA['arr_single_guidance'] = $this->model->qry_single_guidance($v_id);
        $this->view->render('dsp_single_guidance',$VIEW_DATA);
    } 
}