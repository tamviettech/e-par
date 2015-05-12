<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class license_type_model extends Model {

    function __construct() {
        parent::__construct();
    }

    function update_license_type() {
        if (!isset($_POST)) {
            $this->exec_done($this->goback_url);
            return;
        }

        $v_id = get_post_var('hdn_item_id', 0);
        $v_code = get_post_var('txt_code');
        $v_name = get_post_var('txt_name');
        $v_sector = get_post_var('sel_sector');
        $v_order = get_post_var('txt_order');
        $v_status = get_post_var('chk_status', 0);

        $stmt = 'Select Count(*) From t_license_license_type Where PK_LICENSE_TYPE <>? And (C_CODE=? Or C_NAME=?)';
        $params = array($v_id, $v_code, $v_name);
        $count = $this->db->getOne($stmt, $params) < 1;
        var_dump($count);
        if ($v_id) {
            if ($v_id > 0) {//Update
                check_permission('SUA_LOAI_HO_SO', 'LICENSE') or die($this->access_denied());
                $stmt = 'Update t_license_license_type Set
                            C_CODE=?
                            ,C_NAME=?
                            ,C_ORDER=?
                            ,FK_SECTOR=?
                            ,C_STATUS=?
                        Where PK_LICENSE_TYPE=?';
                $params = array(
                    $v_code
                    , $v_name
                    , $v_order
                    , $v_sector
                    , $v_status
                    , $v_id
                );
                $this->db->Execute($stmt, $params);
            } else {  //Insert
                check_permission('THEM_MOI_LOAI_HO_SO', 'LICENSE') or die($this->access_denied());
                $stmt = 'Insert Into t_license_license_type (
                                    C_CODE
                                    ,C_NAME
                                    ,C_ORDER
                                    ,FK_SECTOR
                                    ,C_STATUS
                        ) Values (
                                    ?
                                    ,?
                                    ,?
                                    ,?
                                    ,?
                                 )';
                $params = array(
                    $v_code
                    , $v_name
                    , $v_sector
                    , $v_order
                    , $v_status
                );
                $this->db->Execute($stmt, $params);

                if (DATABASE_TYPE == 'MSSQL') {
                    $v_id = $this->db->getOne("SELECT IDENT_CURRENT('t_license_license_type')");
                } elseif (DATABASE_TYPE == 'MYSQL') {
                    $v_id = $this->db->Insert_ID('t_license_license_type', 'PK_LICENSE_TYPE');
                }
            }

            $this->ReOrder('t_license_license_type', 'PK_LICENSE_TYPE', 'C_ORDER', $v_id, $v_order);
        }//end Kiem tra trung ten, ma
        //Luu dieu kien loc
        $arr_filter = get_filter_condition(array('txt_filter', 'sel_goto_page', 'sel_rows_per_page'));
        $this->exec_done($this->goback_url, $arr_filter);
    }

    function delete_license_type() {
        $v_item_id_list = get_post_var('hdn_item_id_list');

        //Kiem tra Loai danh muc nay co chua Doi tuong danh muc khong?
        if ($v_item_id_list != '') {
            if (strpos($v_item_id_list, ',') != FALSE) {
                $arr_item_id = explode(',', $v_item_id_list);
                foreach ($arr_item_id as $item_id) {
                    $sql = "SELECT count(*) from t_license_record WHERE FK_LICENSE_TYPE = ?";
                    $has_record = $this->db->GetOne($sql, array($item_id));
                    if (!$has_record) {
                        $sql = "Delete From t_license_license_type Where PK_LICENSE_TYPE = ?";
                        $this->db->Execute($sql, array($item_id));
                    }
                }
            } else { //Chi co 1 doi tuong duoc chon
                $sql = "SELECT count(*) from t_license_record WHERE FK_LICENSE_TYPE = ?";
                $has_record = $this->db->GetOne($sql, array($v_item_id_list));
                if (!$has_record) {
                    $sql = "Delete From t_license_license_type Where PK_LICENSE_TYPE = ?";
                    $this->db->Execute($sql, array($v_item_id_list));
                }
            }
        }

        //Luu dieu kien loc
        $arr_filter = get_filter_condition(array('txt_filter', 'sel_goto_page', 'sel_rows_per_page'));
        $this->exec_done($this->goback_url, $arr_filter);
    }

    function qry_single_license_type($v_license_type_id) {
        if ($v_license_type_id > 0) {
            $stmt = 'Select *
                    From t_license_license_type
                    Where PK_LICENSE_TYPE=?';
            $params = array($v_license_type_id);

            return $this->db->GetRow($stmt, $params);
        } else {
            return array('C_ORDER' => $this->get_max('t_license_license_type', 'C_ORDER'));
        }
    }

    function qry_all_sector() {
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

    function qry_all_license_type() {
        //Loc theo ten, ma
        $v_filter = get_post_var('txt_filter');

        page_calc($v_start, $v_end);

        $condition_query = '';
        if ($v_filter !== '') {
            $condition_query = " And (lt.C_NAME Like '%$v_filter%' Or lt.C_CODE Like '%$v_filter%')";
        }

        //Dem tong ban ghi
        $sql_count_record = "Select Count(*) TOTAL_RECORD From t_license_license_type lt Where (1 > 0) $condition_query";

        //Trả về danh sách Loại danh mục thoả mãn điều kiện lọc
        if (DATABASE_TYPE == 'MSSQL') {
            $sql = "Select
                        lt.*
                        , ($sql_count_record) TOTAL_RECORD
                        , ROW_NUMBER() OVER (ORDER BY lt.C_ORDER ASC) RN
                    From t_license_license_type lt LEFT JOIN (SELECT FK_LICENSE_TYPE,
                    COUNT(PK_RECORD) FROM t_license_record GROUP BY FK_LICENSE_TYPE) co
                    ON lt.PK_LICENSE_TYPE = co.FK_LICENSE_TYPE
                    Where (1 > 0) $condition_query";
            return $this->db->getAll("Select * From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end");
        } elseif (DATABASE_TYPE == 'MYSQL') {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;

            $sql = "Select *
                        , ($sql_count_record) TOTAL_RECORD
                    FROM t_license_license_type lt LEFT JOIN (SELECT FK_LICENSE_TYPE,
                    COUNT(PK_RECORD) COUNT_RECORD FROM t_license_record GROUP BY FK_LICENSE_TYPE) co
                    ON lt.PK_LICENSE_TYPE = co.FK_LICENSE_TYPE
                    Where (1 > 0) $condition_query
                    Order By C_ORDER
                    Limit $v_start, $v_limit";

            return $this->db->getAll($sql);
        } elseif (DATABASE_TYPE == 'ORACLE') {
            return array();
        }
    }

}
