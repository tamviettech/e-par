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

//display header
$this->template->title = 'Yêu cầu bổ sung hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_promote = _CONST_RECORD_APPROVAL_SUPPLEMENT;
$v_reason  = '';

if (sizeof($arr_all_record) == 1)
{
    $v_xml_processing   = $arr_all_record[0]['C_XML_PROCESSING'];
    $dom_xml_processing = simplexml_load_string($v_xml_processing);
    $v_reason           = get_xml_value($dom_xml_processing, '//next_task/@promote');
}
?>
<form name="frmMain" method="post" class="div-slimscroll" id="frmMain" action="<?php echo $this->get_controller_url(); ?>do_supplement_request_record">
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
    echo $this->hidden('hdn_approval_value', $v_promote);
    ?>

    <div class="widget-head blue">
        <h3>Danh sách hồ sơ yêu cầu bổ sung</h3>
    </div>
    <table class="none" width="100%" cellspacing="0" cellpadding="4" border="0">
        <tbody>
            <tr>
                <td>
                    <div class="modal_record_type_name">
                        <?php echo $v_record_type_code; ?> - <?php echo $v_record_type_name; ?>
                    </div>
                </td>
                <td>

                </td>
            </tr>
        </tbody>
    </table>

    <!-- Record list -->
    <table width="100%" class="adminlist table table-bordered table-striped">
        <colgroup>
            <col width="5%">
            <col width="*">
            <col width="20%">
            <col width="20%">
            <col width="20%">
        </colgroup>
        <thead>
        <th>STT</th>
        <th>Mã hồ sơ</th>
        <th>Người đăng ký</th>
        <th>Ngày nhận</th>
        <th>Hẹn trả công dân</th>
        </thead>
    </table>
    <div id="box-slimscroll" class="dsp_supplement_request" >
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
                <?php /*
                  <tr>
                  <td style="font-weight: bold" width="15%">
                  Phân công cho:
                  </td>
                  <td>
                  <select name="sel_user">
                  <?php echo View::generate_select_option($arr_all_user) ?>
                  </select>
                  </td>
                  </tr>
                 */ ?>
                <tr>
                    <td style="font-weight: bold" width="15%">
                        Lý do: 
                    </td>
                    <td>
                        <textarea style="width:100%;height:100px;" rows="3"
                                  name="txt_reason" id="txt_reason" cols="20" maxlength="4000"
                                  class="span12"><?php echo $v_reason; ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Buttons -->
        <div class="button-area">
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
    </div><!--well-->
    <div class="clear">&nbsp;</div>

</form>
<script>

                function btn_do_approval_onclick()
                {
                    var f = document.frmMain;
                    var v_approval_value = $("#hdn_approval_value").val();
                    var v_reason = trim($("#txt_reason").val());

                    if (v_reason == '')
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
