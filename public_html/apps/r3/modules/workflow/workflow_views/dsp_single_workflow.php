<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
?>
<?php
//display header
$this->template->title = $this->title = 'Phân công cán bộ vào quy trình xử lý hồ sơ';
$this->active_menu = $this->template->active_menu =  'quan_tri_ho_so';

require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header.php');

$arr_all_record_type = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code  = $VIEW_DATA['record_type_code'];
$xml_user_task       = $VIEW_DATA['xml_user_task'];
?>
<div class="container-fluid">
    <ul class="breadcrumb">
    	<li><a href="<?php echo SITE_ROOT;?>" class="icon-home"></a><span class="divider "><i class="icon-angle-right"></i></span></li>
        <li class="active">Quản trị hệ thống<span class="divider"><i class="icon-angle-right"></i></span></li>
        <li class="active">Quản trị quy trình xử lý hồ sơ</li>
    </ul>
    <div id="update_message" style="visibility: hidden">Updating...</div>
    <form name="frmMain" method="post" id="frmMain" action="" class="form-horizontal"><?php
        echo $this->hidden('controller', $this->get_controller_url());
        echo $this->hidden('hdn_tbl_id', '');
        echo $this->hidden('hdn_group_code', '');
        echo $this->hidden('hdn_next_task_code', '');

        echo $this->hidden('hdn_record_type_code', $v_record_type_code);
        ?>
        <!-- filter -->
        <div id="div_filter">
            (1)&nbsp;Mã loại hồ sơ
            <input type="text" name="txt_record_type_code" id="txt_record_type_code"
                   value="<?php echo $v_record_type_code; ?>"
                   class="upper_text input-small" size="10" maxlength="10"
                   onkeypress="txt_record_type_code_onkeypress(event);"
                   autofocus="autofocus"
                   accesskey="1"
                   />&nbsp;
            <select name="sel_record_type" id="sel_record_type" style="width:75%; color:#000000;"
                    onchange="sel_record_type_onchange(this)">
                <option value="">-- Chọn loại hồ sơ --</option>
                <?php echo $this->generate_select_option($arr_all_record_type, $v_record_type_code); ?>
            </select>
        </div>
        <input type="text" name="noname" style="visibility: hidden"/>
        <div id="procedure">
            <?php if ($v_record_type_code != ''): ?>
                <?php
                $xml_flow_file_path = $this->get_xml_config($v_record_type_code, 'workflow');
                if (is_file($xml_flow_file_path)):
                    $dom   = simplexml_load_file($xml_flow_file_path);
                    $r     = $dom->xpath("/process");
                    $proc  = $r[0];
                    $steps = array();
                    foreach (xpath($proc, '//step') as $step)
                    {
                        $steps[] = $step;
                    }
                    foreach ($proc->no_chain_step as $step)
                    {
                        $steps[] = $step;
                    }
                    $dom_current_assign = simplexml_load_string($xml_user_task);
                    ?>
                    <h3 class=" widget-header">Quy trình: <?php echo $proc->attributes()->code; ?> - <?php echo $proc->attributes()->name; ?></h3>
                    <h4 class=" widget-header">Tổng số ngày thực hiện: <?php echo $proc->attributes()->totaltime; ?></h4>

                    <button type="button" name="btn_edit_workflow" onclick="btn_edit_workflow_onclick(this)" class="btn"><i class="icon-edit"></i> Sửa quy trình XML</button>
                    <button type="button" name="btn_edit_workflow_ui" onclick="btn_edit_workflow_ui_onclick(this)" class="btn"><i class="icon-bar-chart"></i> Sửa quy trình qua giao diện</button>
                    <button type="button" name="btn_copy_assign" onclick="btn_copy_assign_onclick()" class="btn"><i class="icon-copy"></i> Sao chép phân công</button>
                    <button type="button" name="btn_push_assign" onclick="btn_push_assign_onclick()" class="btn"><i class="icon-copy"></i> Đẩy phân công sang nhiều thủ tục khác</button>
                    
                    <br/><br/>
                    <div class="step">
                        <table class="table table-bordered ">
                            <colgroup>
                                <col width="20%" />
                                <col width="20%" />
                                <col width="60%" />
                            </colgroup>
                            <tr height="30px">
                                <th rowspan="2">Bước</th>
                                <th rowspan="2">Công việc</th>
                                <th>Cán bộ thực hiện</th>
                            </tr>
                            <tr>

                            </tr>
                            <?php
                            foreach ($steps as $step):
                                $v_exec_group = $step->attributes()->group;
                                $v_step_name  = $step->attributes()->name;
                                $v_step_time  = $step->attributes()->time;
                                $tasks        = $step->task;
                                $v_rows       = count($tasks);
                                $t            = 0;
                                $v_step_type  = $step->attributes()->no_chain ? 'no_chain_step' : 'step';

                                foreach ($tasks as $task):
                                    $t++;
                                    //xoá next no chain tránh lỗi js
                                    $v_task_code = (string) $task->attributes()->code;
                                    $v_task_name = $task->attributes()->name;
                                    $v_task_time = $task->attributes()->time;
                                    $v_next_task = $task->attributes()->next;
                                    $v_biz_done  = $task->attributes()->biz_done;


                                    $v_no_chain = get_xml_value($dom, "//task[@code='$v_task_code']/../@no_chain");

                                    //Task dau tien cua Step

                                    $v_first_task_of_step = $dom->xpath("//step[task[@code='$v_task_code']]/task[1]/@code");
                                    $v_first_task_of_step = $v_first_task_of_step[0];

                                    //Task cuoi cung cua step truoc
                                    $v_prev_step_last_task = $dom->xpath("//step[task[@code='$v_task_code']]/preceding-sibling::step[1]/task[last()]/@code");
                                    if (sizeof($v_prev_step_last_task) > 0)
                                    {
                                        $v_prev_step_last_task = $v_prev_step_last_task[0];
                                    }
                                    else
                                    {
                                        $v_prev_step_last_task = $dom->xpath("//{$v_step_type}[task[@code='$v_task_code']]/task[position()=1]/@code");
                                        $v_prev_step_last_task = $v_prev_step_last_task[0];
                                    }

                                    $r_prev_task      = $dom->xpath("//task[@next='$v_task_code']/@code");
                                    $v_prev_task_code = isset($r_prev_task[0]) ? $r_prev_task[0] : '';

                                    $v_single_user = isset($task->attributes()->single_user) ? $task->attributes()->single_user : 'false';
                                    //$v_single_user = toStrictBoolean($v_single_user);

                                    $v_task_code_html = str_replace(_CONST_XML_RTT_DELIM, _CONST_HTML_RTT_DELIM, $v_task_code);
                                    if ($v_task_code_html == '')
                                    {
                                        var_dump($v_task_code);
                                    }
                                    $v_next_task_html = str_replace(_CONST_XML_RTT_DELIM, _CONST_HTML_RTT_DELIM, $v_next_task);
                                    if (strpos($v_task_code_html, '[') !== FALSE)
                                    {
                                        $v_task_code_html = substr($v_task_code_html, 0, strpos($v_task_code_html, '['));
                                    }

                                    $v_task_code_html = str_replace(' ', '', $v_task_code_html);
                                    $v_tbl_id         = 'tbl_user_on_task_' . $v_task_code_html;
                                    ?>
                                    <tr>
                                        <?php if ($t == 1): ?>
                                            <td rowspan="<?php echo $v_rows; ?>" class="<?php echo $v_step_type ?>">
                                                <strong>
                                                <?php echo $v_step_name; ?><br></strong>
                                                <i>(<?php echo $v_step_time ?> ngày làm việc)</i>
                                                
                                            </td>
                                        <?php endif; ?>
                                            <td><strong><i><?php echo $v_task_name; ?></i></strong></td>
                                        <td>
                                            <div id="task_<?php echo $v_task_code_html; ?>"
                                                 class="workflow-task" data-next_task="<?php echo $v_next_task_html; ?>"
                                                 data-single_user="<?php echo $v_single_user; ?>"
                                                 data-prev_task="<?php echo $v_prev_task_code; ?>"
                                                 data-step_time="<?php echo $v_step_time; ?>"
                                                 data-task_time="<?php echo $v_task_time; ?>"
                                                 data-first_task="<?php echo $v_first_task_of_step; ?>"
                                                 data-prev_step_last_task="<?php echo $v_prev_step_last_task; ?>"
                                                 data-no_chain="<?php echo $v_no_chain; ?>"
                                                 >
                                                <div class="workflow-user-list">
                                                    <table width="100%" class="table table-bordered table-striped" cellspacing="0" 
                                                           id="<?php echo $v_tbl_id; ?>"
                                                           data-next_task="<?php echo $v_next_task_html; ?>"
                                                           data-task="<?php echo $v_task_code_html; ?>"
                                                           data-prev_task="<?php echo $v_prev_task_code; ?>"
                                                           >
                                                        <colgroup>
                                                            <col width="5%" />
                                                            <col width="60%" />
                                                            <col width="35%" />
                                                        </colgroup>
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Tên</th>
                                                                <th>Chức danh</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        
                                                        <?php $v_count_user = 0; ?>
                                                        <?php if ($xml_user_task != NULL): ?>
                                                            <?php
                                                            if (strpos($v_task_code, '['))
                                                                $v_single_task_code = trim(substr($v_task_code, 0, strpos($v_task_code, '[')));
                                                            else
                                                                $v_single_task_code = $v_task_code;
                                                            $arr_user_task      = $dom_current_assign->xpath("//row[@C_TASK_CODE='" . $v_single_task_code . "']");
                                                            ?>
                                                            <?php foreach ($arr_user_task as $user): ?>
                                                                <?php
                                                                $v_user_code = $user->attributes()->C_USER_LOGIN_NAME;
                                                                $v_user_name = $user->attributes()->C_USER_NAME;
                                                                $v_job_title = $user->attributes()->C_JOB_TITLE;

                                                                $v_checkbox_id = $v_tbl_id . '_chk_user_' . $v_user_code;
                                                                $v_tr_id       = $v_tbl_id . '_tr_user_' . $v_user_code;
                                                                ?>
                                                                <tr id="<?php echo $v_tr_id ?>"  data-step_time="<?php echo $v_step_time ?>" data-task_time="<?php echo $v_task_time ?>">
                                                                    <td class="center">
                                                                        <input type="checkbox" name="chk_user" value="<?php echo $v_user_code ?>" id="<?php echo $v_checkbox_id ?>"
                                                                               data-user_type="user" data-step_time="<?php echo $v_step_time ?>"
                                                                               data-task_time="<?php echo $v_task_time ?>" />
                                                                    </td>
                                                                    <td >
                                                                        <div style="padding-right:100px;position:relative;">
                                                                            <label for="<?php echo $v_checkbox_id ?>">
                                                                                <img src="<?php echo SITE_ROOT ?>public/images/icon-16-user.png" border="0" align="absmiddle" />
                                                                                <?php echo $v_user_name ?>
                                                                                <button type="button" name="btn_change_user"  style="position:absolute;right:3px;"
                                                                                        class="btn btn-primary" 
                                                                                        data-task="<?php echo $v_task_code ?>"
                                                                                        data-user="<?php echo $v_user_code ?>"
                                                                                        data-group_code="<?php echo $v_exec_group ?>"
                                                                                        onclick="switch_user(this)" >
                                                                                        <i class="icon-refresh "></i><i class="icon-user"></i>Đổi cán bộ
                                                                                </button>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td><?php echo $v_job_title ?></td>
                                                                </tr>
                                                                <?php $v_count_user++; ?>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="workflow-user-action">
                                                    <button type="button" name="btn_add_user" 
                                                            class="btn btn-primary add_user input-medium" 
                                                            onclick="dsp_all_user_to_allot('<?php echo $v_tbl_id; ?>', '<?php echo $v_exec_group; ?>')">
                                                            <i class="icon-plus"></i><i class="icon-user"></i>Thêm NSD
                                                    </button>

                                                    <?php if (!($v_count_user == 0 OR !toStrictBoolean($v_single_user))): ?>
                                                        <script>$("#task_<?php echo $v_task_code_html; ?> .add_user").hide();</script>
                                                    <?php endif; ?>
                                                    <button type="button" name="btn_remove_user"  
                                                            class="btn btn-primary input-medium" 
                                                            onclick="remove_user('<?php echo $v_tbl_id; ?>')">
                                                            <i class="icon-remove"></i><i class="icon-user"></i>Bỏ NSD
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="clear">&nbsp;</div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </table>
                    </div> <!--div step-->
                <?php else: ?>
                    <br/>Bạn chưa định nghĩa quy trình xử lý cho thủ tục <b><?php echo $v_record_type_code; ?></b>
                    <br/>Hãy <input type="button" name="btn_add_workflow" onclick="btn_edit_workflow_onclick(this)" class="button add" value="Tạo quy trình XML" />
                    &nbsp;<input type="button" name="btn_add_workflow_ui" onclick="btn_edit_workflow_ui_onclick(this)" class="button add" value="Tạo qua giao diện" />
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="button-area">
            <!--<input type="button" name="update" class="button save" value="<?php echo __('update'); ?>" onclick="btn_update_event_onclick()"/>-->
        </div>
    </form>

    <script>
        function switch_user(source) {
            v_url = $('#controller').val() + 'dsp_switch_user'
                    + QS + 'user=' + $(source).attr('data-user')
                    + '&task=' + $(source).attr('data-task')
                    + '&group_code=' + $(source).attr('data-group_code');
            window.showPopWin(v_url, 600, 400, function(msg) {
                if (msg)
                    alert(msg);
            });
        }
    </script>
</div>
<?php
//chuyển js sang chỗ file khác cho gọn
include dirname(__FILE__) . '/dsp_single_workflow.js.php';
//footer
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer.php');
