<?php
/**


This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

<?php if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}

if (isset($arr_single_rule['PK_RULE']))
{
    $v_rule_id          = $arr_single_rule['PK_RULE'];
    $v_record_type_code = $arr_single_rule['C_RECORD_TYPE_CODE'];
    $v_name             = $arr_single_rule['C_NAME'];
    $v_begin_date       = $arr_single_rule['C_BEGIN_DATE'];
    $v_end_date         = $arr_single_rule['C_END_DATE'];
    $v_status           = $arr_single_rule['C_STATUS'];
    $v_rule_content     = $arr_single_rule['C_RULE_CONTENT'];
    $v_order            = $arr_single_rule['C_ORDER'];
    $v_owner_name       = $arr_single_rule['C_OWNER_NAME'];
    
    
    $v_begin_date       = jwDate::yyyymmdd_to_ddmmyyyy($v_begin_date);
    $v_end_date         = jwDate::yyyymmdd_to_ddmmyyyy($v_end_date);
}
else
{
    $v_rule_id            = 0;
    $v_record_type_code = '';
    $v_name             = '';
    $v_begin_date       = Date('d-m-Y');
    $v_end_date         = '31-12-2099';
    $v_status           = 1;
    $v_rule_content     = '';
    $v_order            = $arr_single_rule['C_ORDER'];
    $v_owner_name       = '';
}
$v_xml_data = '';

$arr_rule_content = json_decode($v_rule_content,1);
//display header
$this->template->title = 'Cập nhật quy luật lọc hồ sơ';
$this->template->display('dsp_header.php');
?>
<form name="frmMain" id="frmMain" action="" method="POST">
	<?php
	echo $this->hidden('controller',$this->get_controller_url());
	echo $this->hidden('hdn_item_id',$v_rule_id);
	echo $this->hidden('hdn_item_id_list','');

	echo $this->hidden('hdn_dsp_single_method','dsp_single_rule');
	echo $this->hidden('hdn_dsp_all_method', 'dsp_all_rule');
	echo $this->hidden('hdn_update_method','update_rule');
	echo $this->hidden('hdn_delete_method','delete_rule');

	echo $this->hidden('XmlData',$v_xml_data);
	?>
	<div class="page-title">Thông tin luật</div>
	
	<table style="width: 100%;" class="none-border-table">
	    <colgroup>
	        <col width="20%" />
	        <col width="80%" />
	    </colgroup>
	    <tr>
	        <td>Theo yêu cầu của phòng ban: <span class="required">(*)</span></td>
	        <td>
	            <input type="text" name="txt_owner_name" id="txt_owner_name" value="<?php echo $v_owner_name;?>" size="80" autofocus="autofocus" 
	                data-validate="text" data-name="Phòng ban" data-xml="no" data-allownull="no" data-doc="no" 
	                style="width: 88%; color: #000000;" 
	            />
	        </td>
	    </tr>
	    <tr>
	        <td>Tên luật <span class="required">(*)</span></td>
	        <td>
	            <input type="text" name="txt_name" id="txt_name" value="<?php echo $v_name;?>" size="80"
	                data-validate="text" data-name="Tên luật" data-xml="no" data-allownull="no" data-doc="no" 
	                style="width: 88%; color: #000000;" 
	            />
	        </td>
	    </tr>
		<tr>
			<td width="20%"><label>Mã loại hồ sơ</label> (Alt+1)</td>
			<td>
			    <input type="text" name="txt_record_type_code"
				    id="txt_record_type_code" value="<?php echo $v_record_type_code; ?>"
				    class="inputbox upper_text" size="10" maxlength="10"
				    onkeypress="txt_record_type_code_onkeypress(event);"
				    accesskey="1" data-allownull="yes"
				    data-validate="text" data-name="Loại hồ sơ" data-xml="no"
				    data-doc="no"
			    />&nbsp; 
			    <select name="sel_record_type" id="sel_record_type" style="width: 77%; color: #000000;" 
    				onchange="sel_record_type_onchange(this)" data-allownull="yes"
    				data-validate="text" data-name="Loại hồ sơ" data-xml="no"
    				data-doc="no"
				>
					<option value="">-- Tất cả --</option>
					<?php echo $this->generate_select_option($arr_all_record_type, $v_record_type_code); ?>
			</select>
			</td>
		</tr>
		<tr>
	        <td>Áp dụng từ ngày</td>
	        <td>
	            <input type="text" name="txt_begin_date" id="txt_begin_date" value="<?php echo $v_begin_date;?>" size="20" />
	            <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_begin_date')">
	            &nbsp;
	            
	            Đến ngày:
	            <input type="text" name="txt_end_date" id="txt_end_date" value="<?php echo $v_end_date;?>" size="20" />
	            <img class="btndate" style="cursor:pointer" id="btnDate" src="<?php echo SITE_ROOT;?>public/images/calendar.gif" onclick="DoCal('txt_end_date')">
	            
	        </td>
	    </tr>
		<tr>
	        <td>Số thứ tự</td>
	        <td>
	            <input type="text" name="txt_order" id="txt_order" value="<?php echo $v_order;?>" />           
	        </td>
	    </tr>
		<tr>
	        <td></td>
	        <td>
	            <label><input type="checkbox" name="chk_status" id="chk_status" value="1" <?php echo ($v_status > 0)?' checked':'';?>> Hoạt động</label>	            
	        </td>
	    </tr>
	    <tr>
	        <td></td>
	        <td><b>Tập các điều kiện về thông tin trong hồ sơ:</b></td>
	    </tr>
	    <tr>
	        <td></td>
	        <td>
	            <a class="addValueAnchor" href="javascript:void(0);" onclick="add_rule_option()">Thêm điều kiện</a>
	            <div id="rule_option" class="Request_Body_Widget_payloadFormPanel">
                    <?php if (is_array($arr_rule_content)):?>
                        <?php foreach ($arr_rule_content as $rule):?>
                            <div class="Request_Body_Widget_flex">
                    			<div class="Request_Body_Widget_flex">
                    				<input type="text" class="gwt-TextBox" name="txt_xml_tag[]" placeholder="Thẻ XML" value="<?php echo $rule['tag'];?>"/>
                    		     </div>
                    			    <input type="hidden" name="hdn_operator[]" value="<?php echo $rule['operator'];?>" />=
                    			    &nbsp;&nbsp;&nbsp;&nbsp;
                    		    <div class="Request_Body_Widget_flex Request_Body_Widget_valueBlock">
                    		    	<input type="text" class="gwt-TextBox formValueInput" name="txt_xml_value[]" placeholder="Có giá trị bằng" value="<?php echo $rule['value'];?>"/>
                    		    </div>
                    		    <div class="Request_Body_Widget_flex">
                    		    	<span class="gwt-InlineLabel removeButton" title="Remove" onclick=" $(this).parent().parent().remove();">x</span>
                    		     </div>
                    		</div>
                        <?php endforeach;?>
                    <?php endif;?>
                    
	            </div> <!-- #rule_option -->
	        </td>
	    </tr>
	</table>
	
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
        }
        return false;
    }

    function sel_record_type_onchange(e)
    {
        e.form.txt_record_type_code.value = e.value;
    }

    function add_rule_option()
    {
		html_option = '<div class="Request_Body_Widget_flex">';
		html_option += '	<div class="Request_Body_Widget_flex">';
		html_option += '		<input type="text" class="gwt-TextBox" name="txt_xml_tag[]" placeholder="Thẻ XML"/>';
		html_option += '     </div>';
		html_option +=	    '<input type="hidden" name="hdn_operator[]" value="eq" />=';
		html_option +=	    '&nbsp;&nbsp;&nbsp;&nbsp;';
		html_option += '    <div class="Request_Body_Widget_flex Request_Body_Widget_valueBlock">';
		html_option += '    	<input type="text" class="gwt-TextBox formValueInput" name="txt_xml_value[]" placeholder="Có giá trị bằng"/>';
		html_option += '    </div>';
		html_option += '    <div class="Request_Body_Widget_flex">';
		html_option += '    	<span class="gwt-InlineLabel removeButton" title="Remove" onclick=" $(this).parent().parent().remove();">x</span>';
		html_option += '     </div>';
		html_option += '</div>';

		$("#rule_option").append(html_option);
    }

    function remove_rule_option(obj)
    {
        
    }
        
</script>
<?php $this->template->display('dsp_footer.php');