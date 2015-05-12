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
?>

<div class="main-wrapper" style="margin-left: 0px !important;">
    <div class="container-fluid">
        <ul class="breadcrumb"></ul>
        <?php
        if (isset($arr_single_record['PK_RECORD']))
        {
            $v_record_id = $arr_single_record['PK_RECORD'];
            $v_code = get_post_var('txt_code', $arr_single_record['C_CODE']);
            $v_related_code = get_post_var('txt_related_code', $arr_single_record['C_RELATED_CODE']);
            $v_license_type_code = get_post_var('sel_license_type_code', $arr_single_record['C_LICENSE_TYPE_CODE']);
            $v_license_no = get_post_var('txt_license_no', $arr_single_record['C_LICENSE_NO']);
            $v_citizen_name = get_post_var('txt_citizen_name', $arr_single_record['C_CITIZEN_NAME']);
            $v_return_email = get_post_var('txt_return_email', $arr_single_record['C_RETURN_EMAIL']);
            $v_return_phone_number = get_post_var('txt_return_phone_number', $arr_single_record['C_RETURN_PHONE_NUMBER']);
            $v_issued_date = get_post_var('txt_issued_date', date_format(new DateTime($arr_single_record['C_ISSUED_DATE']), 'd-m-Y'));
            $v_valid_to_date = get_post_var('txt_valid_to_date', ($arr_single_record['C_VALID_TO_DATE'] != "0000-00-00 00:00:00") ? date_format(new DateTime($arr_single_record['C_VALID_TO_DATE']), 'd-m-Y') : '');
            $v_xml_data = get_post_var('XmlData', $arr_single_record['C_XML_DATA']);
            $v_source = $arr_single_record['C_SOURCE'];
        } else
        {
            $v_record_id = "";
            $v_license_type_code = get_post_var('sel_license_type_code', "");
            $v_code = $v_license_type_code.'-'.Suid::encode(date_format(new DateTime(), 'YmdHis'));
            $v_related_code = get_post_var('txt_related_code', "");
            $v_license_no = get_post_var('txt_license_no', "");
            $v_citizen_name = get_post_var('txt_citizen_name', "");
            $v_return_email = get_post_var('txt_return_email', "");
            $v_return_phone_number = get_post_var('txt_return_phone_number', "");
            $v_issued_date = get_post_var('txt_issued_date', date_format(new DateTime(), 'd-m-Y'));
            $v_valid_to_date = get_post_var('txt_valid_to_date', "");
            $v_xml_data = get_post_var('XmlData', '');
            $v_source = 1;
        }
        ?>
        <form name="frmMain" id="frmMain" action="" method="POST" class="form-horizontal" enctype="multipart/form-data"><?php
            echo $this->hidden('controller', $this->get_controller_url());
            echo $this->hidden('hdn_dsp_single_method', 'dsp_single_record');
            echo $this->hidden('hdn_dsp_all_method', 'dsp_all_record');
            echo $this->hidden('hdn_update_method', 'update_record');
            echo $this->hidden('hdn_delete_method', 'delete_record');

            echo $this->hidden('hdn_item_id', $v_record_id);
            echo $this->hidden('hdn_item_id_list', '');
            echo $this->hidden('XmlData', $v_xml_data);
            echo $this->hidden('hdn_deleted_file_id_list', '');

            $this->write_filter_condition(array('txt_filter', 'sel_goto_page', 'sel_rows_per_page'));
            ?>
            <div class="row-fluid">
                <div class="content-widgets light-gray">
                    <div class="widget-head blue">
                        <h3><?php echo 'Cập nhật hồ sơ' ?></h3>
                    </div>
                    <div class="widget-container">
                        <div class="control-group">
                            <label class="control-label" for="record_code">
                                Mã loại hồ sơ
                                <span class="required">(*)</span>
                            </label>
                            <div class="controls">
                                <input style="width: 50px" type="text"
                                       <?php if (isset($arr_single_record['PK_RECORD'])) echo ' disabled '?>
                                       id="txt_license_type_code"
                                       name="txt_license_type_code" size="10" maxlength="10"
                                       class="inputbox upper_text"
                                       value="<?php echo $v_license_type_code; ?>"
                                       onkeypress="txt_license_type_code_onkeypress(event);"
                                       autofocus="autofocus" accesskey="1">
                                <select name="sel_license_type_code" id="sel_license_type"
                                       <?php if (isset($arr_single_record['PK_RECORD'])) echo ' disabled '?>
                                        onchange="sel_license_type_onchange(this)">
                                    <option value="">-- Chọn loại hồ sơ --</option>
                                    <?php echo $this->generate_select_option($arr_all_license_type, $v_license_type_code); ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="txt_code">
                                Mã hồ sơ
                                <span class="required">(*)</span>
                            </label>
                            <div class="controls">
                                <div class="input-prepend span3">
                                    <input type="text" name="txt_code" value="<?php echo $v_code; ?>" id="txt_code"
                                           class="inputbox upper_text" maxlength="50" readonly="true"
                                           onKeyDown="return handleEnter(this, event);"
                                       <?php if (isset($arr_single_record['PK_RECORD'])) echo ' disabled '?>
                                           data-allownull="no" data-validate="text"
                                           data-name="Mã hồ sơ"
                                           data-xml="no" data-doc="no"
                                           autofocus="autofocus"/>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="txt_license_no">
                                Số giấy phép
                                <span class="required">(*)</span>
                            </label>
                            <div class="controls">
                                <input type="text" name="txt_license_no" value="<?php echo $v_license_no; ?>" id="txt_license_no"
                                       class="inputbox span3" maxlength="500"
                                       onKeyDown="return handleEnter(this, event);"
                                       data-allownull="no" data-validate="text"
                                       data-name="Số giấy phép"
                                       data-xml="no" data-doc="no"
                                       autofocus="autofocus"/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="txt_citizen_name">
                                Tên chủ giấy phép
                                <span class="required">(*)</span>
                            </label>
                            <div class="controls">
                                <input type="text" name="txt_citizen_name" value="<?php echo $v_citizen_name; ?>" id="txt_citizen_name"
                                       class="inputbox span3" maxlength="500"
                                       onKeyDown="return handleEnter(this, event);"
                                       data-allownull="no" data-validate="text"
                                       data-name="Tên chủ giấy phép"
                                       data-xml="no" data-doc="no"
                                       autofocus="autofocus"/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="txt_return_email">
                                Email
                            </label>
                            <div class="controls">
                                <input type="text" name="txt_return_email" value="<?php echo $v_return_email; ?>" id="txt_return_email"
                                       class="inputbox span3" maxlength="254"
                                       onKeyDown="return handleEnter(this, event);"
                                       data-allownull="yes" data-validate="email"
                                       data-name="Email"
                                       data-xml="no" data-doc="no"
                                       autofocus="autofocus"/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="txt_return_phone_number">
                                Điện thoại
                            </label>
                            <div class="controls">
                                <input type="text" name="txt_return_phone_number" value="<?php echo $v_return_phone_number; ?>" id="txt_return_phone_number"
                                       class="inputbox span3" maxlength="500" 
                                       onKeyDown="return handleEnter(this, event);"
                                       data-allownull="yes" data-validate="number"
                                       data-name="Điện thoại"
                                       data-xml="no" data-doc="no"
                                       autofocus="autofocus"/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="txt_related_code">
                                Mã hồ sơ liên quan
                            </label>
                            <div class="controls">

                                <input type="text" name="txt_related_code" value="<?php echo $v_related_code; ?>" id="txt_related_code"
                                       class="inputbox span3 upper_text" maxlength="500"
                                       onKeyDown="return handleEnter(this, event);"
                                       data-allownull="yes" data-validate="text"
                                       data-name="Mã hồ sơ liên quan"
                                       data-xml="no" data-doc="no"
                                       autofocus="autofocus"/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="txt_issued_date">
                                Ngày cấp phép
                                <span class="required">(*)</span>
                            </label>
                            <div class="controls">
                                <input type="textbox" id="txt_issued_date"
                                       name="txt_issued_date" class=" text  valid"
                                       value="<?php echo $v_issued_date ?>"
                                       onkeydown="return handleEnter(this, event);"
                                       data-allownull="no" data-validate="date"
                                       data-name="Ngày cấp phép" data-xml="no">
                                <img class="btndate" style="cursor:pointer"id="btnDate"
                                     src="<?php echo SITE_ROOT ?>public/images/calendar.gif"
                                     onclick="DoCal('txt_issued_date')">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="txt_valid_to_date">
                                Ngày hết hạn giấy phép
                            </label>
                            <div class="controls">
                                <input type="textbox" id="txt_valid_to_date"
                                       name="txt_valid_to_date" class=" text  valid"
                                       value="<?php echo $v_valid_to_date ?>"
                                       onkeydown="return handleEnter(this, event);"
                                       data-allownull="yes" data-validate="date"
                                       data-name="Ngày hết hạn giấy phép" data-xml="no">
                                <img class="btndate" style="cursor:pointer"id="btnDate"
                                     src="<?php echo SITE_ROOT ?>public/images/calendar.gif"
                                     onclick="DoCal('txt_valid_to_date')">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="sel_source">
                                Nguồn dữ liệu
                            </label>
                            <div class="controls">
                                <select name="sel_source" id="sel_source" disabled>
                                    <?php
                                    echo $this->generate_select_option(
                                            array('0' => 'Phần mềm một cửa',
                                        '1' => 'Nhập tay')
                                            , $v_source
                                    );
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="sel_source">
                                Tài liệu đính kèm
                            </label>
                            <div class="controls">
                                <?php
                                $arr_accept = explode(',', _CONST_RECORD_FILE_ACCEPT);
                                $class = '';
                                foreach ($arr_accept as $ext)
                                {
                                    $class .= " accept-$ext";
                                }
                                ?>
                                <input type="file"
                                       style="border: solid #D5D5D5; color: #000000"
                                       class="multi <?php echo $class; ?>"
                                       name="uploader[]" id="File1" accept="<?php echo '.' . str_replace(',', ',.', _CONST_RECORD_FILE_ACCEPT) ?>"/> 
                                <font style="font-weight: normal;">Hệ thống chỉ chấp nhận <?php echo _CONST_RECORD_FILE_ACCEPT ?></font><br/>
                                <?php
                                if (isset($VIEW_DATA['arr_all_record_file']))
                                {
                                    $arr_all_record_file = $VIEW_DATA['arr_all_record_file'];
                                    for ($i = 0; $i < sizeof($arr_all_record_file); $i++)
                                    {
                                        $v_file_id = $arr_all_record_file[$i]['PK_MEDIA'];
                                        $v_file_name = $arr_all_record_file[$i]['C_NAME'];
                                        $v_file_path = SITE_ROOT . 'uploads/license/' . $arr_all_record_file[$i]['C_FILE_NAME'];

                                        echo '<span id="file_' . $v_file_id . '">';
                                        echo '<img src="' . SITE_ROOT . 'public/images/trash.png" style="cursor:pointer" onclick="delete_file(' . $v_file_id . ')">&nbsp;';
                                        echo '<a href="' . $v_file_path . '" target="_blank">' . $v_file_name . '</a><br/>';
                                        echo '</span>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <?php if (($v_record_id !== "" && check_permission("SUA_HO_SO", "LICENSE")) || ($v_record_id == "" && check_permission("THEM_MOI_HO_SO", "LICENSE"))):
                        ?>
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
                <div id="record_detail">
                    <div id="xml_part">
                        <?php echo $this->transform($this->get_xml_config($v_license_type_code, 'form_struct')); ?>
                    </div>
                </div>
                <!-- Button -->
                <div class="form-actions">
                    <?php if (($v_record_id !== "" && check_permission("SUA_HO_SO", "LICENSE")) || ($v_record_id == "" && check_permission("THEM_MOI_HO_SO", "LICENSE"))):
                        ?>
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
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        //Fill data
        var formHelper = new DynamicFormHelper('', '', document.frmMain);
        formHelper.BindXmlData();

        //try{$("#txtName").focus();}catch (e){;}

    });

    function delete_file(id)
    {
        var f = document.frmMain;
        s = document.getElementById('file_' + id);
        s.style.display = "none";

        f.hdn_deleted_file_id_list.value = $("#hdn_deleted_file_id_list").val() + ',' + id;
    }

</script>