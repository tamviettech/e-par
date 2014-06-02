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

//display header
$this->template->title = $this->title = 'Công việc trong bước';
$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';

require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header' . $v_pop_win . '.php');

$v_xml_flow = session::get('v_current_xml_flow');
$dom_flow = simplexml_load_string($v_xml_flow);
$dom_task = $dom_flow->xpath("step[position()=$v_step_id]/task");
?>
<form name="frmMain" method="post" id="frmMain" action="">
    <input type="button" name="btn_save" onclick="btn_save_onclick()" value="Ghi lại" />
    <input type="button" name="btn_add_step" onclick="btn_add_step_onclick()" value="Thêm công việc" />
    <input type="hidden" name="hdn_sorting_info" id="hdn_sorting_info" value="" />
    <div id="response"></div>
    <div id="contentWrap">
        <div id="contentLeft">
            Kéo và thả để sắp xếp thứ tự công việc
            <ul class="ui-sortable" id="all_step">
            <?php
            $v_position = 1;
            foreach ($dom_task as $task):
                $v_code = $task->attributes()->code;
                $v_task_name = $task->attributes()->name;
                $v_time = $task->attributes()->time;
                $v_next = $task->attributes()->next;
                $v_is_single_user = $task->attributes()->single_user;

                $v_task_id= 'task_'. $v_position;
                ?>
                <li id="<?php echo $v_task_id;?>" style="opacity: 1; z-index: 0;" class="ui-state-disabled">
                    <div class="step-header">
                        <div class="step-name quick_action">
                            <h3>
                                <label id="<?php echo $v_task_id;?>_name"><?php echo $v_task_name;?></label>
                            </h3>
                        </div>
                        <div class="step-config quick_action">
                            <a href="javascript:void(0)" title="Hiệu chỉnh thông tin công việc"
                                class="quick_action"
                                onclick="btn_dsp_single_task_onclick(<?php echo $v_position;?>)">
                                <img src="<?php echo SITE_ROOT;?>public/images/edit-32x32.png" /></a>
                            <a href="javascript:void(0)" title="Xoá công việc"
                                class="quick_action"
                                onclick="btn_delete_task_onclick(<?php echo $v_position;?>)">
                                <img src="<?php echo SITE_ROOT;?>public/images/delete-24x24.png" width="24px"/>
                            </a>
                        </div>
                    </div>
                    <div id="task_<?php echo $v_step_id;?>" class="step-info">
                        - Thời gian thực hiện: <?php echo $v_time;?>
                        <br/>- Mã công việc: <?php echo $v_code;?>
                        <br/>- Mã công việc tiếp theo: <?php echo $v_next;?>
                        <br/>- Chỉ 1 người thực hiện: <?php echo $v_is_single_user;?>
                    </div>
                </li>
                <?php $v_position++;?>
            <?php endforeach;?>
            </ul>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function(){
        $(function() {
            $("#contentLeft ul").sortable({ opacity: 0.6, cursor: 'move', update: function() {
                var order = $(this).sortable("serialize")+ '&action=updateRecordsListings';
                $("#hdn_sorting_info").val(order);
            }
            });
        });
    });

    function btn_save_onclick()
    {
        var v_sorting_info = $("#hdn_sorting_info").val();
        v_sorting_info += '&pop_win=1';
        /*
        v_sorting_info += '&record_type_code=' + $("#hdn_record_type_code").val();

        v_sorting_info += '&total_time=' + $("#lbl_totaltime").html();
        v_sorting_info += '&fee=' + $("#lbl_fee").html();
        */

        alert(v_sorting_info);
        /*
        $.post("<?php echo $this->get_controller_url() . 'do_update_step_order_by_ui';?>", v_sorting_info , function(theResponse){
            $("#response").html('<pre>' + theResponse + '</pre>');
        });
        */
    }
</script>
<?php require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer.php');
