<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

//display header
$this->template->title = 'Cập nhật cấu trúc mẫu đơn';
$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

$v_record_type_code = trim(get_request_var('sel_record_type'));
$xml_file_path = $this->get_xml_config($v_record_type_code, 'form_struct');

if (is_file($xml_file_path))
{
    $v_xml_string = file_get_contents($xml_file_path);
}
else
{
    $v_xml_string = '';
    $xml_file_path = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . $v_record_type_code . DS . $v_record_type_code . '_' . 'form_struct' . '.xml';
}
?>
<form name="frmMain" method="post" id="frmMain" action="" >
    <?php echo $this->hidden('hdn_record_type_code',$v_record_type_code);?>
    <?php echo $this->hidden('hdn_xml_file_path',$xml_file_path);?>
    <textarea name="txt_xml_string" style="width: 954px; margin: 2px 0px; height: 466px;"><?php echo $v_xml_string;?></textarea>
    <!-- Button -->
	<div class="button-area">
		<button type="button" class="btn btn-primary" type="button" name="update" value="<?php echo __('update'); ?> (Alt+2)"	onclick="btn_update_plaintext_formstruct_onclick();" accesskey="2"><i class="icon-save"></i>Cập nhật</button>
		<button type="button" class="btn" type="button" name="gui-update" value="Sửa mẫu đơn qua giao diện"	onclick="btn_update_gui_formstruct_onclick();" accesskey="2"><i class="icon-edit"></i>Sửa mẫu đơn qua giao diện</button>
            
		<?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <button style="float: right;margin-bottom: 5px; margin-right: 10px;" type="button" name="trash" class="btn" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>">
        <i class="icon-remove"></i>Đóng cửa sổ</button>
        </div>
</form>
<script>
    function btn_update_plaintext_formstruct_onclick()
    {
        $("#frmMain").attr('action','<?php echo $this->get_controller_url();?>update_plaintext_form_struct');
        $("#frmMain").submit();
    }
    
    function btn_update_gui_formstruct_onclick()
    {
        url = '<?php echo $this->get_controller_url('form_struct');?>' + QS + 'sel_record_type=<?php echo $v_record_type_code;?>';
        url += '&pop_win=1';

        showPopWin(url, 900, 450);
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');