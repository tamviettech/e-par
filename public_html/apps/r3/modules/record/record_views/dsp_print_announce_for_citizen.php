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
		    <?php $v_dept = 'Bộ phận tiếp nhận và trả hồ sơ';?>
            <!-- header -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="header">
                <tr>
                    <td align="center" class="unit_full_name">
                        <?php echo get_xml_value($dom_unit_info, '/unit/full_name');?><br/>
                        <strong style="font-size: 13px;text-decoration: underline;text-transform: uppercase;"><?php echo $v_dept;?></strong>
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
                        <i><?php echo get_xml_value($dom_unit_info, '/unit/name');?>, ngày <?php echo Date('d-m-Y');?></i>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="report-title">
                        <span class="title-1">GIẤY ĐỀ NGHỊ BỔ SUNG HỒ SƠ</span><br/>
                    </td>
                </tr>
            </table>
            <!-- Message -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td colspan="2" class="center">
                        <b><u>Kính gửi:</u></b><i> Ông/bà: <?php echo $arr_single_record['C_CITIZEN_NAME'];?></i>
                    </td>
                </tr>
                <tr>
                    <td>
                        Ngày <?php echo jwDate::yyyymmdd_to_ddmmyyyy($arr_single_record['C_RECEIVE_DATE']);?>, <?php echo $v_dept;?> đã tiếp nhận hồ sơ "<?php echo $arr_single_record['C_RECORD_TYPE_NAME']; ?>" của Ông/bà.
                        <br/><br/>
                        Sau khi kiểm tra hồ sơ, đối chiếu với quy định pháp luật về "<?php echo $arr_single_record['C_RECORD_TYPE_NAME']; ?>". <?php echo $v_dept;?> đề nghị Ông/bà bổ sung hồ sơ với lý do cụ thể như sau:
                    </td>
                </tr>
                <tr>
                    <td>
                        <i>
                        <?php echo get_xml_value(simplexml_load_string($arr_single_record['C_XML_PROCESSING']), "//step[reason!= ''][last()]/reason");?>
                        </i>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <br/><br/>Vậy, đề nghị Ông/bà bổ sung theo quy định ./.
                    </td>
                </tr>
            </table>    
            <br/><br/><br/>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" >
                <tr>
                    <td class="left" width="70%">
                        <b>Nơi nhận: </b> <br/> - Như trên; <br/> - Bộ phận "Một cửa"; <br/> - Lưu VT;
                    </td>
                    <td class="center">
                        <b>CÁN BỘ TIẾP NHẬN</b>
                    </td>
                </tr>
                <tr>
				<td style="height: 180px; align:center">
				</td>
				<td class="center">
                    <strong><?php echo Session::get('user_name')?></strong>
                </td>
			</tr>	
            </table>
    	</div>
    </body>
</html>