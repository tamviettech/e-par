<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//display header
$this->template->title = 'Cập nhật luật';
$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$xml_rule_file_path = $this->get_xml_config('', 'auto_lock_unlock',0);

if (is_file($xml_rule_file_path))
{
    $v_xml_string = file_get_contents($xml_rule_file_path);
}
else
{
    $v_xml_string = '';
}
?>
<div class="container-fluid">
    <form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>/btn_update_plaintext_auto_lock_unlock" class="form-horizontal">
        <?php echo $this->hidden('hdn_record_type_code',$v_record_type_code);?>
        <?php echo $this->hidden('hdn_xml_file_path',$xml_rule_file_path);?>
        <textarea name="txt_xml_string" style="width: 616px; margin: 2px 0px; height: 462px;"><?php echo $v_xml_string;?></textarea>
        <!-- Button -->
        <div class="form-actions">
            <button type="button" name="update" class="btn btn-primary" onclick="btn_addnew_onclick();"><i class="icon-save"></i><?php echo __('update');?></button>
            <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
            <button type="button" class="btn" onclick="<?php echo $v_back_action;?>;"><i class="icon-reply"></i><?php echo __('close window');?></button>
        </div>
    </form>
</div>
<script>
    function btn_update_plaintext_auto_lock_unlock_onclick()
    {
        $("#frmMain").submit();
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');