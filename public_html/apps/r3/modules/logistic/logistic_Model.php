<?php

defined('SERVER_ROOT') or die();

/**
 * @package logistic
 * @author Tam Viet         <ltbinh@gmail.com>
 * @author Duong Tuan Anh   <goat91@gmail.com>
 */
class logistic_Model extends Model
{

    /**
     *
     * @var \ADOConnection 
     */
    public $db;

    /**
     *
     * @var string datetime
     */
    protected $_last_update;

    function __construct()
    {
        parent::__construct();
        $this->_last_update = Session::get('logistic_last_update');
        if (!$this->_last_update)
        {
            $this->_last_update = $this->db->GetOne('Select Now() As time');
        }
    }

    function __destruct()
    {
        Session::init();
        Session::set('logistic_last_update', $this->_last_update);
    }

    /**
     * 
     * @return array Recordset
     */
    function qry_new_actions()
    {
        $task_code     = ' ExtractValue(C_XML_PROCESSING, "//step[last()]/@code")';
        $username      = ' ExtractValue(C_XML_PROCESSING, "//step[last()]/user_name")';
        $datetime      = ' ExtractValue(C_XML_PROCESSING, "//step[last()]/datetime")';
        $sql           = "
                    Select 
                        $task_code As C_TASK_CODE
                        , $username As C_USER_NAME
                        , $datetime As C_DATE_TIME
                    From view_record R
                    INNER JOIN (
                    SELECT pk_record FROM view_record 
                    WHERE (C_CLEAR_DATE IS NULL OR DATEDIFF(C_CLEAR_DATE, NOW()) =0 )
                        AND Cast($datetime As DateTime) > Cast('{$this->_last_update}' As DateTime)
                    ORDER BY $datetime
                ) SUB
                ON R.PK_RECORD = SUB.pk_record
                ";
        $arr_recordset = $this->db->GetAll($sql);
        if (count($arr_recordset))
        {
            $last_record        = $arr_recordset[count($arr_recordset) - 1];
            $this->_last_update = $last_record['C_DATE_TIME'];
        }
        return $arr_recordset;
    }

}