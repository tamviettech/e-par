<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
//header
$this->template->title = 'Quản trị hồ sơ';
$this->template->display('dsp_header.php');
?>

<div class="main-wrapper" style="margin-left: 0px !important;">                    
    <div class="container-fluid">
        <ul class="breadcrumb"></ul>
        <form name="frmMain" id="frmMain" action="" method="POST">
            <?php
            echo $this->hidden('controller', $this->get_controller_url());
            echo $this->hidden('hdn_item_id', '0');
            echo $this->hidden('hdn_item_id_list', '');

            echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
            echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
            echo $this->hidden('hdn_delete_method', 'delete_record');
            echo $this->hidden('hdn_update_method', 'update_record');
            ?>
            <div class="row-fluid">
                <div class="widget-head blue">
                    <h3>Quản lý hồ sơ</h3>
                </div>
                <!-- filter -->
                <div id="div_filter">
                    Mã loại hồ sơ
                    <input style="width: 50px" type="text"
                           id="txt_license_type_code"
                           name="txt_license_type_code" size="10" maxlength="10"
                           class="inputbox upper_text"
                           value="<?php echo $v_license_type_code; ?>"
                           onkeypress="txt_license_type_code_onkeypress(event);"
                           autofocus="autofocus" accesskey="1">
                    <select name="sel_license_type" id="sel_license_type"
                            onchange="sel_license_type_onchange(this)" style="width: 150px">
                        <option value="">-- Chọn loại hồ sơ --</option>
                        <?php echo $this->generate_select_option($arr_all_license_type, $v_license_type_code); ?>
                    </select>
                    Mã hồ sơ hoặc tên chủ giấy phép
                    <input type="text" name="txt_filter" style="width: 100px"
                           value="<?php echo $v_filter; ?>"
                           class="inputbox" size="30" autofocus="autofocus"
                           onkeypress="txt_filter_onkeypress(this.form.btn_filter, event);"/>
                    Cấp trong khoảng từ
                    <input type="text" id="txt_start_date"
                           name="txt_start_date" style="width: 75px"
                           class="inputbox"
                           value="<?php echo $v_start_date ?>"
                           data-allownull="yes" data-validate="date"
                           data-name="Từ ngày" data-xml="no">
                    <img class="btndate" style="cursor:pointer"id="btnDate"
                         src="<?php echo SITE_ROOT ?>public/images/calendar.gif"
                         onclick="DoCal('txt_start_date')">
                    Đến
                    <input type="text" id="txt_end_date"
                           name="txt_end_date" style="width: 75px"
                           class="inputbox"
                           value="<?php echo $v_end_date ?>"
                           data-allownull="yes" data-validate="date"
                           data-name="Đến ngày" data-xml="no">
                    <img class="btndate" style="cursor:pointer"id="btnDate"
                         src="<?php echo SITE_ROOT ?>public/images/calendar.gif"
                         onclick="DoCal('txt_end_date')">

                    <?php $checked = get_post_var('chk_empty_license_no') ? 'checked' : '' ?>
                    <input type="checkbox" name="chk_empty_license_no" id="chk_empty_license_no" <?php echo $checked ?>>
                    <label for="chk_empty_license_no" style="display: inline">Chưa điền số giấy phép</label>
                    
                    <button type="button" class="btn btn-file"
                            onclick="btn_filter_onclick();" name="btn_filter">
                        <i class="icon-search"></i>Lọc
                    </button>
                </div>
                <table class="table table-bordered table-striped" id="table_record">
                    <tr>
                        <th style="width: 5%">
                            <?php if (!empty($arr_all_record)): ?>
                                <input type="checkbox" name="chk_check_all"
                                       onclick="toggle_check_all(this, this.form.chk);">
                                   <?php endif; ?>
                        </th>
                        <th style="width: 13%">Mã hồ sơ</th>
                        <th style="width: 13%">Số giấy phép</th>
                        <th style="width: 40%">Tên chủ giấy phép</th>
                        <th style="width: 15%">Ngày cấp phép</th>
                        <?php if (check_permission('IN_HO_SO', 'LICENSE')): ?>
                            <th style="width: 13%">Thao tác</th>
                        <?php endif; ?>
                    </tr>
                    <?php foreach ($arr_all_record as $record) : ?>
                        <tr>
                            <td class="center">
                                <input type="checkbox" name="chk" value="<?php echo $record['PK_RECORD'] ?>"
                                       onclick="if (!this.checked)
                                                       this.form.chk_check_all.checked = false;"/>
                            </td>
                            <td>
                                <a href="javascript:void(0)"  onclick="row_onclick('<?php echo $record['PK_RECORD'] ?>')">
                                    <?php echo $record['C_CODE'] ?>
                                </a>
                            </td>
                            <td>
                                <a href="javascript:void(0)"  onclick="row_onclick('<?php echo $record['PK_RECORD'] ?>')">
                                    <?php echo $record['C_LICENSE_NO'] ?>
                                </a>
                            </td>
                            <td>
                                <a href="javascript:void(0)"  onclick="row_onclick('<?php echo $record['PK_RECORD'] ?>')">
                                    <?php echo $record['C_CITIZEN_NAME'] ?>
                                </a>
                            </td>
                            <td class="center">
                                <a href="javascript:void(0)" onclick="row_onclick('<?php echo $record['PK_RECORD'] ?>')">
                                    <?php echo date_format(new DateTime($record['C_ISSUED_DATE']), 'd-m-Y') ?>
                                </a>
                            </td>
                            <?php if (check_permission('IN_HO_SO', 'LICENSE')): ?>
                                <td class="center">
                                    <a href="<?php echo $this->get_controller_url() ?>print_record/<?php echo $record['PK_RECORD'] ?>">
                                        <i class="icon-print"></i> In giấy phép
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <div id="dyntable_length" class="dataTables_length">

                    <?php
                    //Phan trang
                    echo $this->paging2($arr_all_record);
                    ?>
                </div><!-- #dyntable_length -->
                <div class="form-actions">                        
                    <?php if (check_permission('THEM_MOI_HO_SO', 'LICENSE')): ?>
                        <button type="button" name="btn_addnew" class="btn btn-primary" onclick="btn_addnew_onclick();">
                            <i class="icon-plus"></i>
                            <?php echo __('add new'); ?> 
                        </button>
                    <?php endif; ?>
                    <?php if (check_permission('XOA_HO_SO', 'LICENSE')): ?>
                        <button type="button" class="btn" onclick="btn_delete_onclick();">
                            <i class="icon-trash"></i>
                            <?php echo __('delete'); ?>                        
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
$this->template->display('dsp_footer.php');
