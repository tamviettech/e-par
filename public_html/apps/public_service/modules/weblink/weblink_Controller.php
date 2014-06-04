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

class weblink_Controller extends Controller
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

        parent::__construct('public_service', 'weblink');
        
        $this->view->arr_count_article = $this->model->gp_qry_count_article();
        $this->model->app_name = $this->app_name;
        $this->view->dsp_side_bar = TRUE;
        $this->model->goback_url = $this->view->get_controller_url();
    }

    public function main()
    {
        if(!check_permission('XEM_DANH_SACH_WEBLINK',$this->app_name)) $this->access_denied();

        $data['arr_all_weblink'] = $this->model->qry_all_weblink();
        $this->view->layout_render('public_service/admin/dsp_layout_admin','dsp_main', $data);
    }

    public function dsp_single_weblink($id)
    {
        if ($id == 0)
        {
            if(!check_permission('THEM_MOI_WEBLINK',$this->app_name)) $this->access_denied();
        }
        else
        {
            if(!check_permission('SUA_WEBLINK',$this->app_name)) $this->access_denied();
        }

        $id = (int) $id;

        $data['v_id'] = $id;
        $data['arr_single_weblink'] = array();
        if ($id > 0)
        {
            $data['arr_single_weblink'] = $this->model->qry_single_weblink($id);
        }
        
         $data['arr_all_group_type'] = $this->model->qry_all_group_type();

        $this->view->layout_render('public_service/admin/dsp_layout_admin','dsp_single_weblink', $data);
    }

    public function update_weblink()
    {
        if(!check_permission('SUA_WEBLINK',$this->app_name)) $this->access_denied();
        
        $this->model->update_weblink();
    }
    
    public function swap_order()
    {
        if(!check_permission('SUA_WEBLINK',$this->app_name))  $this->access_denied();
        
        $item1 = get_post_var('item1');//id
        $item2 = get_post_var('item2');//id swap
        
        $table = 't_ps_weblink';        
        $pk_col = 'PK_WEBLINK';
        $order_col = 'C_ORDER';
        $this->model->swap_order($table, $pk_col, $order_col, $item1, $item2);
    }

    public function delete_weblink()
    {
        if(!check_permission('XOA_WEBLINK',$this->app_name)) $this->access_denied();
        $this->model->delete_weblink();
    }
    
}

?>
