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
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');?>
<?php
class xlist_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Kiểm tra Mã loại danh mục đã tồn lại
     * @return json
     */
    public function check_existing_listtype_code()
    {
        $v_code         = get_request_var('code');
        $v_listtype_id  = get_request_var('listtype_id');

        $stmt = 'Select count(*) count from t_cores_listtype Where (PK_LISTTYPE <> ?) And (C_CODE=?)';
        $params = array($v_listtype_id, $v_code);
        $this->db->debug=0;
        return json_encode($this->db->getRow($stmt, $params));
    }

    /**
     * Kiểm tra Tên loại danh mục đã tồn tại
     * @return json
     */
    public function check_existing_listtype_name()
    {
        $v_name         = get_request_var('name');
        $v_listtype_id  = get_request_var('listtype_id');

        $stmt = 'Select count(*) count from t_cores_listtype Where (PK_LISTTYPE <> ?) And (C_NAME=?)';
        $params = array($v_listtype_id, $v_name);
        $this->db->debug=0;
        return json_encode($this->db->getRow($stmt, $params));
    }

    /**
     * Lấy danh sách Loại danh mục theo điều kiện lọc
     */
    public function qry_all_listtype()
    {
        //Loc theo ten, ma
        $v_filter = get_post_var('txt_filter');

        page_calc($v_start, $v_end);

        $condition_query = '';
        if ($v_filter !== '')
        {
            $condition_query = " And (LT.C_NAME Like '%$v_filter%' Or LT.C_CODE Like '%$v_filter%')";
        }

        //Dem tong ban ghi
        $sql_count_record = "Select Count(*) TOTAL_RECORD From t_cores_listtype LT Where (1 > 0) $condition_query";

        //Trả về danh sách Loại danh mục thoả mãn điều kiện lọc
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select
                        LT.PK_LISTTYPE
                        , LT.C_CODE
                        , LT.C_NAME
                        , LT.C_ORDER
                        ,LT.C_STATUS
                        , ($sql_count_record) TOTAL_RECORD
                        , ROW_NUMBER() OVER (ORDER BY LT.C_ORDER ASC) RN
                    From t_cores_listtype LT
                    Where (1 > 0) $condition_query";
            return $this->db->getAll("Select * From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end");
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;

            $sql = "Select
                        LT.PK_LISTTYPE
                        , LT.C_CODE
                        , LT.C_NAME
                        , LT.C_ORDER
                        ,LT.C_STATUS
                        , ($sql_count_record) TOTAL_RECORD
                    FROM t_cores_listtype LT
                    Where (1 > 0) $condition_query
                    Order By C_ORDER
                    Limit $v_start, $v_limit";

            return $this->db->getAll($sql);
        }
        elseif (DATABASE_TYPE == 'ORACLE')
        {
            return array();
        }
    }

    /**
     * Lấy thông tin chi tiết của một Loại danh mục
     * @return Array 1-D
     */
    public function qry_single_listtype()
    {
        $v_listtype_id = get_post_var('hdn_item_id',0);
        if ($v_listtype_id > 0)
        {
            $stmt = 'Select
                        PK_LISTTYPE
                        , C_CODE
                        , C_NAME
                        , C_OWNER_CODE_LIST
                        , C_XML_FILE_NAME
                        , C_ORDER
                        , C_STATUS
                        ,(SELECT GROUP_CONCAT(C_ORDER)
                             FROM t_cores_listtype
                            where  PK_LISTTYPE <> lt.PK_LISTTYPE
                            ) as C_EXITS_ORDER
                    From t_cores_listtype lt
                    Where PK_LISTTYPE=?';
            $params = array($v_listtype_id);

            return $this->db->GetRow($stmt, $params);
        }
        else
        {
            $stmt = "SELECT GROUP_CONCAT(C_ORDER) as C_EXITS_ORDER,
                            max(C_ORDER) as C_ORDER
                             FROM t_cores_listtype
                            where  PK_LISTTYPE
                           ";
            return  $this->db->GetRow($stmt);
        }
    }

    /**
     * Cập nhật thông tin danh mục
     */
    public function update_listtype() {

        $v_listtype_id        = get_post_var('hdn_item_id');
        $v_listtype_code      = get_post_var('txt_code');
        $v_listtype_name      = get_post_var('txt_name');
        $v_xml_file_name      = get_post_var('txt_xml_file_name');
        $v_order              = get_post_var('txt_order');
        $v_status             = isset($_POST['chk_status']) ? 1 : 0;
        $v_save_and_addnew    = isset($_POST['chk_save_and_addnew']) ? 1 : 0;
        
        //Kiem tra trung ma, ten
        $stmt = 'Select Count(*) From t_cores_listtype Where PK_LISTTYPE <>? And (C_CODE=? Or C_NAME=?)';
        $params = array($v_listtype_id, $v_listtype_code, $v_listtype_name);
        if ($this->db->getOne($stmt, $params) < 1)
        {
            if ($v_listtype_id > 0)  //Update
            {
                $stmt = 'Update t_cores_listtype Set
                            C_CODE=?
                            ,C_NAME=?
                            ,C_OWNER_CODE_LIST=Null
                            ,C_XML_FILE_NAME=?
                            ,C_ORDER=?
                            ,C_STATUS=?
                        Where PK_LISTTYPE=?';
                $params = array(
                                $v_listtype_code
                                ,$v_listtype_name
                                ,$v_xml_file_name
                                ,$v_order
                                ,$v_status
                                ,$v_listtype_id
                                );
                $this->db->Execute($stmt, $params);
            }
            else  //Insert
            {
                $stmt = 'Insert Into t_cores_listtype (
                                    C_CODE
                                    ,C_NAME
                                    ,C_OWNER_CODE_LIST
                                    ,C_XML_FILE_NAME
                                    ,C_ORDER
                                    ,C_STATUS
                        ) Values (
                                    ?
                                    ,?
                                    ,Null
                                    ,?
                                    ,?
                                    ,?
                                 )';
                $params = array(
                               $v_listtype_code
                                ,$v_listtype_name
                                ,$v_xml_file_name
                                ,$v_order
                                ,$v_status
                          );
                $this->db->Execute($stmt, $params);

                if (DATABASE_TYPE == 'MSSQL')
                {
                    $v_listtype_id = $this->db->getOne("SELECT IDENT_CURRENT('t_cores_listtype')");
                }
                elseif (DATABASE_TYPE == 'MYSQL')
                {
                    $v_listtype_id = $this->db->Insert_ID('t_cores_listtype','PK_LISTTYPE');
                }

            } //endif $v_listtype_id

        $this->ReOrder('t_cores_listtype','PK_LISTTYPE','C_ORDER', $v_listtype_id, $v_order);
        }//end Kiem tra trung ten, ma

        //Luu dieu kien loc
        $arr_filter = get_filter_condition(array('txt_filter', 'sel_goto_page','sel_rows_per_page'));

        if ($v_save_and_addnew > 0)
        {
            $this->exec_done($this->goforward_url, $arr_filter);
        }
        else
        {
            $this->exec_done($this->goback_url, $arr_filter);
        }
    }//end func update_listtype

    /**
     * Lấy danh sách Loại Danh mục làm danh sách lựa chọn
     */
    public function qry_all_listtype_option()
    {
        $stmt = "Select LT.PK_LISTTYPE, LT.C_NAME From t_cores_listtype LT Where (LT.C_STATUS > 0) Order by LT.C_ORDER";
        return $this->db->getAssoc($stmt);
    }

    /**
     * Xoá một hay nhiều Loại danh mục
     */
    public function delete_listtype() {
        $v_item_id_list = get_post_var('hdn_item_id_list');

        //Kiem tra Loai danh muc nay co chua Doi tuong danh muc khong?
        if ($v_item_id_list != '')
        {
            if (strpos($v_item_id_list, ',') != FALSE) { //Nhieu doi tuong duoc chon
                $sql = "Select count(*) from t_cores_list Where FK_LISTTYPE in ($v_item_id_list)";
                if ($this->db->getOne($sql) == 0)
                {
                    $sql = "Delete From t_cores_listtype Where PK_LISTTYPE in ($v_item_id_list)";
                    $this->db->Execute($sql);
                }
            }
            else //Chi co 1 doi tuong duoc chon
            {
                $sql = "Select count(*) from t_cores_list Where FK_LISTTYPE=$v_item_id_list";
                if ($this->db->getOne($sql) == 0)
                {
                    $sql = "Delete From t_cores_listtype Where PK_LISTTYPE=$v_item_id_list";
                    $this->db->Execute($sql);
                }
            }
        }

        //Luu dieu kien loc
        $arr_filter = get_filter_condition(array('txt_filter', 'sel_goto_page', 'sel_rows_per_page'));
        $this->exec_done($this->goback_url, $arr_filter);
    }

    //List
    /**
     * Kiểm tra Mã danh mục đã tồn tại
     * @param unknown $code
     * @param unknown $listtype_id
     * @param unknown $list_id
     * @return string
     */
    public function check_existing_list_code($code, $listtype_id, $list_id)
    {
        $code           = replace_bad_char($code);
        $listtype_id    = replace_bad_char($listtype_id);
        $list_id        = replace_bad_char($list_id);
        
        $sql = "Select count(*) count from t_cores_list Where (PK_LIST <> $list_id) AND (C_CODE='$code') AND (FK_LISTTYPE=$listtype_id)";
        $this->db->debug=0;
        return json_encode($this->db->getRow($sql));
    }

    public function check_existing_list_name($name, $listtype_id, $list_id)
    {
        $name = replace_bad_char($name);
        $listtype_id    = replace_bad_char($listtype_id);
        $list_id        = replace_bad_char($list_id);

        $sql = "Select count(*) count from t_cores_list Where (PK_LIST <> $list_id) AND (C_NAME='$name') And (FK_LISTTYPE=$listtype_id)";
        $this->db->debug=0;
        return json_encode($this->db->getRow($sql));
    }

    /**
     * Lay danh sach doi tuong danh muc
     * @return multitype:
     */
    public function qry_all_list()
    {
        //Dieu kien loc
        $v_filter = get_post_var('txt_filter');
        $v_listtype_id = get_post_var('sel_listtype_filter',0);
        if ($v_listtype_id == 0 )
        {
            $v_listtype_id = @$this->db->GetOne('Select PK_LISTTYPE From t_cores_listtype Order by C_ORDER');
        }

        //Ket qua theo trang
        page_calc($v_start, $v_end);

        $condition_query = " FK_LISTTYPE=$v_listtype_id";
        if ($v_filter !== '') {
            $condition_query .= " And (C_NAME Like '%$v_filter%' Or C_CODE Like '%$v_filter%')";
        }

        //Tong so ban ghi
        $sql_count_record = "Select Count(*) TOTAL_RECORD From t_cores_list L Where $condition_query";

        //Ket qua theo dieu kien loc
        if (DATABASE_TYPE == 'MSSQL')
        {
            $sql = "Select
                        PK_LIST
                        , C_CODE
                        , C_NAME
                        , C_ORDER
                        , C_STATUS
                        , ($sql_count_record) TOTAL_RECORD
                        , ROW_NUMBER() OVER (ORDER BY C_ORDER ASC) RN
                    FROM t_cores_list L
                    Where $condition_query";
            return $this->db->getAll("Select * From ($sql) a Where a.rn>=$v_start And a.rn<=$v_end");
        }
        elseif (DATABASE_TYPE == 'MYSQL')
        {
            $v_start = $v_start - 1;
            $v_limit = $v_end - $v_start;

            $sql = "Select
                        PK_LIST
                        , C_CODE
                        , C_NAME
                        , C_ORDER
                        , C_STATUS
                        , ($sql_count_record) TOTAL_RECORD
                    FROM t_cores_list L
                    Where $condition_query
                    Order By C_ORDER
                    Limit $v_start, $v_limit";
            return $this->db->getAll($sql);
        }
        elseif (DATABASE_TYPE == 'ORACLE')
        {
            return array();
        }
    } //end func qry_all_list



    public function qry_single_list() {
        $v_list_id         = get_post_var('hdn_item_id',0);
        $v_listtype_id     = get_post_var('sel_listtype_filter',0);

        if ($v_list_id > 0)
        {
            $stmt = 'Select L.PK_LIST
                            , L.FK_LISTTYPE
                            , L.C_CODE
                            , L.C_NAME
                            , L.C_XML_DATA
                            , L.C_ORDER
                            , L.C_STATUS
                            , LT.C_XML_FILE_NAME
                    From t_cores_list L left join t_cores_listtype LT on L.FK_LISTTYPE=LT.PK_LISTTYPE
                    Where L.PK_LIST=?';
            $params = array('id' => $v_list_id);
            return $this->db->getRow($stmt, $params);
        }
        else
        {
            $a['C_XML_FILE_NAME']   = $this->db->getOne("SELECT C_XML_FILE_NAME FROM t_cores_listtype WHERE PK_LISTTYPE=$v_listtype_id");
            $a['C_ORDER']           = $this->get_max('t_cores_list', 'C_ORDER', "FK_LISTTYPE=$v_listtype_id");
            $a['FK_LISTTYPE']       = $v_listtype_id;
            return $a;
        }
    } //end func qry_single_list

    public function update_list() {

        if (!isset($_POST))
        {
            $this->exec_done($this->goback_url);
            return;
        }

        $v_list_id      = get_post_var('hdn_item_id',0);
        $v_listtype_id  = get_post_var('sel_listtype_filter',0);
        $v_list_code    = get_post_var('txt_code');
        $v_list_name    = get_post_var('txt_name');
        $v_order        = get_post_var('txt_order');
        $v_status       = isset($_POST['chk_status']) ? 1 : 0;
        $v_xml_data     = isset($_POST['XmlData']) ? $_POST['XmlData'] : '<root/>';

        $v_save_and_addnew = isset($_POST['chk_save_and_addnew']) ? 1 : 0;
        
        //Kiem tra trung ten doi tuong
        $sql = "select count(*) "
                . "From t_cores_list "
                . "Where C_NAME = ? "
                . " And FK_LISTTYPE = $v_listtype_id "
                . " And PK_LIST <> $v_list_id";        
        $count_exist_name  = $this->db->GetOne($sql,$v_list_name);
        
        //Kiem tra trung ma
        $sql = "select count(*) "
                . "From t_cores_list "
                . "Where C_CODE = $v_list_code "
                . " And FK_LISTTYPE = $v_listtype_id "
                . " And PK_LIST <> $v_list_id";        
        $count_exist_code  = $this->db->GetOne($sql,$v_list_name);
        
        
        //Luu dieu kien loc
        $v_filter = isset($_POST['txt_filter']) ? $_POST['txt_filter'] : '';
        $v_page = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;

        $arr_filter = get_filter_condition(array(
                                                'sel_listtype_filter'
                                                ,'txt_filter'
                                                ,'sel_goto_page'
                                                ,'sel_rows_per_page'
                                            )
                        );
        
        //Khong trung ten + khong trung ma thi update
        if ( $count_exist_name + $count_exist_code == 0)
        {
            if ($v_list_id > 0) 
            {
                //Update
                $stmt = "Update t_cores_list Set
                            FK_LISTTYPE=$v_listtype_id
                            ,C_CODE='$v_list_code'
                            ,C_NAME='$v_list_name'
                            ,C_OWNER_CODE_LIST=''
                            ,C_XML_DATA='$v_xml_data'
                            ,C_ORDER=$v_order
                            ,C_STATUS=$v_status
                        Where PK_LIST=$v_list_id " ;

                $this->db->Execute($stmt);
            }
            else
            {
                //Insert
                $stmt = "Insert Into t_cores_list (
                            FK_LISTTYPE,
                            C_CODE,
                            C_NAME,
                            C_OWNER_CODE_LIST,
                            C_XML_DATA,
                            C_ORDER,
                            C_STATUS

                        ) values (
                            $v_listtype_id,
                            '$v_list_code',
                            '$v_list_name',
                            '',
                            '$v_xml_data',
                            $v_order,
                            $v_status
                        ) ";

                $this->db->Execute($stmt);

                if (DATABASE_TYPE == 'MSSQL')
                {
                    $v_list_id = $this->db->getOne("SELECT IDENT_CURRENT('t_cores_list')");
                }
                elseif (DATABASE_TYPE == 'MYSQL')
                {
                    $v_list_id = $this->db->Insert_ID('t_cores_list','PK_LIST');
                }
            }//end if ($v_list_id > 0) 
        
            $this->ReOrder('t_cores_list','PK_LIST','C_ORDER', $v_list_id, $v_order,'1',"FK_LISTTYPE=$v_listtype_id");
        }
        else
        {
            $this->exec_fail($this->goforward_url, 'Mã hoặc tên đối tượng đã tồn tại!', $arr_filter);
        }
            
        if ($v_save_and_addnew > 0) 
        {
            $this->exec_done($this->goforward_url, $arr_filter);
        } 
        else
        {
            $this->exec_done($this->goback_url, $arr_filter);
        }
    } //end func update_list

    public function delete_list() {
        if (!isset($_POST))
        {
            $this->exec_done($this->goback_url);
            return;
        }

        $v_item_id_list = isset($_POST['hdn_item_id_list']) ? replace_bad_char($_POST['hdn_item_id_list']) : '';

        //Kiem tra Loai danh muc nay co chua Doi tuong danh muc khong?
        if ($v_item_id_list != '')
        {
            if (strpos($v_item_id_list, ',') !== FALSE)
            {
                //Nhieu doi tuong duoc chon
                $sql = "Delete From t_cores_list Where PK_LIST in ($v_item_id_list)";
            }
            else
            {
                //Chi co 1 doi tuong duoc chon
                $sql = "Delete From t_cores_list Where PK_LIST=$v_item_id_list";

            }
			$this->db->Execute($sql);
        }



        //Luu dieu kien loc
        $v_filter = isset($_POST['txt_filter']) ? $_POST['txt_filter'] : '';
        $v_listtype_id = isset($_POST['sel_listtype_filter']) ? $_POST['sel_listtype_filter'] : '0';
        $v_page = isset($_POST['sel_goto_page']) ? replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
        $arr_filter = array(
            'sel_listtype_filter'   => $v_listtype_id,
            'txt_filter'            => $v_filter,
            'sel_goto_page'   => $v_page,
            'sel_rows_per_page'   => $v_rows_per_page
        );

        $this->exec_done($this->goback_url, $arr_filter);
    } //end func delete_list()

}