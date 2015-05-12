<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}
?>
<?php
//display header
$this->template->title = 'Cập nhật loại hồ sơ';
$this->template->display('dsp_header.php');
?>
<div class="main-wrapper" style="margin-left: 0px !important;">
    <div class="container-fluid">
        <ul class="breadcrumb"></ul>
        <?php
        if (isset($arr_single_license_type['PK_LICENSE_TYPE'])) {
            $v_license_type_id = $arr_single_license_type['PK_LICENSE_TYPE'];
            $v_license_type_name = $arr_single_license_type['C_NAME'];
            $v_license_type_code = $arr_single_license_type['C_CODE'];
            $v_order = $arr_single_license_type['C_ORDER'];
            $v_sector = $arr_single_license_type['FK_SECTOR'];
            $v_status = $arr_single_license_type['C_STATUS'];
        } else {
            $v_license_type_id = "";
            $v_license_type_name = "";
            $v_license_type_code = "";
            $v_order = "";
            $v_sector = "";
            $v_status = "";
        }
        ?>
        <form name="frmMain" id="frmMain" action="" method="POST" class="form-horizontal"><?php
            echo $this->hidden('controller', $this->get_controller_url());
            echo $this->hidden('hdn_dsp_single_method', 'dsp_single_license_type');
            echo $this->hidden('hdn_dsp_all_method', 'dsp_all_license_type');
            echo $this->hidden('hdn_update_method', 'update_license_type');
            echo $this->hidden('hdn_delete_method', 'delete_license_type');

            echo $this->hidden('hdn_item_id', $v_license_type_id);
            echo $this->hidden('hdn_item_id_list', '');

            echo $this->hidden('XmlData', '');

            $this->write_filter_condition(array('txt_filter', 'sel_goto_page', 'sel_rows_per_page'));
            ?>
            <div class="row-fluid">
                <div class="content-widgets light-gray">
                    <div class="widget-head blue">
                        <h3><?php echo 'Cập nhật loại hồ sơ' ?></h3>
                    </div>

                    <div class="widget-container">
                        <div class="control-group">
                            <label class="control-label" for="license_type_code">
                                Mã loại hồ sơ
                                <span class="required">(*)</span>
                            </label>
                            <div class="controls">
                                <input id='license_type_code' type="text" name="txt_code" value="<?php echo $v_license_type_code; ?>" id="txt_code"
                                       class="inputbox" maxlength="15" style="width:40%"
                                       onKeyDown="return handleEnter(this, event);"
                                       data-allownull="no" data-validate="text"
                                       data-name="Mã Loại hồ sơ"
                                       data-xml="no" data-doc="no"
                                       autofocus="autofocus"
                                       />
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="license_type_name">
                                Tên loại hồ sơ
                                <span class="required">(*)</span>
                            </label>
                            <div class="controls">
                                <input id='license_type_name' type="text" name="txt_name" value="<?php echo $v_license_type_name; ?>"
                                       class="inputbox" maxlength="500" style="width:60%"
                                       onKeyDown="return handleEnter(this, event);"
                                       data-allownull="no" data-validate="text"
                                       data-name="Tên loại hồ sơ"
                                       data-xml="no" data-doc="no"
                                       autofocus="autofocus"/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="sector">
                                Lĩnh vực
                                <span class="required">(*)</span>
                            </label>
                            <div class="controls">
                                <select id='sector' name='sel_sector'>
                                    <?php echo $this->generate_select_option($arr_all_sector, $v_sector) ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for='txt_order'>
                                <?php echo __('order'); ?>
                                <span class="required">(*)</span>
                            </label>
                            <div class="controls">
                                <input type="text" name="txt_order"
                                       value="<?php echo $v_order; ?>" id="txt_order"
                                       class="inputbox" size="4" maxlength="3"
                                       onKeyDown="return handleEnter(this, event);"
                                       data-allownull="no" data-validate="number"
                                       data-name="<?php echo __('order'); ?>"
                                       data-xml="no" data-doc="no"
                                       />
                            </div>
                        </div>

                        <div class="controls">
                            <label for="chk_status">
                                <input type="checkbox" name="chk_status" value="1"
                                <?php echo ($v_status > 0) ? ' checked' : ''; ?>
                                       id="chk_status"
                                       /><?php echo __('active status'); ?><br/>
                            </label>
                        </div>

                        <!-- Button -->
                        <div class="form-actions">
                            <?php if(($v_license_type_id !== "" && check_permission("SUA_LOAI_HO_SO", "LICENSE"))
                                    || ($v_license_type_id == "" && check_permission("THEM_MOI_LOAI_HO_SO", "LICENSE"))): ?>
                            <button type="button" name="btn_addnew" class="btn btn-primary" onclick="btn_update_onclick();">
                                <i class="icon-save"></i>
                                <?php echo __('update'); ?>
                            </button>
                            <?php endif; ?>
                            <button type="button" class="btn" onclick="btn_back_onclick();">
                                <i class="icon-reply"></i>
                                <?php echo __('go back'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>