<?php 
/**
// File name   : 
// Version     : 1.0.0.1
// Begin       : 2012-12-01
// Last Update : 2010-12-25
// Author      : TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
//Copyright (C) 2012-2013  TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn

// E-PAR is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// E-PAR is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// See LICENSE.TXT file for more information.
*/

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class nop_ho_so_qua_mang_internet_Model extends Model {

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
		    return SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS . 'common' . DS . 'common_list.xml';;
		}

		$file_path =  SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS
		. $record_type_code . DS . $record_type_code . '_' . $config_type . '.xml';

		if (!is_file($file_path))
		{
			$record_type_code = preg_replace('/([0-9]+)/', '00', $record_type_code);
			$file_path = SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS
			. 'common' . DS . $record_type_code . '_' . $config_type . '.xml';
		}

		return $file_path;

	}

    public function qry_all_send_over_internet_record_type()
    {
        $stmt = "Select
                    PK_RECORD_TYPE
                    , Concat(C_CODE, ' - ', C_NAME) as C_NAME
                From t_r3_record_type
                Where C_STATUS>0
                    And C_SEND_OVER_INTERNET='1'
                Order By C_ORDER";
        return $this->db->getAssoc($stmt);
    }

    public function do_send()
    {
        if (isset($_SESSION['captcha_passed']))
        {
            $v_record_type_id      = get_post_var('hdn_record_type_id',0);
            $v_xml_data            = get_post_var('XmlData','<data/>',0);
            $v_record_no           = get_post_var('txt_record_no');
            $v_return_phone_number = get_post_var('txt_return_phone_number');
            $v_return_email        = get_post_var('txt_return_email');
            $v_note                = get_post_var('tbxNote');

            //Người xử lý ban đầu: Là người được phân công Tiếp Nhận
            $v_record_type_code    = $this->db->getOne('Select C_CODE From t_r3_record_type Where PK_RECORD_TYPE=?', array($v_record_type_id));
            $v_task_code_like      = $v_record_type_code. _CONST_XML_RTT_DELIM . _CONST_TIEP_NHAN_ROLE;

            $stmt                   = 'Select C_USER_LOGIN_NAME From t_r3_user_task Where C_TASK_CODE Like ?';
            $params                 = array('%'.$v_task_code_like);
            $v_next_user_code       = $this->db->getOne($stmt,$params);

            $v_next_task_code      = $v_record_type_code . _CONST_XML_RTT_DELIM . _CONST_XAC_NHAN_HO_SO_NOP_QUA_INTERNET_ROLE;

            $stmt = 'Insert Into t_r3_internet_record(
                        FK_RECORD_TYPE
                        ,C_RECORD_NO
                        ,C_RECEIVE_DATE
                        ,C_RETURN_PHONE_NUMBER
                        ,C_RETURN_EMAIL
                        ,C_XML_DATA
                        ,C_NEXT_TASK_CODE
                        ,C_NEXT_USER_CODE
                    ) Values (
                        ?
                        ,?
                        ,' . $this->build_getdate_function() . '
                        ,?
                        ,?
                        ,?
                        ,?
                        ,?
                    )';

            $params = array(
                    $v_record_type_id
                    ,$v_record_no
                    ,$v_return_phone_number
                    ,$v_return_email
                    ,$v_xml_data
                    ,$v_next_task_code
                    ,$v_next_user_code
            );

            $this->db->Execute($stmt, $params);
            $v_record_id = $this->get_last_inserted_id('t_r3_internet_record', 'PK_RECORD');

            //Chấp nhận lưu trữ thừa DB, tăng tốc độ Query khi Tra cứu, báo cáo
            if (DATABASE_TYPE == 'MSSQL')
            {
                $stmt = "Update t_r3_internet_record Set
                            C_CITIZEN_NAME = C_XML_DATA.value('(//item[@id=''txtName'']/value/text())[1]','nvarchar(200)')
                        Where PK_RECORD=?";
                $params = array($v_record_id);
                $this->db->Execute($stmt, $params);

            }
            elseif (DATABASE_TYPE == 'MYSQL')
            {
                $stmt = "Update t_r3_internet_record Set
                                C_CITIZEN_NAME = ExtractValue(C_XML_DATA, '//item[@id=''txtName'']/value[1]')
                        Where PK_RECORD=?";
                $params = array($v_record_id);
                $this->db->Execute($stmt, $params);
            }

            //File dinh kem
            $count = count($_FILES['uploader']['name']);
            for ($i = 0; $i < $count; $i++)
            {
                if ($_FILES['uploader']['error'][$i] == 0  )
                {
                    $v_file_name = $_FILES['uploader']['name'][$i];
                    $v_tmp_name  = $_FILES['uploader']['tmp_name'][$i];

                    $v_file_ext = array_pop(explode('.', $v_file_name));

                    if (in_array($v_file_ext, explode('|', _CONST_RECORD_FILE_ACCEPT)))
                    {
                        $v_new_file_name = $v_record_no . '_' . $v_file_name;
                        if (move_uploaded_file($v_tmp_name, SERVER_ROOT . "uploads" . DS . 'r3' . DS . 'internet' . DS . $v_new_file_name))
                        {
                            $stmt = 'Insert Into t_r3_internet_record_file(FK_RECORD, C_FILE_NAME) Values(?,?)';
                            $params = array($v_record_id, $v_new_file_name);
                            $this->db->Execute($stmt, $params);
                        }
                    }
                }
            }

            unset($_SESSION['captcha_passed']);

            echo 'Cảm ơn bạn đã nộp hồ sơ! Nhấn <a href="' . SITE_ROOT . 'nop_ho_so">vào đây </a>để quay về trang chính';
        }
        else
        {
            die('Bo lao!');
        }
    }
}