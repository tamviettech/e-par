<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');} ?>
<?php
//display header
$this->template->title = __('update listtype');
$this->template->display('dsp_header.php');
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
<form name="frmMain" id="frmMain" action="" method="POST"><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_listtype');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_listtype');
    echo $this->hidden('hdn_update_method', 'update_listtype');
    echo $this->hidden('hdn_delete_method', 'delete_listtype');

    echo $this->hidden('hdn_item_id', $v_listtype_id);
    echo $this->hidden('hdn_item_id_list', '');

    echo $this->hidden('XmlData', '');

    $this->write_filter_condition(array('txt_filter', 'sel_goto_page','sel_rows_per_page'));
    ?>
    <!-- Toolbar -->
    <h2 class="module_title"><?php echo __('update listtype'); ?></h2>
    <!-- /Toolbar -->

    <!-- Update Form -->
    <div class="Row">
        <div class="left-Col">
            <?php echo __('listtype code'); ?>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_code" value="<?php echo $v_listtype_code; ?>" id="txt_code"
                   class="inputbox" maxlength="255" style="width:40%"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="no" data-validate="text"
                   data-name="<?php echo __('listtype code'); ?>"
                   data-xml="no" data-doc="no"
                   onblur="check_code()" autofocus="autofocus"
                   /><span class="required">(*)</span>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <?php echo __('listtype name'); ?>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_name" value="<?php echo $v_listtype_name; ?>" id="txt_name"
                   class="inputbox" style="width:60%"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="no" data-validate="text"
                   data-name="<?php echo __('listtype name'); ?>"
                   data-xml="no" data-doc="no"
                   onblur="check_name()"

                   /><span class="required">(*)</span>
        </div>
    </div>

    <div class="Row">
        <div class="left-Col">
            <?php echo __('order'); ?>
        </div>
        <div class="right-Col">
            <input type="text" name="txt_order" value="<?php echo $v_order; ?>" id="txt_order"
                   class="inputbox" size="4" maxlength="3"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="no" data-validate="number"
                   data-name="<?php echo __('order'); ?>"
                   data-xml="no" data-doc="no"
                   /><span class="required">(*)</span>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">File XML</div>
        <div class="right-Col">
            <input type="text" name="txt_xml_file_name" id="txt_xml_file_name" style="width:40%"
                   value="<?php echo $v_xml_file_name; ?>" class="inputbox" size="255"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="yes" data-validate="text"
                   data-name="" data-xml="no" data-doc="no"
                   readonly="readonly" />
            <input type="button" name="btn_upload" class="small_button"
                   value="<?php echo __('choose file')?>..."
                   onClick="btn_select_listype_xml_file_onclick()" />
        </div>
    </div>

    <div class="Row">
        <div class="left-Col">
            <?php echo __('status'); ?>
        </div>
        <div class="right-Col">
            <input type="checkbox" name="chk_status" value="1"
                <?php echo ($v_status > 0) ? ' checked' : ''; ?>
                   id="chk_status"
                   /><label for="chk_status"><?php echo __('active status'); ?></label><br/>
            <input type="checkbox" name="chk_save_and_addnew" value="1"
                <?php echo ($v_listtype_id > 0) ? '' : ' checked'; ?>
                   id="chk_save_and_addnew"
                   /><label for="chk_save_and_addnew"><?php echo __('save and add new'); ?></label>
        </div>
    </div>

    <div class="button-area">
        <input type="button" name="btn_update" id="btn_update" class="button" value="<?php echo __('update');?>" onclick="btn_update_onclick()"/>
        <input type="button" name="btn_cancel" id="btn_cancel" class="button" value="<?php echo __('go back'); ?>" onclick="btn_back_onclick();"/>
    </div>
</form>
<script type="text/javascript">
    var f=document.frmMain;
    listtype_id = $("#hdn_item_id").val();

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
<?php
$this->template->display('dsp_footer.php');