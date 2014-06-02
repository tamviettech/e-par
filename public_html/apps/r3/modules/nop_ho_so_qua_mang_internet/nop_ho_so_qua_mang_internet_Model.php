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

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class nop_ho_so_qua_mang_internet_Model extends Model
{

    /**
     * @var \ADOConnection
     */
    public $db;

    function __construct()
    {
        parent::__construct();
    }

    private function _get_xml_config($record_type_code, $config_type)
    {
        if (strtolower($config_type) == 'lookup')
        {
            return SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS
                    . 'common' . DS . 'record_lookup.xml';
        }

        if ($config_type == 'list' && $record_type_code == '')
        {
            return SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS . 'common' . DS . 'common_list.xml';
            ;
        }

        $file_path = SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS
                . $record_type_code . DS . $record_type_code . '_' . $config_type . '.xml';

        if (!is_file($file_path))
        {
            $record_type_code = preg_replace('/([0-9]+)/', '00', $record_type_code);
            $file_path        = SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS
                    . 'common' . DS . $record_type_code . '_' . $config_type . '.xml';
        }

        return $file_path;
    }

    public function qry_all_send_over_internet_record_type($all = false)
    {
        $conds        = '1=1';
        page_calc($v_start, $v_end);
        $txt_linh_vuc = get_post_var('rad_linh_vuc');
        $txt_tu_khoa  = get_post_var('txt_tu_khoa');
        $limit        = $v_end - $v_start + 1;
        $conds        = "C_STATUS>0 And C_SEND_OVER_INTERNET='1'";
        if ($txt_linh_vuc)
        {
            $conds .= " And C_SPEC_CODE = '$txt_linh_vuc'";
        }
        if ($txt_tu_khoa)
        {
            $conds .= " And (C_CODE Like '%$txt_tu_khoa%' Or C_NAME Like '%$txt_tu_khoa%')";
        }


        $table        = 't_r3_record_type';
        $total_record = $this->db->GetOne("Select Count(*) From $table Where $conds");
        $stmt         = "Select
                    PK_RECORD_TYPE
                    , Concat(C_CODE, ' - ', C_NAME) as C_NAME
                    , $total_record As TOTAL_RECORD
                From $table
                Where $conds
                Order By C_ORDER
                ";

        $v_start--;
        if (!$all)
        {
            $stmt .= "Limit $limit Offset $v_start";
        }

        return $this->db->getAll($stmt);
    }

    function qry_single_record_type($record_type_code)
    {
        return $this->db->GetRow('Select * From t_r3_record_type Where C_CODE = ?', array($record_type_code));
    }

    /**
     * 
     * @param bool $is_real_record true là nộp thật, false là nộp để kiểm tra hợp lệ
     * @return \stdClass
     */
    public function do_send($is_real_record = TRUE)
    {
        $v_count_file      = count($_FILES['uploader']['name']);
        $response          = new stdClass();
        $response->success = false;
        $response->message = '';
        $v_challenge       = get_post_var('recaptcha_challenge_field');
        $v_response        = get_post_var('recaptcha_response_field');
        $resp              = recaptcha_check_answer(_CONST_RECAPCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $v_challenge, $v_response);
        if (false)//(!$resp->is_valid)
        {
            $response->success = false;
            $response->message = 'Bạn chưa nhập mã xác nhận hoặc mã xác nhận chưa đúng!';
            return $response;
        }
        if (!$v_count_file or empty($_FILES['uploader']['name'][0]))
        {
            $response->message = 'Hồ sơ này yêu cầu File đính kèm!';
            return $response;
        }
        $v_record_type_id      = get_post_var('hdn_record_type_id', 0);
        $v_record_no           = get_post_var('txt_record_no');
        $v_return_phone_number = get_post_var('txt_return_phone_number');
        $v_return_email        = get_post_var('txt_return_email');
        $v_note                = get_post_var('tbxNote');
        $v_name                = get_post_var('txt_name');
        $is_real_record        = $is_real_record ? 1 : 0;
        $v_xml_data            = "<?xml version=\"1.0\" standalone=\"yes\" ?><data><item id=\"txtName\"><value><![CDATA[$v_name]]></value></item></data>";

        //Người xử lý ban đầu: Là người được phân công Tiếp Nhận
        $v_record_type_code = $this->db->getOne('Select C_CODE From t_r3_record_type Where PK_RECORD_TYPE=?', array($v_record_type_id));
        $v_task_code_like   = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE;

        $stmt             = 'Select C_USER_LOGIN_NAME From t_r3_user_task Where C_TASK_CODE Like ?';
        $params           = array('%' . $v_task_code_like);
        $v_next_user_code = $this->db->getOne($stmt, $params);

        $v_next_task_code = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE;

        $stmt = 'Insert Into t_r3_internet_record(
                        FK_RECORD_TYPE
                        ,C_RECORD_NO
                        ,C_RECEIVE_DATE
                        ,C_RETURN_PHONE_NUMBER
                        ,C_RETURN_EMAIL
                        ,C_XML_DATA
                        ,C_NEXT_TASK_CODE
                        ,C_NEXT_USER_CODE
                        ,C_IS_REAL_RECORD
                        ,C_CITIZEN_NAME
                    ) Values (
                        ?
                        ,?
                        ,' . $this->build_getdate_function() . '
                        ,?
                        ,?
                        ,?
                        ,?
                        ,?
                        ,?
                        ,?
                    )';

        $params = array(
            $v_record_type_id
            , $v_record_no
            , $v_return_phone_number
            , $v_return_email
            , $v_xml_data
            , $v_next_task_code
            , $v_next_user_code
            , $is_real_record
            , $v_name
        );

        $this->db->Execute($stmt, $params);
        $v_record_id = $this->get_last_inserted_id('t_r3_internet_record', 'PK_RECORD');
        //File dinh kem

        for ($i = 0; $i < $v_count_file; $i++)
        {
            if ($_FILES['uploader']['error'][$i] == 0)
            {
                $v_file_name = $_FILES['uploader']['name'][$i];
                $v_tmp_name  = $_FILES['uploader']['tmp_name'][$i];

                $v_file_ext = array_pop(explode('.', $v_file_name));

                if (in_array($v_file_ext, explode(',', _CONST_RECORD_FILE_ACCEPT)))
                {
                    $v_new_file_name = $v_record_no . '_' . $v_file_name;
                    if (move_uploaded_file($v_tmp_name, SERVER_ROOT . "uploads" . DS . 'r3' . DS . 'internet' . DS . $v_new_file_name))
                    {
                        $stmt   = 'Insert Into t_r3_internet_record_file(FK_RECORD, C_FILE_NAME) Values(?,?)';
                        $params = array($v_record_id, $v_new_file_name);
                        $this->db->Execute($stmt, $params);
                    }
                }
            }
        }

        $response->message = 'Cảm ơn bạn đã nộp hồ sơ!';
        $response->success = true;
        return $response;
    }

    /**
     * 
     * @param string $where Sql where clause: a=b AND c=d
     * @param array $params
     * @return array
     */
    function qry_all_record_types($where = '', $params = array())
    {
        $sql = "Select PK_RECORD_TYPE, C_CODE From t_r3_record_type Where C_STATUS > 0 ";
        if ($where)
        {
            $sql .= ' And ' . $where;
        }
        return $this->db->GetAll($sql, $params);
    }

    /**
     * 
     * @param string $select
     * @param string $where
     * @param array $params
     * @return array
     */
    function qry_all_internet_records($select = 'R.*', $where = '', $params = array())
    {
        $sql = "Select $select 
            From t_r3_internet_record  R
            Inner Join t_r3_record_type RT
            On R.FK_RECORD_TYPE = RT.PK_RECORD_TYPE";
        if ($where)
        {
            $sql .= " Where $where ";
        }
        $sql .= "Order By C_RECEIVE_DATE Desc";
        return $this->db->GetAll($sql, $params);
    }

    /**
     * Hồ sơ nộp thử đã có kết quả
     * @return array
     */
    function qry_ho_so_da_kiem_tra($timestamp)
    {
        $datetime = gmdate('Y-m-d H:i:s', (int) $timestamp);
        return $this->qry_all_internet_records('R.C_RECORD_NO, R.C_RECEIVE_DATE,R.C_CITIZEN_NAME
                        , R.C_CLEAR_DATE, R.C_RETURN_EMAIL, RT.C_NAME As C_RECORD_TYPE_NAME'
                        , "C_IS_REAL_RECORD=0 
                            And (C_COMMENT <>'' Or C_COMMENT Is Not Null)
                            And C_CLEAR_DATE > '$datetime'");
    }

    function announce_return_internet_record($arr_single_record)
    {
        require_once SERVER_ROOT . 'libs/Swift/lib/swift_required.php';
        $ssl            = _CONST_SMTP_SSL ? 'ssl' : null;
        $transport      = Swift_SmtpTransport::newInstance(_CONST_SMTP_SERVER, _CONST_SMTP_PORT, $ssl);
        $transport->setUsername(_CONST_SMTP_ACCOUNT);
        $transport->setPassword(_CONST_SMTP_PASSWORD);
        $v_receive_date = date_create($arr_single_record['C_RECEIVE_DATE'])->format('d-m-Y H:i');
        // Tạo đối tượng transport

        $mailer = Swift_Mailer::newInstance($transport);

        $message = Swift_Message::newInstance();
        $message->setSubject('Bộ phận một cửa - Kết quả thụ lý');
        $message->setBody("Kính gửi Ông/bà: {$arr_single_record['C_CITIZEN_NAME']}
                \nNgày {$v_receive_date}  Bộ phận một cửa đã tiếp nhận hồ sơ {$arr_single_record['C_RECORD_TYPE_NAME']} của Ông/bà. 
                \n\nKết quả thụ lý như sau:
                \n{$arr_single_record['C_COMMENT']}");

        $message->setFrom(array(_CONST_SMTP_ACCOUNT => _CONST_SMTP_ACCOUNT_NAME));

        $message->addTo($arr_single_record['C_RETURN_EMAIL']);

        $ret = 0;
        try
        {
            $ret = $mailer->send($message);
        }
        catch (Exception $ex)
        {
            
        }
        return $ret;
    }
    public function qry_all_web_link()
    {
        $sql = "SELECT
                    C_NAME,
                    C_URL,
                    C_FILE_NAME
                  FROM t_ps_weblink
                  WHERE C_STATUS = 1
                  ORDER BY C_ORDER";

        $results =  $this->db->GetAll($sql);
        if($this->db->ErrorNo() == 0)
        {
            return $results;
        }
        return array();
    }
}