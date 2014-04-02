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
    
    public function qry_all_mapping()
    {
        $user_login_name = replace_bad_char($_SESSION['user_login_name']);
        
        $v_record_type_code = get_post_var('sel_record_type','');
        $condition = '';
        if($v_record_type_code != '')
        {
            $condition = " And UT.C_RECORD_TYPE_CODE = '$v_record_type_code'";
        }
        
        
        $sql = "SELECT DISTINCT
                    UT.C_RECORD_TYPE_CODE,
                    RT.C_NAME,
                    M.C_CODE
                  FROM t_r3_user_task UT
                    LEFT JOIN t_r3_record_type RT
                      ON UT.C_RECORD_TYPE_CODE = RT.C_CODE
                      LEFT JOIN t_r3_mapping M
                      ON UT.C_RECORD_TYPE_CODE = M.C_RECORD_TYPE_CODE
                  WHERE C_USER_LOGIN_NAME = '$user_login_name' $condition ORDER BY C_RECORD_TYPE_CODE";
        return $this->db->getAll($sql);
    }
}