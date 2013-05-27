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

//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_all_record         = $VIEW_DATA['arr_all_record'];
//$MY_TASK                = $VIEW_DATA['MY_TASK'];

//header
$this->template->title = 'Tra cứu hồ sơ';
$this->template->display('dsp_header.php');

$v_receive_date_from    = isset($_POST['txt_receive_date_from']) ? replace_bad_char($_POST['txt_receive_date_from']) : '';
$v_receive_date_to      = isset($_POST['txt_receive_date_to']) ? replace_bad_char($_POST['txt_receive_date_to']) : '';

$v_return_date_from     = isset($_POST['txt_return_date_from']) ? replace_bad_char($_POST['txt_return_date_from']) : '';
$v_return_date_to       = isset($_POST['txt_return_date_to']) ? replace_bad_char($_POST['txt_return_date_to']) : '';

$v_record_no            = isset($_POST['txt_record_no']) ? replace_bad_char($_POST['txt_record_no']) : '';

$v_free_text            = get_post_var('txt_free_text');

?>
<form name="frmMain" id="frmMain"
	action="" method="POST">
	<?php
	echo $this->hidden('controller',$this->get_controller_url());
	echo $this->hidden('hdn_item_id','0');
	echo $this->hidden('hdn_item_id_list','');

	echo $this->hidden('hdn_dsp_single_method','dsp_single_record');
	echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
	echo $this->hidden('hdn_update_method','update_record');
	echo $this->hidden('hdn_delete_method','delete_record');
	echo $this->hidden('hdn_handover_method','do_handover_record');

	echo $this->hidden('record_type_code', $v_record_type_code);
	//echo $this->hidden('MY_TASK', $MY_TASK);

	?>
	<!-- filter -->
	<div class="Row">
		<div class="left-Col">
			<label>Mã loại hồ sơ: (Alt+1) </label>
		</div>
		<div class="right-Col">
    		<input type="text"
    			name="txt_record_type_code" id="txt_record_type_code"
    			value="<?php echo $v_record_type_code; ?>"
    			class="inputbox upper_text" size="10" maxlength="10"
    			onkeypress="txt_record_type_code_onkeypress(event);"
    			autofocus="autofocus" accesskey="1" />&nbsp;

    			<select name="sel_record_type" id="sel_record_type"
    			style="width: 76%; color: #000000;"
    			onchange="sel_record_type_onchange(this)">
    			<option value="">-- Chọn loại hồ sơ --</option>
				<?php foreach ($arr_all_record_type as $code=>$info):?>
						<?php $str_selected = ($code == strval($v_record_type_code)) ? ' selected':'';?>
						<option value="<?php echo $code;?>"<?php echo $str_selected?> data-scope="<?php echo $info['C_SCOPE'];?>"><?php echo $info['C_NAME'];?></option>
					<?php endforeach;?>
    			<?php //echo $this->generate_select_option($arr_all_record_type, $v_record_type_code); ?>
    		</select>
	    </div>
	</div>

	<div class="Row">
        <div class="left-Col">
            <label for="NgayNhapHoSo">
                Ngày tiếp nhận:
            </label>
        </div>
        <div class="right-Col">
            <div class="left-Col2">
                <div class="left-item-col" style="font-weight: normal">
                    Từ ngày:
                </div>
                <div class="right-item-col">
                    <input class="text" id="txt_receive_date_from" maxlength="100" name="txt_receive_date_from" style="width:70%" type="text" value="<?php echo $v_receive_date_from;?>" />
                    <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT?>public/images/calendar.gif" onclick="DoCal('txt_receive_date_from')" />
                </div>
            </div>
            <div class="right-Col2">
                <div class="left-item-col" style="font-weight: normal">
                    Đến ngày:
                </div>
                <div class="right-item-col">
                    <input class="text" id="txt_receive_date_to" maxlength="100" name="txt_receive_date_to" style="width:70%" type="text" value="<?php echo $v_receive_date_to;?>" />
                    <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT?>public/images/calendar.gif" onclick="DoCal('txt_receive_date_to')" />
                </div>
            </div>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <label>
                Ngày hẹn trả:
            </label>
        </div>
        <div class="right-Col">
            <div class="left-Col2">
                <div class="left-item-col" style="font-weight: normal">
                    Từ ngày:
                </div>
                <div class="right-item-col">
                    <input class="text" id="txt_return_date_from" maxlength="100" name="txt_return_date_from" style="width:70%" type="text" value="<?php echo $v_return_date_from;?>" />
                    <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT?>public/images/calendar.gif" onclick="DoCal('txt_return_date_from')" />
                </div>
            </div>
            <div class="right-Col2">
                <div class="left-item-col" style="font-weight: normal">
                    Đến ngày:
                </div>
                <div class="right-item-col">
                    <input class="text" id="txt_return_date_to" maxlength="100" name="txt_return_date_to" style="width:70%" type="text" value="<?php echo $v_return_date_to;?>" />
                    <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT?>public/images/calendar.gif" onclick="DoCal('txt_return_date_to')" />
                </div>
            </div>
        </div>
    </div>
    <div class="Row">
		<div class="left-Col">
			<label> Mã hồ sơ: </label>
		</div>
		<div class="right-Col">
			<input type="text" style="width: 250px" name="txt_record_no"
				id="txt_record_no" maxlength="100"
				value="<?php echo $v_record_no;?>" />
		</div>
	</div>
    <div class="Row">
        <div class="left-Col">
            <label>
                Thông tin khác
            </label>
        </div>
        <div class="right-Col">
            <input class="text" id="txt_free_text" maxlength="100" name="txt_free_text" style="width:250px" type="text" value="<?php echo $v_free_text;?>" />
            <input type="button" name="btn_search" value="Tìm kiếm" class="solid search" onclick="this.form.submit()" />
        </div>
    </div>

	<div class="clear">&nbsp;</div>

	<div id="procedure">
		<?php
		if ($this->load_abs_xml($this->get_xml_config($v_record_type_code, 'lookup')))
		{
			echo $this->render_form_display_all_record($arr_all_record, FALSE);
		}
		?>
	</div>
	<div><?php echo $this->paging2($arr_all_record);?></div>

	<!-- Context menu -->
	<ul id="myMenu" class="contextMenu">
		<li class="statistics"><a href="#statistics">Xem tiến độ</a></li>
	</ul>
</form>
<script>
$(document).ready( function() {
    //Show context on each row
    $(".adminlist tr[role='presentation']").contextMenu({
        menu: 'myMenu'
    }, function(action, el, pos) {
        v_record_id = $(el).attr('data-item_id');
        switch (action){
            case 'statistics':
            	dsp_single_record_statistics(v_record_id);
                break;
        }
    });

    //Quick action
    <?php if (strtoupper($this->active_role) == _CONST_TRA_CUU_ROLE): ?>
        $('.adminlist tr[role="presentation"] td[role="action"] .quick_action').each(function(index) {
            v_item_id =   $(this).attr('data-item_id');

            html = '';

            //Thong tin tien do
            html += '<a href="javascript:void(0)" onclick="dsp_single_record_statistics(\'' + v_item_id + '\');" class="quick_action" >';
            html += '<img src="' + SITE_ROOT + 'public/images/statistics-16x16.png" title="Xem tiến độ" /></a>';

            $(this).html(html);
        });

    <?php endif;?>
});
</script>
<?php $this->template->display('dsp_footer.php');