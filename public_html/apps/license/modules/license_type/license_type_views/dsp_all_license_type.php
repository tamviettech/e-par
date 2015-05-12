<?php
if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
}
//header
$this->template->title = 'Quản trị loại hồ sơ';
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

            echo $this->hidden('hdn_dsp_single_method', 'dsp_single_license_type');
            echo $this->hidden('hdn_dsp_all_method', 'dsp_all_license_type');
            echo $this->hidden('hdn_delete_method', 'delete_license_type');
            echo $this->hidden('hdn_update_method', 'update_license_type');
            ?>
            <div class="row-fluid">
                <div class="widget-head blue">
                    <h3>Quản lý loại giấy phép</h3>
                </div>
                <!-- filter -->
                <div id="div_filter">
                    Lọc theo tên loại giấy phép
                    <input type="text" name="txt_filter"
                           value="<?php echo $v_filter; ?>"
                           class="inputbox" size="30" autofocus="autofocus"
                           onkeypress="txt_filter_onkeypress(this.form.btn_filter, event);"/>
                    <button type="button" class="btn btn-file"
                            onclick="btn_filter_onclick();" name="btn_filter">
                        <i class="icon-search"></i>Lọc
                    </button>
                </div>
                <table class="table table-bordered table-striped">
                    <tr>
                        <th style="width: 5%">
                            <?php if (!empty($arr_all_license_type)): ?>
                                <input type="checkbox" name="chk_check_all"
                                       onclick="toggle_check_all(this, this.form.chk);">
                                   <?php endif; ?>
                        </th>
                        <th style="width: 15%">Mã loại giấy phép</th>
                        <th style="width: 50%">Tên loại giấy phép</th>
                        <th style="width: 15%">Thứ tự hiển thị</th>
                        <th style="width: 15%">Trạng thái</th>
                    </tr>
                    <?php foreach ($arr_all_license_type as $license_type) : ?>
                        <tr>
                            <td class="center">
                                <?php if (!$license_type['COUNT_RECORD'] > 0): ?>
                                    <input type="checkbox" name="chk" value="<?php echo $license_type['PK_LICENSE_TYPE'] ?>"
                                           onclick="if (!this.checked)
                                                       this.form.chk_check_all.checked = false;"/>
                                       <?php endif; ?>
                            </td>
                            <td><a href="javascript:void(0)"  onclick="row_onclick('<?php echo $license_type['PK_LICENSE_TYPE'] ?>')"><?php echo $license_type['C_CODE'] ?></a></td>
                            <td><a href="javascript:void(0)"  onclick="row_onclick('<?php echo $license_type['PK_LICENSE_TYPE'] ?>')"><?php echo $license_type['C_NAME'] ?></a></td>
                            <td class="center"><a href="javascript:void(0)" onclick="row_onclick('<?php echo $license_type['PK_LICENSE_TYPE'] ?>')"><?php echo $license_type['C_ORDER'] ?></a></td>
                            <td class="center"><a href="javascript:void(0)" onclick="row_onclick('<?php echo $license_type['PK_LICENSE_TYPE'] ?>')"><?php echo $license_type['C_STATUS'] ? 'Hoạt động' : 'Không hoạt động' ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <div id="dyntable_length" class="dataTables_length">

                    <?php
                    //Phan trang
                    echo $this->paging2($arr_all_license_type);
                    ?>
                </div><!-- #dyntable_length -->
                <div class="form-actions">                        
                    <?php if (check_permission('THEM_MOI_LOAI_HO_SO', 'LICENSE')): ?>
                        <button type="button" name="btn_addnew" class="btn btn-primary" onclick="btn_addnew_onclick();">
                            <i class="icon-plus"></i>
                            <?php echo __('add new'); ?> 
                        </button>
                    <?php endif; ?>
                    <?php if (check_permission('XOA_LOAI_HO_SO', 'LICENSE')): ?>
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
