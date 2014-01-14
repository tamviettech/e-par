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
<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}
?>
<?php
//display header
$this->template->title = __('update list');
$this->template->display('dsp_header.php');
?>
<?php
$arr_single_list = $VIEW_DATA['arr_single_list'];
if (isset($arr_single_list['PK_LIST'])) {
    $v_list_id = $arr_single_list['PK_LIST'];
    $v_list_code = $arr_single_list['C_CODE'];
    $v_list_name = $arr_single_list['C_NAME'];
    $v_order = $arr_single_list['C_ORDER'];
    $v_status = $arr_single_list['C_STATUS'];
    $v_xml_data = $arr_single_list['C_XML_DATA'];
    $v_xml_file_name = $arr_single_list['C_XML_FILE_NAME'];
    $v_listtype_id = $arr_single_list['FK_LISTTYPE'];
} else {
    $v_list_id = 0;
    $v_list_code = '';
    $v_list_name = '';
    $v_order = $arr_single_list['C_ORDER'] + 1;
    $v_status = 1;
    $v_xml_data = '';
    $v_xml_file_name = $arr_single_list['C_XML_FILE_NAME'];
    $v_listtype_id = $arr_single_list['FK_LISTTYPE'];
}
?>
<form name="frmMain" method="post" id="frmMain" action=""><?php
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
    <!-- Toolbar -->
    <h2 class="module_title"><?php echo __('update list'); ?></h2>
    <!-- /Toolbar -->

    <!-- Cot tuong minh -->
    <div class="Row">
        <div class="left-Col"><?php echo __('listtype'); ?></div>
        <div class="right-Col">
            <select name="sel_listtype" disabled="disabled" style="Z-INDEX:-1;">
                <?php echo $this->generate_select_option($VIEW_DATA['arr_all_listtype_option'], $v_listtype_id); ?>
            </select>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col"><?php echo __('list code'); ?></div>
        <div class="right-Col">
            <input type="text" name="txt_code" value="<?php echo $v_list_code; ?>" id="txt_code"
                   class="inputbox" maxlength="255" style="width:40%"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="no" data-validate="text"
                   data-name="<?php echo __('list code'); ?>"
                   data-xml="no" data-doc="no"
                   onblur="check_code()" autofocus="autofocus"
                   /><label class="required">(*)</label>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col"><?php echo __('list name'); ?></div>
        <div class="right-Col">
            <input type="text" name="txt_name" value="<?php echo $v_list_name; ?>" id="txt_name"
                   class="inputbox" style="width:80%"
                   data-allownull="no" data-validate="text"
                   data-name="<?php echo __('list name'); ?>"
                   data-xml="no" data-doc="no"
                   onblur="check_name()"
                   /><label class="required">(*)</label>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col"><?php echo __('order'); ?></div>
        <div class="right-Col">
            <input type="text" name="txt_order" value="<?php echo $v_order; ?>" id="txt_order"
                   class="inputbox" size="4" maxlength="3"
                   data-allownull="no" data-validate="number"
                   data-name="<?php echo __('order'); ?>"
                   data-xml="no" data-doc="no"
                   /><label class="required">(*)</label>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col"><?php echo __('status'); ?></div>
        <div class="right-Col">
            <input type="checkbox" name="chk_status" value="1"
                <?php echo ($v_status > 0) ? ' checked' : ''; ?>
                   id="chk_status"
                   /><label for="chk_status"><?php echo __('active status'); ?></label><br/>
            <input type="checkbox" name="chk_save_and_addnew" value="1"
                <?php echo ($v_list_id > 0) ? '' : ' checked'; ?>
                   id="chk_save_and_addnew"
                   /><label for="chk_save_and_addnew"><?php echo __('save and add new'); ?></label>
        </div>
    </div>
    <!-- XML data -->
    <?php
    if ($v_xml_file_name != '') {
        $this->load_xml($v_xml_file_name);
        echo $this->render_form_display_single();
    }
    ?>
    <!-- Button -->
    <div class="button-area">
        <input type="button" name="update" class="button" value="<?php echo __('update'); ?>" onclick="btn_update_onclick();"/>
        <input type="button" name="cancel" class="button" value="<?php echo __('go back'); ?>" onclick="btn_back_onclick();"/>
    </div>
</form>
<script type="text/javascript">
    var f=document.frmMain;
    listtype_id = $("#sel_listtype_filter").val();
    list_id     = $("#hdn_item_id").val();

    function check_code(){
        if (f.txt_code.value != ''){
            var v_url = f.controller.value + 'check_existing_list_code/' + f.txt_code.value + _CONST_LIST_DELIM + listtype_id + _CONST_LIST_DELIM + list_id;
            $.getJSON(v_url, function(json) {
                if (json.COUNT > 0){
                    show_error('txt_code','Mã đối tượng danh mục đã tồn tai!');
                } else {
                    clear_error('txt_code');
                }
            });
        }
    }

    function check_name(){
        if (f.txt_name.value != ''){
            var v_url = f.controller.value + 'check_existing_list_name/' + f.txt_name.value + _CONST_LIST_DELIM + listtype_id + _CONST_LIST_DELIM + list_id;
            $.getJSON(v_url, function(json) {
                if (json.COUNT > 0){
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
<?php $this->template->display('dsp_footer.php');