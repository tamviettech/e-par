<?php

defined('DS') or die();
require_once SERVER_ROOT . 'libs/adodb5/adodb-active-record.inc.php';

/**
 * @package reportbook
 * @author Duong Tuan Anh <goat91@gmail.com>
 */
class reportbook_Model extends Model
{
    private $_listtype_code    = 'DM_SO_THEO_DOI_HO_SO';
    private $_listtype_id      = 0;
    private $_la_can_bo_cap_xa = false;

    /**
     *
     * @var \ADOConnection
     */
    public $db;

    function __construct()
    {
        parent::__construct();

        $this->_listtype_id = $this->db->GetOne("SELECT pk_listtype 
            FROM t_cores_listtype 
            WHERE c_code = '{$this->_listtype_code}'");

        $this->_la_can_bo_cap_xa = (bool) Session::get('la_can_bo_cap_xa');
    }

    function qry_all_book()
    {
        //Lay value tu truong c_xml_data
        $scope  = "ExtractValue(c_xml_data, '//item[@id=\"txt_scope\"]/value')";
        $conds  = " WHERE fk_listtype = ? AND C_STATUS = 1 ";
        $params = array($this->_listtype_id);
        
        if ($this->_la_can_bo_cap_xa)
        {
            //cap xa
            $conds .= " AND ($scope = 0 OR $scope = 1) ";
        }
        else
        {
            //Huyen va sở
            $conds .= " AND ($scope = 2 OR $scope = 3 OR $scope = 0 OR $scope = 1) ";
        }

        $sql = "SELECT pk_list as id, c_name 
            FROM t_cores_list 
            $conds
            ORDER BY C_ORDER";

        return $this->db->GetAll($sql, $params);
    }

    function qry_single_book($book_id)
    {
        $scope   = "ExtractValue(c_xml_data, '//item[@id=\"txt_scope\"]/value')";
        $book_id = (int) $book_id;
        $sql     = "SELECT pk_list AS id, c_name, c_code, c_xml_data
            FROM t_cores_list 
            WHERE pk_list = ? 
            AND fk_listtype = ?
            ";
        if ($this->_la_can_bo_cap_xa)
        {
            $sql .= " AND ($scope = 0 OR $scope = 1) ";
        }
        else
        {
            //la can bo cap tinh
            $sql .= " AND ($scope = 2 OR $scope = 3 OR $scope = 0 OR $scope = 1)";
        }
        $data               = $this->db->GetRow($sql, array($book_id, $this->_listtype_id));
        $data['c_xml_data'] = isset($data['c_xml_data']) ? $data['c_xml_data'] : '';
        $data['c_xml_data'] = simplexml_load_string($data['c_xml_data']);
        return $data;
    }

    public function qry_all_records($book_id, $begin_date, $end_date, $paging = true)
    {
        $v_scope_id = (int) get_request_var('sel_scope',0,true);//Ma vung tiep nhan ho so
        $v_scope_id = ($v_scope_id > 0) ? $v_scope_id : (int) Session::get('village_id');
        $book_id    = (int) $book_id; // ma sach

        $begin_date = date_create_from_format('d-m-Y', $begin_date)->format('Y-m-d');
        $end_date   = date_create_from_format('d-m-Y', $end_date)->format('Y-m-d');
        
        page_calc($v_start, $v_end);
        $limit      = $v_end - $v_start + 1;
        $offset     = $v_start;

        $fields     = 'R.*,RT.C_SCOPE,RT.C_SPEC_CODE,RT.C_NAME as C_RECORD_TYPE_NAME';
        $tables     = ' view_record R
                    LEFT JOIN t_r3_book_record_type BRT
                      ON R.FK_RECORD_TYPE = BRT.FK_RECORD_TYPE
                    LEFT JOIN t_r3_record_type RT
                      ON R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE';
        $conditions = 'BINARY BRT.C_BOOK_CODE = BINARY (SELECT
                                             C_CODE
                                         FROM t_cores_list
                                         WHERE PK_LIST = ?)
                AND R.c_clear_date IS NOT NULL
                AND R.c_receive_date >= ? 
                AND R.c_receive_date <= ?
                AND R.fk_village_id = ?';

        $params = array($book_id, $begin_date, $end_date, $v_scope_id);

        $count_sql    = "SELECT COUNT(*) FROM $tables WHERE $conditions";
        $total_record = $this->db->GetOne($count_sql, $params);
        $sql          = "
            SELECT $fields 
                , $total_record AS TOTAL_RECORD
                , @rownum := @rownum+1 AS RN
            FROM $tables 
            WHERE $conditions 
            ";
        if ($paging)
        {
            $sql .= "LIMIT $limit OFFSET $offset;";
            $this->db->Execute("SET @rownum =" . ($offset - 1));
        }
        else
        {
            $this->db->Execute("SET @rownum = 0;");
        }
        return $this->db->GetAll($sql, $params);
    }

