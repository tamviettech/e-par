<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');
$v_count_record = count($VIEW_DATA['arr_all_record']);
$v_count_record > 0 OR DIE('ohhh');

$arr_all_record = $VIEW_DATA['arr_all_record'];
$v_alert        = 0;

if ($v_count_record > 1)
{
    foreach ($arr_all_record as $record)
    {
        $dom_processing = simplexml_load_string($record['C_XML_PROCESSING']);
        if (xpath($dom_processing, "//next_task/@fee", XPATH_STRING))
        {
            $v_alert = 1;
            break;
        }
    }
}



$arr_single_task_info = $VIEW_DATA['arr_single_task_info'];
$v_record_id_list     = $VIEW_DATA['record_id_list'];

$v_record_type_code = $arr_all_record[0]['C_RECORD_TYPE_CODE'];
$v_record_type_name = $arr_all_record[0]['C_RECORD_TYPE_NAME'];

// = $arr_single_task_info[''];
//$v_group_name       = $arr_single_task_info['C_GROUP_NAME'];
$arr_all_next_user = $VIEW_DATA['arr_all_next_user'];

//display header
$this->template->title = 'Xét duyệt hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

//Lựa chọn mặc định theo đề xuất của Cán bộ chuyên môn
$v_promote         = '';
$v_reason          = '';
$v_fee             = '';
$v_fee_description = '';

//Tên phòng ban đang thực hiện xét duyệt hồ sơ
$v_approving_group_name = get_xml_value(simplexml_load_string($arr_all_record[0]['C_XML_PROCESSING']), '//next_task/@group_name');

