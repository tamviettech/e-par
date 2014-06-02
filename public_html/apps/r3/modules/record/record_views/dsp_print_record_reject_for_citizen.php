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
<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>In giấy yêu cầu hồ sơ</title>
        <link rel="stylesheet" href="<?php echo SITE_ROOT;?>public/css/reset.css" type="text/css" media="all" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT;?>public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo SITE_ROOT;?>public/css/printer.css" type="text/css" media="all" />
        <script src="<?php echo SITE_ROOT;?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo SITE_ROOT;?>public/js/mylibs.js" type="text/javascript"></script>
    </head>
    <body>
    	<div class="print-button">
    		<input type="button" value="In trang"
    			onclick="window.print();" /> <input type="button"
    			value="Đóng cửa sổ" onclick="window.parent.hidePopWin()" />
    	</div>
    	<div>
		    <?php $dom_unit_info = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');?>
            <!-- header -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="header">
                <tr>
                    <td align="center" class="unit_full_name">
                        <?php echo get_xml_value($dom_unit_info, '/unit/full_name');?><br/>
                        <strong style="font-size: 13px;text-decoration: underline;">
                        Bộ phận tiếp nhận và trả kết quả
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
                    <td colspan="2" class="right italic" style="text-align: right;padding:20px 30px 0px 0px;">
                        <?php echo get_xml_value($dom_unit_info, '/unit/name');?>, ngày <?php echo Date('d-m-Y h:m:i');?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="report-title">
                        <span class="title-1">PHIẾU TỪ CHỐI HỒ SƠ</span><br/>
                    </td>
                </tr>
            </table>
            <!-- Message -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td colspan="2" class="center">
                        <b><u>Kính gửi:</u></b><i> Ông/bà </i><?php echo $arr_single_record['C_CITIZEN_NAME'];?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Sau khi kiểm tra hồ sơ, đối chiếu với quy định pháp luật về "<?php echo $arr_single_record['C_RECORD_TYPE_NAME'];?>". <?php echo get_xml_value($dom_unit_info, '/unit/full_name');?><br/> từ chối xét duyệt các hồ sơ của ông/bà.
                    </td>
                </tr>
                
                <tr>
                    <td><u>Lý do</u>:</td>
                </tr>
                <tr>
                    <td><i>
                        <?php echo $arr_single_record['C_REJECT_REASON'];?></i>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <br/>Vậy, <?php echo get_xml_value($dom_unit_info, '/unit/full_name');?> thông báo cho bộ ông/bà được biết và phối hợp thực hiện.
                    </td>
                </tr>
            </table>    
            <br/><br/><br/>
            <table border="1" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="left" >
                        <b>Nơi nhận: </b> <br/> - Như trên; <br/>- TT UBND TP; <br/> - Bộ phận "Một cửa"; <br/> - Lưu VT;
                    </td>
                    <td style="text-align: center">
                        <b>BỘ PHẬN TIẾP NHẬN VÀ TRẢ KẾT QUẢ</b>
                    </td>
                </tr>
            </table>
    	</div>
    </body>
</html>