<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class folder_Controller extends Controller {

    function __construct() {
        parent::__construct('edoc', 'folder');
        $this->view->template->show_left_side_bar = FALSE;
        $this->view->template->doc_direction = 'FOLDER';


        //Kiem tra session
        session::init();
        //Kiem tra dang nhap
        session::check_login();
    }

    function main()
    {
        $this->dsp_all_folder();
    }

    public function dsp_all_folder()
    {
        $v_filter = isset($_POST['txt_filter']) ? $this->model->replace_bad_char($_POST['txt_filter']) : '';

        $VIEW_DATA['arr_all_folder'] = $this->model->qry_all_folder($v_filter);
        $VIEW_DATA['txt_filter'] = $v_filter;
        $this->view->render('dsp_all_folder', $VIEW_DATA);
    }

    public function dsp_single_folder()
    {
        $v_folder_id = isset($_POST['hdn_item_id']) ? $this->model->replace_bad_char($_POST['hdn_item_id']) : 0;
        if (!( preg_match( '/^\d*$/', trim($v_folder_id)) == 1 ))
        {
            $v_folder_id = 0;
        }

        $VIEW_DATA['arr_single_folder']         = $this->model->qry_single_folder($v_folder_id);
        $VIEW_DATA['arr_all_doc_by_folder']     = $this->model->qry_all_doc_by_folder($v_folder_id);
        $VIEW_DATA['arr_all_shared_user']       = $this->model->qry_all_shared_user($v_folder_id);
        $VIEW_DATA['arr_all_shared_ou']         = $this->model->qry_all_shared_ou($v_folder_id); 

        $this->view->render('dsp_single_folder', $VIEW_DATA);
    }

    public function update_folder()
    {
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_folder';
        $this->model->update_folder();
    }

    public function delete_folder()
    {
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_folder';
        $this->model->delete_folder();
    }
}