if (count($arr_all_record) == 1)
{
    $v_xml_processing = $arr_all_record[0]['C_XML_PROCESSING'];
    $dom_processing   = simplexml_load_string($v_xml_processing);

    $r         = $dom_processing->xpath("//next_task/@promote");
    $v_promote = isset($r[0]) ? strtoupper($r[0]) : '';

    $r        = $dom_processing->xpath("//next_task/@reason");
    $v_reason = isset($r[0]) ? $r[0] : '';

    if ($v_promote == '')
    {
        $r = $dom_processing->xpath("//next_task/@fee");
        if (!isset($r[0]))
        {
            //Tính phí, lệ phí theo mặc định
            $v_xml_workflow    = $this->get_xml_config($v_record_type_code, 'workflow');
            $dom_flow          = simplexml_load_file($v_xml_workflow);
            $r                 = $dom_flow->xpath("//process/@fee");
            $v_fee             = isset($r[0]) ? $r[0] : 0;
            $v_fee_description = ($v_fee != 0) ? 'Theo quy định hiện hành' : 'Thủ tục không thu phí';
        }
        else
        {
            $v_fee             = strtoupper($r[0]);
            $r                 = $dom_processing->xpath("//next_task/@fee_description");
            $v_fee_description = isset($r[0]) ? ($r[0]) : '';
        }
    }
}
if ($v_promote == '')
{
    $v_promote = _CONST_RECORD_APPROVAL_ACCEPT;
}
?>
<form name="frmMain" class="div-slimscroll" method="post" id="frmMain" action="<?php echo $this->get_controller_url(); ?>do_approval_record">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');
    echo $this->hidden('hdn_alert', $v_alert);

    echo $this->hidden('pop_win', $v_pop_win);
    echo $this->hidden('hdn_item_id_list', $v_record_id_list);

    //Ma Loai HS
    echo $this->hidden('hdn_record_type_code', $v_record_type_code);
    echo $this->hidden('hdn_record_type_name', $v_record_type_name);

    //KQ thu ly
    echo $this->hidden('hdn_approval_value', $v_promote);
    echo $this->hidden('hdn_approving_group_name', $v_approving_group_name);

    //Xet duyet binh thuong theo quy trinh hay XET_DUYET_BO_SUNG???
    if (isset($VIEW_DATA['v_is_approve_supplement_record']))
    {
        echo $this->hidden('hdn_is_approve_supplement_record', $v_is_approve_supplement_record);
    }
    ?>

    <div class="widget-head blue">
        <h3>Danh sách hồ xét duyệt</h3>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label for="Loại hồ sơ: ">Loại hồ sơ: </label>
        </div>
        <div class="right-Col" id="record_type">
            <?php echo $v_record_type_code; ?> - <?php echo $v_record_type_name; ?>
        </div>
    </div>
    <!-- Record list -->
    <div id="record_list">
        <table style="width: 100%;" class="adminlist table table-bordered table-striped thead">
            <colgroup>
                <col width="5%">
                <col width="*">
                <col width="15%">
                <col width="20%">
                <col width="15%">
                <col width="15%">
            </colgroup>
            <thead>
                <th>STT</th>
                <th>Mã hồ sơ</th>
                <th>Người đăng ký</th>
                <th>Ngày tiếp nhận</th>
                <th>Ngày hẹn trả</th>
                <th>Cán bộ thụ lý</th>
            </thead>
        </table>
        <div id="box-slimscroll" class="dsp_approval_record">
            <table style="width: 100%;" class="adminlist table table-bordered table-striped">
                <colgroup>
                    <col width="5%">
                    <col width="*">
                    <col width="15%">
                    <col width="20%">
                    <col width="15%">
                    <col width="15%">
                </colgroup>
                <?php for ($i = 0; $i < count($arr_all_record); $i++): ?>
                    <tr id="tr_<?php echo $i; ?>" class="tr_data">
                        <td class="right"><?php echo ($i + 1); ?></td>
                        <td id="td_record_no_<?php echo $i; ?>"><?php echo $arr_all_record[$i]['C_RECORD_NO']; ?></td>
                        <td id="td_citizen_name_<?php echo $i; ?>"><?php echo $arr_all_record[$i]['C_CITIZEN_NAME']; ?></td>
                        <td id="td_receive_date_<?php echo $i; ?>"><?php echo $this->break_date_string(jwDate::yyyymmdd_to_ddmmyyyy($arr_all_record[$i]['C_RECEIVE_DATE'], TRUE)); ?></td>
                        <td id="td_return_date_<?php echo $i; ?>"><?php echo $this->break_date_string($this->return_date_by_text($arr_all_record[$i]['C_RETURN_DATE'])); ?></td>
                        <td id="td_user_exec_<?php echo $i; ?>"><?php echo get_xml_value(simplexml_load_string($arr_all_record[$i]['C_XML_PROCESSING']), "//step[contains(@code,'::THU_LY')][last()]/user_name"); ?></td>
                    </tr>
                <?php endfor; ?>
            </table>
        </div>
    </div>
    <div class="clear" style="height: 10px"></div>
    <!-- End: Record list -->
    <div class="widget-head bondi-blue">
        <h3>Xét duyệt hồ sơ</h3>
    </div>
    <div class="Row">
        <div class="left-Col">
            Kết quả:
        </div>
        <div class="right-Col">
            <label for="rad_<?php echo _CONST_RECORD_APPROVAL_ACCEPT; ?>" class="checkbox inline" style="padding-left: 0px">
            <input type="radio" name="rad_approval" id="rad_<?php echo _CONST_RECORD_APPROVAL_ACCEPT; ?>"
                   value="<?php echo _CONST_RECORD_APPROVAL_ACCEPT; ?>"
                   onclick="rad_approval_onclick(this.value)"
                   <?php echo ($v_promote == _CONST_RECORD_APPROVAL_ACCEPT) ? ' checked' : ''; ?>
                   />
            Phê duyệt hồ sơ
            </label>

            
            <label for="rad_<?php echo _CONST_RECORD_APPROVAL_REEXEC; ?>" class="checkbox inline approval-reexec">
            <input type="radio" name="rad_approval" id="rad_<?php echo _CONST_RECORD_APPROVAL_REEXEC; ?>"
                   value="<?php echo _CONST_RECORD_APPROVAL_REEXEC; ?>"
                   onclick="rad_approval_onclick(this.value)"
                   />
            Yêu cầu thụ lý lại</label>

            
            <label for="rad_<?php echo _CONST_RECORD_APPROVAL_SUPPLEMENT; ?>" class="checkbox inline approval-supplement">
            <input type="radio" name="rad_approval" id="rad_<?php echo _CONST_RECORD_APPROVAL_SUPPLEMENT; ?>"
                   value="<?php echo _CONST_RECORD_APPROVAL_SUPPLEMENT; ?>"
                   onclick="rad_approval_onclick(this.value)"
                   <?php echo ($v_promote == _CONST_RECORD_APPROVAL_SUPPLEMENT) ? ' checked' : ''; ?>
                   />
            Yêu cầu bổ sung hồ sơ</label>

            
            <label for="rad_<?php echo _CONST_RECORD_APPROVAL_REJECT; ?>" class="checkbox inline approval-reject">
            <input type="radio" name="rad_approval" id="rad_<?php echo _CONST_RECORD_APPROVAL_REJECT; ?>"
                   value="<?php echo _CONST_RECORD_APPROVAL_REJECT; ?>"
                   onclick="rad_approval_onclick(this.value)"
                   <?php echo ($v_promote == _CONST_RECORD_APPROVAL_REJECT) ? ' checked' : ''; ?>
                   />
            Từ chối hồ sơ</label>
        </div>
    </div>
    <div class="clear" style="height: 10px;"></div>
    <div id="divFee" class="Row">
        <?php if (intval($v_fee) > 0): ?>
            <div class="Row">
                <div class="left-Col">
                    Phí<span class="required">(*)</span>:
                </div>
                <div class="right-Col">
                    <input type="text" name="txt_fee" id="txt_fee"
                           size="8" maxlength="10" value="<?php echo $v_fee; ?>"/> (đ)
                </div>
            </div>
            <div class="Row">
                <div class="left-Col">
                    <span id="spanLyDo">Diễn giải <span class="required">(*)</span>:</span> 
                </div>
                <div class="right-Col">
                    <textarea style="width:100%;height:100px;" rows="2"
                              name="txt_fee_description" id="txt_fee_description" cols="20" maxlength="400"
                              class="span12"><?php echo $v_fee_description; ?></textarea>
                </div>
            </div>
        <?php endif; ?>
        <?php $v_next_task_code = $arr_all_next_user[0]['C_TASK_CODE']; ?>
        <?php if (count($arr_all_next_user) > 1): ?>
            <div id="divLead" class="Row" <?php echo ($v_promote != _CONST_RECORD_APPROVAL_ACCEPT) ? ' style="display: none;"' : ''; ?>>
                <div class="left-Col">
                    <?php if (preg_match('/' . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . '$/', $v_next_task_code)): ?>
                        <label>Cán bộ thu phí <span class="required">(*)</span>:</label>
                    <?php elseif (preg_match('/' . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . '$/', $v_next_task_code)): ?>
                        <label>Cán bộ trả kết quả <span class="required">(*)</span>:</label>
                    <?php elseif (preg_match('/' . _CONST_XML_RTT_DELIM . _CONST_THU_LY_ROLE . '$/', $v_next_task_code)): ?>
                        <label>Cán bộ thụ lý <span class="required">(*)</span>:</label>
                    <?php elseif (preg_match('/' . _CONST_XML_RTT_DELIM . _CONST_NOP_HO_SO_SANG_CHI_CUC_THUE_ROLE . '$/', $v_next_task_code)): ?>
                        <label>Cán bộ thuế <span class="required">(*)</span>:</label>
                    <?php else: ?>
                        <label>Chuyển hồ sơ đến <span class="required">(*)</span>:</label>
                    <?php endif; ?>
                </div>
                <div class="right-Col">
                    <?php for ($i = 0; $i < count($arr_all_next_user); $i++): ?>
                        <label for="rad_signer_<?php echo $i; ?>">
                            <input type="radio" value="<?php echo $arr_all_next_user[$i]['C_USER_LOGIN_NAME']; ?>"
                            id="rad_signer_<?php echo $i; ?>" name="rad_signer"
                            <?php echo ($i == 0) ? ' checked' : ''; ?> />
                            <?php echo $arr_all_next_user[$i]['C_NAME']; ?> <i>(<?php echo $arr_all_next_user[$i]['C_JOB_TITLE']; ?>)</i>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (preg_match('/' . _CONST_XML_RTT_DELIM . _CONST_THU_PHI_ROLE . '$/', $v_next_task_code) OR preg_match('/' . _CONST_XML_RTT_DELIM . _CONST_TRA_KET_QUA_ROLE . '$/', $v_next_task_code)):
            ?>
            <div class="Row">
                <div class="left-Col">&nbsp;</div>
                <div class="right-Col">
                    <input type="button" class="btn" value="In phiếu bàn giao" name="btn_print_record_list_to_handover_back" onclick="btn_print_record_list_to_handover_back_onclick();" />
                </div>
            </div>
        <?php else: ?>
            <!--
                <div class="Row">
                    <div class="left-Col">&nbsp;</div>
                    <div class="right-Col">
                        <input type="button" class="solid print" value="In danh sách ký duyệt" name="btn_print_record_list_to_sign"onclick="btn_print_record_list_to_sign_onclick();" />
                    </div>
                </div>
            -->
        <?php endif; ?>
    </div>

    <div id="divNote" class="Row" <?php echo ($v_promote == _CONST_RECORD_APPROVAL_ACCEPT) ? ' style="display: none;"' : ''; ?>>
        <div class="left-Col">
            <span id="spanLyDo">Lý do <span class="required">(*)</span>:</span> 
        </div>
        <div class="right-Col">
            <textarea style="width:100%;height:100px;" rows="3"
                      name="txt_reason" id="txt_reason" cols="20" maxlength="4000"
                      class="span12"><?php echo $v_reason; ?></textarea>
        </div>
    </div>
    <div id="print_reject" class="Row" <?php echo ($v_promote == _CONST_RECORD_APPROVAL_REJECT) ? '' : ' style="display: none;"'; ?>>
        <div class="left-Col">&nbsp;</div>
        <div class="right-Col">
            <input type="button" class="btn" value="In phiếu từ chối" name="btn_print_supplement_request" onclick="btn_print_reject_onclick();" />
        </div>
    </div>

    <!-- Sau khi bo sung -->
    <div id="divAfterSupplement" class="Row" <?php echo ($v_promote != _CONST_RECORD_APPROVAL_SUPPLEMENT) ? ' style="display: none;"' : ''; ?>>
        <div class="left-Col">
            <span id="spanLyDo">Sau khi bổ sung <span class="required">(*)</span>:
        </div>
        <div class="right-Col">
            
            <label for="rad_after_supplement_1">
            <input type="radio" name="rad_after_supplement" id="rad_after_supplement_1" value="1" checked/>
            Chuyển đến bước phê duyệt hồ sơ bổ sung</label>

            
            <label for="rad_after_supplement_0">
            <input type="radio" name="rad_after_supplement" id="rad_after_supplement_0" value="0" />
            Duyệt lại từ đầu</label>
        </div>

        <div class="left-Col">&nbsp;</div>
        <div class="right-Col">
            <input type="button" class="btn" value="In phiếu yêu cầu bổ sung" name="btn_print_supplement_request" onclick="btn_print_supplement_request_onclick();" />
        </div>
    </div>

    <div class="clear">&nbsp;</div>
    <!-- Buttons -->
    <div class="button-area">
        <hr/>
        <button type="button" name="btn_do_approval" class="btn btn-primary" onclick="btn_do_approval_onclick();" accesskey="2">
            <i class="icon-save"></i>
            <?php echo __('update'); ?>
        </button>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};'; ?>
        <button type="button" name="cancel" class="btn" onclick="<?php echo $v_back_action;?>" >
            <i class="icon-remove"></i>
            <?php echo __('close window'); ?>
        </button> 
    </div>
