<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}

//View data
$arr_single_record    = @$arr_single_record_statistic['arr_single_record'];
$arr_step_formal_date = @$arr_single_record_statistic['arr_step_formal_date'];

if (isset($arr_single_record['PK_RECORD']) && is_array($arr_single_record))
{
    $v_record_id           = $arr_single_record['PK_RECORD'];
    $v_record_no           = $arr_single_record['C_RECORD_NO'];
    $v_receive_date        = $arr_single_record['C_RECEIVE_DATE'];
    $v_return_phone_number = $arr_single_record['C_RETURN_PHONE_NUMBER'];
    $v_return_date         = $arr_single_record['C_RETURN_DATE'];

    $v_citizen_name     = $arr_single_record['C_CITIZEN_NAME'];
    $v_record_type_name = $arr_single_record['C_RECORD_TYPE_NAME'];
    $v_record_type_code = $arr_single_record['C_RECORD_TYPE_CODE'];

    $v_xml_data       = $arr_single_record['C_XML_DATA'];
    $v_xml_processing = $arr_single_record['C_XML_PROCESSING'];

    $v_receive_date        = jwDate::yyyymmdd_to_ddmmyyyy($v_receive_date, TRUE);
    //$v_return_date = jwDate::yyyymmdd_to_ddmmyyyy($v_return_date);
    $v_return_date_by_text = $this->return_date_by_text($v_return_date);


    //Thong tin cua quy trinh
    $xml_flow_file_path = $this->get_xml_config($v_record_type_code, 'workflow');
    $dom_flow           = simplexml_load_file($xml_flow_file_path);
    $r                  = $dom_flow->xpath("/process/@totaltime");
    $v_flow_total_time  = $r[0];

    //Thong tin tinh trang xu ly ho so
    $dom_processing = simplexml_load_string($v_xml_processing);
}
else
{
    http_response_code(403);
    die();
}
?>
<div id="box-before-record" style="padding-bottom: 5px; float: left;">
    <style type="text/css">table.none td{border:0px}</style>
    <table border="0" cellpadding="4" cellspacing="0" width="100%" class="none">
        <tr>
            <td style="width: 150px; font-weight: bold">
                Loại hồ sơ:
            </td>
            <td colspan="4">
                <?php echo $v_record_type_code; ?> - <?php echo $v_record_type_name; ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold">
                Tên người đăng ký:
            </td>
            <td style="width: 200px">
                <?php echo $v_citizen_name; ?>
            </td>
            <td style="width: 150px; font-weight: bold">
                Mã hồ sơ:
            </td>
            <td>
                <?php echo $v_record_no; ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold">
                Tổng số ngày quy định:
            </td>
            <td>
                <?php echo $v_flow_total_time; ?>
            </td>
            <td style="font-weight: bold">
                Trạng thái hồ sơ:
            </td>
            <td><?php
                if ($arr_single_record['C_CLEAR_DATE'] != NULL)
                {
                    echo 'Đã trả kết quả, ngày ' . jwDate::yyyymmdd_to_ddmmyyyy($arr_single_record['C_CLEAR_DATE']);
                }
                else
                {
                    $v_group_name = $dom_processing->xpath('//next_task/@group_name');
                    $v_group_name = $v_group_name[0];

                    $v_next_role = $dom_processing->xpath('//next_task/@code');
                    $v_next_role = get_role($v_next_role[0]);
                    echo '<span class="gropup-name">' . $v_group_name . '</span> đang <span class="task-name">' . $this->role_text[$v_next_role] . '</span>';
                }
                ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold">
                Ngày tiếp nhận:
            </td>
            <td>
                <?php echo $v_receive_date; ?>
            </td>
            <td style="font-weight: bold">
                Ngày hẹn trả:
            </td>
            <td>
                <?php echo $v_return_date_by_text; ?>
            </td>
        </tr>
    </table>
