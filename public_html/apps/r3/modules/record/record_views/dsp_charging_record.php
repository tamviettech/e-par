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

$arr_single_record         = $VIEW_DATA['arr_single_record'];

$v_record_id        = $arr_single_record['PK_RECORD'];
$v_record_type_code = $arr_single_record['C_RECORD_TYPE_CODE'];
$v_record_type_name = $arr_single_record['C_RECORD_TYPE_NAME'];
$v_citizen_name     = $arr_single_record['C_CITIZEN_NAME'];
$v_record_no        = $arr_single_record['C_RECORD_NO'];

$arr_all_next_user = $VIEW_DATA['arr_all_next_user'];

//display header
$this->template->title = 'Thu phí, lệ phí';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');

//Da tam thu
$v_advance_cost = $arr_single_record['C_ADVANCE_COST'];

$v_advance_cost = str_replace('.', '', $v_advance_cost);
$v_advance_cost = str_replace(',', '', $v_advance_cost);

//Lấy phí, lệ phí theo đề nghị của phòng chuyên môn
$dom_processing = simplexml_load_string($arr_single_record['C_XML_PROCESSING']);
$r = $dom_processing->xpath("//next_task/@fee");
$v_default_fee = isset($r[0]) ? $r[0] : 0;
if ($v_default_fee == '')
{
	$v_default_fee = 0;
}

$r = $dom_processing->xpath("//next_task/@fee_description");
$v_default_fee_description = isset($r[0]) ? $r[0] : '';

/*
//Tính phí, lệ phí theo mặc định
$v_xml_workflow = $this->get_xml_config($v_record_type_code, 'workflow');
$dom_flow = simplexml_load_file($v_xml_workflow);
$r = $dom_flow->xpath("//process/@fee");
$v_default_fee = $r[0];
$v_default_fee_description = 'Theo quy định hiện hành';
*/

$v_default_fee = str_replace(',', '', $v_default_fee);
$v_default_fee = str_replace('.', '', $v_default_fee);

?>
<form name="frmMain" method="post" id="frmMain" action="<?php echo $this->get_controller_url();?>do_charging_record">
    <?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
    echo $this->hidden('hdn_update_method', 'update_record');
    echo $this->hidden('hdn_delete_method', 'delete_record');

    echo $this->hidden('pop_win', $v_pop_win);
    echo $this->hidden('hdn_item_id', $v_record_id);

    //Ma Loai HS
    echo $this->hidden('hdn_record_type_code', $v_record_type_code);
    //Phi da tam thu
    echo $this->hidden('hdn_advance_cost', $v_advance_cost);

    ?>
    <div class="Row">
        <div class="left-Col">Loại hồ sơ:</div>
        <div class="right-Col"><?php echo $v_record_type_code;?> - <?php echo $v_record_type_name;?></div>
    </div>
    <div class="Row">
        <div class="left-Col">Tên người đăng ký</div>
        <div class="right-Col"><?php echo $v_citizen_name;?></div>
    </div>
    <div class="Row">
        <div class="left-Col">Mã hồ sơ</div>
        <div class="right-Col"><?php echo $v_record_no;?></div>
    </div>
    <div class="Row">
        <div class="left-Col">Lệ phí</div>
        <div class="right-Col">
            <input type="text" name="txt_fee" id="txt_fee"
                       size="8" maxlength="10" class="text ui-widget-content ui-corner-all"
                       value="<?php echo number_format($v_default_fee);?>"/> (đ)
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">Diễn giải</div>
        <div class="right-Col">
            <textarea style="width:100%;height:100px;" rows="1"
                    name="txt_fee_description" id="txt_fee_description" cols="20" maxlength="400"
                    class="text ui-widget-content ui-corner-all"><?php echo $v_default_fee_description;?></textarea>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">Đã tạm thu</div>
        <div class="right-Col"><?php echo number_format($v_advance_cost);?>
    </div>
    <div class="Row">
        <div class="left-Col">Phải nộp</div>
        <div class="right-Col"><?php echo number_format($v_default_fee - $v_advance_cost);?></div>
    </div>

    <div id="next_user">
        <?php if (count($arr_all_next_user) > 0): ?>
        <div class="Row">
            <div class="left-Col">Cán bộ <?php echo $this->role_text[get_role($arr_all_next_user[0]['C_TASK_CODE'])];?></div>
            <div class="right-Col">
                <ul id="signer">
                    <?php for ($i=0; $i<count($arr_all_next_user); $i++): ?>
                        <li>
                            <input type="radio" value="<?php echo $arr_all_next_user[$i]['C_USER_LOGIN_NAME'];?>"
                               id="rad_next_user_<?php echo $i;?>" name="rad_next_user"
                               <?php echo ($i==0) ? ' checked' : '';?> />
                            <label for="rad_next_user_<?php echo $i;?>">
                                <?php echo $arr_all_next_user[$i]['C_NAME'];?> <i>(<?php echo $arr_all_next_user[$i]['C_JOB_TITLE'];?>)</i>
                            </label>
                        </li>
                    <?php endfor;?>
                </ul>
            </div>
        </div>
        <?php endif;?>
    </div>
    <!-- Buttons -->
    <div class="button-area">
        <hr/>
        <input type="button" name="btn_do_exec" class="button save" value="Thu phí" onclick="btn_do_charging_onclick();" />
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('close window'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>
</form>
<script>
    function btn_do_charging_onclick()
    {
        document.frmMain.submit();
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');