<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
deny_bad_http_referer();
$v_assistance = isset($_GET['assistance']) ? $_GET['assistance'] : 0;

/* @var $this \r3_View */
//View data
$arr_single_record    = $VIEW_DATA['arr_single_record'];
$arr_all_comment      = $VIEW_DATA['arr_all_comment'];
$arr_all_doc          = $VIEW_DATA['arr_all_doc'];
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

//display header
$this->template->title = 'Thông tin tiến độ xử lý hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

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

//lay tab selected
$v_tab_selected = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 'flow';

function count_task($arr_task, $start = 0)
{
    $v_rows = 0;
    $i      = $start;
    for ($i = $start; $i < count($arr_task); $i++)
    {
        $v_task_code = $arr_task[$i]->attributes()->code; //Mã công việc theo quy trình
        $v_role      = get_role($v_task_code);

        $v_rows++;
        if ($v_role == _CONST_CHUYEN_HO_SO_LEN_SO_ROLE)
        {
            break;
        }
    }
    return $v_rows;
}
?>
<style>
    .layout,body
    {
        background: white;
    }
    .table-striped tbody > tr:nth-child(odd) > td, .table-striped tbody > tr:nth-child(odd) > th {
        background-color: white !important;
    }
</style>
<form name="frmMain" id="frmMain" action="" method="POST" style="background-color: white;" enctype="multipart/form-data">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', $v_record_id);
    echo $this->hidden('hdn_item_id_list', '');

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');

    echo $this->hidden('XmlData', $v_xml_data);
    echo $this->hidden('hdn_add_comment_token', Session::get('add_comment_token'));
    echo $this->user_token();
    ?>
    <!-- Thong tin chung trong Ho so -->
    <div class="group" style="padding-bottom: 5px; float: left;width: 100%">
        <div class="widget-head blue">
            <h3>
                Thông tin chung 
            </h3>
        </div>
        <style type="text/css">table.none td{border:0px}</style>
        <div class="widget-container" style="border: 1px solid #3498DB">
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
                        $val = '';
                        if ($arr_single_record_fee['C_FINAL_FEE'] > 0)
                        {
                            $val = number_format($arr_single_record_fee['C_FINAL_FEE'], 0, ',', '.');
                        }
                        else
                        {
                            $v_advance_cost = str_replace(',', '', $arr_single_record_fee['C_ADVANCE_COST']);
                            $v_advance_cost = str_replace('.', '', $v_advance_cost);
                            $val            = number_format($v_advance_cost, 0, ',', '.');
                        }
                        if ($val)
                        {
                            echo $val . "<sup>đ</sup>";
                        }
                        ?>
                    </td>
                    <td style="font-weight: bold">

                    </td>
                    <td>

                    </td>
                </tr>
            </table>
            <div id="solid-button">

                <!--button in-->
                <button type="button"  name="btn_print1" class="btn btn-sm" onclick="btn_print_record_statistics(<?php echo $v_record_id; ?>);">
                    <i class="icon-print"></i>
                    In chi tiết tiến độ xử lý
                </button>
                <!--button in-->
                <!--button in-->
                <?php if ($v_assistance == 1): ?>
                    <button  type="button" name="btn_print2" class="btn btn-sm" onclick="btn_print_assistance_form_onclick(<?php echo $v_record_id; ?>);">
                        <i class="icon-print"></i>
                        In mẫu hỗ trợ thụ lý
                    </button>
                <?php endif; ?>
                <!--button in-->
                <button type="button" name="btn_print3" class="btn btn-sm" onclick="btn_print_record_form_onclick(<?php echo $v_record_id; ?>);">
                    <i class="icon-print"></i>
                    In đơn
                </button>
                <!--Button close window-->
                <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
                <button type="button" name="trash" class="btn btn-sm" onclick="<?php echo $v_back_action; ?>" >
                    <i class="icon-reply"></i>
                    <?php echo __('close window'); ?>
                </button> 
            </div>
        </div>
    </div>
    <div class="clear">&nbsp;</div>
    <!--tab widget-->
    <div class="tab-widget" id="tab-content">
        <ul class="nav nav-tabs" id="myTab1" >
            <li>
                <a href="#flow" class="blue"><i class="icon-time"></i><span>Tiến độ</span></a>
            </li>
            <li>
                <a href="#comment" class="dark-yellow"><i class="icon-comment"></i><span>Ý kiến / kết quả</span></a>
            </li>
            <li>
                <a href="#attach" class="bondi-blue"><i class="icon-file"></i><span>Tài liệu</span></a>
            </li>
            <li>
                <a href="#content" class="bondi-blue"><i class="icon-list-alt"></i><span>Xem đơn</span></a>
            </li>
            <li>
                <a href="#step_processing" class=" magenta"><i class="icon-tasks"></i><span>Nhật ký xử lý</span></a>
            </li>
            <!--in mau ho tro thu ly-->
            <?php if ($v_assistance == 1): ?>
                <li>
                    <a href="#print_assistance " class=" magenta"><i class="icon-print"></i><span>In mẫu hỗ trợ thụ lý</span></a>
                </li>
            <?php endif; ?>
        </ul>
        <div class="tab-content">
            <!--flow-->
            <div class="tab-pane" id="flow">
                <?php
                //$dom = simplexml_load_file($xml_flow_file_path);
                $r     = $dom_flow->xpath("/process");
                $proc  = $r[0];
                $steps = $proc->step;

                $dom_processing = simplexml_load_string($v_xml_processing);

                $v_prev_task_finish_datetime  = '';
                $v_prev_chain_finish_datetime = '';
                ?>
                <div class="step">
                    <div class="clear" style="height: 10px">&nbsp;</div>
                    <table width="100%" class="adminlist table table-bordered table-striped">
                        <thead>
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
                        </thead>

                        <?php
                        $step_order                   = 0;
                        foreach ($steps as $step):
                            $v_exec_group = $step->attributes()->group;
                            $v_step_name  = $step->attributes()->name;
                            $v_step_time  = $step->attributes()->time;
