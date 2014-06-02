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

class mapping_Controller extends Controller
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
        
        parent::__construct('r3', 'mapping');
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
        $this->view->active_role = $this->view->template->active_role = '';
        
        $this->model->goback_url = $this->view->get_controller_url();
    }

    public function main()
    {
       $this->dsp_main();
    }
    
    public function dsp_main()
    {
        $VIEW_DATA['arr_all_mapping']     = $this->model->qry_all_mapping();
        $VIEW_DATA['arr_all_record_type'] = $this->model->qry_all_record_type();
        $this->view->render('dsp_main',$VIEW_DATA);
    }  
    
    public function update_mapping()
    {
        
        $this->model->update_mapping();
    }
}