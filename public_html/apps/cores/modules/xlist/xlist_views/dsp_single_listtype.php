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
<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');} ?>
<?php
//display header
$this->template->active_menu = $this->active_menu =  'quan_tri_he_thong';
$this->template->title = $this->title = __('update listtype');
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header.php');
?>
<?php
if (isset($arr_single_listtype['PK_LISTTYPE']))
{
    $v_listtype_id           = $arr_single_listtype['PK_LISTTYPE'];
    $v_listtype_code         = $arr_single_listtype['C_CODE'];
    $v_listtype_name         = $arr_single_listtype['C_NAME'];
    $v_owner_code_list       = $arr_single_listtype['C_OWNER_CODE_LIST'];
    $v_xml_file_name         = $arr_single_listtype['C_XML_FILE_NAME'];
    $v_order                 = $arr_single_listtype['C_ORDER'];
    $v_status                = $arr_single_listtype['C_STATUS'];
}
else
{
    $v_listtype_id = 0;
    $v_listtype_code = '';
    $v_listtype_name = '';
    $v_owner_code_list = '';
    $v_xml_file_name = '';
    $v_order = $arr_single_listtype['C_ORDER'] + 1;
    $v_status = 1;
}
?>
<div class="container-fluid">
    <ul class="breadcrumb">
    	<li><a href="<?php echo SITE_ROOT;?>" class="icon-home"></a><span class="divider "><i class="icon-angle-right"></i></span></li>
    	<li class="active">Quản trị hệ thống<span class="divider"><i class="icon-angle-right"></i></span></li>
    	<li class="active">Loại danh mục</li>
    </ul>
    <form name="frmMain" id="frmMain" action="" method="POST" class="form-horizontal"><?php
        echo $this->hidden('controller', $this->get_controller_url());
    
        echo $this->hidden('hdn_dsp_single_method', 'dsp_single_listtype');
        echo $this->hidden('hdn_dsp_all_method', 'dsp_all_listtype');
        echo $this->hidden('hdn_update_method', 'update_listtype');
        echo $this->hidden('hdn_delete_method', 'delete_listtype');
        echo $this->hidden('hdn_exits_order',isset($arr_single_listtype['C_EXITS_ORDER']) ? $arr_single_listtype['C_EXITS_ORDER']: -1);
        echo $this->hidden('hdn_item_id', $v_listtype_id);
        echo $this->hidden('hdn_item_id_list', '');
    
        echo $this->hidden('XmlData', '');
    
        $this->write_filter_condition(array('txt_filter', 'sel_goto_page','sel_rows_per_page'));
        ?>
        
        <div class="row-fluid">
            <div class="content-widgets light-gray">
                <div class="widget-head blue">
        			<h3><?php echo __('update listtype'); ?></h3>
        		</div>
        		<div class="widget-container">
        		
        		    <div class="control-group">
            	        <label class="control-label"><?php echo __('listtype code'); ?><span class="required">(*)</span></label>
            	        <div class="controls">
            	            <input type="text" name="txt_code" value="<?php echo $v_listtype_code; ?>" id="txt_code"
                               class="inputs" maxlength="255" style="width:40%"
                               onKeyDown="return handleEnter(this, event);"
                               data-allownull="no" data-validate="text"
                               data-name="<?php echo __('listtype code'); ?>"
                               data-xml="no" data-doc="no"
                               onblur="check_code()" autofocus="autofocus"
                           />
            			</div>
            		</div>
            		
        		    <div class="control-group">
            	        <label class="control-label"><?php echo __('listtype name'); ?><span class="required">(*)</span></label>
            	        <div class="controls">
            	            <input type="text" name="txt_name" value="<?php echo $v_listtype_name; ?>" id="txt_name"
                               class="input" style="width:60%"
                               onKeyDown="return handleEnter(this, event);"
                               data-allownull="no" data-validate="text"
                               data-name="<?php echo __('listtype name'); ?>"
                               data-xml="no" data-doc="no"
                               onblur="check_name()"
                           />
            			</div>
            		</div>
            		
        		    <div class="control-group">
            	        <label class="control-label"><?php echo __('order'); ?><span class="required">(*)</span></label>
            	        <div class="controls">
            	            <input type="text" name="txt_order" value="<?php echo $v_order; ?>" id="txt_order"
                               class="input" size="4" maxlength="3"
                               onKeyDown="return handleEnter(this, event);"
                               data-allownull="no" data-validate="number"
                               data-name="<?php echo __('order'); ?>"
                               data-xml="no" data-doc="no"
                           />
            			</div>
                        <label class="error_order" style="color: red;"></label>
            		</div>
            		
            		<div class="control-group">
            	        <label class="control-label">File XML</label>
            	        <div class="controls">
            	            <input type="text" name="txt_xml_file_name" id="txt_xml_file_name" style="width:40%"
                                   value="<?php echo $v_xml_file_name; ?>" class="input" size="255"
                                   onKeyDown="return handleEnter(this, event);"
                                   data-allownull="yes" data-validate="text"
                                   data-name="" data-xml="no" data-doc="no"
                                   readonly="readonly" class="uneditable-input"
                            />
                            <button type="button" name="btn_upload" class="btn btn-file" onClick="btn_select_listype_xml_file_onclick()">
                                <i class="icon-search"></i>Chọn file...
                            </button>
                            <button type="button" name="btn_remove_xml_file" class="btn btn-file" onClick="$('#txt_xml_file_name').val('');">
                                <i class="icon-remove"></i>Xoá file
                            </button>
            			</div>
            		</div>
            		
            		<div class="control-group">
            	        <label class="control-label"><?php echo __('status'); ?></label>
            	        <div class="controls">
            	            <label for="chk_status">
                	            <input type="checkbox" name="chk_status" value="1" <?php echo ($v_status > 0) ? ' checked' : ''; ?> id="chk_status"/>
                	            <?php echo __('active status'); ?>
            	            </label>
            	            <label for="chk_save_and_addnew">
                                <input type="checkbox" name="chk_save_and_addnew" value="1" <?php echo ($v_listtype_id > 0) ? '' : ' checked'; ?> id="chk_save_and_addnew"/>
                                <?php echo __('save and add new'); ?>
                            </label>
            			</div>
            		</div>
            		
            		<div class="form-actions">
                        <button type="button" name="btn_update" class="btn btn-primary" onclick="btn_update_listtype_onclick();"><i class="icon-save"></i><?php echo __('update');?></button>
                        <button type="button" name="cancel" class="btn" onclick="btn_back_onclick()"><i class="icon-reply"></i><?php echo __('go back'); ?></button>
            		</div>
        		</div>
    		</div>
		</div>
    </form>