    function qry_all_record_type_to_add($book_code)
    {
        $arr_old_record_type = $this->db->GetCol("SELECT fk_record_type
            FROM t_r3_book_record_type
            WHERE c_book_code = ?", array($book_code));
        $fields              = 'PK_RECORD_TYPE, C_NAME, C_CODE';
        $table               = "t_r3_record_type";
        $cond                = '1=1';
        page_calc($v_start, $v_end);
        $limit               = $v_end - $v_start + 1;
        if (!empty($arr_old_record_type))
        {
            $cond .= ' AND pk_record_type NOT IN(' . implode(',', $arr_old_record_type) . ')';
        }
        if ($v_search_name = get_post_var('txt_search_name'))
        {
            $cond .= " AND (c_code LIKE '%$v_search_name%' OR c_name LIKE '%$v_search_name%')";
        }
        $total_record = $this->db->GetOne("SELECT COUNT(*) FROM $table WHERE $cond");
        $fields .= ", $total_record AS TOTAL_RECORD";
        return $this->db->GetAll("SELECT $fields FROM $table WHERE $cond LIMIT $limit OFFSET $v_start");
    }

    function insert_record_type_to_book()
    {
        $book_id         = get_post_var('book_id');
        $arr_single_book = $this->qry_single_book($book_id);
        $arr_record_type = get_post_var('arr_record_type', array(), false);
        if (empty($arr_record_type) OR !$arr_single_book)
        {
            return;
        }
        $arr_record_type = replace_bad_char(implode(',', $arr_record_type));
        $sql             = "
            INSERT INTO t_r3_book_record_type(c_book_code, fk_record_type)
            SELECT ? AS c_book_code, pk_record_type AS fk_record_type
            FROM t_r3_record_type
            WHERE pk_record_type IN($arr_record_type)
            ";
        $this->db->Execute($sql, array($arr_single_book['c_code']));
    }

    function delete_record_type_from_book()
    {
        $delete_list   = get_post_var('hdn_item_id_list');
        $book_code     = get_post_var('hdn_item_code');
        $book_id       = get_post_var('hdn_item_id');
        $goback_method = get_post_var('hdn_go_back');
        $controller    = get_post_var('controller');

        $sql = "
            DELETE FROM t_r3_book_record_type
            WHERE fk_record_type IN($delete_list)
            AND c_book_code = ?";
        $this->db->Execute($sql, array($book_code));
        $this->exec_done($controller . $goback_method . '/' . $book_id);
    }

    function qry_all_record_type($book_code)
    {
        $arr_old_record_type = $this->db->GetCol("SELECT fk_record_type
            FROM t_r3_book_record_type
            WHERE c_book_code = ?", array($book_code));
        if (!empty($arr_old_record_type))
        {
            $fields              = 'PK_RECORD_TYPE, C_CODE, C_NAME';
            $arr_old_record_type = implode(',', $arr_old_record_type);
            $sql                 = "
                SELECT $fields
                FROM t_r3_record_type
                WHERE pk_record_type IN($arr_old_record_type)
                ORDER BY c_order
            ";
            return $this->db->GetAll($sql);
        }
        return array();
    }
    /**
     * lay danh dach khu vu tiep nhan ho so
     */
    function qry_all_scope()
    {        
        $village_id = (int) Session::get('village_id');
        $results = array();
        if ($this->_la_can_bo_cap_xa)
        {
             $sql = "SELECT * 
                    FROM t_cores_ou 
                    WHERE C_LEVEL = 3
                    and C_STATUS is null
                    and PK_OU = $village_id
                    ORDER BY C_INTERNAL_ORDER";
             $results = $this->db->GetAll($sql);
        }
        else
        {
            //huyen sở
            $sql = "SELECT * 
                    FROM t_cores_ou 
                    WHERE C_STATUS is null
                    and C_LEVEL =3
                    ORDER BY C_INTERNAL_ORDER";
            $results = $this->db->GetArray($sql);
        }
        return $results;
    }
/**
     * Lấy danh sách Role của một user
     * @param string $user_code
     * @return array
     */
    public function qry_all_user_role($user_code)
    {
        if (DATABASE_TYPE == 'MSSQL')
        {
            $stmt = "Select distinct right(C_TASK_CODE, PATINDEX ( '%[A-Z0-9_][" . _CONST_XML_RTT_DELIM . "]%' , reverse(C_TASK_CODE))) C_ROLE
                    From t_r3_user_task  Where C_USER_LOGIN_NAME=?";
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $stmt = "Select distinct Right(C_TASK_CODE, LOCATE('" . _CONST_XML_RTT_DELIM . "', REVERSE(C_TASK_CODE)) - 1) C_ROLE
                    From t_r3_user_task Where C_USER_LOGIN_NAME=?";
        }
        $params = array($user_code);

        if (DEBUG_MODE < 10)
        {
            $this->db->debug = 0;
        }
        $ret             = $this->db->getCol($stmt, $params);
        $this->db->debug = DEBUG_MODE;
        return $ret;
    }
}