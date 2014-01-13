<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

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
<form name="frmMain" method="post" id="frmMain" action="" ng-controller="ui_ctrl">
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
                <table class="no-border">
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
                        <td>XML định nghĩa kết quả trả</td>
                        <td>
                            <div ng-init='xml_results = <?php echo json_encode($arr_all_xml_result) ?>'></div>
                            <select 
                                ng-model="modal_proc_info.data['result']" 
                                ng-options="xml as xml for xml in xml_results">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="button" class="button save" ng-click="modal_proc_info.save()" value="Ghi lại"/>
                            <input type="button" class="button close" ng-click="modal_proc_info.visible = false" value="Huỷ bỏ"/>
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
                <table class="no-border">
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
                            <input type="button" class="button save" ng-click="modal_step.save()" value="Ghi lại"/>
                            <input type="button" class="button close" ng-click="modal_step.visible = false" value="Huỷ bỏ"/>
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
                        <table class="no-border">
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

                                        />
                                    <label for="chk_pause">
                                        Đặt hồ sơ về trạng thái TẠM DỪNG ngay sau khi hoàn thành công việc này
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

                                        />
                                    <label for="chk_unpause">
                                        Hồ sơ tiếp tục HOẠT ĐỘNG ngay sau khi hoàn thành công việc này
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <Td></Td>
                                <td>
                                    <input type="button" class="button save" ng-click="modal_task.modal_edit.save()" value="Ghi lại"/>
                                    <input type="button" class="button close" ng-click="modal_task.modal_edit.visible = false" value="Huỷ bỏ"/>
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
                <input type="button" class="button add" ng-click="modal_task.add()" value="Thêm mới" />
                <input type="button" class="button save" ng-click="modal_task.save()" value="Ghi lại" />
                <input type="button" class="button close" ng-click="modal_task.visible = false" value="Huỷ bỏ" />
                <h4></h4>
                <div id="contentWrap">
                    <div id="contentLeft">
                        <div class="alert alert-info"><b>Kéo</b> và <b>thả</b> để sắp xếp thứ tự công việc</div>
                        <ul id="all_task">
                            <div 
                                ng-repeat="task in modal_task.tasks" 
                                id="task_{{$index}}" 
                                style="opacity: 1; z-index: 0;" 
                                class="ui-state-disabled"
                                >
                                <li style="width:600px">
                                    <div class="step-header">
                                        <div class="step-name quick_action">
                                            <h6>
                                                <label class="{{task.error}}">{{task['@attributes']['name']}}</label>
                                            </h6>
                                        </div>
                                        <div class="step-config quick_action">
                                            <a href="javascript:void(0)" title="Hiệu chỉnh thông tin công việc"
                                               class="quick_action"
                                               ng-click="modal_task.edit($index)"
                                               >
                                                <img src="<?php echo SITE_ROOT; ?>public/images/edit-32x32.png" style="width: 24px;height: 24px;" />
                                            </a>
                                            <a href="javascript:void(0)" title="Xoá công việc"
                                               class="quick_action"
                                               ng-click="modal_task.delete_task($index)"
                                               >
                                                <img src="<?php echo SITE_ROOT; ?>public/images/delete-24x24.png" style="width:24px;;height: 24px;"/>
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
                                <div style="width:32px;margin:0 auto" ng-show="$index < modal_task.tasks.length - 1">
                                    <img width="32" height="32" src="<?php echo SITE_ROOT ?>public/images/arrow_down.png"/>
                                </div>
                            </div><!--ng-repeat-->
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
        <?php $xml_flow_file_path = $this->get_xml_config($v_record_type_code, 'workflow', false); ?>
        <input type="hidden" name="hdn_sorting_info" id="hdn_sorting_info" value="" />
        <div>
            <?php
            if (is_file($xml_flow_file_path))
            {
                $v_xml_flow = file_get_contents($xml_flow_file_path);
            }
            else
            {
                $v_default_xml_flow = '<?xml version="1.0" encoding="UTF-8"?>
                <process author="Tam Viet" code="#CODE#" name="Chưa đặt tên" totaltime="15" version="1" fee="25000">
                </process>';
                $v_xml_flow         = str_replace('#CODE#', $v_record_type_code, $v_default_xml_flow);
            }
            session::set('v_current_xml_flow', $v_xml_flow);
            $dom  = simplexml_load_string($v_xml_flow);
            $r    = $dom->xpath("/process");
            $proc = $r[0];
            if (empty($proc->attributes()->result))
            {
                $proc->addAttribute('result', 'record_result.default.xml');
            }
            $step          = $proc->xpath("//step[not(@no_chain='true')]");
            $no_chain_step = $proc->xpath("//step[@no_chain='true']");

            foreach ($proc->step as $step)
            {
                $is_no_chain = ($step->attributes()->no_chain == 'true');
                if ($is_no_chain)
                {
                    $dom      = dom_import_simplexml($step);
                    $dom->parentNode->removeChild($dom);
                    $no_chain = $proc->addChild('no_chain_step');
                    foreach ($step->attributes() as $k => $v)
                    {
                        $no_chain->addAttribute($k, $v);
                    }
                    foreach ($step->children() as $task)
                    {
                        $no_chain_task = $no_chain->addChild('task');
                        foreach ($task->attributes() as $k => $v)
                        {
                            $no_chain_task->addAttribute($k, $v);
                        }
                    }
                }
                else
                {
                    foreach ($step->children() as $task)
                    {
                        $a = preg_match("/\[(.*)\]/", (string) $task->attributes()->code, $matches);
                        $b = (string) $task->attributes()->next_no_chain;

                        if ($a && !$b)
                        {
                            $c = count($proc->xpath("//task[@code='{$matches[1]}']"));
                            if ($c)
                            {
                                $task->addAttribute('next_no_chain', $matches[1]);
                            }
                            $pos                      = strpos($task->attributes()->code, '[');
                            $task->attributes()->code = trim(substr($task->attributes()->code, 0, $pos));
                        }
                    }
                }
            }
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

            <?php echo $this->hidden('hdn_record_type_code', strval($proc['@attributes']['code'])); ?>
            <?php echo $this->hidden('hdn_xml_flow_file_path', str_replace(array("\\", '/'), "\\" . DS, $xml_flow_file_path)) ?>
            <?php echo $this->hidden('hdn_record_type_name', strval($proc['@attributes']['name'])); ?>
            <?php echo $this->hidden('hdn_total_time', strval($proc['@attributes']['totaltime'])); ?>
            <?php echo $this->hidden('hdn_fee', strval($proc['@attributes']['fee'])); ?>
            <input type="button" class="button lookup" value="Sửa" ng-click="modal_proc_info.show()"/>
            <h4></h4>
        </div>
        <div id="contentWrap">
            <div id="contentLeft" style='width:100%'>
                <div class="alert alert-info"><b>Kéo</b> và <b>thả</b> để sắp xếp thứ tự bước</div>
                <ul class="ui-sortable" id="all_step">
                    <div ng-repeat="step in proc['step']" 
                         class='step_container ui-state-disabled' id="step_{{$index}}" 
                         style="opacity: 1; z-index: 0;" 
                         class=""
                         >
                        <li class='step' ng-class='get_step_class(get_no_chain_task_code($index))'>
                            <div class="step-header">
                                <div class="step-name quick_action">
                                    <h6>
                                        <label class="{{step.error}}" id="lbl_step_{{$index}}">{{step['@attributes']['name']}}</label>
                                    </h6>
                                    <div class="step-buttons">
                                        <a href="javascript:void(0)" title="Công việc trong bước" class="quick_action" ng-click="dsp_all_task(step, false)">
                                            <img src="<?php echo SITE_ROOT; ?>public/images/config.png" style="width:24px;height:24px"/>
                                        </a>
                                        <a href="javascript:void(0)" title="Hiệu chỉnh thông tin bước" class="quick_action" ng-click="dsp_step_details($index, 'step')">
                                            <img src="<?php echo SITE_ROOT; ?>public/images/edit-32x32.png" style="width:24px;height:24px"/>
                                        </a>
                                        <a href="javascript:void(0)" title="Xoá bước" class="quick_action" ng-click="delete_step($index)">
                                            <img src="<?php echo SITE_ROOT; ?>public/images/delete-24x24.png" style="width:24px;height:24px"/>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="step-info">
                                - Tổng số ngày quy định: <label>{{step['@attributes']['time']}}</label>
                                <br/>- Bộ phận thực hiện: <label>{{step['@attributes']['group']}}</label>

                                <div class="parallel_div" ng-show="not_last_step($index) && !get_no_chain_task_code($index)">
                                    <a title="Tạo bước song song" ng-click="add_no_chain_step($index)">
                                        <img 
                                            width="32" height="32" 
                                            src="<?php echo SITE_ROOT ?>public/images/parallel.png"
                                            alt="Tạo bước song song"
                                            />
                                    </a>
                                </div>

                            </div>
                        </li>

                        <div style="display:none">
                            {{ no_chain_step[$index] = get_no_chain_step( get_no_chain_task_code($index) ) }}
                        </div>
                        <div ng-switch on="no_chain_step[$index]" >
                            <div ng-switch-when="false"></div>
                            <div ng-switch-default>
                                <div style='float:left;width:32;height:32px;margin-top: 24px;'>
                                    <img 
                                        width='32' height="32" 
                                        src='<?php echo SITE_ROOT ?>public/images/arrow_right.png'
                                        />
                                </div>
                                <li class='step no_chain'>
                                    <div class="step-header">
                                        <div class="step-name quick_action">
                                            <h6>
                                                <label ng-class="{{no_chain_step.error}}" id="lbl_no_chain_{{$index}}">{{no_chain_step[$index]['@attributes']['name']}}</label>
                                            </h6>
                                            <div class="step-buttons">
                                                <a href="javascript:void(0)" title="Công việc trong bước" class="quick_action" ng-click="dsp_all_task(no_chain_step[$index], true)">
                                                    <img src="<?php echo SITE_ROOT; ?>public/images/config.png" width="24px"/>
                                                </a>
                                                <a href="javascript:void(0)" title="Hiệu chỉnh thông tin bước" class="quick_action" ng-click="dsp_step_details($index, 'no_chain_step')">
                                                    <img src="<?php echo SITE_ROOT; ?>public/images/edit-32x32.png" width="24px"/>
                                                </a>
                                                <a href="javascript:void(0)" title="Xoá bước" class="quick_action" ng-click="delete_no_chain_step(no_chain_step[$index])">
                                                    <img src="<?php echo SITE_ROOT; ?>public/images/delete-24x24.png" width="24px"/>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="step-info">
                                        - Tổng số ngày quy định: <label>{{no_chain_step[$index]['@attributes']['time']}}</label>
                                        <br/>- Bộ phận thực hiện: <label>{{no_chain_step[$index]['@attributes']['group']}}</label>
                                    </div>
                                </li>
                            </div>
                        </div>
                        <div style="width:{{step_width}}px;text-align:center;">
                            <img  ng-show="not_last_step($index)" height="32" width="32" src="<?php echo SITE_ROOT ?>public/images/arrow_down.png"/>
                        </div>
                    </div>
                </ul>
            </div>
            <div style="float:left;width: 200px;padding: 10px;">
                <p class="required" ng-repeat="error in submit_errors">{{error}}</p>
            </div>
            <div class="clear"></div>
            <h4></h4>
            <div style="float:right" class="alert alert-info">
                <input type="button" class="button save" ng-click="submit_workflow()" value="Ghi lại" />
                <input type="button" class="button add" ng-click="add_step()" value="Thêm bước" />
                <input type="button" class="button close" value="Quay lại" ng-click="goback()"/>
                <img width="16" src="<?php echo SITE_ROOT ?>public/images/loading.gif" ng-show="submiting"/>
                <div id="response"></div>
            </div>
        </div>
    <?php endif; ?>
</form>

<?php
$this->template->display('dsp_footer.php');