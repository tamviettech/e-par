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

require_once __DIR__ . '/../record/record_Model.php';

class mapping_Model extends record_Model
{

    /**
     * @var \ADOConnection 
     */
    public $db;

    function __construct()
    {
        parent::__construct();
    }
    
    public function qry_all_record_type()
    {
        $user_login_name = replace_bad_char($_SESSION['user_login_name']);
        
        $stmt = "SELECT DISTINCT
                    C_RECORD_TYPE_CODE,C_NAME
                  FROM t_r3_user_task UT
                  LEFT JOIN t_r3_record_type RT 
                  ON UT.C_RECORD_TYPE_CODE = RT.C_CODE
                  WHERE C_USER_LOGIN_NAME = '$user_login_name'";
        return $this->db->getAll($stmt);
    }
    
    public function update_mapping()
    {
        $user_id = replace_bad_char($_SESSION['user_id']);
        
        $arr_all_record_type = $this->qry_all_record_type();
        
        $v_record_type_code = get_post_var('sel_record_type','');
        $condition = '';
        if($v_record_type_code != '')
        {
            $condition = " And C_RECORD_TYPE_CODE = '$v_record_type_code'";
        }
        
        //xoa toan bo du lieu cu
        $stmt = "DELETE FROM t_r3_mapping WHERE FK_USER = ? $condition";
        $this->db->Execute($stmt,array($user_id));
        
        foreach($arr_all_record_type as $record_type)
        {
            $code = $record_type['C_RECORD_TYPE_CODE'];
            $v_code_mapping  = get_post_var('txt_'.$code,'');
            if($v_code_mapping != '')
            {
                $stmt = "INSERT INTO t_r3_mapping
                                    (FK_USER,
                                     C_RECORD_TYPE_CODE,
                                     C_CODE)
                        VALUES (?,?,?)";
                $this->db->Execute($stmt,array($user_id,$code,$v_code_mapping));
            }
        }
        
        $this->exec_done($this->goback_url);
    }
    /**
     * lay tat ca ma anh xa cua nsd
     * @return type
     */
    public function qry_all_mapping()
    {
        //lay thong tin nsd
        $user_login_name = replace_bad_char($_SESSION['user_login_name']);
        $user_id         = replace_bad_char($_SESSION['user_id']);
        
        //filter
        $v_record_type_code = get_post_var('sel_record_type','');
        $condition = '';
        if($v_record_type_code != '')
        {
            $condition = " And UT.C_RECORD_TYPE_CODE = '$v_record_type_code'";
        }
        
        //query
        $sql = "SELECT
                    UT_RT.C_RECORD_TYPE_CODE,
                    UT_RT.C_NAME,
                    M.C_CODE
                  FROM (SELECT DISTINCT
                          UT.C_RECORD_TYPE_CODE,
                          RT.C_NAME
                        FROM t_r3_user_task UT
                          LEFT JOIN t_r3_record_type RT
                            ON UT.C_RECORD_TYPE_CODE = RT.C_CODE
                        WHERE C_USER_LOGIN_NAME = '$user_login_name' 
                              AND C_STATUS <> 0) UT_RT
                    LEFT JOIN (SELECT
                                 C_CODE,
                                 C_RECORD_TYPE_CODE
                               FROM t_r3_mapping
                               WHERE FK_USER = $user_id) M
                      ON UT_RT.C_RECORD_TYPE_CODE = M.C_RECORD_TYPE_CODE
                  ORDER BY UT_RT.C_RECORD_TYPE_CODE";
        return $this->db->getAll($sql);
    }
}