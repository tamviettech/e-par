<?php
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class xlist_Controller extends Controller {

    function __construct() {
        parent::__construct('cores', 'xlist');
        $this->model->goback_url = $this->view->get_controller_url();
        $this->view->template->show_left_side_bar = FALSE;

        //Kiem tra dang nhap
        $this->check_login();
    }

    //main function
    public function main()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $this->dsp_all_listtype();
    }

    //listtype
    public function dsp_all_listtype()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $VIEW_DATA['v_filter']         = get_post_var('txt_filter');
        $VIEW_DATA['arr_all_listtype'] = $this->model->qry_all_listtype();

        $this->view->render('dsp_all_listtype',$VIEW_DATA);
    }

    public function dsp_single_listtype()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $d['arr_single_listtype'] = $this->model->qry_single_listtype();
        $this->view->render('dsp_single_listtype',$d);
    }

    public function dsp_all_file_xml()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $this->view->render('dsp_all_file_xml');
    }

    public function update_listtype()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_listtype';
        $this->model->goforward_url = $this->view->get_controller_url() . 'dsp_single_listtype' ;
        $this->model->update_listtype();
    }

    public function delete_listtype(){
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_listtype';
        $this->model->delete_listtype();

    }

    public function check_existing_listtype_code()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        echo $this->model->check_existing_listtype_code();
    }
    public function check_existing_listtype_name(){
        //Kiem tra quyen
       (Session::get('is_admin') == 1) Or die($this->access_denied());

        echo $this->model->check_existing_listtype_name();
    }

    //List
    public function check_existing_list_code()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        echo $this->model->check_existing_list_code();
    }

    public function check_existing_list_name()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        echo $this->model->check_existing_list_name();
    }

    public function dsp_all_list()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        //Dieu kien loc
        $VIEW_DATA['v_listtype_id']            = get_post_var('sel_listtype_filter');
        $VIEW_DATA['v_filter']                 = get_post_var('txt_filter');

        $VIEW_DATA['arr_all_listtype_option']  = $this->model->qry_all_listtype_option();
        $VIEW_DATA['arr_all_list']             = $this->model->qry_all_list();

        $this->view->render('dsp_all_list', $VIEW_DATA);
    }

    public function dsp_single_list()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());


        $d['arr_all_listtype_option'] = $this->model->qry_all_listtype_option();
        $d['arr_single_list'] = $this->model->qry_single_list();
        $this->view->render('dsp_single_list',$d);
    }

    public function update_list()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_list';
        $this->model->goforward_url = $this->view->get_controller_url() . 'dsp_single_list' ;
        $this->model->update_list();
    }

    public function delete_list()
    {
        //Kiem tra quyen
       (Session::get('is_admin') == 1) Or die($this->access_denied());

        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_list';
        $this->model->delete_list();
    }
}