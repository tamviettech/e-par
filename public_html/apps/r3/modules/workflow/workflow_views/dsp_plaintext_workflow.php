<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//display header
$this->template->title = 'Cập nhật quy trình';
$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_record_type_code = get_request_var('record_type_code');
$xml_flow_file_path = $this->get_xml_config($v_record_type_code, 'workflow',0);

if (is_file($xml_flow_file_path))
{
    $v_xml_string = file_get_contents($xml_flow_file_path);
}
else
{
    $v_xml_string = '';
}
?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>/update_plaintext_workflow" >
    <?php echo $this->hidden('hdn_record_type_code',$v_record_type_code);?>
    <?php echo $this->hidden('hdn_xml_flow_file_path',$xml_flow_file_path);?>
    <textarea name="txt_plaintext_workflow" style="width: 610px; margin: 2px 0px; height: 460px;"><?php echo $v_xml_string;?></textarea>
    <!-- Button -->
	<div class="button-area">
		<input type="button" name="update" class="button save" 	value="<?php echo __('update'); ?> (Alt+2)"	onclick="btn_update_plaintext_workflow_onclick();" accesskey="2" />
		<?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
	</div>
</form>
<script>
    function btn_update_plaintext_workflow_onclick()
    {
        if (confirm('Cập nhật quy trình sẽ xoá hết thông tin phân công hiện tại.\n Bạn có chắc chắn thay đổi quy trình không?'))
		{
            $("#frmMain").submit();
		}
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');