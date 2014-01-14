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

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class record_type_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function qry_all_record_type($arr_filter)
    {
        //Phan trang
        page_calc($v_start, $v_end);

        $condition_query = '';
        $v_filter        = $arr_filter['txt_filter'];

        if ($v_filter != '')
        {
            $condition_query = " And (RT.C_CODE like '%$v_filter%' Or RT.C_NAME like '%$v_filter%')";
        }

        //Dem tong ban ghi
        $sql_count_record = "Select Count(*) From t_r3_record_type RT Where (1 > 0) $condition_query";

        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select RT.*
                        ,($sql_count_record) as TOTAL_RECORD
                        ,ROW_NUMBER() OVER (ORDER BY RT.C_CODE) as RN
                        ,Case C_SCOPE
                            When 0 Then 'Thủ tục cấp xã'
                            When 1 Then 'Thủ tục Liên thông Xã ->Huyện'
                            When 2 Then 'Thủ tục Liên thông Huyện->Xã'
                            When 3 Then 'Thủ tục Cấp huyện'
                            Else 'Chưa xác định'
                        End As C_SCOPE_TEXT
                    From t_r3_record_type RT
                    Where (1>0) $condition_query";
            return $this->db->GetAll("Select * From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end Order By a.rn");
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;

            $sql = "Select RT.*
                        ,Case C_SCOPE
                            When 0 Then 'Thủ tục cấp xã'
                            When 1 Then 'Thủ tục Liên thông Xã ->Huyện'
                            When 2 Then 'Thủ tục Liên thông Huyện->Xã'
                            When 3 Then 'Thủ tục Cấp huyện'
                            Else 'Chưa xác định'
                        End As C_SCOPE_TEXT
                        , ($sql_count_record) TOTAL_RECORD
                    FROM t_r3_record_type RT
                    Where (1 > 0) $condition_query
                    Order By C_CODE
                    Limit $v_start, $v_limit";

            return $this->db->getAll($sql);
        }
        elseif (DATABASE_TYPE == 'ORACLE')
        {
            return array();
        }
    }

    function qry_all_report_books($record_type_id)
    {
        $record_type_id = (int) $record_type_id;
        $sql            = "
            Select ls.*, link.c_book_code As C_IS_CHECKED From t_cores_listtype lt
            Inner Join t_cores_list ls
            On ls.fk_listtype = lt.pk_listtype
            Left Join t_r3_book_record_type link
            ON (BINARY ls.c_code = BINARY link.c_book_code And link.fk_record_type = ?)
            Where lt.C_CODE = ?
            ";
        return $this->db->GetAll($sql, array($record_type_id, 'DM_SO_THEO_DOI_HO_SO'));
    }

    public function delete_record_type($arr_filter = array())
    {
        $v_record_type_id_list = isset($_POST['hdn_item_id_list']) ? $this->replace_bad_char($_POST['hdn_item_id_list']) : 0;

        if ($v_record_type_id_list != '')
        {
            $sql = "Delete From t_r3_record_type
                    Where PK_RECORD_TYPE In ($v_record_type_id_list)
                        And PK_RECORD_TYPE Not In (Select FK_RECORD_TYPE From t_r3_record)";

            $this->db->Execute($sql);
        }

        $this->exec_done($this->goback_url, $arr_filter);
    }

    public function qry_single_record_type($p_record_type_id)
    {
        if ($p_record_type_id < 1)
        {
            return array('C_ORDER' => $this->get_max('t_r3_record_type', 'C_ORDER') + 1);
        }

        $stmt   = 'Select
                        PK_RECORD_TYPE
                      ,C_CODE
                      ,C_NAME
                      ,C_XML_FORM_STRUCT_FILE_NAME
                      ,C_XML_WORKFLOW_FILE_NAME
                      ,C_XML_HO_TEMPLATE_FILE_NAME
                      ,C_XML_DATA
                      ,C_ORDER
                      ,C_STATUS
                      ,C_SCOPE
                      ,C_SEND_OVER_INTERNET
                      ,C_SPEC_CODE
                      ,C_ALLOW_VERIFY_RECORD
                 From t_r3_record_type RT Where PK_RECORD_TYPE=?';
        $params = array($p_record_type_id);
        return $this->db->GetRow($stmt, $params);
    }

    public function update_record_type($arr_filter)
    {
        $v_record_type_id = get_post_var('hdn_item_id', 0);
        $v_code           = get_post_var('txt_code', '');
        $v_name           = get_post_var('txt_name', '');
        $v_xml_data       = get_post_var('XmlData', '', FALSE);
        $v_scope          = get_post_var('rad_scope', 3);
        $v_order          = get_post_var('txt_order', 1);
        $v_spec_code      = get_post_var('sel_spec_code', 'XX');
        
        $arr_report_books = get_post_var('chk_report_book', array(), false);

        $v_status             = isset($_POST['chk_status']) ? 1 : 0;
        $v_save_and_addnew    = isset($_POST['chk_save_and_addnew']) ? 1 : 0;
        $v_send_over_internet = isset($_POST['chk_send_over_internet']) ? '1' : '0';
        
        $v_allow_verify       = isset($_POST['chk_allow_verify_record']) ? '1' : '0';

        //Kiem tra trung ma
        $sql = "Select Count(*)
                From t_r3_record_type
                Where C_CODE='$v_code' And PK_RECORD_TYPE <> $v_record_type_id";
        if ($this->db->getOne($sql) > 0)
        {
            $this->exec_fail($this->goback_url, 'Mã loại hồ sơ đã tồn tại', $arr_filter);
            return;
        }

        //Kiem tra trung ten
        $sql = "Select Count(*)
                From t_r3_record_type
                Where C_NAME='$v_name' And PK_RECORD_TYPE <> $v_record_type_id";
        if ($this->db->getOne($sql) > 0)
        {
            $this->exec_fail($this->goback_url, 'Tên loại hồ sơ đã tồn tại', $arr_filter);
            return;
        }

        if ($v_record_type_id < 1) //Insert
        {
            $stmt   = 'Insert Into t_r3_record_type (C_CODE, C_NAME, C_XML_DATA,C_STATUS,C_SCOPE,C_ORDER, C_SEND_OVER_INTERNET,C_SPEC_CODE,C_ALLOW_VERIFY_RECORD) Values (?,?,?,?,?,?,?,?,?)';
            $params = array($v_code, $v_name, $v_xml_data, $v_status, $v_scope, $v_order, $v_send_over_internet, $v_spec_code, $v_allow_verify);

            $this->db->Execute($stmt, $params);

            $v_record_type_id = $this->get_last_inserted_id('t_r3_record_type', 'PK_RECORD_TYPE');
        }
        else //Update
        {
            $stmt   = 'Update t_r3_record_type Set
                        C_CODE=?
                        ,C_NAME=?
                        ,C_XML_DATA=?
                        ,C_STATUS=?
                        ,C_SCOPE=?
                        ,C_ORDER=?
                        ,C_SEND_OVER_INTERNET=?
                        ,C_SPEC_CODE=?
                        ,C_ALLOW_VERIFY_RECORD=?
                    Where PK_RECORD_TYPE=?';
            $params = array(
                $v_code,
                $v_name,
                $v_xml_data,
                $v_status,
                $v_scope,
                $v_order,
                $v_send_over_internet,
                $v_spec_code,
                $v_allow_verify,
                $v_record_type_id,
            );

            $this->db->Execute($stmt, $params);
        }

        $this->ReOrder('t_r3_record_type', 'PK_RECORD_TYPE', 'C_ORDER', $v_record_type_id, $v_order);
        $this->update_report_book_report_type($v_record_type_id, $arr_report_books);

        //Luu dieu kien loc
        $arr_filter = get_filter_condition(array('txt_filter', 'sel_goto_page', 'sel_rows_per_page'));
        //Done
        if ($v_save_and_addnew > 0)
        {
            $this->exec_done($this->goforward_url, $arr_filter);
        }
        else
        {
           $this->exec_done($this->goback_url, $arr_filter);
        }
    }

    function update_report_book_report_type($v_record_type_id, $arr_new_books)
    {
        if (!is_array($arr_new_books))
        {
            $arr_new_books = array();
        }

        $this->db->Execute('DELETE FROM t_r3_book_record_type 
            WHERE fk_record_type = ?', array($v_record_type_id));
        $insert_list = '';
        reset($arr_new_books);
        $insert_list .= "'" . replace_bad_char(current($arr_new_books)) . "'";
        while (next($arr_new_books))
        {
            $insert_list .= ",'" . replace_bad_char(current($arr_new_books)) . "'";
        }

        $sql_insert = "
            INSERT INTO t_r3_book_record_type(c_book_code, fk_record_type)
            SELECT ls.c_code, ? AS fk_record_type
            FROM t_cores_list ls
            INNER JOIN t_cores_listtype lt
            ON ls.fk_listtype = lt.pk_listtype
            WHERE ls.c_code IN($insert_list)
            AND lt.c_code = 'DM_SO_THEO_DOI_HO_SO'";
        $this->db->Execute($sql_insert, array($v_record_type_id));
    }

}