<?php 
/**
// File name   : 
// Version     : 1.0.0.1
// Begin       : 2012-12-01
// Last Update : 2010-12-25
// Author      : TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
//Copyright (C) 2012-2013  TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn

// E-PAR is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// E-PAR is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// See LICENSE.TXT file for more information.
*/
?>
<?php if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
} ?>
<!DOCTYPE html>
<html lang="vi" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache" />
        <link rel="shortcut icon" href="favicon.ico" />
        <title>Nộp hồ sơ qua mạng internet</title>
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/1008_24_1_1.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo $this->template->stylesheet_url; ?>" type="text/css" media="screen" />
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.min.js" type="text/javascript"></script>
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css" />
        <script src="<?php echo SITE_ROOT; ?>public/js/mylibs.js" type="text/javascript"></script>

        <!--  Datepicker -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.ui.datepicker-vi.js" type="text/javascript"></script>
        <!-- Upload -->
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MultiFile.pack.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.MetaData.js" type="text/javascript"></script>

        <script src="<?php echo SITE_ROOT; ?>public/js/DynamicFormHelper.js" type="text/javascript"></script>

        <style type="text/css">

        </style>
    </head>
    <body>
        <DIV id="overDiv" style="Z-INDEX: 10000; VISIBILITY: hidden; POSITION: absolute"></DIV>
        <div class="container_24" id="main">
            <div class="grid_24" id="banner">
				<label id="unit_full_name"><?php echo get_xml_value(simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml'), '//full_name')?></label>
			</div>
            <div class="grid_24 top-nav-box" id="header">
                <div id="date"><?php echo jwDate::vn_day_of_week() . ', ' . date("d/m/Y"); ?></div>
            </div>
            <div class="clear">&nbsp;</div>
            <div class="container_24" id="wrapper">
                <div class="grid_5">
                    <div class="edit-box" id="left_side_bar">
                        <div style="width: 96%; padding: 4px;">

                            <div class="menuLeft" id="menuLeft">
                                <ul class="menu">
                                    <li><a href="http://langgiang.gov.vn" target="_blank">Trang thông tin điện tử huyện Lạng Giang</a></li>
                                    <li><a href="<?php echo SITE_ROOT;?>mavach">Tra cứu hồ sơ </a></li>
                                    <li>Gửi hồ sơ qua mạng</li>
                                    <?php foreach ($arr_all_record_type_option as $id => $name): ?>
                                        <li class="item<?php echo ($active_record_type_id == $id) ? ' active': '';?>">
                                            <a href="<?php echo SITE_ROOT; ?>nop_ho_so/nhap_thong_tin/<?php echo $id; ?>"><?php echo $name; ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid_19" id="content_right">
                    <?php if ($active_record_type_id > 0): ?>
                        <?php if (isset($_POST["recaptcha_response_field"])): ?>
                            <?php $resp = recaptcha_check_answer (_CONST_RECAPCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);?>
                            <?php if ($resp->is_valid): ?>
                                <?php $_SESSION['captcha_passed'] = 1;?>
                                <?php $v_record_no = $v_record_type_code . '-' . strtoupper(base_convert(preg_replace('[\D]', '', Date('ymdHis')), 10, 16));?>
                                    <form name="frmMain" id="frmMain" action="" method="POST" enctype="multipart/form-data">
                                        <?php echo $this->hidden('hdn_record_type_id', $active_record_type_id); ?>
                                        <?php echo $this->hidden('hdn_update_method','do_send');?>
                                        <?php echo $this->hidden('controller',SITE_ROOT . 'nop_ho_so/');?>
                                        <?php echo $this->hidden('XmlData','<data/>');?>

                                        <div class="page-title">Nộp hồ sơ qua mạng Internet</div>
                                        <div class="panel_color">Thông tin chung</div>

                                        <table style="width: 100%;" class="none-border-table adminform">
                                            <tr>
                                                <td width="20%"><label>Loại hồ sơ</label></td>
                                                <td colspan="3">
                                                    <select name="sel_record_type" id="sel_record_type" style="width: 77%; color: #000000;"
                                                            data-validate="text" data-name="Loại hồ sơ" data-xml="no"
                                                            data-doc="no" disabled
                                                    >
                                                        <?php echo $this->generate_select_option($arr_all_record_type_option, $active_record_type_id); ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Mã hồ sơ: <span class="required">(*)</span></td>
                                                <td><input readonly="readonly" name="txt_record_no"
                                                           id="txt_record_no" maxlength="50" style="width: 200px" type="text"
                                                           value="<?php echo $v_record_no; ?>" data-allownull="no"
                                                           data-validate="text" data-name="M&atilde; h&#7891; s&#417;"
                                                           data-xml="no" data-doc="no" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Số điện thoại: <span class="required">(*)</span></td>
                                                <td><input name="txt_return_phone_number"
                                                           id="txt_return_phone_number" maxlength="20" style="width: 200px"
                                                           type="text" value=""
                                                           data-allownull="no" data-validate="phone"
                                                           data-name="Số điện thoại"
                                                           data-xml="no" data-doc="no" />
                                                </td>
                                                <td>Email: <span class="required">(*)</span></td>
                                                <td><input name="txt_return_email" id="txt_return_email"
                                                    maxlength="255" style="width: 200px" type="text"
                                                    value="" data-allownull="no"
                                                    data-validate="email" data-name="Địa chỉ email" data-xml="no"
                                                    data-doc="no" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Ghi chú:
                                                </td>
                                                <td colspan="3">
                                                    <textarea style="width:540px;height:40px" rows="2" name="tbxNote" maxlength="2000" id="tbxNote" cols="20"></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>File đính kèm:</td>
                                                <td colspan="3">
                                                    <input type="file" class="multi accept-<?php echo _CONST_RECORD_FILE_ACCEPT; ?>" name="uploader[]" id="File1" />
                                                    <span class="fileUploaderMessage">Hệ thống chỉ chấp nhận file dạng: <?php echo str_replace('|', '; ', _CONST_RECORD_FILE_ACCEPT); ?></span><br />
                                                </td>
                                            </tr>
                                        </table>

                                        <div class="clear">&nbsp;</div>
                                        <!--<div id="detail" style="display: none">-->
                                        <div id="detail">
                                            <div id="xml_part">
                                                <?php echo $this->transform($this->get_xml_config($v_record_type_code, 'form_struct')); ?>
                                            </div>
                                            <div class="button-area">
                                                <input type='button' value='Gửi hồ sơ đăng ký' onclick="btn_gui_hs_onclick()"/>
                                                <input type='button' value='Quay lại' onclick="history.go(-1);"/>
                                            </div>
                                        </div>
                                        <?php unset($_POST["recaptcha_response_field"]);?>
                                    </form>
                            <?php else: ?>
                                <?php dsp_btnContinue();?>
                            <?php endif;?>
                        <?php else: ?>
                            <?php dsp_btnContinue();?>
                        <?php endif ;?>

                        <script>
                            function btn_gui_hs_onclick()
                            {
                                btn_update_onclick();
                            }
                        </script>
                    <?php endif; ?>
                </div>
                <!-- #content_right-->
            </div>
            <!-- .container_24 #wrapper -->
            <div class="clear">&nbsp;</div>
            <div class="grid_24">
                <div id="footer">
                    <hr />
                    R3 - Phần mềm hỗ trợ giải quyết thủ tục hành chính theo cơ chế một cửa </br>
                </div>
            </div>
            <div class="clear">&nbsp;</div>
        </div>
        <!-- class="container_24" #main -->
    </body>
</html>
<?php
function dsp_btnContinue()
{
    ?>
    <form name="frmMain" id="frmMain" action="" method="POST">
        <div class="panel_color">Mã xác nhận</div>
        <div>
            <?php echo recaptcha_get_html(_CONST_RECAPCHA_PUBLIC_KEY);?>
        </div>
        <div class="button-area" id="btnContinue">
            <input type="button" class="button document_next" value="Tiếp tục" onclick="this.form.submit()">
        </div>
    </form><?php
}
?>