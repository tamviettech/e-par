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

class synthesis_Controller extends Controller
{

    /**
     *
     * @var \report_Model
     */
    public $model;

    /**
     *
     * @var View 
     */
    public $view;

    public function __construct()
    {
        parent::__construct('r3', 'synthesis');
        //tao array role tu r3_const
        $this->_arr_roles = json_decode(CONST_ALL_R3_ROLES,true);
        $this->view->template->controller_url     = $this->view->get_controller_url();
        $this->view->role_text                    = $this->_arr_roles;
        
        $this->view->DATETIME_NOW        = $this->model->get_datetime_now();
    }

    public function main()
    {
        $this->tra_cuu_ho_so();
    }
    
    /**
     * layout hie thi
     * @param type $function: ten funtion lay html
     */
    public function dsp_main($function = '')
    {
        $html_content = '';
        
        //kiem tra method co ton tai khong
        if(method_exists(get_class(), $function))
        {
            ob_start();
            $this->$function();
            $html_content = ob_get_clean();
        }
        
        $VIEW_DAtA['html_content'] = $html_content;
        
        $this->view->render('dsp_main',$VIEW_DAtA);
    }
    /**
     * redirec funtion
     */
    public function tra_cuu_ho_so()
    {
        $this->dsp_main('dsp_lookup');
    }
    
    public function dsp_lookup()
    {
        $this->view->title = 'Tra cứu hồ sơ';
        
        
        $VIEW_DATA['arr_all_record'] = $this->model->qry_all_record_for_lookup();
           
        
        $this->view->render('dsp_lookup',$VIEW_DATA);
    }
    
    public function statistics($record_id)
    {
        $record_id = get_request_var('hdn_item_id', $record_id);
        is_id_number($record_id) OR $record_id = 0;

        //Thông tin bản khai
        $VIEW_DATA['arr_single_record'] = $this->model->qry_single_record($record_id);

        $dom_processing = simplexml_load_string($VIEW_DATA['arr_single_record']['C_XML_PROCESSING']);

        //Thong tin tien do
        $v_record_type = $VIEW_DATA['arr_single_record']['C_RECORD_TYPE_CODE'];
        if ($VIEW_DATA['arr_single_record']['C_CLEAR_DATE'] == NULL)
        {
            $dom_step = simplexml_load_file($this->view->get_xml_config($v_record_type, 'workflow'));
        }
        else
        {
            $dom_step = simplexml_load_string($VIEW_DATA['arr_single_record']['C_XML_WORKFLOW']);
        }
        $dom_workflow   = $dom_step;
        $r              = xpath($dom_step, '//step/@time');
        $step_days_list = '';
        foreach ($r as $time)
        {
            $step_days_list .= ($step_days_list != '') ? ";$time" : $time;
        }
        $arr_step_formal_date              = $this->model->formal_record_step_days_to_date($record_id, $step_days_list);
        $VIEW_DATA['arr_step_formal_date'] = $arr_step_formal_date;

        //Lay danh sach step, ngay bat dau, ket thuc thuc te cua step, số ngày đã tiêu tốn thực tế để hoàn thành step
        $steps                            = xpath($dom_workflow, "//step[not(@no_chain = 'true')]");
        $index                            = -1;
        $arr_step_infact_formal_days_diff = array();
        foreach ($steps as $step)
        {
            $index++;
            $v_first_task_code = strval($step->task[0]->attributes()->code);
            $v_last_task_code  = strval($step->task[sizeof($step->task) - 1]->attributes()->code);

            //So ngay phai hoan thanh step theo quy dinh
            $v_formal_step_time = floatval(str_replace(',', '.', $step->attributes()->time));

            //=>Ngay bat dau thuc te cua step, la ngay thuc hien prev_task
            //$v_prev_task_code         = get_xml_value($dom_workflow, "//task[contains(@next,'$v_first_task_code')]/@code");
            //$v_step_begin_date_infact = get_xml_value($dom_processing, "//step[@code='$v_prev_task_code']/datetime");
            $v_step_begin_date_infact = '';
            foreach (xpath($dom_processing, "//step") as $item)
            {
                if (strval($item->attributes()->code) == $v_first_task_code)
                {
                    break;
                }
                else
                {
                    $v_step_begin_date_infact = (string) $item->datetime;
                }
            }

            //=>Ngay ket thuc thuc te cua step
            $v_step_end_date_infact = get_xml_value($dom_processing, "//step[@code='$v_last_task_code']/datetime");

            //=>Số ngày làm việc tiêu tốn thực tế để hoàn thành step
            //$v_step_spent_days_infact       = $this->model->days_between_two_date($v_step_begin_date_infact, $v_step_end_date_infact);
            $begin_count              = false;
            $begin_count_2            = true;
            $v_step_spent_days_infact = 0;
            $v_start                  = '';
            $v_end                    = '';
            $v_next_step              = isset($steps[$index + 1]) ? $steps[$index + 1] : null;
            $v_next_step_first_task   = '';
            if ($v_next_step)
            {
                $v_next_step_first_task = isset($v_next_step->task[0]) ? strval($v_next_step->task[0]->attributes()->code) : '';
            }
            foreach ($dom_processing->children() as $item)
            {
                if ($item->getName() == 'step')
                {
                    $v_end      = (string) $item->datetime;
                    $v_date_inv = $this->model->days_between_two_date($v_start, $v_end);
                    if ((string) $item->attributes()->code == $v_first_task_code)
                    {
                        $begin_count = true;
                    }
                    elseif ((string) $item->attributes()->code == $v_next_step_first_task)
                    {
                        break;
                    }
                    if (strpos((string) $item->attributes()->code, _CONST_THONG_BAO_BO_SUNG_ROLE) !== false)
                    {
                        $begin_count_2 = false;
                    }
                    if ($begin_count && $begin_count_2)
                    {
                        $v_step_spent_days_infact += $v_date_inv;
                    }
                    $v_start = (string) $item->datetime;
                }
                elseif ($item->getName() == 'action' && $item->attributes()->do == 'unpause')
                {
                    $begin_count_2 = true;
                }
            }
            //=> Step nhanh hay cham bao nhieu ngày?            
            $v_step_infact_formal_days_diff = $v_formal_step_time - $v_step_spent_days_infact;

            $arr_step_infact_formal_days_diff[md5($v_last_task_code)] = $v_step_infact_formal_days_diff;
        }
        $VIEW_DATA['arr_step_infact_formal_days_diff'] = $arr_step_infact_formal_days_diff;

        $this->view->render('dsp_single_record_statistics', $VIEW_DATA);
    }
}