</div>
<div class="clear">&nbsp;</div>
<div id="flow">
    <?php
    $xml_flow_file_path = $this->get_xml_config($v_record_type_code, 'workflow');
    if (is_file($xml_flow_file_path)):
        ?>
        <?php
        $r     = $dom_flow->xpath("/process");
        $proc  = $r[0];
        $steps = $proc->step;

        $dom_processing = simplexml_load_string($v_xml_processing);

        $v_prev_task_finish_datetime = '';
        ?>
        <div class="step">
            <table width="100%" border="1" class="adminlist" style="font-family:times; font-size:12px">
                <tr height="30px">
                    <th rowspan="2">Bước</th>
                    <th rowspan="2">Ngày bắt đầu theo QĐ</th>
                    <th rowspan="2">Số ngày QĐ</th>
                    <th rowspan="2">Ngày kết thúc theo QĐ</th>
                    <th colspan="4" style="text-align: center">Tiến độ thực tế</th>
                </tr>
                <tr>
                    <th>Công việc</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>người thực hiện</th>
                </tr>
                <?php
                $step_order                  = 0;
                foreach ($steps as $step):
                    $v_exec_group = $step->attributes()->group;
                    $v_step_name  = $step->attributes()->name;
                    $v_step_time  = $step->attributes()->time;
                    $tasks        = $step->task;
                    $v_rows       = count($tasks);
                    $t            = 0;
                    ?>
                    <tr data-step_order="<?php echo $step_order; ?>">
                        <td rowspan="<?php echo $v_rows; ?>"><?php echo $v_step_name; ?></td>
                        <td rowspan="<?php echo $v_rows; ?>" class="center" id="step_begin_date_<?php echo $step_order; ?>">
                            <?php echo jwDate::yyyymmdd_to_ddmmyyyy($arr_step_formal_date[$step_order]['C_BEGIN_DATE'], 1); ?>
                        </td>
                        <td rowspan="<?php echo $v_rows; ?>" class="center" id="step_time_<?php echo $step_order; ?>"><?php echo $v_step_time; ?></td>
                        <td rowspan="<?php echo $v_rows; ?>" id="step_end_date_<?php echo $step_order; ?>">
                            <?php echo @$this->return_date_by_text($arr_step_formal_date[$step_order]['C_END_DATE']); ?>
                        </td>
                        <?php
                        foreach ($tasks as $task):
                            $v_task_code = $task->attributes()->code; //Mã công việc theo quy trình
                            $v_task_name = $task->attributes()->name;
                            $v_task_time = $task->attributes()->time;
                            $v_next_task = $task->attributes()->next;

                            //La task dau tien cua step
                            $v_is_first_task_of_step = count($dom_flow->xpath("//step/task[position()=1][@code='$v_task_code']/@code")) > 0;
                            $v_is_last_task_of_step  = count($dom_flow->xpath("//step/task[last()][@code='$v_task_code']/@code")) > 0;

                            //Ngay gio thuc hien cong viec
                            $r                      = $dom_processing->xpath("//step[@code='$v_task_code'][last()]/datetime");
                            //$v_task_finish_datetime = isset($r[0]) ? jwDate::yyyymmdd_to_ddmmyyyy($r[0], TRUE) : '';
                            $v_task_finish_datetime = isset($r[0]) ? $r[0] : '';

                            //Nguoi thuc hien cong viec
                            $r                          = $dom_processing->xpath("//step[@code='$v_task_code']/user_name");
                            $v_exec_user_name           = isset($r[0]) ? $r[0] : '';
                            $r                          = $dom_processing->xpath("//step[@code='$v_task_code']/user_job_title");
                            $v_exec_user_name_job_title = isset($r[0]) ? $r[0] : '';

                            //Mã công việc tiếp theo trên thực tế
                            $v_next_task_in_fact = $dom_processing->xpath("//next_task[1]/@code");
                            $v_next_task_in_fact = isset($v_next_task_in_fact[0]) ? $v_next_task_in_fact[0] : '';

                            if (trim($v_task_code) == trim($v_next_task_in_fact))
                            {
                                $r_next           = $dom_processing->xpath("//next_task[1]/@user_name");
                                $v_exec_user_name = $r_next[0];

                                $v_exec_user_name_job_title = $dom_processing->xpath("//next_task[1]/@user_job_title");
                                $v_exec_user_name_job_title = $v_exec_user_name_job_title[0];
                            }

                            if ($v_exec_user_name_job_title != '')
                            {
                                $v_exec_user_name = $v_exec_user_name . ' (' . $v_exec_user_name_job_title . ')';
                            }

                            if ($v_prev_task_finish_datetime == '' && $v_task_finish_datetime != '')
                            {
                                $v_prev_task_finish_datetime = $v_task_finish_datetime;
                            }

                            if ($t == 0)
                            {
                                $v_step_start_datetime = $v_prev_task_finish_datetime;
                            }
                            ?>
                            <?php if ($t != 0): ?>
                            <tr data-step_order="<?php echo $step_order; ?>">
                            <?php endif; ?>
                            <td data-step_order="<?php echo $step_order; ?>"><?php echo $v_task_name; ?></td>
                            <td class="center" data-step_order="<?php echo $step_order; ?>"><?php echo jwDate::yyyymmdd_to_ddmmyyyy($v_prev_task_finish_datetime, 1); ?></td>
                            <td class="center" data-step_order="<?php echo $step_order; ?>"><?php echo jwDate::yyyymmdd_to_ddmmyyyy($v_task_finish_datetime, 1); ?></td>
                            <td data-step_order="<?php echo $step_order; ?>"><?php echo $v_exec_user_name; ?></td>
                        </tr>
                        <?php $v_prev_task_finish_datetime = $v_task_finish_datetime; ?>
                    <?php endforeach; //$task  ?>
                    <?php $t++; ?>
                    <?php $step_order++; ?>
                <?php endforeach; //$step  ?>
            </table>
        </div>
    <?php endif; ?>
</div>