<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

count($VIEW_DATA['arr_all_record']) > 0 OR DIE('ohhh');

$arr_all_record       = $VIEW_DATA['arr_all_record'];
$arr_single_task_info = $VIEW_DATA['arr_single_task_info'];
$v_record_id_list     = $VIEW_DATA['record_id_list'];

$v_record_type_code = $arr_single_task_info['C_RECORD_TYPE_CODE'];
$v_record_type_name = $arr_single_task_info['C_RECORD_TYPE_NAME'];
$v_group_name       = $arr_single_task_info['C_GROUP_NAME'];
$arr_all_next_user  = $VIEW_DATA['arr_all_next_user'];

//display header
$this->template->title = 'Ký duyệt hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_promote = _CONST_RECORD_APPROVAL_ACCEPT;
?>
<form style="min-height: 500px" class="div-slimscroll" name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url(); ?>do_sign_record">
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
    echo $this->hidden('sel_record_type', $v_record_type_code);

    //KQ thu ly
    echo $this->hidden('hdn_approval_value', $v_promote);
    ?>
    <div class="widget-head blue">
        <h3>Danh sách hồ sơ ký duyệt</h3>
    </div>
    <div class="modal_record_type_name">
        <?php echo $v_record_type_code; ?> - <?php echo $v_record_type_name; ?>
    </div>

    <!-- Record list -->
    <table width="100%" class="adminlist table table-bordered table-striped thead">
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
            <th>Ngày hẹn trả</th>
        </tr>
    </table>
    <div id="box-slimscroll" class="dsp_sign_record">
        <table width="100%" class="adminlist table table-bordered table-striped">
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
    <!-- End: Record list -->
    <br>
    <div class="well">
        <table class="none" width="100%" cellspacing="0" cellpadding="4" border="0">
            <tbody>
                <tr>
                    <td style="font-weight: bold" class="left-Col">
                        Xem xét ký duyệt: 
                    </td>
                    <td align="left" class="right-Col">
                        <div class="control-group">
                            <div class="controls">
                                <style>
                                    .radio
                                    {
                                        overflow: hidden;
                                        margin-left: 30px;
                                        margin-top: 10px;
                                        float: left;
                                    }

                                    .button-area {
                                        text-align: center;
                                        padding-top: 0;
                                        padding-bottom: 10px;
                                    }
                                    hr
                                    {
                                        margin: 0;
                                        margin-bottom: 5px;
                                    }
                                </style>
                                <label style="margin-left: -25px;" for="rad_<?php echo _CONST_RECORD_APPROVAL_ACCEPT; ?>" class="radio">
                                    <input type="radio" name="rad_approval" id="rad_<?php echo _CONST_RECORD_APPROVAL_ACCEPT; ?>"
                                           value="<?php echo _CONST_RECORD_APPROVAL_ACCEPT; ?>" checked="checked" onclick="rad_approval_onclick(this.value)"
                                           />
                                    Ký duyệt</label>
                                <label for="rad_<?php echo _CONST_RECORD_APPROVAL_REJECT; ?>" class="radio">
                                    <input type="radio" name="rad_approval" id="rad_<?php echo _CONST_RECORD_APPROVAL_REJECT; ?>"
                                           value="<?php echo _CONST_RECORD_APPROVAL_REJECT; ?>" onclick="rad_approval_onclick(this.value)"
                                           />
                                    Không ký duyệt</label>
                                <label for="rad_<?php echo _CONST_RECORD_APPROVAL_REEXEC; ?>" class="radio">
                                    <input type="radio" name="rad_approval" id="rad_<?php echo _CONST_RECORD_APPROVAL_REEXEC; ?>"
                                           value="<?php echo _CONST_RECORD_APPROVAL_REEXEC; ?>" onclick="rad_approval_onclick(this.value)"
                                           />
                                    Yêu cầu phòng ban trình lại</label>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div id="office" class="Row" style="display: none;">
            <div class="left-Col">
                Phòng ban trình lại <span class="required">(*)</span>:
            </div>
            <div class="right-Col">
                <?php
                //Phong ban nao tham gia thu ly HỒ SƠ nay?
                ?>
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" value="1982" onclick="CheckOne()" class="item" name="office" checked>
                        <?php echo $VIEW_DATA['submit_group_name']; ?></label>
                </div>
            </div>
        </div>

        <div id="divNote" class="Row" style="display: none;">
            <div class="left-Col">
                Lý do <span class="required">(*)</span>:
            </div>
            <div class="right-Col">
                <textarea style="width:100%;height:100px;" rows="2" name="txt_reason" maxlength="200" id="txt_reason" cols="20" class="span12"></textarea>
            </div>
        </div>

        <!-- Buttons -->
        <div class="button-area">
            <hr/>
            <button type="button" name="btn_do_approval" class="btn btn-primary" onclick="btn_do_approval_onclick();" accesskey="2">
                <i class="icon-save"></i>
                <?php echo __('update'); ?>
            </button>
            <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
            <button type="button" name="cancel" class="btn" onclick="<?php echo $v_back_action; ?>" >
                <i class="icon-remove"></i>
                <?php echo __('close window'); ?>
            </button> 
        </div>
    </div>
</form>
<script>
                function rad_approval_onclick(approval_value)
                {
                    $("#hdn_approval_value").val(approval_value);
                    if (approval_value == '<?php echo _CONST_RECORD_APPROVAL_ACCEPT; ?>')
                    {
                        $("#divNote").css('display', 'none');
                        $("#office").css('display', 'none');
                    }
                    else if (approval_value == '<?php echo _CONST_RECORD_APPROVAL_REJECT; ?>')
                    {
                        $("#divNote").css('display', 'table');
                        $("#office").css('display', 'none');
                        document.frmMain.txt_reason.focus();
                    }
                    else if (approval_value == '<?php echo _CONST_RECORD_APPROVAL_REEXEC; ?>')
                    {
                        $("#divNote").css('display', 'table');
                        $("#office").css('display', 'table');
                        document.frmMain.txt_reason.focus();
                    }
                }

                function btn_do_approval_onclick()
                {
                    var f = document.frmMain;
                    var v_approval_value = $("#hdn_approval_value").val();
                    var v_reason = trim($("#txt_reason").val());

                    if (v_approval_value != '<?php echo _CONST_RECORD_APPROVAL_ACCEPT; ?>' && v_reason == '')
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
