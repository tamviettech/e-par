<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class advchat_Controller extends Controller
{
    function __construct()
    {
        parent::__construct('r3', 'advchat');

        //Kiem tra session
        session::init();
        $login_name = session::get('login_name');
        $user_id    = session::get('user_id');
        
        //Kiem tra dang nhap
        session::check_login();
        
        $this->view->DATETIME_NOW        = $this->model->get_datetime_now();
    }

    function main()
    {
        $this->dsp_chat();
    }
    
    /**
     * dsp hien thi man hinh chat
     */
    function dsp_chat()
    {
        $arr_data = $this->model->qry_all_user();
        $VIEW_DATA['arr_all_user']              = isset($arr_data['arr_all_user'])?$arr_data['arr_all_user']:array();
        $VIEW_DATA['arr_all_message']           = isset($arr_data['arr_all_message'])?$arr_data['arr_all_message']:array();
        $VIEW_DATA['arr_all_msg_not_in_server'] = isset($arr_data['arr_all_msg_not_in_server']) ? $arr_data['arr_all_msg_not_in_server'] :array();
        $this->view->render('dsp_chat',$VIEW_DATA);
    }
    /**
     * insert message
     */
    function do_insert_mes()
    {
        $v_status           = get_post_var('status',0); 
        $v_send_user_id     = get_post_var('send_user','');
        $v_recieve_user_id  = get_post_var('recieve_user','');
        $v_send_user_ou     = get_post_var('send_user_ou','');
        $v_recieve_user_ou  = get_post_var('recieve_user_ou','');
        $v_message          = get_post_var('mes','');
        $v_name_user_send   = ($v_status == 0)?'NULL':get_post_var('user_name','');

        if($v_send_user_id != '' && $v_recieve_user_id != '' && $v_message != '')
        {
           echo $this->model->do_insert_mes($v_send_user_id,$v_recieve_user_id,$v_send_user_ou,$v_recieve_user_ou,$v_message,$v_name_user_send);
        }
    }
    /**
     * change message to readed
     */
    public function do_readed()
    {
       $v_send_user_id    = get_post_var('send_user','');
       $v_recieve_user_id = get_post_var('recieve_user','');
       $v_send_user_ou    = get_post_var('send_user_ou','');
       $v_recieve_user_ou = get_post_var('recieve_user_ou','');
       
       if($v_send_user_id != '' && $v_recieve_user_id != '' && $v_send_user_ou != '' && $v_recieve_user_ou != '')
       {
           $this->model->do_readed($v_send_user_id,$v_recieve_user_id,$v_send_user_ou,$v_recieve_user_ou);
       }
    }
    /**
     * coutn unread message
     */
    public function count_unread()
    {
        $this->model->db->debug = 0;
        if(DEBUG_MODE > 10)
        {
            $this->model->db->debug = 1;
        }
        
        $count = 0;
        $user_id = get_post_var('user_id','');
        $user_ou = get_post_var('user_ou','');
        if($user_id != '' && $user_ou != '')
        {
            $count = $this->model->count_unread($user_id,$user_ou);
        }
        echo $count;
    } 
}