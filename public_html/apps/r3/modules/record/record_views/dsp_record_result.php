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
$this->template->title = 'Kết quả hồ sơ';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
?>
<form name="frmMain" id="frmMain" action="<?php echo $this->get_controller_url();?>do_return_record" method="POST">
	<?php
	echo $this->hidden('controller',$this->get_controller_url());
	echo $this->hidden('hdn_item_id',$v_record_id);
	echo $this->hidden('hdn_item_id_list',$v_record_id);
	echo $this->hidden('sel_record_type',$record_type);
	echo $this->hidden('hdn_update_method','do_return_record');
	echo $this->hidden('pop_win','1');

    echo $this->hidden('XmlData','');
    ?>
    <div id="record_result">
        <?php echo $this->transform($this->get_xml_config(NULL, 'result')); ?>
	</div>
    <!-- Button -->
	<div class="button-area">
		<input type="button" name="update" class="button save" value="<?php echo __('update'); ?> (Alt+2)" onclick="btn_update_onclick();" accesskey="2" />
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
	</div>
</form>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');