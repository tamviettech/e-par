<?php
/**
Copyright (C) 2012 Tam Viet Tech.

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
/* @var $this \View */
defined('SERVER_ROOT') or die();
$VIEW_DATA['dom_data']      = $dom_data                   = simplexml_load_string($arr_single_record['C_XML_DATA']);
$VIEW_DATA['dom_unit_info'] = $dom_unit_info              = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
$now                        = date_create($now);
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Thông báo thực hiện nghĩa vụ tài chính</title>
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/reset.css" type="text/css" media="all" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>public/css/printer.css" type="text/css" media="all" />
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
    </head>
    <body contenteditable>
        <div class="print-button">
            <input type="button" value="In trang" onclick="window.print();
                    return false;" />
            <input type="button" value="Đóng cửa sổ" onclick="window.parent.hidePopWin()" />
        </div>
        <div>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="header">
                <tr>
                    <td align="center" class="unit_full_name">
                        <?php echo get_xml_value($dom_unit_info, '/unit/full_name'); ?><br/>
                        <strong>
                            <u style="font-size: 13px;text-transform: uppercase;">
                                <?php echo Session::get('ou_name') ?>
                            </u>
                        </strong>
                    </td>
                    <td align="center">
                        <span style="font-size: 12px">
                            <strong>CỘNG HOÀ XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong>
                        </span>
                        <br/>
                        <strong>
                            <u style="font-size: 10px">Độc lập - Tự do - Hạnh phúc</u>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:right">
                        <span style="text-transform: none">
                            <?php echo xpath($dom_unit_info, "//name", XPATH_STRING) ?>,
                            &nbsp;ngày <?php echo $now->format('d') ?>
                            &nbsp;tháng <?php echo $now->format('m') ?>
                            &nbsp;năm <?php echo $now->format('Y') ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="report-title">
                        <span class="title-1">GIẤY HẸN</span><br/>
                        <h5>Về việc thông báo thực hiện nghĩa vụ tài chính</h5>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: left;">
                        <p>
                            <?php echo Session::get('ou_name'); ?>
                            &nbsp;-&nbsp;
                            <?php echo xpath($dom_unit_info, '//full_name', XPATH_STRING) ?> 
                            mời Ông/Bà đến trụ sở Uỷ ban vào lúc
                            &nbsp;<b><?php echo date_create($appointment)->format('d-m-Y H:i') ?></b>
                            &nbsp;để nhận thông báo về việc thực hiện nghĩa vụ tài chính đối với hồ sơ
                            &nbsp;<b><?php echo $arr_single_record['C_RECORD_NO'] ?></b>:
                        </p>
                        <p>
                            <b>1.</b> Tên người sử dụng đất:
                            <b><?php echo xpath($dom_data, "//item[@id='txtName']/value", XPATH_STRING) ?></b>
                        </p>
                        <p> 
                            <b>2.</b> Thửa đất số:    <?php echo xpath($dom_data, "//item[@id='txtPlaceNo']/value", XPATH_STRING) ?>
                            <?php echo str_repeat('&nbsp;', 32) ?>
                            thuộc tờ bản đồ số:   <?php echo xpath($dom_data, "//item[@id='txtMapNo']/value", XPATH_STRING) ?>
                        </p>
                        <p> 
                            <b>3.</b> Địa chỉ thửa đất:   <?php echo xpath($dom_data, "//item[@id='txtPlaceAdd']/value", XPATH_STRING) ?>
                            <?php echo str_repeat('&nbsp;', 32) ?>
                            diện tích:
                            <b><?php echo xpath($dom_data, "//item[@id='txtArea']/value", XPATH_STRING) ?>m2</b>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
