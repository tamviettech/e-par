<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');
count($VIEW_DATA['arr_all_record']) > 0 OR DIE();

$arr_all_record       = $VIEW_DATA['arr_all_record'];
$arr_single_task_info = $VIEW_DATA['arr_single_task_info'];
$v_record_id_list     = $VIEW_DATA['record_id_list'];

$v_record_type_code = $arr_single_task_info['C_RECORD_TYPE_CODE'];
$v_record_type_name = $arr_single_task_info['C_RECORD_TYPE_NAME'];
$v_group_name       = $arr_single_task_info['C_GROUP_NAME'];

//Step name ??
$v_task_code = $arr_all_record[0]['C_NEXT_TASK_CODE'];
$role        = get_role($v_task_code);
if ($role == _CONST_YEU_CAU_THU_LY_LAI_ROLE)
{
    $v_task_code = str_replace(_CONST_XML_RTT_DELIM . _CONST_YEU_CAU_THU_LY_LAI_ROLE, _CONST_XML_RTT_DELIM . _CONST_THU_LY_ROLE, $v_task_code);
}
$dom_workflow = simplexml_load_file($this->get_xml_config($v_record_type_code, 'workflow'));
$v_step_name  = $dom_workflow->xpath("//step[task[@code='$v_task_code']]/@name");
$v_step_name  = $v_step_name[0];

//display header
$this->template->title = 'Hoàn thành thụ lý hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

