<?php 
/**
// File name   : 
// Version     : 1.0.0.1
// Begin       : 2012-12-01
// Last Update : 2010-12-25
// Author      : TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
//Copyright (C) 2012-2013  TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn

// E-PAR is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// E-PAR is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// See LICENSE.TXT file for more information.
*/

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class liveboard_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }
    
    public function qry_all_record_type()
    {
        $stmt = 'SELECT PK_RECORD_TYPE, C_CODE, C_NAME 
                FROM t_r3_record_type 
                WHERE C_STATUS>0
                ORDER BY C_CODE';
        return $this->db->getAssoc($stmt);
    }
    
    public function qry_count_today_receive_record()
    {
        $stmt = 'SELECT 
                        FK_RECORD_TYPE
                        , COUNT(*) C_COUNT
                FROM view_processing_record
                WHERE DATEDIFF(NOW(),C_RECEIVE_DATE)=0
                GROUP BY FK_RECORD_TYPE';
        return $this->db->getAssoc($stmt);
    }
    
    public function qry_count_today_handover_record()
    {
        $v_task_code = _CONST_XML_RTT_DELIM . _CONST_BAN_GIAO_ROLE;
        $stmt = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where DATEDIFF(NOW(), CAST(ExtractValue(C_XML_PROCESSING, '//step[contains(@code,''$v_task_code'')][position()=1]/datetime') AS DATETIME))=0
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }
    
    //Dem hs dang xu ly
    public function qry_count_execing_record()
    {
        /*
        $stmt = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where C_BIZ_DAYS_EXCEED Is Null And (C_NEXT_TASK_CODE Is Not Null) And 
                    (   C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_PHAN_CONG_ROLE . "'
                        OR C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_LY_ROLE . "'
                        OR C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_ROLE . "'
                        OR C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_YEU_CAU_THU_LY_LAI_ROLE . "'
                        OR C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_XET_DUYET_BO_SUNG_ROLE . "'
                    )
                Group by FK_RECORD_TYPE";
                */
        $stmt = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where C_BIZ_DAYS_EXCEED Is Null And (C_NEXT_TASK_CODE Is Not Null) And 
                    (   C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                        And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
                    )
                Group by FK_RECORD_TYPE";
          
        return $this->db->getAssoc($stmt);
    }
    
    //Dung tien do
    public function qry_count_in_schedule_record()
    {
        $stmt = 'Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where C_BIZ_DAYS_EXCEED Is Null
                        And (Datediff(Now(), C_DOING_STEP_DEADLINE_DATE) <= 0)
                        And (C_NEXT_TASK_CODE Is Not Null)
                Group by FK_RECORD_TYPE';
        return $this->db->getAssoc($stmt);
    }
    
    //Cham tien do
    public function qry_count_over_deadline_record()
    {
        $stmt = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where C_BIZ_DAYS_EXCEED Is Null
                        And (Datediff(Now(),C_DOING_STEP_DEADLINE_DATE) > 0)
                        And (C_NEXT_TASK_CODE Is Not Null)
                        And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                        And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }
    
    //Qua han, den ngay tra KQ ma chua 
    public function qry_count_expried_record()
    {
        $stmt = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where C_BIZ_DAYS_EXCEED Is Null And (datediff(C_RETURN_DATE, Now()) < 0)
                    And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                    And C_NEXT_TASK_CODE Not Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }
    
    //Phai bo sung
    public function qry_count_supplement_record()
    {
        $stmt = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where C_NEXT_TASK_CODE like '%" . _CONST_XML_RTT_DELIM . _CONST_BO_SUNG_ROLE . "'
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }
    
    //Cho tra
    public function qry_count_waiting_for_return_record()
    {
        $stmt = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where
                    (   C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . "'
                        Or C_NEXT_TASK_CODE Like '%" . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . "'
                     )
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }
    
    //Da tra ket qua
    public function qry_count_returned_record()
    {
        $stmt = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_record
                Where C_CLEAR_DATE Is Not Null
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }
    
    //Dang tam dung
    public function qry_count_pausing_record()
    {
        $stmt = "Select
                      FK_RECORD_TYPE
                    , COUNT(*) C_COUNT
                From view_processing_record
                Where C_CLEAR_DATE Is Null
                        And C_IS_PAUSING=1
                Group by FK_RECORD_TYPE";
        return $this->db->getAssoc($stmt);
    }
}