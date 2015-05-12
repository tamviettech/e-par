<?php
defined('DS') or die();

/* @var $this \View */
$dom_unit_info = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
$dom_data      = simplexml_load_string($arr_single_record['C_XML_DATA']);
$now           = date_create($now);
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>In giấy bàn giao hồ sơ</title>
        <link rel="stylesheet" href="/lang-giang/public/css/reset.css" type="text/css" media="all" />
        <link rel="stylesheet" href="/lang-giang/public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="/lang-giang/public/css/printer.css" type="text/css" media="all" />
        <script src="/lang-giang/public/js/jquery/jquery.min.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="print-button">
            <input type="button" value="In trang" onclick="window.print();
                    return false;">
            <input type="button" value="Đóng cửa sổ" onclick="window.parent.hidePopWin(false)">
        </div>
        <div>
            <!-- header -->
            <h3><center>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</center></h3>
            <h4>
                <center>Độc lập - Tự do - Hạnh phúc</center>
                <center>___________________________</center>
            </h4>
            <br/>
            <h2><center>ĐƠN XIN RÚT HỒ SƠ</center></h2>
            <p align="center">Kính gửi: <?php echo xpath($dom_unit_info, '//full_name', XPATH_STRING) ?></p>
            <br/>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="">
                <colgroup>
                    <col width="5%">
                    <col width="95%">
                </colgroup>
                <tbody>
                    <tr>
                        <td>1.</td>
                        <td>Thông tin cơ bản:</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="dots"><label>Họ tên người nộp: </label></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="dots"><label>Địa chỉ: </label></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="dots"><label>Số điện thoại: </label></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>2.</td>
                        <td>Hồ sơ xin rút:</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="dots"><label>Mã hồ sơ: <?php echo $arr_single_record['C_RECORD_NO'] ?></label></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="dots">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>3.</td>
                        <td class="dots"><label>Lý do:</label></td>
                    </tr>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="dots">&nbsp;</td>
                    </tr>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="dots">&nbsp;</td>
                    </tr>
                </tbody>
            </table>
            <br/>
            <p align="right">
                <?php echo xpath($dom_unit_info, '//name', XPATH_STRING) ?>,&nbsp;
                ngày <?php echo $now->format('d') ?>&nbsp;
                tháng <?php echo $now->format('m') ?>&nbsp;
                năm <?php echo $now->format('Y') ?>
            </p>
            <br/>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tbl-signer">
                <tbody>
                    <tr>
                        <td>
                            <strong>NGƯỜI NỘP</strong>
                            <br>
                            <i>(Ký, ghi rõ họ tên)</i>
                        </td>
                        <td style="height: 150px; align:center">
                            <strong>CÁN BỘ TIẾP NHẬN</strong>
                            <br>
                            <i>(Ký, ghi rõ họ tên)</i>
                        </td>
                        <td style="height: 150px; align:center">
                            <strong>ĐẠI DIỆN PHÒNG BAN CHUYÊN MÔN</strong><br>
                            <i>(Ký, ghi rõ họ tên)</i>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:33%">
                            <strong></strong>
                        </td>
                        <td style="height: 150px; align:center;width:33%">
                            <strong><span style="text-transform: uppercase;"><?php echo Session::get('user_name') ?></span></strong>
                        </td>
                        <td style="height: 150px; align:center;text-transform: uppercase;width:33%">
                            <strong></strong>
                        </td>
                    </tr>
                </tbody>
            </table>           
        </div>
    </body>
</html>