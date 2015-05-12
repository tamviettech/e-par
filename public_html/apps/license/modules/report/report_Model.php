<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of report_Model
 *
 * @author Loving
 */
class report_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    function qry_all_license_type()
    {
        return $this->db->GetAssoc("SELECT C_CODE, CONCAT(C_CODE,' - ',C_NAME) FROM t_license_license_type ORDER BY C_ORDER");
    }

    function qry_number_of_record_each_month_by_year($year)
    {
        $sql = "SELECT MONTH(C_ISSUED_DATE) AS C_MONTH"
                . ",COUNT(*)"
                . " FROM t_license_record"
                . " WHERE YEAR(C_ISSUED_DATE) = ?"
                . " GROUP BY C_MONTH"
                . " ORDER BY C_MONTH ASC";
        return $this->db->GetAssoc($sql,array($year));
    }

    function qry_number_of_each_record_each_month_by_year($year)
    {
        $result = array();
        for ($i = 1; $i <= 12; $i++)
        {
            $sql = "SELECT lt.C_NAME, COUNT(*) as COUNT_RECORD"
                    . " FROM t_license_record re"
                    . " JOIN t_license_license_type lt"
                    . " ON re.FK_LICENSE_TYPE = lt.PK_LICENSE_TYPE"
                    . " WHERE month(re.C_ISSUED_DATE) = ? AND YEAR(re.C_ISSUED_DATE) = ?"
                    . " GROUP BY re.FK_LICENSE_TYPE ";
            $result[$i] = $this->db->GetAssoc($sql, array($i, $year));
        }
        return $result;
    }

    function qry_all_distinct_year()
    {
        return $this->db->GetAssoc('SELECT DISTINCT YEAR(C_ISSUED_DATE)'
                        . ', YEAR(C_ISSUED_DATE) AS VA'
                        . ' FROM t_license_record');
    }

    function qry_record($list_type_code, $year)
    {
        $sql = 'SELECT * FROM t_license_record re JOIN t_license_license_type lt ON re.FK_LICENSE_TYPE = lt.PK_LICENSE_TYPE WHERE 1 = 1';
        $arr_params = array();
        if ($list_type_code)
        {
            $sql .= '  AND lt.C_CODE = ?';
            $arr_params[] = $list_type_code;
        }
        if ($year)
        {
            $sql.= ' AND YEAR(re.C_ISSUED_DATE) = ?';
            $arr_params[] = $year;
        }
        return $this->db->GetAll($sql, $arr_params);
    }

    function qry_license_type_name_by_code($code)
    {
        return $this->db->GetOne('SELECT C_NAME FROM t_license_license_type WHERE C_CODE = ?', array($code));
    }

}
