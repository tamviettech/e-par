<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');} ?>
<?php
//display header
$this->template->active_menu = $this->active_menu =  'quan_tri_he_thong';
$this->template->title = $this->title = __('update list');
require_once(SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_header.php');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$arr_single_list = $VIEW_DATA['arr_single_list'];
if (isset($arr_single_list['PK_LIST'])) {
    $v_list_id       = $arr_single_list['PK_LIST'];
    $v_list_code     = $arr_single_list['C_CODE'];
    $v_list_name     = $arr_single_list['C_NAME'];
    $v_order         = $arr_single_list['C_ORDER'];
    $v_status        = $arr_single_list['C_STATUS'];
    $v_xml_data      = $arr_single_list['C_XML_DATA'];
    $v_xml_file_name = $arr_single_list['C_XML_FILE_NAME'];
    $v_listtype_id   = $arr_single_list['FK_LISTTYPE'];
} else {
    $v_list_id       = 0;
    $v_list_code     = '';
    $v_list_name     = '';
    $v_order         = $arr_single_list['C_ORDER'] + 1;
    $v_status        = 1;
    $v_xml_data      = '';
    $v_xml_file_name = $arr_single_list['C_XML_FILE_NAME'];
    $v_listtype_id   = $arr_single_list['FK_LISTTYPE'];
}
?>
<div class="container-fluid">
    <ul class="breadcrumb">
    	<li><a href="<?php echo SITE_ROOT;?>" class="icon-home"></a><span class="divider "><i class="icon-angle-right"></i></span></li>
    	<li class="active">Quản trị hệ thống<span class="divider"><i class="icon-angle-right"></i></span></li>
    	<li class="active">Đối tượng danh mục</li>
    </ul>
    <form name="frmMain" method="post" id="frmMain" action="" class="form-horizontal"><?php
        echo $this->hidden('controller', $this->get_controller_url());
    
        echo $this->hidden('hdn_dsp_single_method', 'dsp_single_list');
        echo $this->hidden('hdn_dsp_all_method', 'dsp_all_list');
        echo $this->hidden('hdn_update_method', 'update_list');
        echo $this->hidden('hdn_delete_method', 'delete_list');
    
        echo $this->hidden('hdn_item_id', $v_list_id);
        echo $this->hidden('XmlData', $v_xml_data);
    
        // Luu dieu kien loc
        $v_filter = isset($_POST['txt_filter']) ? $_POST['txt_filter'] : '';
        $v_page = isset($_POST['sel_goto_page']) ? Model::replace_bad_char($_POST['sel_goto_page']) : 1;
        $v_rows_per_page = isset($_POST['sel_rows_per_page']) ? Model::replace_bad_char($_POST['sel_rows_per_page']) : _CONST_DEFAULT_ROWS_PER_PAGE;
    
        echo $this->hidden('txt_filter', $v_filter);
        echo $this->hidden('sel_listtype_filter', $v_listtype_id);
        echo $this->hidden('sel_goto_page', $v_page);
        echo $this->hidden('sel_rows_per_page', $v_rows_per_page);
        ?>
        <div class="row-fluid">
            <div class="content-widgets light-gray">
                <div class="widget-head blue">
        			<h3><?php echo __('update list'); ?></h3>
        		</div>
        		
        		<div class="widget-container">
        		    <div class="control-group">
            	        <label class="control-label"><?php echo __('listtype'); ?></label>
            	        <div class="controls">
            	            <select name="sel_listtype" disabled="disabled" style="Z-INDEX:-1;">
                                <?php echo $this->generate_select_option($VIEW_DATA['arr_all_listtype_option'], $v_listtype_id); ?>
                            </select>
            			</div>
            		</div>
            		
        		    <div class="control-group">
            	        <label class="control-label"><?php echo __('list code'); ?><span class="required">(*)</span></label>
            	        <div class="controls">
            	            <input type="text" name="txt_code" value="<?php echo $v_list_code; ?>" id="txt_code"
                               class="input" maxlength="255" style="width:40%"
                               onKeyDown="return handleEnter(this, event);"
                               data-allownull="no" data-validate="text"
                               data-name="<?php echo __('list code'); ?>"
                               data-xml="no" data-doc="no"
                               onblur="check_code()" autofocus="autofocus"
                           />
            			</div>
            		</div>
            		
        		    <div class="control-group">
            	        <label class="control-label"><?php echo __('list name'); ?><span class="required">(*)</span></label>
            	        <div class="controls">
            	            <input type="text" name="txt_name" value="<?php echo $v_list_name; ?>" id="txt_name"
                               class="input" style="width:80%"
                               data-allownull="no" data-validate="text"
                               data-name="<?php echo __('list name'); ?>"
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
                               data-allownull="no" data-validate="number"
                               data-name="<?php echo __('order'); ?>"
                               data-xml="no" data-doc="no"
                           />
            			</div>
            		</div>
            		
        		    <div class="control-group">
            	        <label class="control-label"><?php echo __('status'); ?><span class="required">(*)</span></label>
            	        <div class="controls">
            	            <label for="chk_status">
            	                <input type="checkbox" name="chk_status" value="1"
                                <?php echo ($v_status > 0) ? ' checked' : ''; ?>
                                   id="chk_status"
                                   /><?php echo __('active status'); ?>
                               </label>
                           <label for="chk_save_and_addnew">
                               <input type="checkbox" name="chk_save_and_addnew" value="1"
                                <?php echo ($v_list_id > 0) ? '' : ' checked'; ?>
                                   id="chk_save_and_addnew"
                                   /><?php echo __('save and add new'); ?>
                           </label>
            			</div>
            		</div>
            		
            		<!-- XML data -->
                    <?php
                    if ($v_xml_file_name != '') {
                        $this->load_xml($v_xml_file_name);
                        echo $this->render_form_display_single();
                    }?>
                    
                    <div class="form-actions">
                        <button type="button" name="btn_update" class="btn btn-primary" onclick="btn_update_list_sonclick();"><i class="icon-save"></i><?php echo __('update');?></button>
                        <button type="button" name="cancel" class="btn" onclick="btn_back_onclick()"><i class="icon-reply"></i><?php echo __('go back'); ?></button>
            		</div>
        		</div>
    		</div>
		</div>
    </form>
</div>
<script type="text/javascript">
    var f=document.frmMain;
    listtype_id = $("#sel_listtype_filter").val();
    list_id     = $("#hdn_item_id").val();
    
    function btn_update_list_sonclick()
    {
         var f = document.frmMain;
        m = $("#controller").val() + f.hdn_update_method.value + '/0/';
        if(parseInt(f.txt_order.value )<0)
        {
            alert('Số thứ tự bạn nhập không chính xác!');
            f.txt_order.focus();
            return false;
        }
        var xObj = new DynamicFormHelper('','',f);
        if (xObj.ValidateForm(f)){
        f.XmlData.value = xObj.GetXmlData();
        $("#frmMain").attr("action", m);
        f.submit();
    }
    }
    
    function check_code(){
        if (trim(f.txt_code.value != '')){
            var v_url = f.controller.value + 'check_existing_list_code/' + trim(f.txt_code.value) + _CONST_LIST_DELIM + listtype_id + _CONST_LIST_DELIM + list_id;
            $.getJSON(v_url, function(json) {
                if (json.count > 0){
                    show_error('txt_code','Mã đối tượng danh mục đã tồn tai!');
                } else {
                    clear_error('txt_code');
                }
            });
        }
    }

    function check_name(){
        if (trim(f.txt_name.value != '')){
            var v_url = f.controller.value + 'check_existing_list_name/' + trim(f.txt_name.value) + _CONST_LIST_DELIM + listtype_id + _CONST_LIST_DELIM + list_id;
            $.getJSON(v_url, function(json) {
                if (json.count > 0){
                    show_error('txt_name','Tên đối tượng danh mục đã tồn tai!');
                } else {
                    clear_error('txt_name');
                }
            });
        }
    }

    $(document).ready(function() {
        //Fill data
        var formHelper = new DynamicFormHelper('','',document.frmMain);
        formHelper.BindXmlData();
    });
</script>
<?php require SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'dsp_footer.php';