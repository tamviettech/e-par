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

defined('DS') or die;

class public_Model extends Model
{

    /**
     * @var \ADOConnection
     */
    public $db;

    function __construct()
    {
        parent::__construct();
    }

    public function reset_processing_record()
    {
        set_time_limit(0);
        ini_set('memory_limit', '500M');
        
        $arr_all_record = $this->db->getCol("select
                                                PK_RECORD
                                              from t_r3_record
                                              where C_DELETED <> 1");
        foreach($arr_all_record as $record_id)
        {
            $arr_record_info = $this->db->getRow("SELECT
                                                        RT.C_CODE,
                                                        R.C_XML_PROCESSING
                                                      FROM t_r3_record R
                                                        LEFT JOIN t_r3_record_type RT
                                                          ON R.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
                                                      WHERE R.PK_RECORD = $record_id");
            $record_type = $arr_record_info['C_CODE'];
            $xml_processing = $arr_record_info['C_XML_PROCESSING'];
            $xml_processing = xml_remove_declaration($xml_processing);
            $xml_processing = xml_add_declaration($xml_processing);

            $dom_processing = simplexml_load_string($xml_processing);

            $workflow_file_path = $this->_get_xml_workflow_file_path($record_type);
            $dom_workflow = simplexml_load_file($workflow_file_path);

            $arr_last_task = $dom_workflow->xpath('//step/task[last()]/@code');

            foreach($arr_last_task as $task_code)
            {
                $check = get_xml_value($dom_workflow,'//task[@biz_done="true"]/../following-sibling::step/task[@code="'.$task_code.'"]/@code');
                $arr_exception = array(_CONST_TRA_KET_QUA_ROLE,_CONST_THU_PHI_ROLE);
                //khong xu ly nhung buoc sau biz_done
                if($check == $task_code OR in_array(get_role($task_code), $arr_exception))
                {
                    continue;
                }

                //kiem tra processing da thuc hien buoc $task_code chua
                $check = $dom_processing->xpath("//step[@code='$task_code']");
                if(!empty($check))
                {
                    $index = count($dom_processing->xpath("//step[@code='$task_code']/preceding-sibling::step"));

                    if(!isset($dom_processing->step[$index]->to_group_code))
                    {
                        $to_group_code = $this->db->getOne("SELECT
                                                                C_GROUP_CODE
                                                              FROM (SELECT
                                                                      C_NEXT_TASK_CODE
                                                                    FROM t_r3_user_task
                                                                    WHERE C_RECORD_TYPE_CODE = '$record_type'
                                                                        AND C_TASK_CODE = '$task_code') temp
                                                                LEFT JOIN t_r3_user_task UT
                                                                  ON temp.C_NEXT_TASK_CODE = UT.C_TASK_CODE
                                                              LIMIT 1");

                        $dom_processing->step[$index]->to_group_code = $to_group_code;
                    }

                    if(!isset($dom_processing->step[$index]->deadline))
                    {
                        $v_step_time      = get_xml_value($dom_workflow, '//task[@code="'.$task_code.'"]/../@time');
                        $v_prev_step_code = get_xml_value($dom_workflow, '//task[@code="'.$task_code.'"]/../preceding-sibling::step[position()=1]/task[last()]/@code');
                        if($v_prev_step_code == '')
                        {
                            $v_doing_step_begin_date = $this->db->getOne("SELECT C_RECEIVE_DATE FROM t_r3_record WHERE PK_RECORD = $record_id");
                        }
                        else
                        {

                            $v_doing_step_begin_date = $this->db->getOne("SELECT
                                                                                ExtractValue(C_XML_PROCESSING,'//step[@code=\"$v_prev_step_code\"]/datetime')
                                                                              FROM t_r3_record
                                                                              WHERE PK_RECORD = $record_id");
                        }
                        $v_step_deadline_date = $this->_step_deadline_calc($v_step_time,$v_doing_step_begin_date);

                        $dom_processing->step[$index]->deadline = $v_step_deadline_date;
                    }
                }
            }

            $new_xml = $dom_processing->saveXML();
            $this->db->Execute("UPDATE t_r3_record SET C_XML_PROCESSING = ? WHERE PK_RECORD = ?",array($new_xml,$record_id));
        }
    }
    
    private function _get_xml_workflow_file_path($v_record_type_code)
    {
        return SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . $v_record_type_code . DS . $v_record_type_code . '_workflow' . '.xml';
    }
    
    /**
     * Tinh ngay ket thuc cua step
     * @param Int $days So ngay cua step
     * @param datetime $begin_datetime Bat dau tinh tu ngay
     */
    private function _step_deadline_calc($days, $begin_datetime = NULL)
    {
        if ($begin_datetime == NULL)
        {
            $begin_datetime = $this->get_datetime_now();
        }
        $v_begin_hour = Date('H', strtotime($begin_datetime));

        //Tinh ket qua theo so ngay cua thu tuc
        if ($days == 0) //Thu tuc 0 ngay
        {
            if ($v_begin_hour < 12) //Nhận sáng trả sáng
            {
                $v_end_date = substr($begin_datetime, 0, 10) . chr(32) . _CONST_MORNING_END_WORKING_TIME;
            }
            else //Nhận chiều trả chiều
            {
                $v_end_date = substr($begin_datetime, 0, 10) . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
            }
        }
        elseif ($days == 0.5) //Thủ tục 0.5 ngày
        {
            if ($v_begin_hour < 12) //Nhận sáng trả chiều
            {
                $v_end_date = substr($begin_datetime, 0, 10) . chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
            }
            else //Nhận chiều trả sáng ngày làm việc tiếp theo
            {
                $v_end_date = $this->next_working_day(1, $begin_datetime) . chr(32) . _CONST_MORNING_END_WORKING_TIME;
            }
        }
        else //Cộng số ngày làm việc, Nhận sáng trả sáng, nhận chiều trả chiều
        {
            $v_end_date = $this->next_working_day($days, $begin_datetime);
            if ($v_begin_hour < 12) //Nhận sáng trả sáng
            {
                $v_end_date .= chr(32) . _CONST_MORNING_END_WORKING_TIME;
            }
            else //Nhận chiều trả chiều
            {
                $v_end_date .= chr(32) . _CONST_AFTERNOON_END_WORKING_TIME;
            }
        }

        return $v_end_date;
    }

}