//Tính phí, lệ phí theo mặc định
$v_xml_workflow            = $this->get_xml_config($v_record_type_code, 'workflow');
$dom_flow                  = simplexml_load_file($v_xml_workflow);
//$r = $dom_flow->xpath("//process/@fee");
$v_default_fee             = 0;
$v_default_fee_description = 'Phí giải quyết hồ sơ';
?>
<form name="frmMain" method="post" class="div-slimscroll" id="frmMain" action="<?php echo $this->get_controller_url(); ?>do_exec_record_by_village">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');

    echo $this->hidden('pop_win', $v_pop_win);
    echo $this->hidden('hdn_item_id_list', $v_record_id_list);

    //Ma Loai HS
    echo $this->hidden('hdn_record_type_code', $v_record_type_code);

    //KQ thu ly
    echo $this->hidden('hdn_exec_value', _CONST_RECORD_APPROVAL_ACCEPT);
    ?>
    <div class="primary-head">
        <h3 class="page-header" style="margin-top: 0;padding-top: 0">
            <?php echo $v_step_name; ?>
        </h3>
    </div>
    
    <div class="widget-head blue">
        <h3>Danh sách hồ sơ thụ lý</h3>
    </div>
    <div class="clear" style="height: 10px">&nbsp;</div>
    <div class="Row">
        <div class="left-Col">
            <label for="Loại hồ sơ: ">Loại hồ sơ: </label>
        </div>
        <div class="right-Col">
            <?php echo $v_record_type_code; ?> - <?php echo $v_record_type_name; ?>
        </div>
    </div>

    <!-- Record list -->
    <table class="adminlist table table-bordered table-striped">
        <colgroup>
                <col width="5%">
                <col width="*">
                <col width="20%">
                <col width="20%">
                <col width="20%">
        </colgroup>
        <tr>
            <th>STT</th>
            <th>Mã hồ sơ</th>
            <th>Người đăng ký</th>
            <th>Ngày nhận</th>
            <th>Hẹn trả công dân</th>
        </tr>
    </table>
    <div id="box-slimscroll" class="dsp_exec_record_by_village">
        <table class="adminlist table table-bordered table-striped thead">
            <colgroup>
                    <col width="5%">
                    <col width="*">
                    <col width="20%">
                    <col width="20%">
                    <col width="20%">
            </colgroup>
            <?php for ($i = 0; $i < count($arr_all_record); $i++): ?>
                <tr>
                    <td class="right"><?php echo ($i + 1); ?></td>
                    <td><?php echo $arr_all_record[$i]['C_RECORD_NO']; ?></td>
                    <td><?php echo $arr_all_record[$i]['C_CITIZEN_NAME']; ?></td>
                    <td><?php echo jwDate::yyyymmdd_to_ddmmyyyy($arr_all_record[$i]['C_RECEIVE_DATE'], TRUE); ?></td>
                    <td><?php echo r3_View::return_date_by_text($arr_all_record[$i]['C_RETURN_DATE']); ?></td>
                </tr>
            <?php endfor; ?>
        </table>
    </div>
    <div class="clear" style="height: 10px">&nbsp;</div>
    <!-- End: Record list -->
    
    <div class="widget-head blue">
        <h3>Thụ lý hồ sơ</h3>
    </div>
    <div class="clear" style="height: 5px">&nbsp;</div>
    <div class="Row" style="margin-bottom: 0">
        <div class="left-Col">
            Kết quả:
        </div>
        <div class="right-Col">
            <label >
                <label class="checkbox inline">
                    <input type="radio" name="rad_exec" id="rad_<?php echo _CONST_RECORD_APPROVAL_ACCEPT; ?>"
                   value="<?php echo _CONST_RECORD_APPROVAL_ACCEPT; ?>"
                   onclick="rad_exec_onclick(this.value)" checked
                   />
                    Đề nghị phê duyệt hồ sơ
                </label>

                <label class="checkbox inline">
                    <input type="radio" name="rad_exec" id="rad_<?php echo _CONST_RECORD_APPROVAL_REJECT; ?>"
                       value="<?php echo _CONST_RECORD_APPROVAL_REJECT; ?>"
                       onclick="rad_exec_onclick(this.value)"
                       />
                    Từ chối hồ sơ
                </label>
            </label>
        </div>
    </div>
    <div id="divFee" class="Row">
        <div class="Row">
            <div class="left-Col">
                Phí <span class="required">(*)</span>: 
            </div>
            <div class="right-Col">
                <input type="text" name="txt_fee" id="txt_fee"
                       size="8" maxlength="10" class="text ui-widget-content ui-corner-all"
                       value="<?php echo $v_default_fee; ?>"
                       onchange="ReadNumberToString('txt_fee', 'lbl_fee')" /> (đ)
                &nbsp;
                <label id="lbl_fee"></label>
            </div>
        </div>
        <div class="Row">
            <div class="left-Col">
                <span id="spanLyDo">Diễn giải <span class="required">(*)</span>:
            </div>
            <div class="right-Col">
                <textarea style="width:100%;height:78px;" rows="2"
                          name="txt_fee_description" id="txt_fee_description" cols="20" maxlength="400"
                          class="text ui-widget-content ui-corner-all"><?php echo $v_default_fee_description; ?></textarea>
            </div>
        </div>
        <?php if(!empty($arr_all_next_user)):?>
        <div class="Row">
            <div class="left-Col">
                Phê duyệt hồ sơ: 
            </div>
            <div class="right-Col">
                <?php $i=0;?>
                <?php foreach($arr_all_next_user as $arr_user):
                        $user_login_name = $arr_user['C_LOGIN_NAME'];
                        $user_name       = $arr_user['C_NAME'];
                ?>
                <label>
                    <input type="radio" name="rad_next_user" value="<?php echo $user_login_name?>" <?php echo ($i==0)?'checked':'';?>>
                    <?php echo $user_name?>
                </label>
                <?php $i++;?>
                <?php endforeach;?>
            </div>
        </div>
        <?php endif;?>
    </div>
    <div id="divNote" class="Row" style="display: none;">
        <div class="left-Col">
            <span id="spanLyDo">Lý do <span class="required">(*)</span>:
        </div>
        <div class="right-Col">
            <textarea style="width:100%;height:100px;" rows="2"
                      name="txt_reason" id="txt_reason" cols="20" maxlength="400"
                      class="text ui-widget-content ui-corner-all"></textarea>
        </div>
    </div>
    <!-- Buttons -->
     <hr/>
    <div class="button-area">
        <!--button xet duyet-->
        <button type="button" name="trash" class="btn" onclick="btn_do_exec_onclick();">
            <i class="icon-ok-sign"></i>
            Cập nhật
        </button>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        
        <!--Button close window-->
        <button type="button" name="trash" class="btn" onclick="<?php echo $v_back_action; ?>" >
            <i class="icon-remove"></i>
            <?php echo __('close window'); ?>
        </button> 
    </div>
</form>
<script>
                       function rad_exec_onclick(exec_value)
                       {
                           $("#hdn_exec_value").val(exec_value);
                           if (exec_value == '<?php echo _CONST_RECORD_APPROVAL_ACCEPT; ?>')
                           {
                               $("#divNote").css('display', 'none');
                               $("#divFee").css('display', 'table');
                           }
                           else
                           {
                               $("#divNote").css('display', 'table');
                               $("#divFee").css('display', 'none');
                               document.frmMain.txt_reason.focus();
                           }
                       }

                       function btn_do_exec_onclick()
                       {
                           var f = document.frmMain;
                           var v_exec_value = $("#hdn_exec_value").val();
                           var v_note = trim($("#txt_reason").val());

                           if (v_exec_value != '<?php echo _CONST_RECORD_APPROVAL_ACCEPT; ?>' && v_note == '')
                           {
                               alert('Lý do không được bỏ trống!');
                               f.txt_reason.focus();
                               return false;
                           }

                           f.submit();
                       }
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');