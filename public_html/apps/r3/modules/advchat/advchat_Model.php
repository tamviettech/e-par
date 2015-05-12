<?php
require_once __DIR__ . '/../record/record_Model.php';
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed ');

class advchat_Model extends record_Model
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
     * lay tat ca NSD
     * @return type array
     */
    public function qry_all_user()
    {
        
        $v_user_id = session::get('user_id');
        $sql = "SELECT 
                        C_NAME,
                        (SELECT CONCAT('<root>',GROUP_CONCAT('<row '
                                                        ,CONCAT(' PK_USER =\"', PK_USER, '\"')
                                                        ,CONCAT(' C_NAME  =\"', C_NAME, '\"')
                                                        , ' />'
                                                        SEPARATOR ''
                                                    ),'</root>')
                        FROM t_cores_user WHERE FK_OU = OU.PK_OU AND PK_USER <> $v_user_id) AS C_XML_USER
                FROM t_cores_ou OU";
        $MODEL_DATA['arr_all_user'] = $this->db->getAll($sql);
        $stmt = "SELECT
                    C_SENDER,
                    (SELECT
                       CONCAT('<root>',GROUP_CONCAT('<message>',CONCAT('<![CDATA[',m.C_MESSAGE,']]>'),'</message>' SEPARATOR ''),'</root>')
                     FROM t_r3_chat_messages m
                     WHERE m.C_SENDER = M.C_SENDER
                         AND C_IS_READ <> 1
                         AND C_RECEIVER = ?
                         AND C_RECIEVE_USER_OU = ?
                         AND C_NAME_USER_SEND   =  'NULL'
                    ) AS C_XML_MESSAGE
                  FROM t_r3_chat_messages M
                  WHERE C_IS_READ <> 1
                      AND C_RECEIVER = ?
                      and C_RECIEVE_USER_OU = ?
                      AND C_NAME_USER_SEND  = 'NULL'
                  GROUP BY C_SENDER";
        $arr_param = array($v_user_id,CONST_MY_OU_NAME,$v_user_id,CONST_MY_OU_NAME);
        $MODEL_DATA['arr_all_message'] = $this->db->GetAssoc($stmt,$arr_param);
        
// danh sach tin tu nguoi gui # server
        $stmt_not_in_server = "SELECT
                                        C_SENDER,
                                        C_NAME_USER_SEND,
                                        C_SEND_USER_OU,
                                        C_NAME_USER_SEND,
                                    (SELECT
                                       CONCAT('<root>',
                                            GROUP_CONCAT('<message>',CONCAT('<![CDATA[',m.C_MESSAGE,']]>'),'</message>' SEPARATOR ''),'</root>')
                                     FROM t_r3_chat_messages m
                                     WHERE m.C_SENDER = M.C_SENDER
                                         AND C_IS_READ <> 1
                                         AND C_RECEIVER = ?
                                         AND C_RECIEVE_USER_OU = ?
                                         AND C_NAME_USER_SEND  != 'NULL'
                                    ) AS C_XML_MESSAGE
                                  FROM t_r3_chat_messages M
                                  WHERE C_IS_READ <> 1
                                      AND C_RECEIVER = ?
                                      and C_RECIEVE_USER_OU = ?
                                      AND C_NAME_USER_SEND  != 'NULL'
                                  GROUP BY C_SENDER";
        $arr_param_not_in_server                 = array($v_user_id,CONST_MY_OU_NAME,$v_user_id,CONST_MY_OU_NAME);
        $MODEL_DATA['arr_all_msg_not_in_server'] = $this->db->getAll($stmt_not_in_server,$arr_param_not_in_server);
        
        return $MODEL_DATA;
    }
    /**
     * inser message
     * @param type $v_send_user_id
     * @param type $v_recieve_user_id
     * @param type $v_message
     * @param type $v_is_read
     */
    public function do_insert_mes($v_send_user_id,$v_recieve_user_id,$v_send_user_ou,$v_recieve_user_ou,$v_message,$v_name_user_send)
    {
        $time   = time();
        $stmt   = 'INSERT INTO t_r3_chat_messages(C_SENDER,C_RECEIVER,C_MESSAGE,C_TIME,C_IS_READ,C_SEND_USER_OU,C_RECIEVE_USER_OU,C_NAME_USER_SEND)
            VALUES(?,?,?,?,?,?,?,?)';
        $params = array($v_send_user_id, $v_recieve_user_id, $v_message, $time, 0,$v_send_user_ou,$v_recieve_user_ou,$v_name_user_send);
        $this->db->execute($stmt, $params);
        return  $this->db->ErrorNo();
    }
    
    /**
     * change to readed
     * @param type $v_send_user_id
     * @param type $v_recieve_user_id
     * @param type $v_send_user_ou
     * @param type $v_recieve_user_ou
     */
    public function do_readed($v_send_user_id,$v_recieve_user_id,$v_send_user_ou,$v_recieve_user_ou)
    {
        $stmt = "UPDATE t_r3_chat_messages
                SET C_IS_READ = 1
                WHERE C_IS_READ = 0
                    AND C_SENDER = ?
                    AND C_RECEIVER = ?
                    AND C_SEND_USER_OU = ?
                    AND C_RECIEVE_USER_OU = ?";
        $params = array($v_send_user_id, $v_recieve_user_id, $v_send_user_ou, $v_recieve_user_ou);
        $this->db->execute($stmt, $params);
    }
    
    /**
     * count all unread message
     * @param type $user_id
     * @param type $user_ou
     * @return type int
     */
    public function count_unread($user_id,$user_ou)
    {
        $stmt = "SELECT
                    COUNT(*)
                  FROM t_r3_chat_messages
                  WHERE C_IS_READ = 0
                      AND C_RECEIVER = ?
                      AND C_RECIEVE_USER_OU = ?";
        return $this->db->getOne($stmt,array($user_id,$user_ou));
    }

}
