<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}

//display header
$this->active_menu =  'quan_tri_he_thong';
$this->title = 'Cập nhật ứng dụng';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->show_left_side_bar = FALSE;
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header' . $v_pop_win . '.php');
//------------------------------------------------------------------------------

$arr_single_application         = $VIEW_DATA['arr_single_application'];

$v_application_id       = $arr_single_application['PK_APPLICATION'];
$v_code                 = $arr_single_application['C_CODE'];
$v_name                 = $arr_single_application['C_NAME'];
$v_permit_xml_file_name = $arr_single_application['C_XML_PERMIT_FILE_NAME'];
$v_order                = $arr_single_application['C_ORDER'];
$v_status               = $arr_single_application['C_STATUS'];
$v_xml_data             = $arr_single_application['C_XML_DATA'];
$v_description          = $arr_single_application['C_DESCRIPTION'];
$v_default_module       = $arr_single_application['C_DEFAULT_MODULE'];
?>
<div class="container-fluid">
    <ul class="breadcrumb">
    	<li><a href="<?php echo SITE_ROOT;?>" class="icon-home"></a><span class="divider "><i class="icon-angle-right"></i></span></li>
    	<li><a href="javascript:;">Quản trị hệ thống</a><span class="divider"><i class="icon-angle-right"></i></span></li>
    	<li><a href="<?php echo $this->get_controller_url();?>">Ứng dụng</a><span class="divider"><i class="icon-angle-right"></i></span></li>
    	<li class="active">Cập nhật Ứng dụng</li>
    </ul>
    <form name="frmMain" method="post" id="frmMain" action="" class="form-horizontal"><?php
        echo $this->hidden('controller', $this->get_controller_url());
    
        echo $this->hidden('hdn_dsp_single_method', 'dsp_single_application');
        echo $this->hidden('hdn_dsp_all_method', 'dsp_all_application');
        echo $this->hidden('hdn_update_method', 'update_application');
        echo $this->hidden('hdn_delete_method', 'delete_application');
    
        echo $this->hidden('hdn_item_id', $v_application_id);
        echo $this->hidden('XmlData', $v_xml_data);
    
        echo $this->hidden('pop_win', $v_pop_win);
        ?>
        <div class="row-fluid">
            <div class="content-widgets light-gray">
                <div class="widget-head blue">
        			<h3>Cập nhật ứng dụng</h3>
        		</div>
        		<div class="widget-container">
            		<div class="control-group">
            	        <label class="control-label">Mã ứng dụng <span class="required">(*)</span></label>
            	        <div class="controls">
            	            <input type="text" name="txt_code" value="<?php echo $v_code; ?>" id="txt_code"
                               class="input" maxlength="255" style="width:40%"
                               onKeyDown="return handleEnter(this, event);"
                               data-allownull="no" data-validate="text"
                               data-name="Mã ứng dụng"
                               data-xml="no" data-doc="no"
                               autofocus="autofocus"
                           />
            			</div>
            		</div>
            		
            		<div class="control-group">
            	        <label class="control-label">Tên ứng dụng<span class="required">(*)</span> </label>
            	        <div class="controls">
            	            <input type="text" name="txt_name" value="<?php echo $v_name; ?>" id="txt_name"
                                   class="inputbox" style="width:80%"
                                   onKeyDown="return handleEnter(this, event);"
                                   data-allownull="no" data-validate="text"
                                   data-name="Tên ứng dụng"
                                   data-xml="no" data-doc="no"
                            />
            			</div>
            		</div>
            		
            		<div class="control-group">
            	        <label class="control-label">Chú giải</label>
            	        <div class="controls">
            	            <textarea name="txt_description" id="txt_description"
                               class="input" style="width:80%"
                               onKeyDown="return handleEnter(this, event);"
                               data-allownull="yes" data-validate="text"
                               data-name="Tên ứng dụng"
                               data-xml="no" data-doc="no"
                               ><?php echo $v_description; ?></textarea>
            			</div>
            		</div>
            		
            		<div class="control-group">
            	        <label class="control-label">Module mặc định<span class="required">(*)</span></label>
            	        <div class="controls">
            	            <input type="text" name="txt_default_module" value="<?php echo $v_default_module; ?>" id="txt_default_module"
            	                   class="input" style="width:80%"
                                   onKeyDown="return handleEnter(this, event);"
                                   data-allownull="no" data-validate="text"
                                   data-name="Module mặc định"
                                   data-xml="no" data-doc="no"
                            />
            			</div>
            		</div>
            		
            		<div class="control-group">
            	        <label class="control-label"><?php echo __('order'); ?><span class="required">(*)</span> </label>
            	        <div class="controls">
            	            <input type="text" name="txt_order" value="<?php echo $v_order; ?>" id="txt_order"
                               class="inputbox" size="4" maxlength="3"
                               onKeyDown="return handleEnter(this, event);"
                               data-allownull="no" data-validate="number"
                               data-name="<?php echo __('order'); ?>"
                               data-xml="no" data-doc="no"
                           />
            			</div>
            		</div>
            		
            		<div class="control-group">
            	        <label class="control-label">File XML</label>
            	        <div class="controls">
            	            <input type="text" name="txt_xml_file_name" id="txt_xml_file_name" style="width:40%"
                                   value="<?php echo $v_permit_xml_file_name; ?>" class="inputbox" size="255"
                                   onKeyDown="return handleEnter(this, event);"
                                   data-allownull="yes" data-validate="text"
                                   data-name="" data-xml="no" data-doc="no"
                                   readonly="readonly" class="uneditable-input"
                            />
                            <button type="button" name="btn_upload" class="btn btn-file" onClick="btn_select_application_xml_file_onclick()">
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
            	           <label>
            	               <input type="checkbox" name="chk_status" value="1" <?php echo ($v_status > 0) ? ' checked' : ''; ?> id="chk_status" />
            	               <?php echo __('active status'); ?>
        	               </label>
            			</div>
            		</div>
            		
            		<div class="form-actions">
                        <button type="button" name="btn_update" class="btn btn-primary" onclick="btn_update_onclick();"><i class="icon-save"></i><?php echo __('update');?></button>
                        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
                        <button type="button" name="cancel" class="btn" onclick="<?php echo $v_back_action;?>"><i class="icon-reply"></i><?php echo __('go back'); ?></button>
            		</div>
        		</div>
    		</div>
        </div>
    </form>
</div>
<script>
    function btn_select_application_xml_file_onclick()
    {
        var url =  $("#controller").val() + 'dsp_all_file_xml';
        showPopWin(url ,500,300, null);
    }
</script>
<?php require SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer' .$v_pop_win . '.php';