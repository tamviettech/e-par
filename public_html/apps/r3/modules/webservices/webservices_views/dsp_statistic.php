<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
$v_unit_full_name = $this->get_unit_info('full_name');
//View data
$arr_single_record    = $VIEW_DATA['arr_single_record'];
$arr_step_formal_date = $VIEW_DATA['arr_step_formal_date'];

if (isset($arr_single_record['PK_RECORD']))
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

    $dom_record_result = simplexml_load_string($arr_single_record['XML_RECORD_RESULT']);

    $v_receive_date        = jwDate::yyyymmdd_to_ddmmyyyy($v_receive_date, 1);
    //$v_return_date = jwDate::yyyymmdd_to_ddmmyyyy($v_return_date);
    $v_return_date_by_text = $this->return_date_by_text($v_return_date);
}
else
{
    die();
}

//Thong tin cua quy trinh
$xml_flow_file_path = $this->get_xml_config($v_record_type_code, 'workflow');
$dom_flow           = simplexml_load_file($xml_flow_file_path);
$r                  = $dom_flow->xpath("/process/@totaltime");
//Da tra ket qua chua?
$v_is_cleared       = $arr_single_record['C_XML_WORKFLOW'];
if ($v_is_cleared == NULL) //Chua tra ket qua
{
    $v_flow_total_time_text = $v_flow_total_time      = $r[0];
    if ($v_flow_total_time < 0)
    {
        $v_flow_total_time_text = $arr_single_record['C_TOTAL_TIME'] . ' (<i>Thay đổi theo từng hồ sơ</i>)';
    }
}
else
{
    $dom_flow               = simplexml_load_string($arr_single_record['C_XML_WORKFLOW']);
    $r                      = $dom_flow->xpath("/process/@totaltime");
    $v_flow_total_time_text = $v_flow_total_time      = $r[0];
    if ($v_flow_total_time < 0)
    {
        $v_flow_total_time_text = $arr_single_record['C_TOTAL_TIME'] . ' (<i>Thay đổi theo từng hồ sơ</i>)';
    }
}

//Thong tin tinh trang xu ly ho so
$dom_processing = simplexml_load_string($v_xml_processing);

$v_org_return_date         = get_xml_value($dom_processing, '/data/@org_return_date');
$v_org_return_date_by_text = '';
if ($v_org_return_date != '')
{
    $v_org_return_date_by_text = $this->return_date_by_text($v_org_return_date);
}

function count_task($arr_task, $start=0)
{
    $v_rows = 0;
    $i = $start;
    for($i = $start; $i < count($arr_task); $i++)
    {
        $v_task_code = $arr_task[$i]->attributes()->code; //Mã công việc theo quy trình
        $v_role = get_role($v_task_code);

        $v_rows++;
        if($v_role == _CONST_CHUYEN_HO_SO_LEN_SO_ROLE)
        {
            break;
        }
    }
    return $v_rows;
}
//$dom = simplexml_load_file($xml_flow_file_path);
$r     = $dom_flow->xpath("/process");
$proc  = $r[0];
$steps = $proc->step;

$dom_processing = simplexml_load_string($v_xml_processing);

$v_prev_task_finish_datetime  = '';
$v_prev_chain_finish_datetime = '';
?>
<!--<table width="100%" class="adminlist table table-bordered table-striped">-->
<?php /*
    <tr>
        <td colspan="8">
            <table border="0" cellpadding="4" cellspacing="0" width="100%" class="none">
                <tr>
                    <td style="width: 150px; font-weight: bold">
                        Đơn vị tiếp nhận: 
                    </td>
                    <td></td>
                </tr>
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
                        
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold">
                        Tổng số ngày quy định:
                    </td>
                    <td>
                        <?php echo $v_flow_total_time_text; ?>
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
                        <?php if ($v_org_return_date_by_text != '' && ($v_org_return_date_by_text != $v_return_date_by_text)): ?>
                            <span style="text-decoration: line-through;"> (<?php echo $v_org_return_date_by_text; ?>)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold">
                        Phí, lệ phí:
                    </td>
                    <td>
                        <?php 
                            if ($arr_single_record_fee['C_FINAL_FEE'] > 0)
                            {
                                echo number_format($arr_single_record_fee['C_FINAL_FEE'], 0, ',', '.');
                            }
                            else
                            {
                                $v_advance_cost = str_replace(',', '', $arr_single_record_fee['C_ADVANCE_COST']);
                                $v_advance_cost = str_replace('.', '', $v_advance_cost);
                                echo number_format($v_advance_cost, 0, ',', '.');
                            }
                        ?>
                        <sup>đ</sup>
                    </td>
                    <td style="font-weight: bold">

                    </td>
                    <td>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
<!--                <thead>
        <tr height="30px" >
            <th rowspan="2">Bước</th>
            <th rowspan="2">Ngày bắt đầu<br/> dự kiến</th>
            <th rowspan="2">Số ngày QĐ</th>
            <th rowspan="2">Ngày kết thúc<br/> dự kiến</th>
            <th colspan="4">Tiến độ thực tế</th>
        </tr>
        <tr>
            <th>Công việc</th>
            <th>Bắt đầu</th>
            <th>Kết thúc</th>
            <th>người thực hiện</th>
        </tr>
    </thead>-->
*/?>
    <?php
    $step_order                   = 0;
    foreach ($steps as $step):
        $v_exec_group = $step->attributes()->group;
        $v_step_name  = $step->attributes()->name;
        $v_step_time  = $step->attributes()->time;
