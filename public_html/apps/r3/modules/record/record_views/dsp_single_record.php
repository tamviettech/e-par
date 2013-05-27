<?php 

/**
// File name   : dsp_single_record.php
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

if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}

//View data
$arr_all_record_type    = $VIEW_DATA['arr_all_record_type'];
$v_record_type_code     = $VIEW_DATA['record_type_code'];
$arr_single_record      = $VIEW_DATA['arr_single_record'];
$MY_TASK                = $VIEW_DATA['MY_TASK'];

if (isset($arr_single_record['PK_RECORD']))
{
    $v_record_id            = $arr_single_record['PK_RECORD'];
    $v_record_no            = $arr_single_record['C_RECORD_NO'];
    $v_receive_date         = $arr_single_record['C_RECEIVE_DATE'];
    $v_return_phone_number  = $arr_single_record['C_RETURN_PHONE_NUMBER'];
    $v_return_email         = $arr_single_record['C_RETURN_EMAIL'];
    $v_return_date          = $arr_single_record['C_RETURN_DATE'];

    $v_xml_data             = $arr_single_record['C_XML_DATA'];

    $v_total_time           = $arr_single_record['C_TOTAL_TIME'];

    //Convert date
    $v_receive_date         = jwDate::yyyymmdd_to_ddmmyyyy($v_receive_date, TRUE);

    $v_return_date_by_text  = $this->return_date_by_text($v_return_date);
}
else
{
    $v_record_id = 0;
    $v_record_no = $v_record_type_code . '-' . strtoupper(base_convert(preg_replace('[\D]','', Date('ymdHis')), 10, 16));

    $v_receive_date = jwDate::yyyymmdd_to_ddmmyyyy($this->DATETIME_NOW, 1);
    $v_return_phone_number = '';
    $v_return_email = '';
    $v_xml_data = '';

    $v_return_date = $arr_single_record['C_RETURN_DATE'];
    $v_return_date_by_text  = $this->return_date_by_text($v_return_date);

    $v_total_time = $arr_single_record['C_TOTAL_TIME'];
}

//display header
$v_page_title = $v_record_id > 0 ? 'Cập nhật hồ sơ' : 'Tiếp nhận hồ sơ';
$this->template->title = $v_page_title;
$this->template->display('dsp_header.php');

?>
<form name="frmMain" id="frmMain" action="" method="POST"
	enctype="multipart/form-data">
	<?php
	echo $this->hidden('controller',$this->get_controller_url());
	echo $this->hidden('hdn_item_id',$v_record_id);
	echo $this->hidden('hdn_item_id_list','');

	echo $this->hidden('hdn_dsp_single_method','dsp_single_record');
	echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
	echo $this->hidden('hdn_update_method','update_record');
	echo $this->hidden('hdn_delete_method','delete_record');

	echo $this->hidden('XmlData',$v_xml_data);

	echo $this->hidden('hdn_return_date',$v_return_date);
	echo $this->hidden('hdn_total_time',$v_total_time);

	echo $this->hidden('MY_TASK', $MY_TASK);

	echo $this->hidden('hdn_deleted_file_id_list', '');
	?>

	<div class="page-title">Tiếp nhận hồ sơ</div>

	<div class="panel_color">Thông tin chung</div>
	<table style="width: 100%;" class="none-border-table">
		<tr>
			<td width="20%"><label>Mã loại hồ sơ</label> (Alt+1)</td>
			<td colspan="3"><input type="text" name="txt_record_type_code"
				id="txt_record_type_code" value="<?php echo $v_record_type_code; ?>"
				class="inputbox upper_text" size="10" maxlength="10"
				onkeypress="txt_record_type_code_onkeypress(event);"
				autofocus="autofocus" accesskey="1" data-allownull="no"
				data-validate="text" data-name="Loại hồ sơ" data-xml="no"
				data-doc="no" />&nbsp; <select name="sel_record_type"
				id="sel_record_type" style="width: 77%; color: #000000;"
				onchange="sel_record_type_onchange(this)" data-allownull="no"
				data-validate="text" data-name="Loại hồ sơ" data-xml="no"
				data-doc="no">
					<option value="">-- Chọn loại hồ sơ --</option>
					<?php foreach ($arr_all_record_type as $code=>$info):?>
						<?php $str_selected = ($code == strval($v_record_type_code)) ? ' selected':'';?>
						<option value="<?php echo $code;?>"<?php echo $str_selected?> data-scope="<?php echo $info['C_SCOPE'];?>"><?php echo $info['C_NAME'];?></option>
					<?php endforeach;?>
							
					<?php //echo $this->generate_select_option($arr_all_record_type, $v_record_type_code); ?>
			</select>
			</td>
		</tr>
		<tr>
			<td>Mã hồ sơ: <span class="required">(*)</span>
			</td>
			<td><input readonly="readonly" name="txt_record_no"
				id="txt_record_no" maxlength="50" style="width: 200px" type="text"
				value="<?php echo $v_record_no;?>" data-allownull="no"
				data-validate="text" data-name="M&atilde; h&#7891; s&#417;"
				data-xml="no" data-doc="no" />
			</td>
			<td>Số điện thoại nhận SMS:</td>
			<td><input name="txt_return_phone_number"
				id="txt_return_phone_number" maxlength="20" style="width: 200px"
				type="text" value="<?php echo $v_return_phone_number;?>"
				data-allownull="yes" data-validate="text"
				data-name="S&#7889; &#273;i&#7879;n tho&#7841;i nh&#7853;n SMS"
				data-xml="no" data-doc="no" />
			</td>
		</tr>
		<tr>
			<td>Ngày giờ tiếp nhận: <span class="required">(*)</span>
			</td>
			<td><input readonly="readonly" id="txt_receive_date"
				name="txt_receive_date" style="width: 200px" type="text"
				value="<?php echo $v_receive_date;?>" data-allownull="no"
				data-validate="text" data-name="Ngày giờ tiếp nhận" data-xml="no"
				data-doc="no" />
			</td>
			<td>Email:</td>
			<td><input name="txt_return_email" id="txt_return_email"
				maxlenght="255" style="width: 200px" type="text"
				value="<?php echo $v_return_email;?>" data-allownull="yes"
				data-validate="email" data-name="Địa chỉ email" data-xml="no"
				data-doc="no" />
			</td>
		</tr>
        <?php if ($v_total_time >= 0): ?>
            <tr>
                <td>Thời gian giải quyết:</td>
                <td><?php echo $v_total_time;?> ngày làm việc</td>
                <td>Ngày hẹn trả: <span class="required">(*)</span>
                </td>
                <td><input readonly="readonly" id="txt_return_date"
                    name="txt_return_date" style="width: 200px" type="text"
                    value="<?php echo $v_return_date_by_text;?>" data-allownull="no"
                    data-validate="text"
                    data-name="Ngày hẹn trả" data-xml="no"
                    data-doc="no" />
                </td>
            </tr>
        <?php else: ?>
            <tr>
                <td>Thời gian giải quyết:<span class="required">(*)</span></td>
                <td>
                    <input type="text" name="txt_total_time" id="txt_total_time"
                           style="width: 100px" value="<?php echo ($v_total_time > 0) ? $v_total_time : '';?>"
                           data-allownull="no" data-validate="numberString"
                           data-name="Thời gian giải quyết" data-xml="no" data-doc="no"
                           autofocus="autofocus" maxlength="3"
                           onblur="calc_return_date()"
                    /> (ngày làm việc)
                </td>
                <td>Ngày hẹn trả: <span class="required">(*)</span>
                </td>
                <td>
                    <input id="txt_return_date"
                        name="txt_return_date" style="width: 100px" type="text"
                        value="<?php echo $v_return_date_by_text;?>" data-allownull="no"
                        data-validate="text"
                        data-name="Ngày hẹn trả" data-xml="no"
                        data-doc="no" readonly
                        onchange="calc_working_days()"
                    />
                    <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_return_date')"/>
                    <select name="sel_return_date_noon" id="sel_return_date_noon" onchange="save_return_date();">
                        <option value="<?php echo _CONST_MORNING_END_WORKING_TIME;?>">Từ <?php echo _CONST_MORNING_BEGIN_WORKING_TIME;?> đến <?php echo _CONST_MORNING_END_WORKING_TIME;?></option>
                        <option value="<?php echo _CONST_AFTERNOON_END_WORKING_TIME;?>">Từ <?php echo _CONST_AFTERNOON_BEGIN_WORKING_TIME;?> đến <?php echo _CONST_AFTERNOON_END_WORKING_TIME;?></option>
                    </select>
                </td>
            </tr>
        <?php endif; ?>

		<tr>
			<td>Thêm file đính kèm:</td>
			<td colspan="2"><input type="file"
				style="border: solid #D5D5D5; color: #000000"
				class="multi accept-<?php echo _CONST_RECORD_FILE_ACCEPT;?>"
				name="uploader[]" id="File1" /> <font style="font-weight: normal;">Hệ
					thống chỉ chấp nhận đuôi file dạng:</font><span
				class="fileUploaderMessage"> doc; docx; pdf;</span><br /> <?php if (isset($VIEW_DATA['arr_all_record_file']))
				{
				    $arr_all_record_file = $VIEW_DATA['arr_all_record_file'];
				    for ($i=0; $i<sizeof($arr_all_record_file); $i++)
				    {
				        $v_file_id      = $arr_all_record_file[$i]['PK_RECORD_FILE'];
				        $v_file_name    = $arr_all_record_file[$i]['C_FILE_NAME'];
				        $v_file_path    = SITE_ROOT . 'uploads/r3/' . $v_file_name;

				        echo '<span id="file_' . $v_file_id . '">';
				        echo '<img src="' . SITE_ROOT . 'public/images/trash.png" style="cursor:pointer" onclick="delete_file(' . $v_file_id . ')">&nbsp;';
				        echo '<a href="' . $v_file_path .'" target="_blank">' .$v_file_name . '</a><br/>';
				        echo '</span>';
				    }
				}?></td>

			<td style="text-align: right">
			    <input type="button" name="update"
    				class="button save"
    				value="<?php echo __('update'); ?> (Alt+2)"
    				onclick="save_return_date();btn_update_onclick();" accesskey="2" /> 
				<input
				type="button" name="cancel" class="button close"
				value="<?php echo __('go back'); ?>"
				onclick="btn_back_onclick();" accesskey="9" />
				
			</td>
		</tr>
	</table>

	<div id="record_detail">
		<div id="xml_part">
			<?php echo $this->transform($this->get_xml_config($v_record_type_code, 'form_struct')); ?>
		</div>
	</div>

	<!-- Button -->
	<div class="button-area">
		<input type="button" name="update" class="button save"
			value="<?php echo __('update'); ?> (Alt+2)"
			onclick="btn_update_onclick();" accesskey="2" /> <input type="button"
			name="cancel" class="button close"
			value="<?php echo __('go back'); ?>"
			onclick="btn_back_onclick();" accesskey="9" />
	</div>
</form>
<script>
    $(document).ready(function() {
        //Fill data
        var formHelper = new DynamicFormHelper('','',document.frmMain);
        formHelper.BindXmlData();

        //try{$("#txtName").focus();}catch (e){;}

    });

    function txt_record_type_code_onkeypress(evt)
    {
        if (IE()){
            theKey=window.event.keyCode
        } else {
            theKey=evt.which;
        }

        if(theKey == 13){
            v_record_type_code = trim($("#txt_record_type_code").val()).toUpperCase();
            $("#sel_record_type").val(v_record_type_code);
            if ($("#sel_record_type").val() != '')
            {
                $("#frmMain").submit();
            }
            else
            {
                $("#record_detail").hide();
            }
        }
        return false;
    }

    function sel_record_type_onchange(e)
    {
        e.form.txt_record_type_code.value = e.value;
        if (trim(e.value) != '')
        {
            $("#frmMain").submit();
        }
        else
        {
            $("#record_detail").hide();
        }
    }

    function delete_file(id)
    {
        var f = document.frmMain;
        s = document.getElementById('file_' + id);
        s.style.display = "none";

        f.hdn_deleted_file_id_list.value = $("#hdn_deleted_file_id_list").val() + ','  + id;
    }

    function calc_return_date()
    {
        v_working_days = trim($("#txt_total_time").val());

        var v_url = '<?php echo $this->get_controller_url();?>arp_calc_return_date_ddmmyyyy/'  + v_working_days;
        $.ajax({
            url:v_url
            ,success:function(result){
                $("#txt_return_date").val(result);
                save_return_date();
            }
        });
    }

    function calc_working_days()
    {
        v_return_date = trim($("#txt_return_date").val());
        var v_url = '<?php echo $this->get_controller_url();?>arp_calc_working_days/&return_date='  + v_return_date;
        $.ajax({
            url:v_url
            ,success:function(result){
                $("#txt_total_time").val(result);
                save_return_date();
            }
        });

    }

    function save_return_date()
    {
        if (parseFloat($("#hdn_total_time").val()) < 0)
        {
            v_full_return_date = ddmmyyyy_to_yyyymmdd($("#txt_return_date").val()) + ' ' + $("#sel_return_date_noon").val();
            $("#hdn_return_date").val(v_full_return_date);
        }
    }

</script>
<?php $this->template->display('dsp_footer.php');