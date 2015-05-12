<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//display header
$this->template->title = 'Bàn giao hồ sơ Huyện từ chối';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_promote = _CONST_RECORD_APPROVAL_REJECT;
$v_reason = isset($v_reason) ? $v_reason  : '';

?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>do_handover_reject">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());
    echo $this->hidden('pop_win', $v_pop_win);
    echo $this->hidden('hdn_item_id', $v_record_id);

    //Ma Loai HS
    echo $this->hidden('hdn_record_type_code', $v_record_type_code);

    ?>
    <div class="panel_color_form">Lý do<span class="required">(*)</span>:</div>
    <div id="divNote" class="Row">
        <div class="left-Col">
        </div>
        <div class="right-Col">
            <textarea style="width:100%;height:100px;" rows="3"
            	name="txt_reason" id="txt_reason" cols="20" maxlength="4000"
            	class="text ui-widget-content ui-corner-all"><?php echo $v_reason;?></textarea>
        </div>
    </div>
    <div class="clear">&nbsp;</div>
    <!-- Buttons -->
    <div class="button-area">
        <hr/>
        <button type="button" nam="btn_do_approval" class="btn btn-primary" onclick="btn_do_approval_onclick();"><i class="icon-save"></i>Cập nhật</button>        
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <button type="button" class="btn" name="cancel" onclick="<?php echo $v_back_action;?>"><i class="icon-remove"><?php echo __('close window'); ?></i></button>
        
    </div>
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
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');