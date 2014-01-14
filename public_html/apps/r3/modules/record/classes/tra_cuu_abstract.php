<?php
/**
Copyright (C) 2012 Tam Viet Tech.

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

defined('SERVER_ROOT') or die;

abstract class tra_cuu_abstract
{

    /**
     * @var \record_Controller
     */
    public $controller;
    protected $_activity_filter;
    protected $_arr_fields;
    protected $_table;
    protected $_arr_conds;
    protected $_arr_sidebars;

    abstract protected function _create_arr_conds();

    function __construct($controller)
    {
        $this->controller       = $controller;
        $this->_activity_filter = get_request_var('tt');
        $this->_arr_fields      = $this->_create_field_list();
        $this->_table           = 't_r3_record';
        $this->_arr_sidebars    = $this->_create_arr_sidebars();
    }

    protected function add_side_bar($innerHTML, $posistion)
    {
        
    }

    function render()
    {
        $VIEW_DATA['arr_all_record'] = $this->qry_all_record();
        $this->controller->view->render('dsp_all_record_tra_cuu', $VIEW_DATA);
    }

    function qry_all_record()
    {
        
    }

    function create_from_and_where_query()
    {
        
    }

    protected function _create_arr_sidebars()
    {
        return array(
            0  => 'Tất cả hồ sơ'
            , 1  => 'Hồ sơ vừa tiếp nhận'
            , 2  => 'Hồ sơ chờ bổ sung'
            , 10 => 'Hồ sơ đang tạm dừng'
            , 3  => 'Hồ sơ bị từ chối'
            , 4  => 'Hồ sơ đang giải quyết'
            , 5  => 'Hồ sơ đang trình ký'
            , 6  => 'Hồ sơ chờ trả kết quả'
            , 7  => 'Hồ sơ đã trả kết quả'
            , 8  => 'Hồ sơ đang chậm tiến độ'
            , 9  => 'Hồ sơ quá hạn trả kết quả'
            , 11 => 'Khôi phục hồ sơ bị xoá'
        );
    }

    protected function _create_field_list()
    {
        $v_user_code = Session::get('login_name');
        return array('@rownum:=@rownum + 1 AS RN'
            , "CASE WHEN R.C_NEXT_USER_CODE='$v_user_code' THEN 1 ELSE 0 END AS C_OWNER"
            , 'Case When (R.C_REJECTED = 1) Then 3 When (R.C_REJECTED <> 1 And (R.C_CLEAR_DATE Is Not Null)) Then 2 Else 1 End as C_ACTIVITY'
            , '$v_total_record AS TOTAL_RECORD'
            , 'CASE WHEN (DATEDIFF(NOW(), R.C_DOING_STEP_DEADLINE_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_DOING_STEP_DEADLINE_DATE)<0 ) END AS C_DOING_STEP_DAYS_REMAIN'
            , 'CASE WHEN (DATEDIFF(NOW(),R.C_RETURN_DATE)>0) THEN (SELECT -1 * (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())<=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)>0 ) ELSE (SELECT (COUNT(*)) FROM view_working_date WD WHERE DATEDIFF(WD.C_DATE, NOW())>=0 AND DATEDIFF(WD.C_DATE, R.C_RETURN_DATE)<0 ) END AS C_RETURN_DAYS_REMAIN'
            , 'R.PK_RECORD'
            , 'R.FK_RECORD_TYPE'
            , 'R.C_RECORD_NO'
            , 'CAST(R.C_RECEIVE_DATE AS CHAR(19)) AS C_RECEIVE_DATE'
            , 'CAST(R.C_RETURN_DATE AS CHAR(19)) AS C_RETURN_DATE'
            , 'R.C_RETURN_PHONE_NUMBER'
            , 'R.C_XML_DATA'
            , 'R.C_XML_PROCESSING'
            , 'R.C_DELETED'
            , 'R.C_CLEAR_DATE'
            , 'R.C_XML_WORKFLOW'
            , 'R.C_RETURN_EMAIL'
            , 'R.C_REJECTED'
            , 'R.C_REJECT_REASON'
            , 'R.C_CITIZEN_NAME'
            , 'R.C_ADVANCE_COST'
            , 'R.C_CREATE_BY'
            , 'R.C_NEXT_TASK_CODE'
            , 'R.C_NEXT_USER_CODE'
            , 'R.C_NEXT_CO_USER_CODE'
            , 'R.C_LAST_TASK_CODE'
            , 'R.C_LAST_USER_CODE'
            , 'CAST(R.C_DOING_STEP_BEGIN_DATE AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE'
            , 'R.C_DOING_STEP_DEADLINE_DATE'
            , 'R.C_BIZ_DAYS_EXCEED'
            , 'a.C_TASK_CODE'
            , 'a.C_STEP_TIME'
            , '$p_activity_filter as TT'
            , '(Select C_CONTENT  FROM t_r3_record_comment
                                Where FK_RECORD=R.PK_RECORD
                                Order By C_CREATE_DATE DESC
                                Limit 1
                        ) C_LAST_RECORD_COMMENT'
            , 'R.C_PAUSE_DATE'
            , 'R.C_UNPAUSE_DATE');
    }

}