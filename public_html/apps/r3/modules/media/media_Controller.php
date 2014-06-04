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

class media_Controller extends Controller
{

    /**
     *
     * @var \report_Model
     */
    public $model;

    /**
     *
     * @var View 
     */
    public $view;
    protected $_arr_user_role   = array();

    public function __construct()
    {
        //kiem tra dang nhap
        session::init();
       //Kiem tra dang nhap
        session::check_login();
        
        parent::__construct('r3', 'media');
        //tao array role tu r3_const
        $this->_arr_roles = json_decode(CONST_ALL_R3_ROLES,true);
        $this->view->template->show_left_side_bar = TRUE;
        $this->view->template->controller_url     = $this->view->get_controller_url();
        $this->view->role_text                    = $this->_arr_roles;
        //tao controller url record cho menu
        $this->view->template->controller_url = $this->view->get_controller_url('record','r3');
        
        //tao menu 
        $menu = Array();

        $arr_my_role = $this->model->qry_all_user_role(Session::get('user_code'));
        foreach ($this->_arr_roles as $key => $val)
        {
            if ($this->check_permission($key) && in_array($key, $arr_my_role))
            {
                $menu[$key]             = $val;
                $this->_arr_user_role[] = strtoupper($key);
            }
        }

        $arr_not_admin_roles = array(_CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE
            , _CONST_KIEM_TRA_TRUOC_HO_SO_ROLE, _CONST_RUT_ROLE);
        $is_admin            = (bool) Session::get('is_admin');
        foreach ($arr_not_admin_roles as $role)
        {
            if ($this->check_permission($role) && !$is_admin)
            {
                $menu[$role]            = $this->_arr_roles[$role];
                $this->_arr_user_role[] = $role;
            }
        }

        $arr_more_roles = array(_CONST_Y_KIEN_LANH_DAO_ROLE, _CONST_TRA_CUU_ROLE
            , _CONST_TRA_CUU_LIEN_THONG_ROLE, _CONST_TRA_CUU_TAI_XA_ROLE, _CONST_BAO_CAO_ROLE);
        foreach ($arr_more_roles as $role)
        {
            if ($this->check_permission($role))
            {
                $menu[$role]            = $this->_arr_roles[$role];
                $this->_arr_user_role[] = $role;
            }
        }
        $this->view->template->arr_roles = $menu;
        $this->view->arr_roles           = $menu;
        
        $this->view->DATETIME_NOW        = $this->model->get_datetime_now();
    }

    public function main()
    {
       $this->dsp_main('dsp_all_media');
    }
    
    
    /**
     * layout hien thi
     * @param type $function: ten funtion lay html
     */
    public function dsp_main($function = '')
    {
        $html_content = '';
        
        //kiem tra method co ton tai khong
        if(method_exists(get_class(), $function))
        {
            ob_start();
            $this->$function();
            $html_content = ob_get_clean();
        }
        
        $VIEW_DAtA['html_content'] = $html_content;
        
        $this->view->render('dsp_main',$VIEW_DAtA);
    }
    
    /**
     * hien thi danh sach file da phuong tien
     */
    public function dsp_all_media()
    {
        $this->view->template->active_role = '';
        
        $VIEW_DATA['arr_all_folder'] = $this->model->qry_all_folder();
        $VIEW_DATA['arr_all_user_share_to_you'] = $this->model->qry_all_user_share_to_you();
        
        $this->view->render('dsp_all_media',$VIEW_DATA);
    }
    
    /**
     * hien thi man hinh upload
     */
    public function dsp_upload()
    {
        $this->view->render('dsp_upload');
    }
    
    /**
     * thuc hien upload file
     */
    public function do_upload()
    {
        $folder_id     = get_post_var('hdn_folder_id','');
        $share_user_id = get_post_var('hdn_share_user_id','');
        
        $this->model->do_upload($folder_id,$share_user_id);
    }
    
