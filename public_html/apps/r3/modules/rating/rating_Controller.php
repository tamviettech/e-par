<?php

defined('SERVER_ROOT') or die();

/**
 * 
 */
class rating_Controller extends Controller
{

    /**
     * @var \View 
     */
    public $view;

    /**
     * @var \rating_Model
     */
    public $model;

    function __construct()
    {
        parent::__construct('r3', 'rating');
        $this->view->template->show_left_side_bar = false;
    }

    function main()
    {
        $VIEW_DATA['arr_all_slots'] = $this->model->qry_all_slot();
        $this->view->render('main', $VIEW_DATA);
    }

    /**
     * $_GET[value] = 'like', 'dislike
     * @param mixed $user_or_slot
     */
    function svc_rate_user($user_or_slot)
    {
        header('Content-type: application/json');
        $rate_value = get_request_var('value');
        if (is_numeric($user_or_slot))
        {
            //Đánh giá bằng slot
            $arr_single_user = $this->model->qry_single_user_by_slot($user_or_slot);
        }
        else
        {
            //đánh giá bằng user
            $arr_single_user = $this->model->qry_single_user("C_LOGIN_NAME=?", array($user_or_slot));
        }
        if ($arr_single_user)
        {
            $this->model->rate_user($arr_single_user['C_LOGIN_NAME'], $rate_value);
        }
    }

    function dsp_single_slot($slot_id)
    {
        $arr_single_slot = false;
        if ($slot_id)
        {
            $arr_single_slot = $this->model->qry_single_slot($slot_id);
            $arr_users       = $this->model->qry_all_onegate_user();
        }
        else
        {
            $arr_users = $this->model->qry_no_slot_user();
        }
        $VIEW_DATA['arr_single_slot'] = $arr_single_slot;
        $VIEW_DATA['arr_users']       = $arr_users;
        $VIEW_DATA['id']              = $slot_id;

        $this->view->render('dsp_single_slot', $VIEW_DATA);
    }

    function dsp_statistic($user_login_name)
    {
        
    }

    function update_slot()
    {
        $this->model->update_slot();
    }

    function delete_slot()
    {
        $this->model->delete_slot();
    }

}