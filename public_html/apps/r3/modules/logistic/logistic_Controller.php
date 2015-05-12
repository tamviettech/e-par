<?php

defined('SERVER_ROOT') or die();

/**
 * @package logistic
 * @author Tam Viet <ltbinh@gmail.com>
 * @author Duong Tuan Anh <goat91@gmail.com>
 */
class logistic_Controller extends Controller
{

    /**
     *
     * @var \logistic_Model 
     */
    public $model;

    /**
     *
     * @var View 
     */
    public $view;

    function __construct()
    {
        parent::__construct('r3', 'logistic');
        $this->view->template->show_left_side_bar = false;
        Session::init();
        //Kiem tra dang nhap
        session::check_login();
        
        check_permission('THEO_DOI_NGUOI_DUNG', 'R3') or die('Bạn không có quyền thực hiện chức năng này');
    }

    function main()
    {
        $this->view->render('dsp_main');
    }

    function dsp_new_actions()
    {
        $VIEW_DATA['arr_new_actions'] = $this->model->qry_new_actions();
        $this->view->render('dsp_new_actions', $VIEW_DATA);
    }

}