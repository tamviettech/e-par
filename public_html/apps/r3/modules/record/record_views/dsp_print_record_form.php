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

<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}

$v_record_type_code = $arr_single_record['C_RECORD_TYPE_CODE'];

$full_path = $this->get_xml_config($v_record_type_code, 'form_struct');
$full_path = str_replace('\\', '/', $full_path);
$v_style_string = '<style>
    textarea,
    input.text
    {
        border-top: 0px;
        border-right: 0px;
        border-left: 0px;        
        border-bottom: 1px dotted;
    }
    .panel_color *
    {
        font-size: 13px;
        padding: 5px 0 0 5px;
        width: 99%;
        height: 20px;
        margin-bottom: 5px;
        font-weight: bold;
        vertical-align: middle;
    }
</style>';
$html = '<html>
    <head>
        <title>In đơn</title>
        <link rel="stylesheet" href="' . SITE_ROOT . 'public/css/reset.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="' . SITE_ROOT . 'public/css/text.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="' . SITE_ROOT . 'public/css/printer.css" type="text/css" media="all" />
        <script src="' . SITE_ROOT . 'public/js/jquery/jquery.min.js" type="text/javascript"></script>
        '. $v_style_string .'
    </head><body contenteditable>';
$html .= '<div class="print-button">
                <input type="button" value="In trang" class="print" onclick="window.print(); return false;" />
                <input type="button" value="Đóng cửa sổ" class="close" onclick="window.parent.hidePopWin();return false;" />
            </div>';

//<!-- header-->
//$dom_unit_info = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
//Thu tuc cap huyen -> lay ten Huyen
if (Session::get('user_level') < 3)
{
    $v_unit_full_name = $this->get_unit_info('full_name');
}
else //Thu tuc cap xa -> lay ten xa
{
    $v_unit_full_name = Session::get('ou_name');
}
$html .= '<table border="0" cellpadding="0" cellspacing="0" width="100%" class="header">
			<tr>
				<td class="unit_full_name">' . $v_unit_full_name . '</td>
				<td align="center">
					<span style="font-size: 12px">
						<strong>CỘNG HOÀ XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong>
					</span>
				</td>
			</tr>
			<tr>
				<td align="center">
					<strong>
						<u style="font-size: 13px">' . str_repeat('&nbsp;', 50) . '</u>
					</strong>
				</td>
				<td align="center">
					<strong>
						<u style="font-size: 10px">Độc lập - Tự do - Hạnh phúc</u>
					</strong>
				</td>
			</tr>
			<tr>
	            <td colspan="2" class="report-title">
	                <span class="title-1">ĐƠN ĐỀ NGHỊ</span><br/>
	                <span class="title-2"><strong>' . $arr_single_record['C_RECORD_TYPE_NAME'] . '</strong></span><br/>
					<span><strong>Mã hồ sơ: </strong>' . $arr_single_record['C_RECORD_NO'] . '</span><br/>
	            </td>
	        </tr>			
		</table>';
//--End header
if (file_exists($full_path)){
    $this->xslt = new Xslt();
    $this->xslt->setXmlString($arr_single_record['C_XML_DATA']);

    $v_xsl_string = file_get_contents(SERVER_ROOT . 'libs' .DS . 'transform_to_print_form.xslt');
   
    $this->xslt->setXslString($v_xsl_string);
    $this->xslt->setParameter(array(
                    'p_site_root'                  => SITE_ROOT
                    ,'p_server_root'               => str_replace('\\', '/', SERVER_ROOT)
                    , 'p_current_date'             => date('d-m-Y')
                    , 'p_form_struct_file_name'    => $full_path
    ));
    if ($this->xslt->transform()) {
        $html .= $this->xslt->getOutput();
        $this->xslt->destroy();
    }
}
$html .= '</body></html>';
echo $html;

/*
// create new PDF document
$v_layout = strtoupper('P');
$pdf      = new ZREPORT($v_layout, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Ngo Duc Lien');

// set header and footer fonts
$pdf->setPrintHeader(0);
$pdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 023', '');
//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, 'B', 16));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', 13));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------
// add a page
$pdf->AddPage($v_layout);

$pdf->SetFont('liennd.times', '', 11);



$pdf->writeHtmlReport($html);
$pdf->lastPage();

//Change To Avoid the PDF Error
@ob_end_clean();
//Close and output PDF document
$v_attach_file_path = 'in_don.pdf';
//$pdf->Output($v_attach_file_path, 'I');
*/
