<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}

deny_bad_http_referer();

/* @var $this \r3_View */
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
$v_tab_selected = isset($_REQUEST['tab'])?$_REQUEST['tab']:0;
?>
<form name="frmMain" id="frmMain" action="" method="POST" style="background-color: white;">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('hdn_item_id', $v_record_id);
    echo $this->hidden('hdn_item_id_list', '');

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');

    echo $this->hidden('XmlData', $v_xml_data);
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
            </table>
            <div id="solid-button">
                <!--Button close window-->
                <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
                <button type="button" name="trash" class="btn" onclick="<?php echo $v_back_action; ?>" >
                    <i class="icon-remove"></i>
                    <?php echo __('close window'); ?>
                </button> 
            </div>
        </div>
    </div>
    <div class="clear">&nbsp;</div>
    <!--tab widget-->
    <div class="tab-widget" >
        <ul class="nav nav-tabs" id="myTab1" >
            <li>
                <a href="#flow" class="blue"><i class="icon-spinner"></i><span>Tiến độ</span></a>
            </li>
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
                            $tasks        = $step->task;
                            $v_rows       = count($tasks);
                            $t            = 0;

                            $v_is_no_chain_step = $step->attributes()->no_chain;
                            $v_tr_class         = ($v_is_no_chain_step == 'true') ? ' class="no_chain"' : '';
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
                                        <?php if ($v_role != _CONST_THU_PHI_ROLE && $v_role != _CONST_TRA_KET_QUA_ROLE && $v_role != _CONST_NHAN_BIEN_LAI_NOP_THUE_ROLE): ?>
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
                                        <?php endif; ?>
                                    </td>
                                </tr>
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
                            <?php endforeach; //$task?>
                            <?php $t++; ?>
                            <?php $step_order++; ?>
                        <?php endforeach; //$step ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--solid button-->
     <div class="clear" style="height: 10px">&nbsp;</div>
     <div id="solid-button">
        <!--Button close window-->
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        <button type="button" name="trash" class="btn" onclick="<?php echo $v_back_action; ?>" >
            <i class="icon-remove"></i>
            <?php echo __('close window'); ?>
        </button> 
    </div>
</form>
<script>
    $(function () {
        $('#myTab1 a:eq(<?php echo $v_tab_selected?>)').tab('show')
    })
    
    $(document).ready(function() {
        //Fill data
        var formHelper = new DynamicFormHelper('', '', document.frmMain);
        formHelper.BindXmlData();

        try {
            $("#txtName").focus();
        } catch (e) {
            ;
        }
        //menu record statistic
        //show_id  = $('#menu_record_statistics li a:eq(<?php echo $v_tab_selected?>)').attr('href');
        //select_function(show_id);
    });
    //menu_record_statistics
    $('#menu_record_statistics li a').click(function(e)
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
        selector_current = selector + '[href="'+show_id+'"]';
        $(selector_current).parent('li').append('<div class="active-menu">&nbsp;</div>');
    }
    
    //an tat ca cac div
    function hide_all_div(selector)
    {
        $(selector).each(function(){
           //an div
           id = $(this).attr('href');
           $(id).hide();
           //xoa current menu(gach chan mau do)
           $('.active-menu').remove();
        });
    }
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');