<?php
defined('DS') or die('no direct access');

class sticky_Controller extends Controller
{
    function __construct()
    {
        parent::__construct('public_service', 'sticky');
        @session::init();
        $login_name = session::get('login_name');
        
        if ($login_name == NULL)
        {
            session::destroy();
            header('location:' . SITE_ROOT . 'login.php');
            exit;
        }
        
        $this->view->arr_count_article     = $this->model->gp_qry_count_article();
        $this->view->dsp_side_bar          = TRUE;
        $this->model->goback_url           = $this->view->get_controller_url();
        $this->model->app_name              = $this->app_name;
    }

    function main()
    {
        $this->view->title = 'Quản lý tin bài nổi bật';
        $this->view->layout_render('public_service/admin/dsp_layout_admin','main');
    }

    function dsp_all_sticky($v_default = 0)
    {
        check_permission('XEM_DANH_SACH_NOI_BAT',$this->app_name) or $this->access_denied();
        $v_default    = intval($v_default);
        $other_clause = '';
        if ($v_default < 0 or $v_default > 2)
        {
            die(__('invalid request data'));
        }
        
        if ($v_default == 0)
        {
            $v_website_id = Session::get('session_website_id');
            $v_website_id         = (isset($v_website_id) >0) ? (int)$v_website_id : 0;
            $other_clause             = ' And FK_WEBSITE =' . $v_website_id;
            
            $data['arr_all_category'] = $this->model->qry_all_category($other_clause);
            $default_category         = isset($data['arr_all_category'][0]) ? $data['arr_all_category'][0]['PK_CATEGORY'] : 0;
            //reset other_clause
            //other_clause cho sticky
            $v_category               = intval(get_request_var('sel_category', $default_category));
            $other_clause             = ' And S.FK_CATEGORY = ' . $v_category;
        }
        //lay tat ca danh sach tin noi bat tren trang chu
        else if($v_default == 1)
        {
            $other_clause = ' And C_DEFAULT = ' . $v_default;
        }
        //lay tat ca tin dang chu y trong ngay
        else if($v_default == 2)
        {
            $other_clause = ' And C_TYPE = ' . $v_default;
        }

        $data['v_default']      = $v_default;
        $data['arr_all_sticky'] = $this->model->qry_all_sticky($other_clause);
        $this->view->render('dsp_all_sticky', $data);
    }

    function insert_sticky()
    {
        check_permission('THEM_MOI_NOI_BAT',$this->app_name) or $this->access_denied();
        $this->model->insert_sticky();
    }

    function delete_sticky()
    {
        check_permission('XOA_NOI_BAT',$this->app_name) or $this->access_denied();
        $this->model->delete_sticky();
    }

    function swap_sticky_order()
    {
        check_permission('SUA_NOI_BAT',$this->app_name) or $this->access_denied();
        $this->model->swap_sticky_order();
    }
}