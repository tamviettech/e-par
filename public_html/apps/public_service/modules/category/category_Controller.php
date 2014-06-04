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

class category_Controller extends Controller
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

        parent::__construct('public_service', 'category');
        $this->view->arr_count_article     = $this->model->gp_qry_count_article();
        $this->view->dsp_side_bar =true;
        $this->model->app_name = $this->app_name;
    }

    public function main()
    {
        $this->view->title = 'Chuyên mục';
        $this->view->layout_render('public_service/admin/dsp_layout_admin','main');
    }

    public function dsp_all_category()
    {
        //quyen truy cap
        if (!check_permission('XEM_DANH_SACH_CHUYEN_MUC',$this->app_name)) $this->access_denied();
        $this->view->title = 'Chuyên mục';
        $VIEW_DATA['arr_all_category'] = $this->model->qry_all_category();
        $this->view->render('dsp_all_category', $VIEW_DATA);
    }

    public function swap_category_order()
    {
        //quyen truy cap
        if (!check_permission('SUA_CHUYEN_MUC',$this->app_name)) $this->access_denied();
        
        //validate
        $item1 = get_request_var('item1', 0);
        $item2 = get_request_var('item2', 0);
      
        if (intval($item2) == $item2 && intval($item1) == $item1)
        {
            $this->model->swap_category_order($item1, $item2);
        }
        else
        {
            echo 'Du lieu khong hop le';
        }
    }

    public function dsp_single_category($id = 0)
    {
        //quyen truy cap
        if (check_permission('SUA_CHUYEN_MUC',$this->app_name) == false)
            $this->access_denied();

        $id                               = intval($id);
        $other_clause = '';
        $VIEW_DATA['arr_single_category'] = $this->model->qry_single_category($id);
        if (empty($VIEW_DATA['arr_single_category']) && $id != 0)
        {
            die(__('this object is nolonger available!'));
        }
        if ($id)
        {
            $internal_order = $VIEW_DATA['arr_single_category']['C_INTERNAL_ORDER'];
            $other_clause .= " And C_INTERNAL_ORDER Not Like '$internal_order%' ";
        }

        $VIEW_DATA['arr_all_category'] = $this->model->qry_all_category($other_clause);
        
        $this->view->layout_render('public_service/admin/dsp_layout_admin','dsp_single_category', $VIEW_DATA);
    }

    public function update_category($no_use_var = 0)
    {
        //quyen truy cap
        if (check_permission('SUA_CHUYEN_MUC',$this->app_name) == false)
            $this->access_denied();
        $this->view->title = 'Chuyên mục';
        $this->model->update_category();
    }

    public function delete_category()
    {
        //quyen truy cap
        if (check_permission('XOA_CHUYEN_MUC',$this->app_name) == false)
            $this->access_denied();

        $this->model->delete_category();
    }

    public function dsp_featured_category()
    {
        //quyen truy cap
        if (check_permission('XEM_DANH_SACH_CHUYEN_MUC_NOI_BAT',$this->app_name) == false)
            $this->access_denied();

        $view_data['arr_all_featured'] = $this->model->qry_all_featured();
        $this->view->render('dsp_featured_category', $view_data);
    }

    //cac $_GET option:
    public function dsp_all_category_svc()
    {
        //quyen truy cap
        if (check_permission('XEM_DANH_SACH_CHUYEN_MUC',$this->app_name) == false)
            $this->access_denied();
        $this->view->title = 'Chuyên mục';
        $VIEW_DATA['arr_all_category'] = $this->model->qry_all_category();
        $this->view->layout_render('public_service/admin/dsp_layout_admin_pop_win','dsp_all_category_svc', $VIEW_DATA);
    }

    public function insert_featured_category()
    {
        //quyen truy cap
        if (check_permission('THEM_MOI_CHUYEN_MUC_NOI_BAT',$this->app_name) == false)
            $this->access_denied();

        $this->model->insert_featured_category();
    }

    public function delete_featured_category()
    {
        //quyen truy cap
        if (check_permission('XOA_CHUYEN_MUC_NOI_BAT',$this->app_name) == false)
            $this->access_denied();

        $this->model->delete_featured_category();
    }

    public function swap_featured_order()
    {
        //quyen truy cap
        if (check_permission('SUA_CHUYEN_MUC_NOI_BAT',$this->app_name) == false)
            $this->access_denied();
        $this->model->swap_featured_order();
    }

}

?>
