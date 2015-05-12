<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class license_type_Controller extends Controller
{

    function __construct()
    {
        deny_bad_http_referer();
        parent::__construct('license', 'license_type');
        $this->view->template->show_left_side_bar = FALSE;
        session::check_login();
    }

    function main()
    {
        $this->dsp_all_license_type();
    }

    function dsp_all_license_type()
    {
        check_permission('XEM_DANH_SACH_LOAI_HO_SO', 'LICENSE') or die($this->access_denied());
        $VIEW_DATA['v_filter'] = get_post_var('txt_filter');
        $VIEW_DATA['arr_all_license_type'] = $this->model->qry_all_license_type();
        $this->view->render('dsp_all_license_type', $VIEW_DATA);
    }

    function update_license_type()
    {
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_license_type';
        $this->model->goforward_url = $this->view->get_controller_url() . 'dsp_single_license_type';
        $this->model->update_license_type();
    }

    function delete_license_type()
    {
        check_permission('XOA_LOAI_HO_SO', 'LICENSE') or die($this->access_denied());
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_license_type';
        $this->model->delete_license_type();
    }

    function dsp_single_license_type($v_license_type_id)
    {
        check_permission('XEM_DANH_SACH_LOAI_HO_SO', 'LICENSE') or die($this->access_denied());
        $VIEW_DATA['arr_all_sector'] = $this->model->qry_all_sector();
        if ($v_license_type_id)
        {
            $VIEW_DATA['arr_single_license_type'] = $this->model->qry_single_license_type($v_license_type_id);
        }
        $this->view->render('dsp_single_license_type', $VIEW_DATA);
    }

}
