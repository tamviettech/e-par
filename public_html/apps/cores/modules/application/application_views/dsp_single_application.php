<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

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

<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}

//display header
$this->template->title = 'Cập nhật ứng dụng';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
$this->template->display('dsp_header' . $v_pop_win . '.php');
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
<form name="frmMain" method="post" id="frmMain" action="" ><?php
    echo $this->hidden('controller', $this->get_controller_url());

    echo $this->hidden('hdn_dsp_single_method', 'dsp_single_application');
    echo $this->hidden('hdn_dsp_all_method', 'dsp_all_application');
    echo $this->hidden('hdn_update_method', 'update_application');
    echo $this->hidden('hdn_delete_method', 'delete_application');

    echo $this->hidden('hdn_item_id', $v_application_id);
    echo $this->hidden('XmlData', $v_xml_data);

    echo $this->hidden('pop_win', $v_pop_win);
    ?>
    <!-- Toolbar -->
    <h2 class="module_title">Cập nhật ứng dụng</h2>
    <!-- /Toolbar -->

    <!-- Cot tuong minh -->
    <div class="Row">
        <div class="left-Col">Mã ứng dụng<span class="required">(*)</span> </div>
        <div class="right-Col">
            <input type="text" name="txt_code" value="<?php echo $v_code; ?>" id="txt_code"
                   class="inputbox" maxlength="255" style="width:40%"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="no" data-validate="text"
                   data-name="Mã ứng dụng"
                   data-xml="no" data-doc="no"
                   autofocus="autofocus"
            />
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">Tên ứng dụng<span class="required">(*)</span> </div>
        <div class="right-Col">
            <input type="text" name="txt_name" value="<?php echo $v_name; ?>" id="txt_name"
                   class="inputbox" style="width:80%"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="no" data-validate="text"
                   data-name="Tên ứng dụng"
                   data-xml="no" data-doc="no"
            />
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">Chú giải</div>
        <div class="right-Col">
            <textarea name="txt_description" id="txt_description"
                   class="inputbox" style="width:80%"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="yes" data-validate="text"
                   data-name="Tên ứng dụng"
                   data-xml="no" data-doc="no"
                   ><?php echo $v_description; ?></textarea>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">Module mặc định<span class="required">(*)</span> </div>
        <div class="right-Col">
            <input type="text" name="txt_default_module" value="<?php echo $v_default_module; ?>" id="txt_default_module"
                   class="inputbox" style="width:80%"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="no" data-validate="text"
                   data-name="Module mặc định"
                   data-xml="no" data-doc="no"
            />
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
                   data-name="<?php echo _LANG_ORDER_LABEL; ?>"
                   data-xml="no" data-doc="no"
                   /><span class="required">(*)</span>
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">File XML</div>
        <div class="right-Col">
            <input type="text" name="txt_xml_file_name" id="txt_xml_file_name" style="width:40%"
                   value="<?php echo $v_permit_xml_file_name; ?>" class="inputbox" size="255"
                   onKeyDown="return handleEnter(this, event);"
                   data-allownull="yes" data-validate="text"
                   data-name="" data-xml="no" data-doc="no"
                   readonly="readonly" />
            <input type="button" name="btn_upload" class="small_button"
                   value="Chọn file..."
                   onClick="btn_select_application_xml_file_onclick()" />
        </div>
    </div>
    <div class="Row">
        <div class="left-Col">
            <?php echo _('status'); ?>
        </div>
        <div class="right-Col">
            <input type="checkbox" name="chk_status" value="1"
                <?php echo ($v_status > 0) ? ' checked' : ''; ?>
                   id="chk_status"
                   /><label for="chk_status"><?php echo __('active status'); ?></label><br/>
        </div>
    </div>

    <div class="button-area">
        <input type="button" name="btn_update" id="btn_update" class="button save" value="<?php echo __('update');?>" onclick="btn_update_onclick()"/>
        <?php $v_back_action = ($v_pop_win === '') ? 'btn_back_onclick();' : 'try{window.parent.hidePopWin();}catch(e){window.close();};';?>
        <input type="button" name="cancel" class="button close" value="<?php echo __('go back'); ?>" onclick="<?php echo $v_back_action;?>"/>
    </div>

</form>
<script>
    function btn_select_application_xml_file_onclick()
    {
        var url =  $("#controller").val() + 'dsp_all_file_xml';
        showPopWin(url ,500,300, null);
    }
</script>
<?php $this->template->display('dsp_footer' .$v_pop_win . '.php');