<?php 
/**
// File name   : 
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

if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

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
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>/btn_update_plaintext_auto_lock_unlock" >
    <?php echo $this->hidden('hdn_record_type_code',$v_record_type_code);?>
    <?php echo $this->hidden('hdn_xml_file_path',$xml_rule_file_path);?>
    <textarea name="txt_xml_string" style="width: 616px; margin: 2px 0px; height: 462px;"><?php echo $v_xml_string;?></textarea>
    <!-- Button -->
	<div class="button-area">
		<input type="button" name="update" class="button save" 	value="<?php echo __('update'); ?> (Alt+2)"	onclick="btn_update_plaintext_auto_lock_unlock_onclick();" accesskey="2" />
		<?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
	</div>
</form>
<script>
    function btn_update_plaintext_auto_lock_unlock_onclick()
    {
        $("#frmMain").submit();
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');