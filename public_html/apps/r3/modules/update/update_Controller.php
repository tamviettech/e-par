<?php

class update_Controller extends Controller
{

    /** @var update_model */
    public $model;

    function __construct($app, $module)
    {
        parent::__construct($app, $module);
        if (!Session::get('is_admin'))
        {
            die('Phải đăng nhập admin');
        }
        $this->model->db->debug = 1;
        error_reporting(E_ALL);
    }

    /**
     * Gộp cột tên + địa chỉ, nới rộng cột hành động
     */
    function gop_cot_ten_dia_chi()
    {
        $this->model->gop_cot_ten_dia_chi();
    }

}
