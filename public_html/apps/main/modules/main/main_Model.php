<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');?>
<?php
class main_Model extends Model{
    
    /**
     * @var \ADOConnection 
     */
    public $db;

    function __construct()
    {
        parent::__construct();
    }
    
    public function qry_all_permitted_application()
    {
        $v_user_code       = Session::get('user_code');
        $v_is_admin        = Session::get('is_admin');

        $sql = "SELECT 
                    TRIM(A.C_CODE) AS C_CODE
                    ,TRIM(A.C_NAME) AS C_NAME
                    ,A.C_DEFAULT_MODULE
                FROM t_cores_application A
                WHERE A.C_STATUS > 0 And (
                    (A.PK_APPLICATION IN
                        (SELECT UF.FK_APPLICATION
                         FROM t_cores_user_function UF
                         LEFT JOIN t_cores_application a ON UF.FK_APPLICATION = a.PK_APPLICATION
                         WHERE (FK_USER = (SELECT PK_USER
                                              FROM t_cores_user
                                              WHERE C_LOGIN_NAME = '{$v_user_code}'
                                            )
                                )
                         UNION 
                         SELECT GF.FK_APPLICATION
                         FROM t_cores_group_function GF
                         LEFT JOIN t_cores_application a ON GF.FK_APPLICATION = a.PK_APPLICATION
                         WHERE FK_GROUP IN
                                 (SELECT FK_GROUP
                                  FROM t_cores_user_group
                                  WHERE FK_USER = (SELECT PK_USER
                                                   FROM t_cores_user
                                                   WHERE C_LOGIN_NAME = '{$v_user_code}'
                                                   )
                                )
                        )
                    )";
            $sql .= (isset($v_is_admin) ? "OR ({$v_is_admin} > 0)" : "");
            $sql .= ")";
            $sql .= "ORDER BY A.C_ORDER";
        return $this->db->getAll($sql);
    }
    
}
