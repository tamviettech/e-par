<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class Record_model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    function delete_record()
    {
        $v_item_id_list = get_post_var('hdn_item_id_list');
        if ($v_item_id_list != '')
        {
            $sql = "UPDATE t_license_record SET C_DELETED = 1 WHERE PK_RECORD in ($v_item_id_list)";
            $this->db->Execute($sql);
        }

        //Luu dieu kien loc
        $arr_filter = get_filter_condition(array('txt_filter', 'sel_goto_page', 'sel_rows_per_page'));
        $this->exec_done($this->goback_url, $arr_filter);
    }

    function qry_license_type_code_by_record_id($v_license_type_id)
    {
        return $this->db->GetOne('SELECT LT.C_CODE FROM t_license_license_type LT JOIN t_license_record RE ON RE.FK_LICENSE_TYPE = LT.PK_LICENSE_TYPE WHERE RE.PK_RECORD = ?', array($v_license_type_id));
    }

    function qry_all_active_license_type()
    {
        return $this->db->GetAssoc("SELECT C_CODE, CONCAT(C_CODE,' - ',C_NAME) FROM t_license_license_type WHERE C_STATUS = 1 ORDER BY C_ORDER");
    }

    function qry_all_sector()
    {
        $stmt = "Select
                    L.PK_LIST
                    , L.C_NAME
                From t_cores_list As L
                Where L.C_STATUS > 0
                    And L.FK_LISTTYPE=(Select PK_LISTTYPE From t_cores_listtype Where C_CODE=? And C_STATUS>0)
                    Order By C_ORDER";
        $params = array('DANH_MUC_LINH_VUC');
        return $this->db->getAssoc($stmt, $params);
    }

    function qry_single_record($v_record_id)
    {
        $stmt = "SELECT PK_RECORD"
                . ", C_RELATED_CODE"
                . ", LT.C_CODE AS C_LICENSE_TYPE_CODE"
                . ", RE.C_CODE AS C_CODE"
                . ", C_LICENSE_NO"
                . ", C_CITIZEN_NAME"
                . ", C_RETURN_EMAIL"
                . ", C_RETURN_PHONE_NUMBER"
                . ", C_CREATED_DATE"
                . ", C_ISSUED_DATE"
                . ", C_VALID_TO_DATE"
                . ", C_SOURCE"
                . ", RE.C_XML_DATA"
                . ", C_DELETED"
                . " FROM t_license_license_type LT JOIN t_license_record RE"
                . " ON LT.PK_LICENSE_TYPE = RE.FK_LICENSE_TYPE"
                . " WHERE PK_RECORD = ?";
        return $this->db->GetRow($stmt, array($v_record_id));
    }

    function qry_all_record()
    {
        //Loc theo ten, ma
        $v_filter = get_post_var('txt_filter');
        $v_code = get_post_var('txt_license_type_code');
        $v_start_date = jwDate::ddmmyyyy_to_yyyymmdd(get_post_var('txt_start_date'));
        $v_end_date = jwDate::ddmmyyyy_to_yyyymmdd(get_post_var('txt_end_date'));
        $v_empty_license_no = get_post_var('chk_empty_license_no');
        page_calc($v_start, $v_end);

        $condition_query = '';
        if ($v_filter !== '')
        {
            $condition_query = " And (RE.C_CITIZEN_NAME Like '%$v_filter%' Or RE.C_CODE Like '%$v_filter%')";
        }

        if ($v_code !== '')
        {
            $sql = 'SELECT PK_LICENSE_TYPE FROM t_license_license_type WHERE C_CODE = ?';
            $v_license_type_id = $this->db->GetOne($sql, array($v_code));
            $condition_query .= " And RE.FK_LICENSE_TYPE = $v_license_type_id ";
        }

        if ($v_start_date !== '')
        {
            $condition_query .= " AND C_ISSUED_DATE >= '$v_start_date 00:00:00'";
        }
        if ($v_end_date !== '')
        {
            $condition_query .= " AND C_ISSUED_DATE <= '$v_end_date 23:59:59'";
        }
        $condition_query.= ' AND C_DELETED = 0';
        if ($v_empty_license_no != null)
        {
            $condition_query .= " And C_LICENSE_NO = ''";
        }
        //Dem tong ban ghi
        $sql_count_record = "Select Count(*) TOTAL_RECORD From t_license_record RE Where (1 > 0) $condition_query";

        //Trả về danh sách Loại danh mục thoả mãn điều kiện lọc
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select
                        lt.*
                        , ($sql_count_record) TOTAL_RECORD
                        , ROW_NUMBER() RN
                    From t_license_record RE
                    Where (1 > 0) $condition_query";
            return $this->db->getAll("Select * From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end");
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;

            $sql = "Select *
                        , ($sql_count_record) TOTAL_RECORD
                    FROM t_license_record RE
                    Where (1 > 0) $condition_query
                    Limit $v_start, $v_limit";

            return $this->db->getAll($sql);
        }
        elseif (DATABASE_TYPE == 'ORACLE')
        {
            return array();
        }
    }

    function prepare_update_record()
    {
        $arr_update['v_record_id'] = isset($_POST['hdn_item_id']) ? replace_bad_char($_POST['hdn_item_id']) : 0;
        $arr_update['v_license_type_code'] = isset($_POST['sel_license_type_code']) ? replace_bad_char($_POST['sel_license_type_code']) : '';
        $arr_update['v_code'] = isset($_POST['txt_code']) ? replace_bad_char(strtoupper($_POST['txt_code'])) : '';
        $arr_update['v_license_no'] = isset($_POST['txt_license_no']) ? replace_bad_char($_POST['txt_license_no']) : '';
        $arr_update['v_citizen_name'] = isset($_POST['txt_citizen_name']) ? replace_bad_char($_POST['txt_citizen_name']) : '';
        $arr_update['v_return_email'] = isset($_POST['txt_return_email']) ? replace_bad_char($_POST['txt_return_email']) : '';
        $arr_update['v_return_phone_number'] = isset($_POST['txt_return_phone_number']) ? replace_bad_char($_POST['txt_return_phone_number']) : '';
        $arr_update['v_related_code'] = isset($_POST['txt_related_code']) ? replace_bad_char($_POST['txt_related_code']) : '';
        $arr_update['v_issued_date'] = isset($_POST['txt_issued_date']) ? replace_bad_char(jwDate::ddmmyyyy_to_yyyymmdd($_POST['txt_issued_date'])) : '';
        $arr_update['v_valid_to_date'] = isset($_POST['txt_valid_to_date']) ? replace_bad_char(jwDate::ddmmyyyy_to_yyyymmdd($_POST['txt_valid_to_date'])) : '';

        $arr_update['v_xml_data'] = isset($_POST['XmlData']) ? $_POST['XmlData'] : '<root/>';

        $arr_update['v_license_type_id'] = $this->db->GetOne(
                'SELECT PK_LICENSE_TYPE FROM t_license_license_type'
                . ' WHERE C_CODE = ?'
                , array($arr_update['v_license_type_code'])
        );
        $this->update_record($arr_update);
    }

    public function update_record($arr_update)
    {
        if (!check_permission("SUA_HO_SO", "LICENSE") && !check_permission("THEM_MOI_HO_SO", "LICENSE"))
        {
            die();
        }
        foreach ($arr_update as $k => $v)
        {
            $$k = $v;
        }
        if ($v_record_id < 1 && check_permission("THEM_MOI_HO_SO", "LICENSE"))
        {
            $stmt = 'INSERT INTO t_license_record('
                    . 'C_RELATED_CODE'
                    . ',FK_LICENSE_TYPE'
                    . ',C_CODE'
                    . ',C_LICENSE_NO'
                    . ',C_CITIZEN_NAME'
                    . ',C_RETURN_EMAIL'
                    . ',C_RETURN_PHONE_NUMBER'
                    . ',C_CREATED_DATE'
                    . ',C_ISSUED_DATE'
                    . ',C_VALID_TO_DATE'
                    . ',C_SOURCE'
                    . ',C_XML_DATA'
                    . ',C_DELETED) values(?,?,?,?,?,?,?,NOW(),?,?,1,?,0)';
            $this->db->Execute($stmt, array(
                $v_related_code
                , $v_license_type_id
                , $v_code
                , $v_license_no
                , $v_citizen_name
                , $v_return_email
                , $v_return_phone_number
                , $v_issued_date
                , $v_valid_to_date
                , $v_xml_data
                    )
            );
        }
        else if ($v_record_id > 0 && check_permission("SUA_HO_SO", "LICENSE"))  //Update
        {
            $stmt = 'UPDATE t_license_record SET '
                    . ' C_RELATED_CODE = ?'
                    . ',C_LICENSE_NO = ?'
                    . ',C_CITIZEN_NAME = ?'
                    . ',C_RETURN_EMAIL = ?'
                    . ',C_RETURN_PHONE_NUMBER = ?'
                    . ',C_ISSUED_DATE = ?'
                    . ',C_VALID_TO_DATE = ?'
                    . ',C_XML_DATA = ?'
                    . ' WHERE PK_RECORD = ?'
                    . ' ';
            $this->db->Execute($stmt, array(
                $v_related_code
                , $v_license_no
                , $v_citizen_name
                , $v_return_email
                , $v_return_phone_number
                , $v_issued_date
                , $v_valid_to_date
                , $v_xml_data
                , $v_record_id
                    )
            );
        }
        else
        {
            die();
        }

        //Upload file
        $count = count($_FILES['uploader']['name']);
        for ($i = 0; $i < $count; $i++)
        {
            if ($_FILES['uploader']['error'][$i] == 0)
            {
                $v_file_name = $_FILES['uploader']['name'][$i];
                $v_tmp_name = $_FILES['uploader']['tmp_name'][$i];
                $v_file_ext = array_pop(explode('.', $v_file_name));
                $v_system_file_name = uniqid();
                if ($v_file_ext)
                {
                    $v_system_file_name .= '.' . $v_file_ext;
                }
                if (in_array($v_file_ext, explode(',', _CONST_RECORD_FILE_ACCEPT)))
                {
                    if (move_uploaded_file($v_tmp_name, SERVER_ROOT . "uploads" . DS . 'license' . DS . $v_system_file_name))
                    {
                        $stmt = 'INSERT INTO t_license_media(C_NAME, C_EXT, FK_RECORD,C_FILE_NAME,C_UPLOAD_DATE) Values(?,?,?,?,NOW())';
                        $params = array($v_file_name, $v_file_ext, $v_record_id, $v_system_file_name);
                        $this->db->Execute($stmt, $params);
                    }
                }
            }
        }
        //Delete File
        //Xoa file dinh kem
        $v_deleted_file_id_list = ltrim($_POST['hdn_deleted_file_id_list'], ',');
        if ($v_deleted_file_id_list != '')
        {
            $sql = "SELECT C_FILE_NAME FROM t_license_media Where PK_MEDIA in (?)";
            $arr_system_file_name = $this->db->GetCol($sql, array($v_deleted_file_id_list));
            foreach ($arr_system_file_name as $value)
            {
                unlink(SERVER_ROOT . "uploads" . DS . 'license' . DS . $value);
            }
            $sql = "Delete From t_license_media Where PK_MEDIA in (?)";
            $this->db->Execute($sql, array($v_deleted_file_id_list));
        }

        $arr_filter = array(
            'sel_license_type' => $v_license_type_code,
        );
        $this->exec_done($this->goback_url, $arr_filter);
    }

//end func update_record


    public function qry_all_record_file($item_id)
    {
        if (intval($item_id) < 1)
        {
            return NULL;
        }
        $stmt = 'Select * FROM t_license_media Where FK_RECORD=?';
        return $this->db->getAll($stmt, array($item_id));
    }

}
