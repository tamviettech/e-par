<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class main_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }

    public function qry_all_application()
    {
        $stmt = 'Select a.C_CODE, a.C_NAME, a.C_DESCRIPTION, a.C_DEFAULT_MODULE
                From t_cores_application a
                Where a.C_STATUS>0
                    And (
                    a.PK_APPLICATION in (Select distinct FK_APPLICATION From t_cores_user_function Where FK_USER=?)
                    Or a.PK_APPLICATION in (Select distinct FK_APPLICATION
                        From dbo.t_cores_group_function GF left join t_cores_user_group UG On GF.FK_GROUP=UG.FK_GROUP
                        Where UG.FK_USER=?)
                    )
                Order By C_ORDER';
        return $this->db->getAll($stmt, array(Session::get('user_id'),Session::get('user_id')));
    }
}