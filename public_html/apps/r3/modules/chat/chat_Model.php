<?php
/**
Copyright (C) 2012 Tam Viet Tech.

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
    exit('No direct script access allowed ');

class chat_Model extends Model
{

    private $_user_login_name;
    private $_user_id;
    private $_sess_key = 'established_chat_connections';

    /**
     *
     * @var \ADOConnection 
     */
    public $db;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 
     * @param int $user_id 
     * @param string $user_login_name
     */
    function init_data($user_id, $user_login_name)
    {
        $this->db->debug        = 0;
        $this->_user_id         = $user_id;
        $this->_user_login_name = $user_login_name;
    }

    /**
     * 
     * @return array
     */
    function get_my_chat_info()
    {
        $sql = "Select * From t_r3_chat_users Where C_LOGIN_NAME = ? Limit 1";
        return $this->db->getRow($sql, array($this->_user_login_name));
    }

    /**
     * 
     * @param int $info_id 
     * @param string $status 'available', 'busy', 'not_here',...
     */
    function update_my_chat_info($info_id, $status = null)
    {
        $update_data                  = array();
        $update_data['C_ACCESS_TIME'] = time();
        if ($status)
        {
            $update_data['C_STATUS'] = $status;
        }

        if ($info_id) //update
        {
            $sql       = "Update t_r3_chat_users Set";
            reset($update_data);
            $first_val = current($update_data);
            $first_col = key($update_data);
            $sql .= " $first_col = ?";
            while (next($update_data))
            {
                $col = key($update_data);
                $sql .= "\n,$col = ?";
            }
            $sql.= "\n Where PK_USER = ?";
            $update_data[] = $info_id;
        }
        else //insert
        {
            $update_data['C_LOGIN_NAME']       = $this->_user_login_name;
            $update_data['C_STATUS']           = 'available';
            $update_data['C_UNREAD_COUNT']     = 0;
            $update_data['C_HAS_OFFLINE_CONN'] = 0;
            $str_sql_values                    = '?';
            for ($i = 0; $i < count($update_data) - 1; $i++)
            {
                $str_sql_values .= ',?';
            }
            $sql = "Insert Into t_r3_chat_users(" . implode(',', array_keys($update_data)) . ") Values($str_sql_values)";
        }
        $this->db->Execute($sql, $update_data);
    }

    function get_first_unread_message($partner_login)
    {
        $msg_id = Session::get('first_unread_message');
        if ($msg_id === null)
        {
            $sql    = '
                SELECT MIN(pk_message) AS id 
                FROM t_r3_chat_messages
                WHERE c_is_read <> 1
                AND c_sender = ? 
                AND c_receiver = ?
                ';
            $params = array($partner_login, $this->_user_login_name);
            $msg_id = (int) $this->db->GetOne($sql, $params);
            Session::set('first_unread_message', $msg_id);
        }
        return $msg_id;
    }

    function get_last_read_message($partner_login)
    {
        $msg_id = Session::get('last_read_message');
        if ($msg_id === null)
        {
            $sql    = '
                SELECT MAX(pk_message) AS id 
                FROM t_r3_chat_messages
                WHERE c_is_read = 1
                AND
                (
                    (c_sender = ? AND c_receiver = ?)
                    OR (c_sender = ? AND c_receiver = ?)
                )
                ';
            $params = array($this->_user_login_name, $partner_login
                , $partner_login, $this->_user_login_name);
            $msg_id = (int) $this->db->GetOne($sql, $params);
            Session::set('last_read_message', $msg_id);
        }
        return $msg_id;
    }

    function get_first_session_msg($partner_login)
    {
        $msg_id       = Session::get('first_session_message');
        $first_unread = $this->get_first_unread_message($partner_login);
        $last_read    = $this->get_last_read_message($partner_login);

        if ($msg_id === null)
        {
            $msg_id = $first_unread ? $first_unread : ($last_read);
            Session::set('first_session_message', $msg_id);
        }
        return $msg_id;
    }

    function get_all_sess_message($partner_login)
    {
        $first_msg_id = $this->get_first_session_msg($partner_login);
        return $this->get_all_messages_gt($partner_login, $first_msg_id);
    }

    /**
     * 
     * @param string $type 'send', 'receive', 'all'
     * @param string $status 'unread', 'read', 'all'
     * @param string $partner_login 
     * @param int $limit 
     * @return array
     */
    function get_messages($type = 'all', $status = 'all', $partner_login = null, $limit = 0)
    {
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
        $conditions = '';
        $order      = ' C_TIME ';
        $params     = array();
        switch ($type)
        {
            case 'send':
                $conditions .= ' And C_SENDER = ?';
                $params[] = $this->_user_id;
                break;
            case 'receive':
                $conditions .= ' And C_RECEIVER =?';
                $params[] = $this->_user_id;
                break;
            case 'all':
                break;
            default:
                return 'Sai $_GET[type]';
        }

        switch ($status)
        {
            case 'unread':
                $conditions .= ' And C_IS_READ = 0';
                break;
            case 'read':
                $conditions.= ' And C_IS_READ = 1';
                $order = ' C_TIME Desc ';
                break;
            case 'all':
                break;
            default:
                return 'Sai $_GET[status]';
        }

        if ($partner_login)
        {
            $conditions .= ' AND (
                                (C_SENDER = ? And C_RECEIVER = ?)
                                Or(C_SENDER = ? And C_RECEIVER = ?)
                             )';
            $params[] = $partner_login;
            $params[] = $this->_user_login_name;
            $params[] = $this->_user_login_name;
            $params[] = $partner_login;
        }
        else
        {
            $conditions .= ' And (C_SENDER = ? Or C_RECEIVER = ?)';
            $params[] = $this->_user_login_name;
            $params[] = $this->_user_login_name;
        }

        $sql = "
            Select C_SENDER, C_RECEIVER, C_MESSAGE, C_TIME
            From t_r3_chat_messages
            Where 1=1
            $conditions
            Order By $order";
        if ($limit)
        {
            $sql.= " Limit $limit";
        }
        $data = $this->db->getAll($sql, $params);
        if ($status == 'read')
        {
            $data = array_reverse($data);
        }
        $this->db->Execute('Update t_r3_chat_messages Set c_is_read = 1 Where c_receiver = ?', array($this->_user_login_name));
        return $data;
    }

    function get_all_messages_gt($partner_login, $msg_id)
    {
        $select_sql    = "Select C_SENDER, C_RECEIVER, C_MESSAGE, C_TIME
            From t_r3_chat_messages
            Where 1=1
            AND(    (c_sender = ? AND c_receiver = ?)
                    OR (c_sender = ? AND c_receiver = ?) )
            AND pk_message >= ?
            Order By C_TIME";
        $select_params = array($this->_user_login_name, $partner_login
            , $partner_login, $this->_user_login_name, $msg_id);

        $update_sql    = '
            UPDATE t_r3_chat_messages 
            SET c_is_read = 1 
            WHERE c_sender=? 
            AND c_receiver=? 
            AND pk_message >= ?';
        $update_params = array($partner_login, $this->_user_login_name, $msg_id);
        $this->db->Execute($update_sql, $update_params);

        return $this->db->getAll($select_sql, $select_params);
    }

    /**
     * 
     * @return array
     */
    function get_all_ou()
    {
        return $this->db->getAll("Select PK_OU, C_NAME From t_cores_ou Order By C_INTERNAL_ORDER");
    }

    /**
     * Gửi tin nhắn
     */
    function send_message($message, $receiver)
    {
        $sender = $this->_user_login_name;
        $time   = time();
        $sql    = 'INSERT INTO t_r3_chat_messages(c_sender, c_receiver, c_message, c_time, c_is_read)
            VALUES(?,?,?,?,?)';
        $params = array($sender, $receiver, $message, $time, 0);
        $this->db->execute($sql, $params);

        $id = $this->db->Insert_ID();

        //notice partner
        $this->notify_message($receiver, 1);
        $this->add_session_connection($receiver);
        return $id;
    }

    function notify_message($user_login, $user_unread)
    {
        $chat_conn = $this->get_single_connection($user_login);
        $time      = time();
        if ($chat_conn)
        {
            $stmt1  = ' Update t_r3_chat_connections 
                        Set 
                            C_1ST_UNREAD =C_1ST_UNREAD + ? 
                            ,C_REFRESH_VERSION = C_REFRESH_VERSION + 1
                        Where C_1ST_USER = ? 
                        And C_2ND_USER = ?';
            $stmt2  = ' Update t_r3_chat_connections 
                        Set 
                            C_2ND_UNREAD =C_2ND_UNREAD + ? 
                            ,C_REFRESH_VERSION = C_REFRESH_VERSION + 1
                        Where C_2ND_USER = ? 
                        And C_1ST_USER = ?';
            $params = array($user_unread, $user_login, $this->_user_login_name);
            $this->db->Execute($stmt1, $params);
            $this->db->Execute($stmt2, $params);
        }
        else
        {
            $fields = 'c_1st_user, c_2nd_user, c_1st_unread, c_2nd_unread, c_refresh_version';
            $stmt   = " Insert Into t_r3_chat_connections($fields) Values(?,?,?,?,?)";
            $params = array($this->_user_login_name, $user_login, 0, $user_unread, 1);
            $this->db->Execute($stmt, $params);
        }
    }

    /**
     * 
     */
    function clean_message($str)
    {
        $search  = array('<', '>', "'");
        $replace = array();
        foreach ($search as $v)
        {
            $replace[] = htmlentities($v);
        }
        $str     = str_replace($search, $replace, $str);
        $pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“�?‘’]))/";
        $str     = preg_replace($pattern, '<a href="$1">$1</a>', $str);
        $str     = stripslashes($str);
        return $str;
    }

    /**
     * 
     * @param int $ou_id 
     * @param string $online 'true', 'false', 'all' 
     * @return array
     */
    function get_user_list($ou_id, $online)
    {
        $offline_delay = 5; //Th�?i gian không truy cập coi như offline
        $time          = time();
        $conditions    = ' Where u.C_STATUS = 1';
        $conditions .= ' And u.FK_OU = ?';
        $conditions .= ' And u.C_LOGIN_NAME <> ?';
        $params[]      = $ou_id;
        $params[]      = $this->_user_login_name;

        //danh sách online
        $arr_online = $this->db->getCol("
                Select C_LOGIN_NAME 
                From t_r3_chat_users
                Where C_ACCESS_TIME >= ($time - $offline_delay)
                And C_LOGIN_NAME <> '{$this->_user_login_name}'");
        if (is_array($arr_online))
        {
            reset($arr_online);
            $online_list = "'" . current($arr_online) . "'";
            while (next($arr_online))
            {
                $online_list .= ",'" . current($arr_online) . "'";
            }
        }
        else
        {
            $online_list = '0';
        }

        switch ($online)
        {
            case 'true':
                $conditions .= " And C_LOGIN_NAME In($online_list)";
                break;
            case 'false':
                $conditions .= " And C_LOGIN_NAME Not In($online_list)";
                break;
            case 'all':
                break;
            default:
                return 'Sai $_GET[online]';
        }
        $sql = "
            Select 
                u.C_NAME, u.C_LOGIN_NAME, cu.C_STATUS
                , (CASE When u.C_LOGIN_NAME In($online_list) Then 1 Else 0 End) as C_IS_ONLINE
            From t_cores_user u
            Left Join t_r3_chat_users cu
                On  u.C_LOGIN_NAME = cu.C_LOGIN_NAME
            $conditions";
        return $this->db->getAll($sql, $params);
    }

    function get_all_connections()
    {
        $established_connections = Session::get($this->_sess_key);
        if (empty($established_connections))
        {
            $established_connections = array(0);
        }
        $established_connections = implode(',', $established_connections);
        $fields                  = 'PK_CONNECTION, C_1ST_USER, C_2ND_USER
            , C_1ST_USER_LAST_ACTION, C_2ND_USER_LAST_ACTION, C_REFRESH_VERSION';

        $stmt1                   = " SELECT  $fields, 1 AS C_POSITION 
                      FROM t_r3_chat_connections 
                      WHERE (c_1st_unread > 0  OR pk_connection IN($established_connections))
                      AND c_1st_user = ?";
        $stmt2                   = " SELECT $fields, 2 AS C_POSITION 
                    FROM t_r3_chat_connections 
                    WHERE (c_2nd_unread > 0 OR pk_connection IN($established_connections))
                    AND c_2nd_user = ?";
        $params                  = array($this->_user_login_name, $this->_user_login_name);
        $data                    = $this->db->GetAll($stmt1 . ' UNION ALL ' . $stmt2, $params);
        $established_connections = array();
        foreach ($data as $conn)
        {
            $established_connections[] = $conn['PK_CONNECTION'];
        }
        Session::set($this->_sess_key, $established_connections);

        $this->db->Execute('UPDATE t_r3_chat_connections SET c_1st_unread = 0 WHERE c_1st_user = ?', $params);
        $this->db->Execute('UPDATE t_r3_chat_connections SET c_2nd_unread = 0 WHERE c_2nd_user = ?', $params);
        return $data;
    }

    function get_single_connection($partner_login)
    {
        $stmt   = ' Select 
                        PK_CONNECTION, C_1ST_USER, C_2ND_USER, C_1ST_USER_LAST_ACTION
                        , C_2ND_USER_LAST_ACTION
                  From 
                    t_r3_chat_connections
                  Where (C_1ST_USER = ? AND C_2ND_USER = ?)
                  Or (C_1ST_USER = ? AND C_2ND_USER = ?)';
        $params = array($this->_user_login_name, $partner_login
            , $partner_login, $this->_user_login_name);
        return $this->db->getRow($stmt, $params);
    }

    function add_session_connection($partner_login)
    {
        $this->notify_message($partner_login, 0);
        $arr_single_connection = $this->get_single_connection($partner_login);
        $arr_established_conns = Session::get($this->_sess_key);
        if (!$arr_established_conns)
        {
            $arr_established_conns = array();
        }
        if (in_array($arr_single_connection['PK_CONNECTION'], $arr_established_conns))
        {
            return;
        }

        $arr_established_conns[] = $arr_single_connection['PK_CONNECTION'];

        Session::set($this->_sess_key, $arr_established_conns);
    }

    function remove_session_connection($partner_login)
    {
        if (!$partner_login)
        {
            return;
        }
        $arr_single_connection = $this->get_single_connection($partner_login);
        $arr_established_conns = Session::get($this->_sess_key) OR array();
        if (!$arr_single_connection)
        {
            return;
        }
        $id = $arr_single_connection['PK_CONNECTION'];
        foreach ($arr_established_conns as $k => $v)
        {
            if ($v == $id)
            {
                unset($arr_established_conns[$k]);
                sort($arr_established_conns);
                Session::set($this->_sess_key, $arr_established_conns);
                break;
            }
        }
    }

}