//                            $tasks        = $step->task;
        $tasks        = $dom_flow->xpath('//step[position()='.($step_order+1).']//task');
        $v_rows       = count_task($tasks);

//                            $v_rows       = count($tasks);
        $t            = 0;

        $v_is_no_chain_step = $step->attributes()->no_chain;
        $v_tr_class         = ($v_is_no_chain_step == 'true') ? ' class="no_chain"' : '';
        ?>
        <tr data-step_order="<?php echo $step_order; ?>">
            <td rowspan="<?php echo $v_rows; ?>">
                <?php echo $v_step_name; ?>
                <br>
                <i style="font-weight: bold">
                    (Giải quyết Liên thông với <?php echo $v_unit_full_name?>)
                </i>
                <br>
                <i style="font-weight: bold">
                    Mã HS: <?php echo $v_record_no; ?>
                </i>
            </td>

            <td rowspan="<?php echo $v_rows; ?>" class="left" id="step_begin_date_<?php echo $step_order; ?>">
                <?php echo ($v_flow_total_time >= 0) ? $this->break_date_string($this->return_date_by_text(@$arr_step_formal_date[$step_order]['C_BEGIN_DATE'])) : ''; ?>
            </td>

            <td rowspan="<?php echo $v_rows; ?>" class="center" id="step_time_<?php echo $step_order; ?>"><?php echo ($v_flow_total_time >= 0) ? str_replace('.', ',', $v_step_time) : ''; ?></td>


            <td rowspan="<?php echo $v_rows; ?>" id="step_end_date_<?php echo $step_order; ?>">
                <?php echo ($v_flow_total_time >= 0) ? $this->break_date_string($this->return_date_by_text(@$arr_step_formal_date[$step_order]['C_END_DATE'])) : ''; ?>
            </td>

            <?php
            $task_index = 0;
            $v_role_chuyen_ho_so_len_so = false;

            foreach ($tasks as $task):
                $v_task_code = $task->attributes()->code; //Mã công việc theo quy trình
                $v_task_name = $task->attributes()->name;
                $v_task_time = $task->attributes()->time;
                $v_next_task = $task->attributes()->next;
                $v_role = get_role($v_task_code);

                //La task dau tien cua step
                $v_is_first_task_of_step = count($dom_flow->xpath("//step/task[position()=1][@code='$v_task_code']/@code")) > 0;
                $v_is_last_task_of_step  = count($dom_flow->xpath("//step/task[last()][@code='$v_task_code']/@code")) > 0;

                //Ngay gio thuc hien cong viec
                $r = array();
                if (strpos($v_task_code, _CONST_XET_DUYET_ROLE) !== false)
                {
                    $r = $dom_processing->xpath("//step[contains(@code,'" . _CONST_XET_DUYET_BO_SUNG_ROLE . "')][last()]/datetime");
                }
                if (count($r) == 0 OR (date_create($r[0]) < date_create($v_prev_task_finish_datetime) ))
                {
                    $r = $dom_processing->xpath("//step[@code='$v_task_code'][last()]/datetime");
                }
                //$v_task_finish_datetime = isset($r[0]) ? jwDate::yyyymmdd_to_ddmmyyyy($r[0], TRUE) : '';
                $v_task_finish_datetime = isset($r[0]) ? $r[0] : '';

                //SEQ Cong viec da ghi trong processing log (C_XML_PROCESSING)
                $v_task_seq = get_xml_value($dom_processing, "//step[@code='$v_task_code'][last()]/@seq");

                //Nguoi thuc hien cong viec
                $r                          = $dom_processing->xpath("//step[@code='$v_task_code']/user_name");
                $v_exec_user_name           = isset($r[0]) ? $r[0] : '';
                $r                          = $dom_processing->xpath("//step[@code='$v_task_code']/user_job_title");
                $v_exec_user_name_job_title = isset($r[0]) ? $r[0] : '';

                //Mã công việc tiếp theo trên thực tế
                $v_next_task_in_fact = $dom_processing->xpath("//next_task[1]/@code");
                $v_next_task_in_fact = @$v_next_task_in_fact[0];

                if (trim($v_task_code) == trim($v_next_task_in_fact))
                {
                    $r_next           = $dom_processing->xpath("//next_task[1]/@user_name");
                    $v_exec_user_name = $r_next[0];

                    $v_exec_user_name_job_title = $dom_processing->xpath("//next_task[1]/@user_job_title");
                    $v_exec_user_name_job_title = isset($v_exec_user_name_job_title[0]) ? $v_exec_user_name_job_title[0] : '';
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

                //Neu la task dau tien cua step, luu lai ngay gio bat dau thuc te cua step
                if ($v_is_first_task_of_step)
                {
                    $v_step_start_datetime_in_fact = $v_step_start_datetime;
                }
                //Neu la task cuoi cung cua step, luu lai ngay gio ket thuc thuc te cua step
                if ($v_is_last_task_of_step)
                {
                    $v_step_finish_datetime_in_fact = $v_task_finish_datetime;
                }

                //Task nay co lam tam dung (pause) ho so khong?
                $v_is_pause = get_xml_value($dom_processing, "//action[@do='pause' and ref_seq='$v_task_seq']/id");

                //Task nay co lam ho so tiep tuc duoc xu ly (unpause) khong?
                $v_is_unpause = get_xml_value($dom_processing, "//action[@do='unpause' and ref_seq='$v_task_seq']/id");
                ?>
                <?php if ($t != 0): ?>
                <tr data-step_order="<?php echo $step_order; ?>">
                <?php endif; ?>
                <?php 
                    if($v_role_chuyen_ho_so_len_so == true):
                        $v_rows = count_task($tasks, $task_index);
                ?>
                    <td rowspan="<?php echo $v_rows; ?>"><?php echo $v_step_name; ?></td>

                    <td rowspan="<?php echo $v_rows; ?>" class="left" id="step_begin_date_<?php echo $step_order; ?>">
                        <?php echo ($v_flow_total_time >= 0) ? $this->break_date_string($this->return_date_by_text(@$arr_step_formal_date[$step_order]['C_BEGIN_DATE'])) : ''; ?>
                    </td>

                    <td rowspan="<?php echo $v_rows; ?>" class="center" id="step_time_<?php echo $step_order; ?>"><?php echo ($v_flow_total_time >= 0) ? str_replace('.', ',', $v_step_time) : ''; ?></td>


                    <td rowspan="<?php echo $v_rows; ?>" id="step_end_date_<?php echo $step_order; ?>">
                        <?php echo ($v_flow_total_time >= 0) ? $this->break_date_string($this->return_date_by_text(@$arr_step_formal_date[$step_order]['C_END_DATE'])) : ''; ?>
                    </td>
                <?php endif;?>
                <td data-step_order="<?php echo $step_order; ?>" data-task_code="<?php echo $v_task_code; ?>">
                    <?php echo $v_task_name; ?>
                    <?php if ($v_is_pause != ''): ?>
                        <img src="<?php echo SITE_ROOT; ?>public/images/icon-pause-24x24.png" />
                    <?php endif; ?>
                    <?php if ($v_is_unpause != ''): ?>
                        <img src="<?php echo SITE_ROOT; ?>public/images/icon-play-24x24.png" />
                    <?php endif; ?>
                </td>
                <td class="center" data-step_order="<?php echo $step_order; ?>"><?php echo jwDate::yyyymmdd_to_ddmmyyyy($v_prev_task_finish_datetime, 1); ?></td>
                <td class="center" data-step_order="<?php echo $step_order; ?>">
                    <?php echo jwDate::yyyymmdd_to_ddmmyyyy($v_task_finish_datetime, 1); ?>
                    <?php if ($v_role != _CONST_THU_PHI_ROLE && $v_role != _CONST_TRA_KET_QUA_ROLE && $v_role != _CONST_NHAN_BIEN_LAI_NOP_THUE_ROLE && $v_role != _CONST_TRA_KET_QUA_LT_ROLE): ?>
                        <?php if ($v_is_last_task_of_step && $v_step_finish_datetime_in_fact != ''): ?>
                            <br/>
                            <?php $v_diff_days_infact = @$arr_step_infact_formal_days_diff[md5($v_task_code)]; ?>
                            <?php if ($v_diff_days_infact < 0): ?>
                                <span class="days-remain overdue">Chậm <?php echo abs($v_diff_days_infact); ?> ngày</span>
                            <?php elseif ($v_diff_days_infact == 0): ?>
                                <span class="days-remain today">Đúng hạn</span>
                            <?php else: ?>
                                <span class="days-remain during">Trước hạn <?php echo $v_diff_days_infact; ?> ngày</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td data-step_order="<?php echo $step_order; ?>">
                    <?php echo $v_exec_user_name; ?>
                    <?php if ($v_role == _CONST_TRA_KET_QUA_ROLE && $arr_single_record['C_CLEAR_DATE'] != NULL): ?>
                        <?php
                        if ($dom_record_result != NULL)
                        {
                            $r = @$dom_record_result->xpath("//item[value='true']/@id");
                            if (sizeof($r) > 0)
                            {
                                echo '<br><i><u>Kết quả trả: </u></i>';
                                $dom_result_name = simplexml_load_file($this->get_xml_config(NULL, 'result'));
                                foreach ($r as $v_result_id)
                                {
                                    if ($v_result_id == 'ckbTaiLieuKhac')
                                    {
                                        echo '<br><b>' . get_xml_value($dom_record_result, "//item[@id='txtTaiLieuKhac']/value") . '</b>';
                                    }
                                    else
                                    {
                                        echo '<br><b>-' . get_xml_value($dom_result_name, "//item[@id='$v_result_id']/@title") . '</b>';
                                    }
                                }
                            }
                        }
                        ?>
                    <?php endif; //$v_role = _CONST_TRA_KET_QUA_ROLE?>
                    <?php 
                        if($v_role == _CONST_TRA_KET_QUA_LT_ROLE && $arr_single_record['C_CLEAR_DATE'] != NULL)
                        {
                            if ($dom_record_result != NULL)
                            {
                                echo '<br><i><u>Kết quả trả: </u></i>';
                                $result = get_xml_value($dom_record_result, '//result');
                                echo '<br> <b>- ' . $result;
                            }
                        }
                    ?>
                </td>
            </tr>
            <?php 
            $v_role_chuyen_ho_so_len_so = false;
            if($v_role == _CONST_CHUYEN_HO_SO_LEN_SO_ROLE)
            {
                $v_role_chuyen_ho_so_len_so = true;
                $xpath = "//task[@code='".$v_task_code."']/@exchange";//tao xpath de lay thong tin exchange
                $exchange_code = get_xml_value($dom_flow, $xpath);//lay exchange task code

                $xpath = "//task[@exchange]/@code";
                $arr_exchange_task = $dom_flow->xpath($xpath);
                $index = 1;
                if(count($arr_exchange_task) > 1)
                {
                    for($i=0;$i<count($arr_exchange_task);$i++)
                    {
                        $dom_exchange_task_code = $arr_exchange_task[$i];
                        if($dom_exchange_task_code->attributes()->code = $v_task_code)
                        {
                            $index = $i + 1;
                            break;
                        }
                    }
                }
                 //lay thong tin tien do tu webservice cua don vi lien thong
                 $arr_info = $arr_all_exchange_unit[$exchange_code];
                 $location = $arr_info['C_LOCATION'];
                 $uri = $arr_info['C_URI'];
                 $function = 'statistics';
                 $arr_param = array($v_record_id,$index);
//                                     $arr_param = array(1,2);
                 $client = new SoapClient(null, array('location' => $location,'uri' => $uri));
                 $result = $client->__soapCall($function, $arr_param);
            ?>
            <tr data-step_order="<?php echo $step_order; ?>">
                <td colspan="8">
                    <?php echo $result;?>
                </td>
            </tr>
            <?php
            }//end if check role _CONST_CHUYEN_HO_SO_LEN_SO_ROLE
            ?>
            <?php
            $v_prev_task_finish_datetime = $v_task_finish_datetime;
            if (strval($step->attributes()->no_chain) != 'true')
            {
                $v_prev_chain_finish_datetime = $v_task_finish_datetime;
            }
            elseif ($v_is_last_task_of_step)
            {
                $v_prev_task_finish_datetime = $v_prev_chain_finish_datetime;
            }
            ?>
        <?php 
        $task_index++;
        endforeach; //$task?>
        <?php $t++; ?>
        <?php $step_order++; ?>
    <?php endforeach; //$step ?>
<!--</table>-->
