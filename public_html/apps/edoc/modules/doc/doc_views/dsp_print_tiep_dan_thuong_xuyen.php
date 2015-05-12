<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}
//display header
$this->template->title = 'Cập nhật văn bản';

$v_pop_win = isset($_REQUEST['pop_win']) ? '_pop_win' : '';
#$this->template->display('dsp_header' . $v_pop_win . '.php');
//------------------------------------------------------------------------------
$v_doc_id               = $VIEW_DATA['doc_id'];
$arr_single_doc         = $VIEW_DATA['arr_single_doc'];
$arr_direction_text     = $VIEW_DATA['arr_direction_text'];
$arr_type_option        = $VIEW_DATA['arr_type_option'];

if (isset($arr_single_doc['PK_DOC']))
{
    $v_doc_id       = $arr_single_doc['PK_DOC'];
    $v_type         = $arr_single_doc['C_TYPE'];
    $v_direction    = $arr_single_doc['C_DIRECTION'];
    $v_xml_data     = $arr_single_doc['C_XML_DATA'];
    $v_is_folded    = $arr_single_doc['C_FOLDED'];

    $dom_xml_data = simplexml_load_string($v_xml_data);
}
else
{
   exit;
}

if (isset($_POST['type']))
{
    $v_type = $_POST['type'];
}
if (isset($_POST['direction']))
{
    $v_direction = $_POST['direction'];
}

$x = $dom_xml_data->xpath("//item[@id='doc_ong_ba'][last()]/value");
$v_ten_cong_dan = $x[0];
$html_string = str_replace('{TEN_CONG_DAN}', $v_ten_cong_dan, $html_string);

$x = $dom_xml_data->xpath("//item[@id='doc_dia_chi'][last()]/value");
$v_dia_chi_cong_dan = $x[0];
$html_string = str_replace('{DIA_CHI_CONG_DAN}', $v_dia_chi_cong_dan, $html_string);

$x = $dom_xml_data->xpath("//item[@id='doc_noi_dung'][last()]/value");
$v_noi_dung_kien_nghi = $x[0];
$html_string = str_replace('{NOI_DUNG_KIEN_NGHI}', $v_noi_dung_kien_nghi, $html_string);

$x = $dom_xml_data->xpath("//item[@id='doc_xu_ly'][last()]/value");
$v_xu_ly = $x[0];
$html_string = str_replace('{NOI_DUNG_XU_LY}', $v_xu_ly, $html_string);
?>
<html>
<head>
    <link rel="stylesheet" href="<?php echo SITE_ROOT;?>public/css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo SITE_ROOT;?>public/css/text.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo SITE_ROOT;?>public/css/printer.css" type="text/css" media="all" />
    <script src="<?php echo SITE_ROOT;?>public/js/jquery/jquery.min.js" type="text/javascript"></script>
    <title>Phiếu tiếp công dân định kỳ</title>
</head>
<body>
	<div class="print-button">
		<input type="button" value="In trang" class="print"
			onclick="window.print(); return false;" /> <input type="button"
			value="Đóng cửa sổ" class="close"
			onclick="window.parent.hidePopWin();return false;" />
	</div>

	<table width="100%" border="0" style="width:100%">
		<tr>
			<td align="center" width="50%">TRỤ SỞ TIẾP CÔNG DÂN <br />TỈNH VĨNH PHÚC
			<br>______________________<br>
			Số:................../PTD
			</td>
			<td align="center">CỘNG HOÀ XÃ HỘI CHỦ NGHĨA VIỆT NAM<br /> Độc
				lập - Tự do - Hạnh phúc <br>______________________<br>
				Vĩnh Phúc, ngày <?php echo Date('d');?> tháng <?php echo Date('m');?> năm <?php echo Date('Y');?>
			</td>
		</tr>
	</table>
	<div class="report-title">PHIẾU TIẾP CÔNG DÂN</div>
	<p>
		Hôm nay, ngày <?php echo Date('d');?>/<?php echo Date('m');?>/<?php echo Date('Y');?>,
		tại Trụ sở Tiếp công dân tỉnh, các ông (bà): ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...  đã tiếp:
		<br />Ông (bà): <?php echo $v_ten_cong_dan?>
		<br />Địa chỉ: <?php echo $v_dia_chi_cong_dan?>
	</p>

	<div id="noi_dung_kien_nghi">
		<label>1. Nội dung KNTC, kiến nghị của công dân:</label>
		<p><?php echo $v_noi_dung_kien_nghi?></p>
	</div>
	<div id="xu_ly_cua_nguoi_chu_tri">
		<label>2. Xử lý của nguời chủ trì:</label>
		<p><?php echo $v_xu_ly?></p>
	</div>
	<table class="signer">
		<tr>
			<td width="50%">&nbsp;</td>
			<td  width="50%" align="center">
			     T/L. NGƯỜI CHỦ TRÌ<br/> TRƯỞNG PHÒNG TIẾP CÔNG DÂN <br/>- VĂN PHÒNG UBND TỈNH
				<br/>
				<br/>
				<br/>
				<br/>
				Hạ Khải Hoàn
			</td>
		</tr>
	</table>
</body>
</html>