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
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class mavach_Model extends Model {

    function __construct()
    {
        parent::__construct();
    }

    private function _get_xml_config($record_type_code, $config_type)
	{
		if (strtolower($config_type) == 'lookup')
		{
			return SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS
			. 'common' . DS . 'record_lookup.xml';
		}

		if ($config_type == 'list' && $record_type_code == '')
		{
		    return SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS . 'common' . DS . 'common_list.xml';;
		}

		$file_path =  SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS
		. $record_type_code . DS . $record_type_code . '_' . $config_type . '.xml';

		if (!is_file($file_path))
		{
			$record_type_code = preg_replace('/([0-9]+)/', '00', $record_type_code);
			$file_path = SERVER_ROOT . 'apps' . DS . 'r3' . DS . 'xml-config' . DS
			. 'common' . DS . $record_type_code . '_' . $config_type . '.xml';
		}

		return $file_path;

	}

    private function _formal_record_step_days_to_date($record_id, $step_days_list)
    {
        $ret = Array();
        $arr_step_time = explode(';', $step_days_list);

        $v_prev_end_date = NULL;
        $v_init_date = $this->db->getOne("Select C_RECEIVE_DATE From view_record R Where PK_RECORD=$record_id");
        for ($i=0; $i<sizeof($arr_step_time);$i++)
        {
            $v_step_time    = $arr_step_time[$i];
            $v_begin_date   = ($i==0) ? $v_init_date : $v_prev_end_date;
            $v_end_date     = $this->_step_deadline_calc($v_step_time, $v_begin_date);

            $ret[$i]['C_STEP_TIME']  = $v_step_time;
            $ret[$i]['C_BEGIN_DATE'] = $v_begin_date;
            $ret[$i]['C_END_DATE']   = $v_end_date;

            $v_prev_end_date = $v_end_date;
        }
       return $ret;
    }

    private function _step_deadline_calc($days, $begin_datetime=NULL)
    {
        if ($begin_datetime == NULL)
        {
            $begin_datetime = $this->get_datetime_now();
        }
        $v_begin_hour = Date('H',  strtotime($begin_datetime));

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
            $v_end_date = $this->next_working_day((int)$days, $begin_datetime);
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

    public function qry_single_record_statistics($record_no)
    {
        $stmt = 'Select PK_RECORD From t_r3_record Where C_RECORD_NO=?';
        $record_id = $this->db->getOne($stmt, array($record_no));

        $ret = NULL;
        if ($record_id != NULL)
        {
            //Thông tin bản khai
            if (DATABASE_TYPE == 'MSSQL')
            {
            $stmt = 'SELECT
                            R.[PK_RECORD]
                            , R.[FK_RECORD_TYPE]
                            , R.[C_RECORD_NO]
                            , Convert(varchar(19), R.[C_RECEIVE_DATE], 120) as [C_RECEIVE_DATE]
                            , Convert(varchar(19), R.[C_RETURN_DATE], 120) as [C_RETURN_DATE]
                            , R.[C_RETURN_PHONE_NUMBER]
                            , R.[C_XML_DATA]
                            , R.[C_XML_PROCESSING]
                            , R.[C_DELETED]
                            , Convert(varchar(19), R.[C_CLEAR_DATE], 120) as [C_CLEAR_DATE]
                            , R.[C_XML_WORKFLOW]
                            , R.[C_RETURN_EMAIL]
                            , R.[C_REJECTED]
                            , R.[C_REJECT_REASON]
                            , R.[C_CITIZEN_NAME]
                            , R.[C_ADVANCE_COST]
                            , R.[C_CREATE_BY]
                            , R.[C_NEXT_TASK_CODE]
                            , R.[C_NEXT_USER_CODE]
                            , R.[C_NEXT_CO_USER_CODE]
                            , R.[C_LAST_TASK_CODE]
                            , R.[C_LAST_USER_CODE]
                            , Convert(varchar(19), R.[C_DOING_STEP_BEGIN_DATE], 120) as [C_DOING_STEP_BEGIN_DATE]
                            , R.[C_DOING_STEP_DEADLINE_DATE]
                            , R.[C_BIZ_DAYS_EXCEED]
                            , RT.C_NAME as C_RECORD_TYPE_NAME
                            , RT.C_CODE as C_RECORD_TYPE_CODE
                        From [dbo].[view_record] R Left Join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                        Where R.PK_RECORD=?';
            }
            elseif (DATABASE_TYPE == 'MYSQL')
            {

                $stmt = 'Select
                              R.PK_RECORD
                            , R.FK_RECORD_TYPE
                            , R.C_RECORD_NO
                            , Cast(R.C_RECEIVE_DATE  AS CHAR(19)) AS C_RECEIVE_DATE
                            , Cast(R.C_RETURN_DATE  AS CHAR(19)) AS C_RETURN_DATE
                            , R.C_RETURN_PHONE_NUMBER
                            , R.C_XML_DATA
                            , R.C_XML_PROCESSING
                            , R.C_DELETED
                            , Cast(R.C_CLEAR_DATE  AS CHAR(19)) AS C_CLEAR_DATE
                            , R.C_XML_WORKFLOW
                            , R.C_RETURN_EMAIL
                            , R.C_REJECTED
                            , R.C_REJECT_REASON
                            , R.C_CITIZEN_NAME
                            , R.C_ADVANCE_COST
                            , R.C_CREATE_BY
                            , R.C_NEXT_TASK_CODE
                            , R.C_NEXT_USER_CODE
                            , R.C_NEXT_CO_USER_CODE
                            , R.C_LAST_TASK_CODE
                            , R.C_LAST_USER_CODE
                            , Cast(R.C_DOING_STEP_BEGIN_DATE  AS CHAR(19)) AS C_DOING_STEP_BEGIN_DATE
                            , R.C_DOING_STEP_DEADLINE_DATE
                            , R.C_BIZ_DAYS_EXCEED
                            , RT.C_NAME as C_RECORD_TYPE_NAME
                            , RT.C_CODE as C_RECORD_TYPE_CODE
                        From view_record R Left join t_r3_record_type RT On R.FK_RECORD_TYPE=RT.PK_RECORD_TYPE
                        Where R.PK_RECORD=?';
            }
            $arr_single_record = $this->db->getRow($stmt, array($record_id));
            if(sizeof($arr_single_record)==0 && $record_id != NULL)
            {
                // Hồ sơ đã bị xóa
                return $ret['record_deleted'] = '1';
            }
            //Thong tin tien do
            $v_record_type = isset($arr_single_record['C_RECORD_TYPE_CODE'])? $arr_single_record['C_RECORD_TYPE_CODE'] : '';
            
            if (empty($arr_single_record['C_CLEAR_DATE']))
            {
                $dom_step = simplexml_load_file($this->_get_xml_config($v_record_type, 'workflow'));
            }           
            else
            {
                $dom_step = simplexml_load_string($arr_single_record['C_XML_WORKFLOW']);
            }
            
            $r = $dom_step->xpath('//step/@time');
            $step_days_list = '';
            foreach ($r as $time)
            {
                $step_days_list .= ($step_days_list != '') ? ";$time" : $time;
            }
            $arr_step_formal_date = $this->_formal_record_step_days_to_date($record_id, $step_days_list);

            $ret['arr_single_record'] = $arr_single_record;
            $ret['arr_step_formal_date'] = $arr_step_formal_date;
        }

        return $ret;
    }
}