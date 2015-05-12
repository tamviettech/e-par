<?php

defined('SERVER_ROOT') or die();

class rating_Model extends Model
{

    const VALUE_PLEASED    = 1;
    const VALUE_COMPLAINED = -1;

    /**
     * @var \ADOConnection 
     */
    public $db;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $where
     * @param array $input_array
     * @return array
     */
    function qry_single_user($where = '', $input_array = array())
    {
        $sql = "Select * From t_cores_user";
        if ($where)
        {
            $sql .= " Where $where ";
        }
        return $this->db->GetRow($sql, $input_array);
    }

    function qry_single_user_by_slot($slot)
    {
        $sql = "Select u.*  
                From t_r3_onegate_slot s
                Inner Join t_cores_user u
                On s.C_LOGIN_NAME = u.C_LOGIN_NAME
                Where s.C_SLOT = ?
             ";
        return $this->db->GetRow($sql, array($slot));
    }

    /**
     * @return array
     */
    function qry_all_slot()
    {
        $sql = "
            Select 
                u.*
                , s.C_SLOT, s.PK_ONEGATE_SLOT
                , pleased.C_COUNT_PLEASED
                , complained.C_COUNT_COMPLAINED
            From t_r3_onegate_slot s
            Inner Join t_cores_user u
            On s.C_LOGIN_NAME = u.C_LOGIN_NAME
            Left Join(
                Select C_LOGIN_NAME, Count(*) As C_COUNT_PLEASED
                From t_r3_citizen_ratings
                Where C_VALUE = " . self::VALUE_PLEASED . "
                Group By C_LOGIN_NAME
            ) pleased
            On u.C_LOGIN_NAME = pleased.C_LOGIN_NAME
            Left Join(
                Select C_LOGIN_NAME, Count(*) As C_COUNT_COMPLAINED
                From t_r3_citizen_ratings
                Where C_VALUE = " . self::VALUE_COMPLAINED . "
                Group By C_LOGIN_NAME
            ) complained
            ON u.C_LOGIN_NAME = complained.C_LOGIN_NAME
            Order by C_SLOT    
        ";
        return $this->db->GetAll($sql);
    }

    function qry_single_slot($slot_id)
    {
        $sql = " Select * From t_r3_onegate_slot Where PK_ONEGATE_SLOT=?";
        return $this->db->GetRow($sql, array($slot_id));
    }

    function qry_no_slot_user()
    {
        $other_clause = " u.C_LOGIN_NAME Not In(Select C_LOGIN_NAME From t_r3_onegate_slot) ";
        return $this->qry_all_onegate_user($other_clause);
    }

    function qry_all_onegate_user($other_clause = '', $input_array = array())
    {
        $sql = "
            Select u.*
            From t_cores_user u
            Where u.PK_USER In(
                Select FK_USER 
                From t_cores_user_group
                Where FK_GROUP = (Select PK_GROUP From t_cores_group Where C_CODE = 'BP_MOT_CUA')
            )
            Order By u.C_NAME
        ";
        if ($other_clause)
        {
            $sql .= " And $other_clause";
        }
        return $this->db->GetAll($sql, $input_array);
    }

    function update_slot()
    {
        $v_slot_id = get_request_var('hdn_item_id');
        $v_slot    = (int) get_request_var('txt_slot');
        $v_user    = get_request_var('sel_user');

        if (!$v_slot_id)
        {
            $sql    = "Insert Into t_r3_onegate_slot(C_LOGIN_NAME, C_SLOT) Values(?,?)";
            $params = array($v_user, $v_slot);
        }
        else
        {
            $sql    = "Update t_r3_onegate_slot Set C_LOGIN_NAME=?, C_SLOT=? Where PK_ONEGATE_SLOT=?";
            $params = array($v_user, $v_slot, $v_slot_id);
        }

        $this->db->Execute($sql, $params);
        if ($this->db->ErrorNo() == 0)
        {
            $this->popup_exec_done(1);
        }
        else
        {
            $this->popup_exec_fail($this->db->ErrorMsg());
        }
    }

    function delete_slot()
    {
        $v_list = get_request_var('hdn_item_id_list');
        $sql    = "Delete From t_r3_onegate_slot Where PK_ONEGATE_SLOT In($v_list)";
        $this->db->Execute($sql);
        if ($this->db->ErrorNo() == 0)
        {
            $this->exec_done(SITE_ROOT . 'r3/rating');
        }
        else
        {
            $this->exec_fail(SITE_ROOT . 'r3/rating', $this->db->ErrorMsg());
        }
    }

}