    /**
     * tao thu muc moi
     */
    public function create_folder()
    {
        //set debug
        if(DEBUG_MODE > 10)
        {
            $this->model->db->debug = 1;
        }
        else
        {
            $this->model->db->debug = 0;
        }
        
        //data post
        $v_parent_id = get_post_var('parent_id','');
        $v_dirname   = get_post_var('dir_name','');
        
        $arr_info = $this->model->create_folder($v_parent_id,$v_dirname);
        
        echo json_encode($arr_info);
    }
    
    /**
     * danh sach media cua folder
     */
    public function dsp_media_of_folder()
    {
        //du lieu
        $v_folder_id  = get_post_var('folder_id','');
        $v_is_share   = get_post_var('is_share','');
        $v_user_share = get_post_var('user_share','');
        //lay tat ca media cua folder
        $arr_data = $this->model->qry_media_of_folder($v_folder_id,$v_is_share,$v_user_share);
        
        $VIEW_DATA['arr_media_of_folder'] = $arr_data['arr_media_of_folder'];
        $VIEW_DATA['parent_folder_id'] = $arr_data['parent_folder_id'];
        
        $VIEW_DATA['is_share'] = $v_is_share;
        
        $this->view->render('dsp_media_of_folder',$VIEW_DATA);
    }
    
    /**
     * delete media
     */
    public function do_delete_media()
    {
        $this->model->db->debug = 0;
        if(DEBUG_MODE > 10)
        {
            $this->model->db->debug = 1;
        }
        
        $v_list_id = get_post_var('hdn_item_id_list','');
        $v_user_id = get_post_var('user_id','');
        if($v_user_id != '' OR $v_user_id != NULL)
        {
            $arr_info = $this->model->do_delete_media($v_list_id,$v_user_id);
        }
        else
        {
            $arr_info = $this->model->do_delete_media($v_list_id);
        }
        echo json_encode($arr_info);
    }
    
    /**
     * thuc hien doi ten media
     */
    public function do_rename_media()
    {
        $v_id      = get_post_var('v_id','');
        $v_newname = get_post_var('new_name','');
        $message   = $this->model->do_rename_media($v_id,$v_newname);
        
        echo $message;
    }
    
    /**
     * chia se file media
     */
    public function dsp_share_media()
    {
        $v_media_id = get_request_var('media_id','');
        //kiem tra dieu kien
        if($v_media_id == '' OR $v_media_id == NULL OR is_numeric($v_media_id) == FALSE)
        {
            die('Bạn cần chọn thư mục để chia sẻ');
        }
        //lay tat ca NSD da duoc share voi media
        $VIEW_DATA['arr_all_user_shared'] = $this->model->qry_all_user_shared($v_media_id);
        //media info
        $VIEW_DATA['arr_info_of_media'] = $this->model->qry_info_of_media($v_media_id);
        
        $this->view->render('dsp_share_media',$VIEW_DATA);
    }
    
    /**
     * hien thi danh sach tat ca NSD
     */
    public function dsp_all_users_to_add()
    {
        $VIEW_DATA['arr_all_user_to_add'] = $this->model->qry_all_user_by_ou_to_add();

        $this->view->render('dsp_all_user_to_add', $VIEW_DATA);   
    }
    
    /**
     * share media
     */
    public function do_share_media()
    {
        $this->model->do_share_media();
    }
    
    /**
     * hien thi danh sach media share
     */
    public function dsp_all_share_to_you()
    {
        $v_user_share     = get_post_var('user_id','');
        $parent_folder_id = get_post_var('parent_folder_id','');
        
        $VIEW_DATA['arr_all_media_share'] = $this->model->qry_all_media_share_to_you($v_user_share);
        $VIEW_DATA['parent_folder_id'] = $parent_folder_id;
        
        $this->view->render('dsp_all_share_to_you',$VIEW_DATA);
    }
}