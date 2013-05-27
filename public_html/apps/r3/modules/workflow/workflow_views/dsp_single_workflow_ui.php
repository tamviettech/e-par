<?php
/**
 // File name   : dsp_single_workflow_ui.php
 // Version     : 1.0.0.1
 // Begin       : 2012-12-01
 // Last Update : 2010-12-25
 // Author      : TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn
 // License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
 // -------------------------------------------------------------------
 //Copyright (C) 2012-2013  TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn

 // E-PAR is free software: you can redistribute it and/or modify it
 // under the terms of the GNU Lesser General Public License as
 // published by the Free Software Foundation, either version 3 of the
 // License, or (at your option) any later version.
 //
 // E-PAR is distributed in the hope that it will be useful, but
 // WITHOUT ANY WARRANTY; without even the implied warranty of
 // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 // See the GNU Lesser General Public License for more details.
 //
 // See LICENSE.TXT file for more information.
 */
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
?>
<?php
//display header
$this->template->title = 'Quản trị quy trình xử lý hồ sơ';
$this->template->display('dsp_header.php');
?>
<style>
    .darkness, .modal_container{
        width: 100%;
        height: 100%;
        position: fixed;
        top:0;
        left:0;
    }
    .darkness, .darkness_inner{background-color: black; opacity: 0.8;}
    .darkness{z-index:999}
    .modal_container{z-index: 999;}
    #modal_step, #modal_task, #modal_proc_info{background-color: white;margin: 0 auto;margin-top: 50px;}
    #modal_proc_info, #modal_step{width:400px;height:300px;}
    #modal_task{width: 800px;height: 600px;}

    .modal_header{padding: 0px 3px;background-color: gray;text-align: right;line-height: 30px;height: 30px}
    .modal_header .modal_title{float:left;color:white;line-height: 30px;font-weight: bold;height: 30px}
    #modal_step .modal_body, #modal_task .modal_body, #modal_proc_info .modal_body{padding: 5px;height: 560px;overflow-y: auto;}
    .modal_body{position: relative}
    .holder{height: 108px;border: 3px dashed #666;background: none;}
    table td{border: 0px;}
    #modal_task_edit, #modal_step_edit{margin: 0 auto;margin-top: 100px;left:100px; width: 600px; height: 400px;background-color: white;}
</style>
<script src="<?php echo SITE_ROOT ?>public/js/angular.min.js"></script>
<form name="frmMain" method="post" id="frmMain" action="" ng-controller="ui_ctrl">
    {{old_tasks}}
    <div class="darkness" ng-show="modal_step.visible || modal_task.visible || modal_proc_info.visible">&nbsp;</div>
    <div 
        class="modal_container" 
        ng-show="modal_step.visible || modal_task.visible || modal_proc_info.visible"
        >
        <div id="modal_proc_info" ng-show="modal_proc_info.visible">
            <div class="modal_header">
                <div class="modal_title">Thông tin quy trình</div>
                <input type="button" value="Đóng cửa sổ" ng-click="modal_proc_info.visible = false"/>
            </div>
            <div class="modal_body">
                <table border="0">
                    <tr>
                        <td>Mã quy trình <span class="required">(*)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><input type="text" size="50" ng-model="modal_proc_info.data['code']"/></td>
                    </tr>
                    <tr>
                        <td>Tên quy trình <span class="required">(*)</span></td>
                        <td><input type="text" size="50" ng-model="modal_proc_info.data['name']"/></td>
                    </tr>
                    <tr>
                        <td>Tổng số ngày thực hiện <span class="required">(*)</span></td>
                        <td><input type="text" ng-model="modal_proc_info.data['totaltime']"/></td>
                    </tr>
                    <tr>
                        <td>Lệ phí <span class="required">(*)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><input type="text" ng-model="modal_proc_info.data['fee']"/></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="button" ng-click="modal_proc_info.save()" value="Ghi lại"/>
                            <input type="button" ng-click="modal_proc_info.visible = false" value="Huỷ bỏ"/>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="modal_step" ng-show="modal_step.visible">
            <div class="modal_header">
                <div class="modal_title">Hiệu chỉnh thông tin bước</div>
                <input type="button" value="Đóng cửa sổ" ng-click="modal_step.visible = false;"/>
            </div>
            <div class="modal_body">
                <table border="0">
                    <tr>
                        <td>Mã bước</td>
                        <td>
                            <input 
                                type="text" 
                                ng-model="modal_step.step['@attributes']['code']" 
                                size="40"
                                />
                        </td>
                    </tr>
                    <tr>
                        <td>Tên bước <span class="required">(*)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>
                            <input 
                                type="text" 
                                ng-model="modal_step.step['@attributes']['name']" 
                                size="40"
                                />
                        </td>
                    </tr>
                    <tr>
                        <td>Bộ phận thực hiện <span class="required">(*)</span></td>
                        <td>
                            <select 
                                ng-model="modal_step.step['@attributes']['group']"
                                ng-options="group.C_CODE as group.C_NAME for group in groups"
                                >
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Tổng số ngày quy định <span class="required">(*)</span></td>
                        <td>
                            <input 
                                type="text" 
                                ng-model="modal_step.step['@attributes']['time']" 
                                size="40"
                                />
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="button" ng-click="modal_step.save()" value="Ghi lại"/>
                            <input type="button" ng-click="modal_step.visible = false" value="Huỷ bỏ"/>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="modal_task" ng-show="modal_task.visible">
            <div class="darkness" ng-show="modal_task.modal_edit.visible"></div>
            <div class="modal_container" ng-show="modal_task.modal_edit.visible">
                <div id="modal_task_edit" ng-show="modal_task.modal_edit.visible">
                    <div class="modal_header">
                        <div class="modal_title">Thông tin bước</div>
                        <input type="button" value="Đóng cửa sổ" ng-click="modal_task.modal_edit.visible = false"/>
                    </div>
                    <div class="modal_body">
                        <table border="0">
                            <tr>
                                <td>Tên công việc <span class="required">(*)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td>
                                    <input 
                                        type="text" 
                                        ng-model="modal_task.modal_edit.task['@attributes']['name']" 
                                        size="40"
                                        />
                                </td>
                            </tr>
                            <tr>
                                <td>Mã công việc <span class="required">(*)</span></td>
                                <td>
                                    <input 
                                        type="text" 
                                        ng-model="modal_task.modal_edit.task['@attributes']['code']"
                                        />
                                </td>
                            </tr>
                            <tr>
                                <td>Thời gian xử lý <span class="required">(*)</span></td>
                                <td>
                                    <input 
                                        type="text" 
                                        ng-model="modal_task.modal_edit.task['@attributes']['time']" 
                                        size="40"
                                        />
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <input 
                                        type="checkbox"
                                        id="chk_single_user"
                                        ng-model="modal_task.modal_edit.task['@attributes']['single_user']" 
                                        value="true"
                                        />
                                    <label for="chk_single_user">Chỉ một người thực hiện</label>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <input 
                                        type="checkbox" 
                                        id="chk_biz_done"
                                        ng-model="modal_task.modal_edit.task['@attributes']['biz_done']" 
                                        value="true"
                                        ng-disabled="modal_task.modal_edit.disable_biz_done()"
                                        />
                                    <label for="chk_biz_done">Hoàn thành thụ lý</label>
                                </td>
                            </tr>
                            <tr>
                                <td>

                                </td>
                                <td>
                                    <input 
                                        type="checkbox"
                                        id="chk_auto_add_time"
                                        ng-model="modal_task.modal_edit.task['@attributes']['auto_add_time']" 
                                        value="true"
                                        ng-disabled="modal_task.modal_edit.disable_add_time()"
                                        />
                                    <label for="chk_auto_add_time">
                                        Cộng thêm ngày cho lãnh đạo nếu phòng chuyên môn hoàn thành trước hạn
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>

                                </td>
                                <td>
                                    <input 
                                        type="checkbox"
                                        id="chk_pause"
                                        ng-model="modal_task.modal_edit.task['@attributes']['pause']" 
                                        value="true"
                                        ng-disabled="modal_task.modal_edit.disable_pause()"
                                        />
                                    <label for="chk_pause">
                                        Dừng hồ sơ để thực hiện nghĩa vụ thuế
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>

                                </td>
                                <td>
                                    <input 
                                        type="checkbox"
                                        id="chk_unpause"
                                        ng-model="modal_task.modal_edit.task['@attributes']['unpause']" 
                                        value="true"
                                        ng-disabled="modal_task.modal_edit.disable_unpause()"
                                        />
                                    <label for="chk_unpause">
                                        Hồ sơ sẽ được tiếp tục hoạt động ngay sau khi Nhận biên lai thuế
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <Td></Td>
                                <td>
                                    <input type="button" ng-click="modal_task.modal_edit.save()" value="Ghi lại"/>
                                    <input type="button" ng-click="modal_task.modal_edit.visible = false" value="Huỷ bỏ"/>
                                </td>  
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal_header">
                <div class="modal_title">Công việc trong bước</div>
                <input type="button" value="Đóng cửa sổ" ng-click="modal_task.visible = false"/>
            </div>
            <div class="modal_body">
                <input type="button" ng-click="modal_task.add()" value="Thêm mới" />
                <input type="button" ng-click="modal_task.save()" value="Ghi lại" />
                <input type="button" ng-click="modal_task.visible = false" value="Huỷ bỏ" />
                <div id="contentWrap">
                    <div id="contentLeft">
                        Kéo và thả để sắp xếp thứ tự công việc
                        <ul id="all_task">
                            <li 
                                ng-repeat="task in modal_task.tasks" 
                                id="task_{{$index}}" 
                                style="opacity: 1; z-index: 0;" 
                                class="ui-state-disabled"
                                >
                                <div class="step-header">
                                    <div class="step-name quick_action">
                                        <h3>
                                            <label class="{{task.error}}">{{task['@attributes']['name']}}</label>
                                        </h3>
                                    </div>
                                    <div class="step-config quick_action">
                                        <a href="javascript:void(0)" title="Hiệu chỉnh thông tin công việc"
                                           class="quick_action"
                                           ng-click="modal_task.edit($index)"
                                           >
                                            <img src="<?php echo SITE_ROOT; ?>public/images/edit-32x32.png" />
                                        </a>
                                        <a href="javascript:void(0)" title="Xoá công việc"
                                           class="quick_action"
                                           ng-click="modal_task.delete_task($index)"
                                           >
                                            <img src="<?php echo SITE_ROOT; ?>public/images/delete-24x24.png" width="24px"/>
                                        </a>
                                    </div>
                                </div>
                                <div class="step-info">
                                    - Thời gian thực hiện: {{task['@attributes']['time']}}
                                    <br/>- Mã công việc: {{task['@attributes']['code']}}
                                    <br/>- Mã công việc tiếp theo: {{task['@attributes']['next']}}
                                    <br/>- Chỉ 1 người thực hiện: {{task['@attributes']['single_user']}}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    echo $this->hidden('controller', $this->get_controller_url() . 'ui');
    echo $this->hidden('hdn_tbl_id', '');
    echo $this->hidden('hdn_group_code', '');
    echo $this->hidden('hdn_next_task_code', '');

    echo $this->hidden('hdn_record_type_code', $v_record_type_code);
    ?>
    <!-- filter -->
    <div id="div_filter">
        (1)&nbsp;<label>Mã loại hồ sơ</label>
        <input type="text" name="txt_record_type_code" id="txt_record_type_code"
               value="<?php echo $v_record_type_code; ?>"
               class="inputbox upper_text" size="10" maxlength="10"
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
    <?php if ($v_record_type_code != ''): ?>
        <?php $xml_flow_file_path = $this->get_xml_config($v_record_type_code, 'workflow'); ?>
        <input type="hidden" name="hdn_sorting_info" id="hdn_sorting_info" value="" />
        <div>
            <?php
            if (is_file($xml_flow_file_path))
            {
                $v_xml_flow = file_get_contents($xml_flow_file_path);
            }
            else
            {
                $v_xml_flow = str_replace('#CODE#', $v_record_type_code, $v_default_xml_flow);
            }
            session::set('v_current_xml_flow', $v_xml_flow);
            $dom        = simplexml_load_string($v_xml_flow);
            $r          = $dom->xpath("/process");
            $proc       = $r[0];
            $steps      = $proc->step;
            ?>
            <div ng-init='proc = <?php echo json_encode($proc) ?>'></div>
            <div ng-init='roles = <?php echo json_encode($arr_all_role) ?>'></div>
            <div ng-init='groups = <?php echo json_encode($arr_all_group) ?>'></div>
            <div ng-init="controller = '<?php echo $this->get_controller_url() ?>'"></div>
            <div ng-init="hdn_record_type_code = '<?php echo $v_record_type_code ?>'"></div>
            <div ng-init='hdn_xml_flow_file_path = "<?php echo str_replace(array("\\", '/'), "\\" . DS, $xml_flow_file_path) ?>"'></div>
            <h6>Quy trình: {{proc['@attributes']['code']}} - {{proc['@attributes']['name']}}</h6>
            <h6>Tổng số ngày thực hiện: <label id="lbl_totaltime">{{proc['@attributes']['totaltime']}}</label> ngày</h6>
            <h6>Phí, lệ phí: <label id="lbl_fee">{{proc['@attributes']['fee']}}</label> (đ)</h6>

            <?php echo $this->hidden('hdn_record_type_code', strval($proc->attributes()->code)); ?>
            <?php echo $this->hidden('hdn_record_type_name', strval($proc->attributes()->name)); ?>
            <?php echo $this->hidden('hdn_total_time', strval($proc->attributes()->totaltime)); ?>
            <?php echo $this->hidden('hdn_fee', strval($proc->attributes()->fee)); ?>

            <input type="button" value="Sửa" ng-click="modal_proc_info.show()"/>
        </div>
        <div id="contentWrap">
            <div id="contentLeft">
                Kéo và thả để sắp xếp thứ tự bước
                <ul class="ui-sortable" id="all_step">
                    <li ng-repeat="step in proc['step']" id="step_{{$index}}" style="opacity: 1; z-index: 0;" class="ui-state-disabled">
                        <div class="step-header">
                            <div class="step-name quick_action">
                                <h6>
                                    <a href="javascript:void(0)" title="Công việc trong bước" class="quick_action" ng-click="dsp_all_task($index)">
                                        <img src="<?php echo SITE_ROOT; ?>public/images/config.png" width="24px"/>
                                    </a>
                                    <a href="javascript:void(0)" title="Hiệu chỉnh thông tin bước" class="quick_action" ng-click="dsp_step_details($index)">
                                        <img src="<?php echo SITE_ROOT; ?>public/images/edit-32x32.png" width="24px"/>
                                    </a>
                                    <a href="javascript:void(0)" title="Xoá bước" class="quick_action" ng-click="delete_step($index)">
                                        <img src="<?php echo SITE_ROOT; ?>public/images/delete-24x24.png" width="24px"/>
                                    </a>
                                    <label class="{{step.error}}" id="lbl_step_{{$index}}">{{step['@attributes']['name']}}</label>
                                </h6>
                            </div>
                        </div>
                        <div class="step-info">
                            - Tổng số ngày quy định: <label>{{step['@attributes']['time']}}</label>
                            <br/>- Bộ phận thực hiện: <label>{{step['@attributes']['group']}}</label>
                        </div>
                    </li>
                </ul>
            </div>
            <div style="float:left;width: 200px;padding: 10px;">
                <p class="required" ng-repeat="error in submit_errors">{{error}}</p>
            </div>
            <div id="contentRight">
                <input type="button" ng-click="submit_workflow()" value="Ghi lại" />
                <input type="button" ng-click="add_step()" value="Thêm bước" />
                <input type="button" value="Quay lại" ng-click="goback()"/>
                <img width="16" src="<?php echo SITE_ROOT ?>public/images/loading.gif" ng-show="submiting"/>
                <div id="response"></div>

            </div>
        </div>
    <?php endif; ?>
</form>
<script type="text/javascript">
    //    $(document).ready(function(){
    //        $(function() {
    //            $("#contentLeft ul").sortable({ opacity: 0.6, cursor: 'move', update: function() {
    //            var order = $(this).sortable("serialize")+ '&action=updateRecordsListings';
    //            $("#hdn_sorting_info").val(order);
    //            }
    //            });
    //        });
    //    });
    //
    //    function btn_save_onclick()
    //    {
    //        var v_sorting_info = $("#hdn_sorting_info").val();
    //        v_sorting_info += '&pop_win=1';
    //        v_sorting_info += '&record_type_code=' + $("#hdn_record_type_code").val();
    //        v_sorting_info += '&record_type_name=' + encodeURIComponent($("#hdn_record_type_name").val());
    //        v_sorting_info += '&total_time=' + $("#lbl_totaltime").html();
    //        v_sorting_info += '&fee=' + $("#lbl_fee").html();
    //
    //        $.post("<?php echo $this->get_controller_url() . 'do_update_step_order_by_ui'; ?>", v_sorting_info , function(theResponse){
    //            $("#response").html('<pre>' + theResponse + '</pre>');
    //        });
    //    }
    //
    //
    //    function btn_add_step_onclick()
    //    {
    //        var v_new_step_id = $("#all_step li").length + 1;
    //        var v_new_step_name = v_new_step_id + '. Đây là step ' + v_new_step_id;
    //        var v_new_step_html = '<li id="step_' + v_new_step_id + '" style="opacity: 1; z-index: 0;" class="">' + v_new_step_name + '</li>';
    //
    //        var v_new_step_html = '<li id="step_' + v_new_step_id + '" style="opacity: 1; z-index: 0;" class="">';
    //        v_new_step_html += '<div class="step-header">';
    //        v_new_step_html += '<h6>' + v_new_step_name + '</h6>';
    //        v_new_step_html += '</div>';
    //        v_new_step_html += '<div id="task_' + v_new_step_id + '" class="step-info">';
    //        v_new_step_html += '- Tổng số ngày quy định: xxx';
    //        v_new_step_html += '- <br/>- Bộ phận thực hiện: xxx';
    //        v_new_step_html += '</li>';
    //
    //        $("#all_step").append(v_new_step_html);
    //    }
    //
    //    function btn_edit_process_attributes_onclick()
    //    {
    //        var url = '<?php echo $this->get_controller_url(); ?>dsp_single_process/';
    //        url += '&pop_win=1';
    //        url += '&record_type_code=' + $("#hdn_record_type_code").val();
    //        url += '&record_type_name=' + encodeURIComponent($("#hdn_record_type_name").val());
    //        url += '&total_time=' + $("#lbl_totaltime").html();
    //        url += '&fee=' + $("#lbl_fee").html();
    //        showPopWin(url, 450, 350, do_assign);
    //    }
    //
    //    function do_assign(returnVal)
    //    {
    //        myObject = eval('(' + returnVal + ')');
    //        $("#lbl_totaltime").html(myObject[0]);
    //        $("#lbl_fee").html(myObject[1]);
    //    }
    //
    //    function btn_delete_step_onclick(id)
    //    {
    //        if (confirm('Bạn chắc chắn xoá bước này?'))
    //        {
    //            alert('Send Ajax request to remove step from process');
    //        }
    //    }
    //
    //    function btn_dsp_single_step_onclick(id)
    //    {
    //    	var url = '<?php echo $this->get_controller_url(); ?>dsp_single_step/';
    //        url += '&pop_win=1';
    //        url += '&step_id='+id;
    //        url += '&record_type_code=' + $("#hdn_record_type_code").val();
    //        url += '&record_type_name=' + encodeURIComponent($("#hdn_record_type_name").val());
    //        showPopWin(url, 600, 400, do_update_step);
    //    }
    //
    //    function do_update_step(returnVal)
    //    {
    //    	myObject = eval('(' + returnVal + ')');
    //        v_step_id = myObject[0];
    //
    //        $("#step_" + v_step_id + "_name").html(myObject[1]);
    //        $("#step_" + v_step_id + "_group").html(myObject[2]);
    //        $("#step_" + v_step_id + "_time").html(myObject[3]);
    //    }
    //
    //    function dsp_all_task_in_step(step_id)
    //    {
    //    	var url = '<?php echo $this->get_controller_url(); ?>dsp_all_task_in_step/' + step_id + '/';
    //
    //    	url += '&pop_win=1';
    //    	url += '&record_type_code=' + $("#hdn_record_type_code").val();
    //        url += '&record_type_name=' + encodeURIComponent($("#hdn_record_type_name").val());
    //
    //        showPopWin(url, 650, 600, do_update_task_in_step);
    //    }
    //    function do_update_task_in_step()
    //    {
    //        alert('reload');
    //    }


</script>
<?php
$this->template->display('dsp_footer.php');