</div>
<script type="text/javascript">
    var f=document.frmMain;
    listtype_id = $("#hdn_item_id").val();
    function btn_update_listtype_onclick()
    {
        m = $("#controller").val() + f.hdn_update_method.value + '/0/';
        v_order          = f.txt_order.value;
        arr_exits_order  = (f.hdn_exits_order.value).split(',');
        if(parseInt(v_order) <= 0 && v_order.length >0 )
        {
            f.txt_order.focus();
            $('.error_order').text('Thứ tự hiển thị không hợp lệ!');
            return false;
        }
        else if(arr_exits_order.indexOf(v_order) >0 && v_order.length >0 && parseInt(f.hdn_item_id.value) <= 0)
        {
            f.txt_order.focus();
            $('.error_order').text('Thứ tự hiển thị đã bị trùng!');
            return false;
        }
        else
        {
            $('.error_order').text('');
        }
        
        var xObj = new DynamicFormHelper('','',f);
        if (xObj.ValidateForm(f)){
        f.XmlData.value = xObj.GetXmlData();
        $("#frmMain").attr("action", m);
        f.submit();
        }
    }
    function check_code(){
        if (f.txt_code.value != ''){
            v_url = f.controller.value + 'check_existing_listtype_code/&code=' + f.txt_code.value + '&listtype_id=' + listtype_id;
            $.getJSON(v_url, function(json) {
                if (json.count > 0){
                    show_error('txt_code','Mã loại danh mục đã tồn tai!');
                } else {
                    clear_error('txt_code');
                }
            });
        }
    }

    function check_name(){
        if (f.txt_name.value != ''){
        	v_url = f.controller.value + 'check_existing_listtype_name/&name=' + f.txt_name.value + '&listtype_id=' + listtype_id;
            $.getJSON(v_url, function(json) {
                if (json.count > 0){
                    show_error('txt_name','Tên loại danh mục đã tồn tai!');
                } else {
                    clear_error('txt_name');
                }
            });
        }
    }
</script>
<?php require SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer.php';