</form>
<script>
                       function rad_approval_onclick(approval_value)
                       {
                           $("#hdn_approval_value").val(approval_value);
                           if (approval_value == '<?php echo _CONST_RECORD_APPROVAL_ACCEPT; ?>')
                           {
                               $("#divNote").css('display', 'none');
                               $("#divFee").css('display', 'table');
                               $("#divLead").css('display', 'table');
                           }
                           else
                           {
                               $("#divNote").css('display', 'table');
                               $("#divFee").css('display', 'none');
                               $("#divLead").css('display', 'none');
                               document.frmMain.txt_reason.focus();
                           }

                           if (approval_value == '<?php echo _CONST_RECORD_APPROVAL_SUPPLEMENT; ?>')
                           {
                               $("#divAfterSupplement").css('display', 'table');
                           }
                           else
                           {
                               $("#divAfterSupplement").css('display', 'none');
                           }
                           if (approval_value == '<?php echo _CONST_RECORD_APPROVAL_REJECT; ?>')
                           {
                               $("#print_reject").css('display', 'table');
                           }
                           else
                           {
                               $("#print_reject").css('display', 'none');
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

                       function btn_print_supplement_request_onclick()
                       {
                           if (trim($("#txt_reason").val()) == "")
                           {
                               alert('Lý do không được bỏ trống!');
                               $("#txt_reason").focus();
                               return;
                           }

                           var url = '<?php echo $this->get_controller_url(); ?>dsp_print_supplement_request/<?php echo $v_record_id_list; ?>';
                           url += '/?record_type_code=' + $("#record_type_code").val();
                           url += '&pop_win=1';
                           showPopWin(url, 800, 450, null, true);
                       }

                       function btn_print_reject_onclick()
                       {
                           if (trim($("#txt_reason").val()) == "")
                           {
                               alert('Lý do không được bỏ trống!');
                               $("#txt_reason").focus();
                               return;
                           }

                           var url = '<?php echo $this->get_controller_url(); ?>dsp_print_reject_record/<?php echo $v_record_id_list; ?>/';
                           url += QS + 'record_type_code=' + $("#record_type_code").val();
                           url += '&pop_win=1';
                           showPopWin(url, 800, 450, null, true);
                       }

                       function btn_print_record_list_to_sign_onclick()
                       {
                           v_record_id_list = $("#hdn_item_id_list").val();
                           v_record_type_code = $("#hdn_record_type_code").val();
                           v_record_type_name = $("#hdn_record_type_name").val();

                           if (v_record_id_list != '')
                           {
                               var url = '<?php echo $this->get_controller_url(); ?>dsp_print_record_list_to_sign/';
                               url += QS + 'record_id_list=' + v_record_id_list;
                               url += '&record_type_code=' + v_record_type_code;
                               url += '&record_type_name=' + v_record_type_name;

                               showPopWin(url, 800, 450, null, true);
                           }
                       }

                       //In bien ban ban giao ve cho Mot Cua
                       function btn_print_record_list_to_handover_back_onclick()
                       {
                           v_record_id_list = $("#hdn_item_id_list").val();
                           v_record_type_code = $("#hdn_record_type_code").val();
                           v_record_type_name = $("#hdn_record_type_name").val();

                           if (v_record_id_list != '')
                           {
                               var url = '<?php echo $this->get_controller_url(); ?>dsp_print_record_list_to_handover_back/';
                               url += '&record_id_list=' + v_record_id_list;
                               url += '&record_type_code=' + v_record_type_code;
                               url += '&record_type_name=' + v_record_type_name;

                               showPopWin(url, 800, 450, null, true);
                           }
                       }

                       $(document).ready(function() {
                           if ($('#hdn_alert').val() === '1') {
                               alert('Đề nghị xét duyệt từng hồ sơ đối với trường hợp phải thu phí!');
                           }
                       });
</script>
<?php
$this->template->display('dsp_footer' . $v_pop_win . '.php');