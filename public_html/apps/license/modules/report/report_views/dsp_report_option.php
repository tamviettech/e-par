<?php
if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
?>
<?php
//display header
$this->template->title = 'Cập nhật hồ sơ';
$this->template->display('dsp_header.php');

$v_license_type_code = get_post_var('sel_license_type_code', "");
$sel_year = get_post_var('sel_year', 0);

echo $this->hidden('controller', $this->get_controller_url());
?>

<div class="main-wrapper" style="margin-left: 0px !important;">
    <div class="container-fluid">
        <ul class="breadcrumb"></ul>
        <form name="frmMain" class="form-horizontal" id="frmMain" action="" method="POST" class="form-horizontal" enctype="multipart/form-data">
            <div class="control-group" id="control-group-report-type">
                <div class="control-label">
                    Loại báo cáo
                </div>
                <div class="controls">
                    <select name="sel_report_type" id="sel_report_type" class="span4" onchange="sel_report_type_onchange()">
                        <?php echo $this->generate_select_option($arr_report_type)?>
                    </select>
                </div>
            </div>
            <div class="control-group" id="control-group-license-type">
                <div class="control-label">
                    Lọc theo loại hồ sơ
                </div>
                <div class="controls">
                    <input style="width: 50px" type="text"
                           id="txt_license_type_code"
                           name="txt_license_type_code" size="10" maxlength="10"
                           class="inputbox upper_text"
                           value="<?php echo $v_license_type_code ?>"
                           onchange="txt_license_type_code_onkeypress(event, 1);"
                           autofocus="autofocus" accesskey="1">
                    <select name="sel_license_type_code" id="sel_license_type"
                            onchange="sel_license_type_onchange(this, 1)">
                        <option value="">-- Mọi loại hồ sơ --</option>
                        <?php echo $this->generate_select_option($arr_all_license_type, $v_license_type_code); ?>
                    </select>
                </div>
            </div>
            <div class="control-group" id="control-group-year">
                <div class="control-label">
                    Lọc theo năm
                </div>
                <div class="controls">
                    <select name="sel_year" id="sel_year">
                        <option value="">-- Mọi năm --</option>
                        <?php echo $this->generate_select_option($arr_years, $sel_year); ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">&nbsp;</label>
                <div class="controls">
                    <button type="button" class="btn btn-primary" onclick="print_report('pdf');"><i class="icon-file-alt"></i> Xuất PDF</button>
                    <button type="button" class="btn btn-primary" onclick="print_report('xls');"><i class="icon-table"></i> Xuất Excel</button>
                </div>
            </div><!--group-->
            <script>
                function print_report(type) {
                    var report_type = $('#sel_report_type').val() || 0;
                    var type_code = $('#sel_license_type').val() || 0;
                    var year = $('#sel_year').val() || 0;

                    var v_url = $('#controller').val() + 'print_report/' + report_type + '-' + type_code + '-' + year + '-' + type;
                    console.log(v_url);
                    showPopWin(v_url, 800, 500);
                }
            </script>
        </form>
    </div>
</div>