//                            $tasks        = $step->task;
                            $tasks        = $dom_flow->xpath('//step[position()=' . ($step_order + 1) . ']//task');
                            $v_rows       = count_task($tasks);

//                            $v_rows       = count($tasks);
                            $t = 0;

                            $v_is_no_chain_step         = $step->attributes()->no_chain;
                            $v_tr_class                 = ($v_is_no_chain_step == 'true') ? ' class="no_chain"' : '';
                            ?>
                            <tr data-step_order="<?php echo $step_order; ?>">
                                <td rowspan="<?php echo $v_rows; ?>"><?php echo $v_step_name; ?></td>

                                <td rowspan="<?php echo $v_rows; ?>" class="left" id="step_begin_date_<?php echo $step_order; ?>">
                                    <?php echo ($v_flow_total_time >= 0) ? $this->break_date_string($this->return_date_by_text(@$arr_step_formal_date[$step_order]['C_BEGIN_DATE'])) : ''; ?>
                                </td>

                                <td rowspan="<?php echo $v_rows; ?>" class="center" id="step_time_<?php echo $step_order; ?>"><?php echo ($v_flow_total_time >= 0) ? str_replace('.', ',', $v_step_time) : ''; ?></td>


                                <td rowspan="<?php echo $v_rows; ?>" id="step_end_date_<?php echo $step_order; ?>">
                                    <?php echo ($v_flow_total_time >= 0) ? $this->break_date_string($this->return_date_by_text(@$arr_step_formal_date[$step_order]['C_END_DATE'])) : ''; ?>
                                </td>

                                <?php
                                $task_index                 = 0;
                                $v_role_chuyen_ho_so_len_so = false;

                                foreach ($tasks as $task):
                                    $v_task_code = $task->attributes()->code; //Mã công việc theo quy trình
                                    $v_task_name = $task->attributes()->name;
                                    $v_task_time = $task->attributes()->time;
                                    $v_next_task = $task->attributes()->next;
                                    $v_role      = get_role($v_task_code);

                                    //La task dau tien cua step
                                    $v_is_first_task_of_step = count($dom_flow->xpath("//step/task[position()=1][@code='$v_task_code']/@code")) > 0;
                                    $v_is_last_task_of_step  = count($dom_flow->xpath("//step/task[last()][@code='$v_task_code']/@code")) > 0;

                                    //Ngay gio thuc hien cong viec
                                    $r = array();
                                    if (strpos($v_task_code, _CONST_XET_DUYET_ROLE) !== false)
                                    {
                                        $r = $dom_processing->xpath("//step[contains(@code,'" . _CONST_XET_DUYET_BO_SUNG_ROLE . "')][last()]/datetime");
                                    }
                                    if (count($r) == 0 OR ( date_create($r[0]) < date_create($v_prev_task_finish_datetime) ))
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
                                    if ($v_role_chuyen_ho_so_len_so == true):
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
                                    <?php endif; ?>
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
                                        if ($v_role == _CONST_TRA_KET_QUA_LT_ROLE && $arr_single_record['C_CLEAR_DATE'] != NULL)
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
                                if ($v_role == _CONST_CHUYEN_HO_SO_LEN_SO_ROLE)
                                {
                                    $v_role_chuyen_ho_so_len_so = true;
                                    $xpath                      = "//task[@code='" . $v_task_code . "']/@exchange"; //tao xpath de lay thong tin exchange
                                    $exchange_code              = get_xml_value($dom_flow, $xpath); //lay exchange task code

                                    $xpath             = "//task[@exchange]/@code";
                                    $arr_exchange_task = $dom_flow->xpath($xpath);
                                    $index             = 1;
                                    if (count($arr_exchange_task) > 1)
                                    {
                                        for ($i = 0; $i < count($arr_exchange_task); $i++)
                                        {
                                            $dom_exchange_task_code                     = $arr_exchange_task[$i];
                                            if ($dom_exchange_task_code->attributes()->code = $v_task_code)
                                            {
                                                $index = $i + 1;
                                                break;
                                            }
                                        }
                                    }
                                    //lay thong tin tien do tu webservice cua don vi lien thong
                                    $arr_info  = $arr_all_exchange_unit[$exchange_code];
                                    $location  = $arr_info['C_LOCATION'];
                                    $uri       = $arr_info['C_URI'];
                                    $function  = 'statistics';
                                    $arr_param = array($v_record_id, $index);
//                                     $arr_param = array(1,2);
                                    $client    = new SoapClient(null, array('location' => $location, 'uri' => $uri));
                                    $result    = $client->__soapCall($function, $arr_param);
                                    echo $result;
                                    ?>

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
                            endforeach; //$task
                            ?>
                            <?php $t++; ?>
                            <?php $step_order++; ?>
                        <?php endforeach; //$step  ?>
                    </table>
                </div>
            </div>
            <!--comment-->
            <div class="tab-pane " id="comment" selected>
                <?php
                echo $this->hidden('sid', session_id());
                echo $this->hidden('user_token', Session::get('user_token'));
                ?>
                <div class="clear" style="height: 10px">&nbsp;</div>
                <table id="tbl_comment" class="adminlist table table-bordered table-striped">
                    <colgroup>
                        <col width="5%" />
                        <col width="45%" />
                        <col width="20%" />
                        <col width="15%" />
                        <col width="15%" />
                    </colgroup>
                    <thead>
                        <tr id="tbl_comment_header">
                            <th>#</th>
                            <th>Nội dung</th>
                            <th>Người gửi</th>
                            <th>Ngày gửi</th>
                            <th>File đính kèm</th>
                        </tr>
                    </thead>
                    <?php
                    for ($c = 0; $c < sizeof($arr_all_comment); $c++):
                        $v_comment_id     = $arr_all_comment[$c]['PK_RECORD_COMMENT'];
                        $v_content        = $arr_all_comment[$c]['C_CONTENT'];
                        $v_user_code      = $arr_all_comment[$c]['C_USER_CODE'];
                        $v_user_name      = $arr_all_comment[$c]['C_USER_NAME'];
                        $v_user_job_title = $arr_all_comment[$c]['C_USER_JOB_TITLE'];
                        $v_create_date    = $arr_all_comment[$c]['C_CREATE_DATE'];
                        $v_comment_type   = $arr_all_comment[$c]['C_TYPE'];

                        $v_xml_file = $arr_all_comment[$c]['C_XML_FILE'];

                        $v_class = ($v_comment_type == 1) ? ' class="bod_comment"' : '';
                        ?>
                        <tr data-cid="c_<?php echo $v_comment_id; ?>" <?php echo $v_class ?>>
                            <td class="center">
                                <input type="checkbox" name="chk_comment" value="<?php echo $v_comment_id; ?>"
                                <?php echo ($v_user_code == Session::get('user_code')) ? '' : ' disabled'; ?>
                                       data-user="<?php echo $v_user_code; ?>"
                                       />
                            </td>
                            <td>
                                <?php echo $v_content; ?>
                            </td>
                            <td>
                                <?php echo $v_user_name; ?>(<?php echo $v_user_job_title; ?>)
                            </td>
                            <td>
                                <?php echo $v_create_date; ?>
                            </td>
                            <td style="text-align: center">
                                <?php
                                if (strlen($v_xml_file) > 0)
                                {
                                    $v_xml_file = xml_add_declaration($v_xml_file);
                                    $dom_file   = simplexml_load_string($v_xml_file);
                                    $arr_file   = $dom_file->xpath('//file');
                                    foreach ($arr_file as $file)
                                    {
                                        $v_url_file = SITE_ROOT . "uploads" . DS . 'r3' . DS . $file;
                                        echo "<a target='_blank' href='$v_url_file' title='$file' style='margin-right: 5px;'>
                                                    <i class='icon-file-alt'></i>
                                                 </a>";
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endfor; ?>
                    <?php echo $this->add_empty_rows($c + 1, 5, 5); ?>
                </table>
                <!-- Button -->
                <br>
                <div class="well">
                    <div class="Row" style="margin-top: 10px;">
                        <div class="left-Col" style="width:15%;">
                            <label>
                                <?php echo (Session::get('is_bod_member') == 1) ? 'Nội dung ý kiến chỉ đạo mới:' : 'Nội dung ý kiến mới:'; ?>
                            </label>
                        </div>
                        <div class="right-Col">
                            <textarea style="width: 100%; height: 80px; margin: 0px;margin-left:0px;" rows="2" name="txt_content" id="txt_content" cols="20" maxlength="400"></textarea>  
                        </div>
                    </div>
                    <div class="Row">
                        <div class="left-Col" style="width:15%;">
                            <label>Tài liệu đính kèm: </label>
                        </div>
                        <div class="right-Col">
                            <input type="file" style="border: solid #D5D5D5; color: #000000" class="multi accept-<?php echo _CONST_RECORD_FILE_ACCEPT; ?>" name="uploader[]"
                                   id="File1" accept="<?php echo '.' . str_replace(',', ',.', _CONST_RECORD_FILE_ACCEPT) ?>"/>
                            <font style="font-weight: normal;">Hệ thống chỉ chấp nhận đuôi file:<?php echo _CONST_RECORD_FILE_ACCEPT ?></font>
                        </div>
                    </div>
                    <div class="button-area">
                        <?php $a = (Session::get('is_bod_member') == 1) ? 'Thêm ý kiến chỉ đạo' : 'Thêm Ý kiến'; ?>
                        <!--buton them moi-->

                        <button type="button" name="addnew" class="btn btn-primary btn-sm" onclick="btn_add_comment_onclick();">
                            <i class="icon-plus"></i>
                            <?php echo $a; ?>
                        </button>
                        <?php if (check_permission(_CONST_XOA_Y_KIEN_ROLE, 'R3')): ?>
                            <!--button xoa-->
                            <button type="button" name="trash" class="btn btn-sm" onclick="btn_delete_comment_onclick();">
                                <i class="icon-trash"></i>
                                <?php echo 'Xoá ý kiến' ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div><!--well-->
            </div>

            <!--  Tai lieu  -->
            <div class="tab-pane " id="attach">
                <div class="clear" style="height: 10px">&nbsp;</div>
                <table class="adminlist table table-bordered table-striped" style="width:100%" id="tbl_doc">
                    <tr id="tbl_doc_header">
                        <th style="text-align: center;">#</th>
                        <th style="text-align: center;">Số kí hiệu</th>
                        <th>Tên tài liệu</th>
                        <th>Nơi ban hành</th>
                        <th style="text-align: center;">Người cập nhật</th>
                        <th>Ngày cập nhật</th>
                        <th>Tài liệu đính kèm</th>
                    </tr>
                    <?php for ($i = 0; $i < sizeof($arr_all_doc); $i++): ?>
                        <tr data-did="d_<?php echo $arr_all_doc[$i]['PK_RECORD_DOC'] ?>" data-user="<?php echo $arr_all_doc[$i]['C_USER_CODE']; ?>">
                            <td style="text-align: center;">
                                <input type="checkbox" name="chk_doc" value="<?php echo $arr_all_doc[$i]['PK_RECORD_DOC']; ?>"
                                <?php echo ($arr_all_doc[$i]['C_USER_CODE'] == Session::get('user_code')) ? '' : ' disabled'; ?>
                                       data-user="<?php echo $arr_all_doc[$i]['C_USER_CODE']; ?>"
                                       />
                            </td>
                            <td style="text-align: center;"><?php echo $arr_all_doc[$i]['C_DOC_NO']; ?></td>
                            <td><?php echo $arr_all_doc[$i]['C_DESCRIPTION']; ?></td>
                            <td><?php echo $arr_all_doc[$i]['C_ISSUER']; ?></td>
                            <td style="text-align: center;"><?php echo $arr_all_doc[$i]['C_USER_NAME']; ?></td>
                            <td><?php echo $arr_all_doc[$i]['C_CREATE_DATE']; ?></td>
                            <td>
                                <?php if ($arr_all_doc[$i]['C_RECORD_DOC_FILE_LIST'] != NULL): ?>
                                    <?php $df   = simplexml_load_string('<root>' . $arr_all_doc[$i]['C_RECORD_DOC_FILE_LIST'] . '</root>'); ?>
                                    <?php $rows = $df->xpath('//row'); ?>
                                    <?php
                                    foreach ($rows as $row):
                                        $v_file_name      = $row->attributes()->C_FILE_NAME;
                                        $v_file_path      = SITE_ROOT . 'uploads/r3/' . $v_file_name;
                                        $v_file_extension = @array_pop(explode('.', $v_file_name));
                                        echo '<img src="' . SITE_ROOT . 'public/images/' . $v_file_extension . '-icon.png" width="16px" height="16px"/>';
                                        echo '<a href="' . $v_file_path . '" target="_blank">' . $v_file_name . '</a>';
                                    endforeach;
                                    ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endfor; ?>
                    <?php echo $this->add_empty_rows($i + 1, 5, 7); ?>
                </table>
                <!-- Button -->
                <div class="button-area">
                    <!--buton them moi-->
                    <button type="button" name="addnew" class="btn btn-primary btn-sm" onclick="btn_add_doc_onclick();">
                        <i class="icon-plus"></i>
                        <?php echo 'Thêm tài liệu'; ?>
                    </button>

                    <!--button xoa-->
                    <button type="button" name="trash" class="btn btn-sm" onclick="btn_delete_doc_onclick();">
                        <i class="icon-trash"></i>
                        <?php echo 'Xoá tài liệu' ?>
                    </button>
                </div>
            </div>

            <!-- Xem don -->
            <div class="tab-pane " id="content">
                <div class="clear" style="height: 10px">&nbsp;</div>
                <div>
                    <!--button in-->
                    <!--            <button type="button" name="trash" class="btn" onclick="btn_print_record_form_onclick();">
                                    <i class="icon-print"></i>
                    <?php echo 'In đơn'; ?>
                                </button>-->
                </div>
                <div>
                    <?php if ($VIEW_DATA['arr_all_record_file']): ?>
                        <label>File đính kèm:</label>
                        <?php
                        $arr_all_record_file = $VIEW_DATA['arr_all_record_file'];
                        for ($i = 0; $i < sizeof($arr_all_record_file); $i++)
                        {
                            $v_file_id   = $arr_all_record_file[$i]['PK_RECORD_FILE'];
                            $v_file_name = $arr_all_record_file[$i]['C_FILE_NAME'];
                            $v_media_id  = $arr_all_record_file[$i]['FK_MEDIA'];

                            if ($v_media_id != NULL && $v_media_id != '' && $v_media_id > 0)
                            {
                                $v_year            = $arr_all_record_file[$i]['C_YEAR'];
                                $v_month           = $arr_all_record_file[$i]['C_MONTH'];
                                $v_day             = $arr_all_record_file[$i]['C_DAY'];
                                $v_media_file_name = $arr_all_record_file[$i]['C_MEDIA_FILE_NAME'];
                                $v_file_name       = $arr_all_record_file[$i]['C_NAME'];
                                $v_file_extension  = $arr_all_record_file[$i]['C_EXT'];

                                $v_file_path = CONST_FILE_UPLOAD_LINK . "$v_year/$v_month/$v_day/$v_media_file_name";
                            }
                            else
                            {
                                $v_file_path      = SITE_ROOT . 'uploads/r3/' . $v_file_name;
                                $v_file_extension = array_pop(explode('.', $v_file_name));
                            }


                            echo '&nbsp;&nbsp;<img src="' . SITE_ROOT . 'public/images/' . $v_file_extension . '-icon.png" width="16px" height="16px"/>';
                            echo '<span id="file_' . $v_file_id . '">';
                            echo '<a href="' . $v_file_path . '" target="_blank">' . $v_file_name . '</a><br/>';
                            echo '</span>';
                        }
                        ?>
                    <?php endif; ?>
                </div>
                <?php echo $this->transform($this->get_xml_config($v_record_type_code, 'form_struct')); ?>
            </div>
            <!--  Nhat ky xu ly -->
            <div class="tab-pane " id="step_processing">
                <div class="clear" style="height: 10px">&nbsp;</div>
                <table class="adminlist table table-bordered table-striped" width="100%">
                    <colgroup>
                        <col width="15%" />
                        <col width="15%" />
                        <col width="55%" />
                        <col width="15%" />
                    </colgroup>
                    <tr>
                        <th>Cán bộ thực hiện</th>
                        <th>Công việc</th>
                        <th>Ghi chú</th>
                        <th>Ngày thực hiện</th>
                    </tr>
                    <?php $dom_processing = simplexml_load_string($v_xml_processing); ?>
                    <?php $steps          = $dom_processing->xpath('//data/step'); ?>
                    <?php foreach ($steps as $step): ?>
                        <?php $v_role = get_role($step->attributes()->code); ?>
                        <tr>
                            <td><?php echo $step->user_name; ?></td>
                            <td>
                                <?php echo isset($this->role_text[$v_role]) ? $this->role_text[$v_role] : $v_role; ?>
                            </td>
                            <td>
                                <?php echo $step->reason; ?>
                                <?php if ($v_role == _CONST_TRA_KET_QUA_ROLE && $arr_single_record['C_CLEAR_DATE'] != NULL): ?>
                                    <?php
                                    if ($dom_record_result != NULL)
                                    {
                                        $r = $dom_record_result->xpath("//item[value='true']/@id");
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
                                <?php endif; ?>
                            </td>
                            <td><?php echo jwDate::yyyymmdd_to_ddmmyyyy($step->datetime, TRUE); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <!--in mau ho tro thu lý-->
            <?php if ($v_assistance == 1): ?>
                <?php if ($this->check_permission(_CONST_THU_LY_ROLE) == TRUE OR $this->check_permission(_CONST_THU_LY_CAP_XA_ROLE) == TRUE): ?>
                    <div id="print_assistance">
                        <div class="clear" style="height: 10px">&nbsp;</div>
                        <?php
                        $v_test_dir = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . 'common' . DS . 'auto_lock_unlock.xml';

                        $v_file_ext_config = $this->get_xml_config($v_record_type_code, 'ext_config');
                        if (file_exists($v_file_ext_config))
                        {
                            $dom    = simplexml_load_file($v_file_ext_config);
                            $x_path = '//assistance/item';
                            $r      = $dom->xpath($x_path);
                        }
                        else
                        {
                            echo 'Chưa có mẫu hỗ trợ thụ lý';
                        }
                        ?>
                        <div class="Row">
                            <div class="left-Col">Chọn mẫu hỗ trợ thụ lý</div>
                            <div class="right-Col">
                                <select id="sel_assistance" name="sel_assistance">
                                    <?php
                                    for ($i = 0; $i < count($r); $i++):
                                        $v_file_dir  = $r[$i]->attributes()->file;
                                        $v_file_name = $r[$i]->attributes()->name;
                                        ?>
                                        <option value="<?php echo $v_file_dir ?>"><?php echo $v_file_name ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <!--button area-->
                        <?php if ($v_assistance == 1):; ?>
                            <div class="button-area">
                                <!--button in-->
                                <button type="button" name="trash" class="btn btn-sm" onclick="btn_print_assistance_form_onclick(<?php echo $v_record_id; ?>);">
                                    <i class="icon-print"></i>
                                    In mẫu hỗ trợ thụ lý
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <!--End in mau ho tro thu ly-->
        </div>
    </div>
    <!--solid button-->
    <div class="clear" style="height: 10px">&nbsp;</div>
    <div id="solid-button">
        <!--Button close window-->
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        <button type="button" name="trash" class="btn btn-sm" onclick="<?php echo $v_back_action; ?>" >
            <i class="icon-reply"></i>
            <?php echo __('close window'); ?>
        </button> 
    </div>
</form>
<script>
                        var f = document.frmMain;
                        $(function () {
                            $('#myTab1 a[href="#<?php echo $v_tab_selected ?>"]').tab('show');

                        })

                        $(document).ready(function () {
                            //Fill data
                            var formHelper = new DynamicFormHelper('', '', document.frmMain);
                            formHelper.BindXmlData();

                            try {
                                $("#txtName").focus();
                            } catch (e) {
                                ;
                            }
                            //menu record statistic
                            //show_id  = $('#menu_record_statistics li a:eq(<?php echo $v_tab_selected ?>)').attr('href');
                            //select_function(show_id);
                        });

                        function btn_add_comment_onclick()
                        {
                            var v_content = trim(f.txt_content.value);

                            if (v_content == '')
                            {
                                alert('Vui lòng nhập nội dung ý kiến!');
                                f.txt_content.focus();
                                return false;
                            }
                            $('#frmMain').attr('action', '<?php echo $this->get_controller_url(); ?>do_add_comment');
                            $('#frmMain').submit();
                        }

                        function btn_delete_comment_onclick()
                        {
                            //Lay danh sach da chon
                            var tbl_s = "#tbl_comment input[name='chk_comment']";
                            if ($('#tbl_comment input[name="chk_comment"]').filter(':checked').length < 1)
                            {
                                alert('Bạn phải chọn ý kiếm cần xóa !!!');
                                return false;
                            }

                            if (confirm('Bạn chắc chắn xoá ý kiến?'))
                            {
                                $(tbl_s).each(function (index) {
                                    if ($(this).is(':checked'))
                                    {
                                        v_comment_id = $(this).val();
                                        v_created_by = $(this).attr('data-user');
                                        v_user_token = '<?php echo Session::get('user_token'); ?>';

                                        $.ajax({
                                            url: '<?php echo $this->get_controller_url(); ?>do_delete_comment',
                                            type: "POST",
                                            data: {comment_id: v_comment_id
                                                , user_code: v_created_by
                                                , user_token: v_user_token
                                            },
                                            dataType: "json"
                                        });
                                        tr_s = "#tbl_comment tr[data-cid='c_" + v_comment_id + "']";
                                        $(tr_s).remove();
                                    }
                                });
                            }
                        }

                        function btn_add_doc_onclick()
                        {
                            var url = '<?php echo $this->get_controller_url(); ?>dsp_add_doc/' + $("#hdn_item_id").val()
                                    + '/&hdn_item_id=' + $("#hdn_item_id").val()
                                    + '&pop_win=1';
                            showPopWin(url, 800, 500, null, true);
                        }

                        function btn_delete_doc_onclick()
                        {
                            //var v_doc_id_list = get_select
                            //Lay danh sach da chon
                            var tbl_s = "#tbl_doc input[name='chk_doc']";
                            if (confirm('Bạn chắc chắn xoá các tài liệu đã chọn?'))
                            {
                                $(tbl_s).each(function (index) {
                                    if ($(this).is(':checked'))
                                    {
                                        v_doc_id = $(this).val();
                                        v_created_by = $(this).attr('data-user');
                                        v_user_token = $("#user_token").val();

                                        $.ajax({
                                            url: '<?php echo $this->get_controller_url(); ?>do_delete_doc',
                                            type: "POST",
                                            data: {doc_id: v_doc_id
                                                , user_code: v_created_by
                                                , user_token: v_user_token
                                            },
                                            dataType: "json"
                                        });
                                        tr_s = "#tbl_doc tr[data-did='d_" + v_doc_id + "']";
                                        $(tr_s).remove();
                                    }
                                });
                            }
                        }
                        //in mau don
                        function btn_print_record_form_onclick(record_id)
                        {
                            var v_url = '<?php echo $this->get_controller_url(); ?>dsp_print_record_form/' + record_id;
                            showPopWin(v_url, 800, 500);

                        }
                        //in mau ho tro thu ly
                        function btn_print_assistance_form_onclick(record_id)
                        {
                            var v_file_assistance_template = $('#sel_assistance').val();
                            var v_url = '<?php echo $this->get_controller_url(); ?>dsp_print_assistance_form/' + record_id + '&tpl_file_dir=' + v_file_assistance_template;
                            showPopWin(v_url, 800, 500);
                        }

                        //menu_record_statistics
                        $('#menu_record_statistics li a').click(function (e)
                        {
                            e.preventDefault();
                            show_id = $(this).attr('href');
                            select_function(show_id);


                        });
                        //chon chuc nang
                        function select_function(show_id)
                        {
                            //an tat ca div
                            selector = '#menu_record_statistics li a';
                            hide_all_div(selector);

                            //hien thi div
                            $(show_id).show();
                            //tao current function
                            selector_current = selector + '[href="' + show_id + '"]';
                            $(selector_current).parent('li').append('<div class="active-menu">&nbsp;</div>');
                        }

                        //an tat ca cac div
                        function hide_all_div(selector)
                        {
                            $(selector).each(function () {
                                //an div
                                id = $(this).attr('href');
                                $(id).hide();
                                //xoa current menu(gach chan mau do)
                                $('.active-menu').remove();
                            });
                        }
                        //In chi tiet tien do xu ly
                        function btn_print_record_statistics(record_id)
                        {
                            var v_url = $("#controller").val() + 'statistics/' + record_id + '&hdn_item_id=' + record_id + '&pop_win=1' + '&print_record_satistic=1';
                            showPopWin(v_url, 800, 500);
                        }
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');
