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

/**
 * @author Duong Tuan Anh <goat91@gmail.com>
 * @package chat
 * 
 */
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class chat_Controller extends Controller
{

    private $_user_chat_info;

    /**
     *
     * @var \chat_Model
     */
    public $model;

    function __construct()
    {
        parent::__construct('r3', 'chat');

        //Kiem tra session
        session::check_login();

        $this->model->init_data($user_id, $login_name);
        $this->_user_chat_info = $this->model->get_my_chat_info();
        $info_id               = isset($this->_user_chat_info['PK_USER']) ? $this->_user_chat_info['PK_USER'] : 0;
        $this->model->update_my_chat_info($info_id);
        $this->_user_chat_info = $this->model->get_my_chat_info();
    }

    function main()
    {
        $this->dsp_chatbox();
    }
    

    function svc_my_chat_info()
    {
        echo json_encode($this->_user_chat_info);
    }

    /**
     * Lấy tin nhắn 
     * $_GET['type'] = 'send', 'receive', 'all'
     * $_GET['status'] = 'unread', 'read', 'all'
     * $_GET['partner_login']
     * $_GET['limit']
     */
    function svc_get_messages()
    {
        $type          = get_request_var('type');
        $status        = get_request_var('status');
        $partner_login = get_request_var('partner_login');
        $limit         = get_request_var('limit');
        $arr_msg       = $this->model->get_messages($type, $status, $partner_login, $limit);
        foreach ($arr_msg as &$msg)
        {
            $msg['C_TIME']    = date('d-m-Y H:i', $msg['C_TIME']);
            $msg['C_MESSAGE'] = str_rot13($msg['C_MESSAGE']);
        }
        echo json_encode($arr_msg);
    }

    /**
     * $_GET['partner_login']
     */
    function svc_get_sess_message()
    {
        $partner_login = get_request_var('partner_login');
        $arr_msg       = $this->model->get_all_sess_message($partner_login);
        foreach ($arr_msg as &$msg)
        {
            $msg['C_TIME']    = date('d-m-Y H:i', $msg['C_TIME']);
            $msg['C_MESSAGE'] = str_rot13($msg['C_MESSAGE']);
        }
        echo json_encode($arr_msg);
    }

    /**
     * Lấy danh sách người dùng theo từng OU
     * $_GET[online] = 'true', 'false', 'all'
     */
    function svc_get_user_list()
    {
        $online = get_request_var('online');

        $arr_all_ou = $this->model->get_all_ou();
        foreach ($arr_all_ou as &$ou)
        {
            $ou['users'] = $this->model->get_user_list($ou['PK_OU'], $online);
        }
        echo json_encode($arr_all_ou);
    }

    function dsp_chatbox()
    {
        $this->view->render('dsp_chatbox');
    }

    function send_message()
    {
        $msg      = $this->model->clean_message(get_post_var('message', '', false));
        $msg      = str_rot13($msg);
        $receiver = get_post_var('receiver');
        if (strlen($msg) && strlen($receiver))
        {
            $msg_id  = $this->model->send_message($msg, $receiver);
            $arr_msg = $this->model->get_all_sess_message($receiver);
            foreach ($arr_msg as &$msg)
            {
                $msg['C_TIME']    = date('d-m-Y H:i', $msg['C_TIME']);
                $msg['C_MESSAGE'] = str_rot13($msg['C_MESSAGE']);
            }
            echo json_encode($arr_msg);
        }
    }

    function svc_my_chat_conn()
    {
        echo json_encode($this->model->get_all_connections());
    }

    function test()
    {
        $this->model->db->debug = 1;
        $this->model->notify_message('ngovantu', 1);
    }

    function __call($name, $arguments)
    {
        if (!isset($_SESSION['debug']))
            $_SESSION['debug']   = array();
        $_SESSION['debug'][] = array($name, $arguments);
    }

    /**
     * $_GET[partner]
     */
    function svc_remove_connection()
    {
        $this->model->remove_session_connection(get_request_var('partner'));
    }

    /**
     * $_GET[partner]
     */
    function svc_add_connection()
    {
        $this->model->add_session_connection(get_request_var('partner'));
    }

}