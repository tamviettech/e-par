<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class user_Controller extends Controller {

    function __construct()
    {
        deny_bad_http_referer();
        
        parent::__construct('cores', 'user');
        $this->view->template->show_left_side_bar =FALSE;

        //Kiem tra dang nhap
        session::check_login();
    }

    function main()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $this->dsp_all_sub_ou();
    }

    function dsp_all_sub_ou($ou_id=-1)
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $ou_id = replace_bad_char($ou_id);
        if (!( preg_match( '/^\d*$/', trim($ou_id)) == 1 ))
        {
            $ou_id = $this->model->get_root_ou();
        }

        $VIEW_DATA['ou_id'] = $ou_id;
        $VIEW_DATA['arr_all_sub_ou']        = $this->model->qry_all_sub_ou($ou_id);
        $VIEW_DATA['arr_all_user_by_ou']    = $this->model->qry_all_user_by_ou($ou_id);
        $VIEW_DATA['arr_ou_path']           = $this->model->qry_ou_path($ou_id);
        $VIEW_DATA['arr_all_group_by_ou']   = $this->model->qry_all_group_by_ou($ou_id);

        $this->view->render('dsp_all_sub_ou', $VIEW_DATA);
    }

    public function dsp_single_user($user_id)
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $user_id = replace_bad_char($user_id);
        if (!is_id_number($user_id))
        {
            $user_id = 0;
        }

        $v_parent_ou_id = get_request_var('parent_ou_id',0);

        $VIEW_DATA['arr_parent_ou_path']            = $this->model->qry_ou_path($v_parent_ou_id);
        $VIEW_DATA['arr_all_application_option']    = $this->model->qry_all_application_option();

        //Cac Group ma NSD nay dang tham gia
        $VIEW_DATA['arr_all_group_by_user']         = $this->model->qry_all_group_by_user($user_id);

        //Thong tin chi tiet
        $VIEW_DATA['arr_single_user']               = $this->model->qry_single_user($user_id);
        $this->view->render('dsp_single_user', $VIEW_DATA);
    }

    public function dsp_single_group($group_id)
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $group_id = replace_bad_char($group_id);
        if (!is_id_number($group_id))
        {
            $group_id = 0;
        }

        $v_parent_ou_id = get_request_var('parent_ou_id');
        $VIEW_DATA['arr_parent_ou_path']    = $this->model->qry_ou_path($v_parent_ou_id);

        $VIEW_DATA['arr_single_group']      = $this->model->qry_single_group($group_id);
        $VIEW_DATA['arr_all_user_by_group'] = $this->model->qry_all_user_by_group($group_id);

        $VIEW_DATA['arr_all_application_option']    = $this->model->qry_all_application_option();

        $this->view->render('dsp_single_group', $VIEW_DATA);
    }

    public function dsp_single_ou($ou_id)
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $ou_id = replace_bad_char($ou_id);
        if (!is_id_number($ou_id))
        {
            $ou_id = 0;
        }

        $v_parent_ou_id = replace_bad_char($_REQUEST['parent_ou_id']);

        $VIEW_DATA['arr_parent_ou_path']    = $this->model->qry_ou_path($v_parent_ou_id);
        $VIEW_DATA['arr_single_ou']         = $this->model->qry_single_ou($ou_id);
        
        $this->view->render('dsp_single_ou', $VIEW_DATA);
    }

    public function dsp_ou_tree()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $VIEW_DATA['arr_ou_tree'] = $this->model->qry_ou_tree();
        $this->view->render('dsp_ou_tree', $VIEW_DATA);
    }

    public function update_ou()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $v_parent_ou_id       = replace_bad_char($_POST['hdn_parent_ou_id']);

        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_sub_ou/' . $v_parent_ou_id;
        $this->model->update_ou();
    }

    public function delete_ou()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $v_parent_ou_id       = $this->get_post_var('hdn_current_ou_id');
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_sub_ou/' . $v_parent_ou_id;
        $this->model->delete_ou();
    }

    public function update_user()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $v_parent_ou_id       = $this->get_post_var('hdn_current_ou_id');

        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_sub_ou/' . $v_parent_ou_id;
        $this->model->update_user();
    }

    public function delete_user()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $v_parent_ou_id       = $this->get_post_var('hdn_current_ou_id');
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_sub_ou/' . $v_parent_ou_id;

        $this->model->delete_user();
    }

    public function update_group()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $v_parent_ou_id       = $this->get_post_var('hdn_current_ou_id');
        $this->model->goback_url    = $this->view->get_controller_url() . 'dsp_all_sub_ou/' . $v_parent_ou_id;

        $this->model->update_group();
    }
    public function delete_group()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $v_parent_ou_id       = $this->get_post_var('hdn_current_ou_id');
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_sub_ou/' . $v_parent_ou_id;

        $this->model->delete_group();
    }

    public function dsp_all_user_to_add()
    {
        //Kiem tra quyen
        //(Session::get('is_admin') == 1) Or die($this->access_denied());

        $VIEW_DATA['arr_all_user_to_add'] = $this->model->qry_all_user_to_add();
        $this->view->render('dsp_all_user_to_add', $VIEW_DATA);
    }

    //Dich vu hien thi danh sach NSD theo phong ban (vật lý)
    public function dsp_all_user_by_ou_to_add()
    {
        $VIEW_DATA['arr_all_user_to_add'] = $this->model->qry_all_user_by_ou_to_add();

        $this->view->render('dsp_all_user_by_ou_to_add', $VIEW_DATA);
    }

    public function dsp_all_group_to_add()
    {
        $VIEW_DATA['arr_all_group_to_add'] = $this->model->qry_all_group_to_add();
        $this->view->render('dsp_all_group_to_add', $VIEW_DATA);
    }

    public function dsp_all_user_and_group_to_add()
    {
        $v_my_dept_only = isset($_GET['my_dept_only']) ? replace_bad_char($_GET['my_dept_only']) : 0;
        $VIEW_DATA['arr_all_user_to_add'] = $this->model->qry_all_user_to_add($v_my_dept_only);
        $VIEW_DATA['arr_all_group_to_add'] = $this->model->qry_all_group_to_add($v_my_dept_only);
        $this->view->render('dsp_all_user_and_group_to_add', $VIEW_DATA);
    }

    //HIen thi danh sach tat ca user, group de phan quyen
    public function dsp_all_user_and_group_to_grand()
    {
        $v_filter = '';
        if (isset($_POST['txt_filter']))
        {
            $v_filter = $this->model->replace_bad_char($_POST['txt_filter']);
        }

        $VIEW_DATA['arr_all_user_to_grand']     = $this->model->qry_all_user_to_grand($v_filter);
        $VIEW_DATA['arr_all_group_to_grand']    = $this->model->qry_all_group_to_grand($v_filter);
        $VIEW_DATA['filter']                    = $v_filter;

        $this->view->render('dsp_all_user_and_group_to_grand', $VIEW_DATA);
    }

    function dsp_single_user_to_grand()
    {
        $VIEW_DATA['user_id'] = $this->model->replace_bad_char($_REQUEST['hdn_item_id']);
        $VIEW_DATA['user_name'] = $this->model->replace_bad_char($_REQUEST['hdn_item_name']);
        $VIEW_DATA['user_type'] = $this->model->replace_bad_char($_REQUEST['type']);

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['user_id'])) == 1 ))
        {
           exit;
        }

        $VIEW_DATA['arr_all_application_option']    = $this->model->qry_all_application_option();

        $this->view->render('dsp_single_user_to_grand', $VIEW_DATA);
    }
    function dsp_single_group_to_grand()
    {
        $VIEW_DATA['group_id'] = $this->model->replace_bad_char($_REQUEST['hdn_item_id']);
        $VIEW_DATA['group_name'] = $this->model->replace_bad_char($_REQUEST['hdn_item_name']);

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['user_id'])) == 1 ))
        {
           exit;
        }

        $VIEW_DATA['arr_all_application_option']    = $this->model->qry_all_application_option();

        $this->view->render('dsp_single_group_to_grand', $VIEW_DATA);
    }


    public function arp_user_permit_on_application()
    {
        $VIEW_DATA['user_id']           = isset($_REQUEST['user_id']) ? $this->model->replace_bad_char($_REQUEST['user_id']) : 0;
        $VIEW_DATA['application_id']    = isset($_REQUEST['app_id']) ? $this->model->replace_bad_char($_REQUEST['app_id']) : 0;

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['user_id'])) == 1 ))
        {
           exit;
        }

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['application_id'])) == 1 ))
        {
           exit;
        }

        echo json_encode($this->model->qry_single_user_permit_on_application($VIEW_DATA['user_id'] , $VIEW_DATA['application_id']));
    }

    public function arp_group_permit_on_application()
    {
        $VIEW_DATA['group_id']           = isset($_REQUEST['group_id']) ? $this->model->replace_bad_char($_REQUEST['group_id']) : 0;
        $VIEW_DATA['application_id']    = isset($_REQUEST['app_id']) ? $this->model->replace_bad_char($_REQUEST['app_id']) : 0;

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['group_id'])) == 1 ))
        {
           exit;
        }

        if (!( preg_match( '/^\d*$/', trim($VIEW_DATA['application_id'])) == 1 ))
        {
           exit;
        }

        echo json_encode($this->model->qry_single_group_permit_on_application($VIEW_DATA['group_id'] , $VIEW_DATA['application_id']));
    }

    public function update_user_permit()
    {
        $this->model->update_user_permit();
    }
    public function update_group_permit()
    {
        $this->model->update_group_permit();
    }

    public function dsp_change_password()
    {
        $this->view->render('dsp_change_password');
    }

    public function do_change_password()
    {
       $this->model->do_change_password();
    }
    public function dsp_all_ou_to_add()
    {
        $VIEW_DATA['arr_all_ou'] = $this->model->qry_all_ou();
        $this->view->render('dsp_all_ou_to_add',$VIEW_DATA);
    }
}