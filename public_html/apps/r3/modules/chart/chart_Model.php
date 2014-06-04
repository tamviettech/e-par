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

class chart_Model extends record_Model
{

    /**
     * @var \ADOConnection 
     */
    public $db;

    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * lay nam tiep nhan ho so 
     * @return type
     */
    public function get_year()
    {
        $sql = "Select
                    MAX(Y.C_YEAR) As C_MAX_YEAR,
                    MIN(Y.C_YEAR) As C_MIN_YEAR
                  From (select
                          YEAR(C_RECEIVE_DATE) as C_YEAR
                        from view_record) Y";
        return $this->db->getRow($sql);
    }
    /**
     * lay tat tien do tat ca ho so theo nam
     * @param type $v_year
     * @return type
     */
    public function qry_record_progress($v_year = '')
    {
        if($v_year == '')
        {
            $v_year = DATE('Y');
        }
        $DATA_MODEL = array();
        
        //lay tong so record
        $sql = "select COUNT(*) from view_record Where Year(C_RECEIVE_DATE) = '$v_year'";
        $DATA_MODEL['C_TOTAL_RECORD'] = $this->db->getOne($sql);
        
        //dang xu ly - cham tien do
        $sql = "Select
                    COUNT(*)
                  From view_processing_record
                  where ((C_IS_PAUSING <> 1
                          and DATEDIFF(NOW(),C_RETURN_DATE) > 0
                          and ISNULL(C_BIZ_DAYS_EXCEED))
                          OR C_BIZ_DAYS_EXCEED < 0)
                      And Year(C_RECEIVE_DATE) = '$v_year'";
        $DATA_MODEL['C_COUNT_CHUA_TRA_CHAM_TIEN_DO'] = $this->db->getOne($sql);
        
        //dang xu ly - chua den han
        $sql = "Select
                    COUNT(*)
                  From view_processing_record
                  Where (C_BIZ_DAYS_EXCEED >= 0
                          OR C_IS_PAUSING = 1
                          OR (DATEDIFF(NOW(),C_RETURN_DATE) <= 0
                              And ISNULL(C_BIZ_DAYS_EXCEED)))
                      And Year(C_RECEIVE_DATE) = '$v_year'";
        $DATA_MODEL['C_COUNT_CHUA_TRA_CHUA_DEN_HAN'] = $this->db->getOne($sql);
        
        //da tra  - cham han
        $sql = "select
                    COUNT(*)
                  from view_record
                  Where C_CLEAR_DATE is not null
                      and Year(C_RECEIVE_DATE) = '$v_year'
                      And ((DATEDIFF(C_CLEAR_DATE,C_RETURN_DATE) > 0 AND C_BIZ_DAYS_EXCEED IS NULL) 
                             OR C_BIZ_DAYS_EXCEED < 0)";
        $DATA_MODEL['C_COUNT_DA_TRA_CHAM_HAN'] = $this->db->getOne($sql);
        //da tra  - dung han
        $sql = "select
                    COUNT(*)
                  from view_record
                  Where C_CLEAR_DATE is not null
                      and Year(C_RECEIVE_DATE) = '$v_year'
                      And ((DATEDIFF(C_CLEAR_DATE,C_RETURN_DATE) = 0 AND C_BIZ_DAYS_EXCEED IS NULL)
                            OR C_BIZ_DAYS_EXCEED = 0)";
        $DATA_MODEL['C_COUNT_DA_TRA_DUNG_HAN'] = $this->db->getOne($sql);
        //da tra  - som han
        $sql = "select
                    COUNT(*)
                  from view_record
                  Where C_CLEAR_DATE is not null
                      and Year(C_RECEIVE_DATE) = '$v_year'
                      And ((DATEDIFF(C_CLEAR_DATE,C_RETURN_DATE) < 0 AND C_BIZ_DAYS_EXCEED IS NULL)
                               OR C_BIZ_DAYS_EXCEED > 0)"; 
        $DATA_MODEL['C_COUNT_DA_TRA_SOM_HAN'] = $this->db->getOne($sql);
        
        return $DATA_MODEL;
    }
    
    /**
     * lay tat ca ho so tiep nhan va da tra ket qua theo nam
     * @param type $v_year
     * @return array
     */
    public function qry_record_receive_respond($v_year = '')
    {
        if($v_year == '')
        {
            $v_year = DATE('Y');
        }
        $DATA_MODEL = array();
        //ho so tiep nhan
        $sql = "Select
                    C_MONTH,
                    COUNT(PK_RECORD) as C_COUNT_TIEP_NHAN
                  From (Select
                          PK_RECORD,
                          MONTH(C_RECEIVE_DATE) as C_MONTH
                        from view_record
                        where YEAR(C_RECEIVE_DATE) = '$v_year') TEMP
                  GROUP by TEMP.C_MONTH";
        $DATA_MODEL['TIEP_NHAN'] = $this->db->GetAssoc($sql);
        
        //ho so da tra ket qua
        $sql = "Select
                    C_MONTH,
                    COUNT(PK_RECORD) as C_COUNT_DA_TRA
                  From (Select
                          PK_RECORD,
                          MONTH(C_RECEIVE_DATE) as C_MONTH
                        from view_record
                        where YEAR(C_RECEIVE_DATE) = '$v_year'
                            And C_CLEAR_DATE is not null) TEMP
                  GROUP by TEMP.C_MONTH";
        $DATA_MODEL['DA_TRA'] = $this->db->GetAssoc($sql);
        
        return $DATA_MODEL;
    }
}