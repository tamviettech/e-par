<?php if (!defined('SERVER_ROOT')) { exit('No direct script access allowed');}


//Data
$v_direction        = $VIEW_DATA['direction'];
$v_type             = $VIEW_DATA['type'];
$v_begin_date       = $VIEW_DATA['begin_date'];
$v_end_date         = $VIEW_DATA['end_date'];

$arr_all_doc_for_print = $VIEW_DATA['arr_all_doc_for_print'];

$arr_all_doc_type_option = $VIEW_DATA['arr_all_doc_type_option'];

$arr_vbden_column_width = array(
    'ngay_den'          => 65,
    'so_den'            => 50,
    'tac_gia'           => 100,
    'so_ky_hieu'        => 65,
    'ngay_thang'        => 65,
    'trich_yeu'         => 240,
    'nguoi_nhan'        => 90,
    'ky_nhan'           => 40,
    'ghi_chu'           => 35,
);
$arr_vbdi_column_width = array(
    'so_ky_hieu'        => 65,
    'ngay_di'           => 65,
    'trich_yeu'         => 240,
    'nguoi_ky'          => 100,
    'noi_nhan'          => 100,
    'noi_luu'           => 90,
    'so_luong_ban'      => 40,
    'ghi_chu'           => 35,
);
//-----------------------------------------------------------------------------

// create new PDF document
$pdf = new ZREPORT(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Ngo Duc Lien');

// set header and footer fonts
$pdf->setPrintHeader(0);
$pdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 023', 'asdadsadsadsa');
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
$pdf->AddPage('LANDSCAPE');

//Form title
//$pdf->company_info();

$pdf->SetFont('liennd.times', '', 16);
$txt = "UBND HUYỆN DIỄN CHÂU\n VĂN PHÒNG";
$pdf->MultiCell(140, 3, $txt, 0, 'C', 0, 0, '', '', true);
$txt = "Cộng hoà xã hội chủ nghĩa Việt Nam \n Độc lập - Tự do - Hạnh phúc";
$pdf->MultiCell(140, 5, $txt, 0, 'C', 0, 0, '', '', true);

$v_title = ($v_direction == 'VBDEN') ? 'SỔ ĐĂNG KÝ VĂN BẢN ĐẾN' : 'SỔ ĐĂNG KÝ VĂN BẢN ĐI';
$pdf->report_title($v_title, $arr_all_doc_type_option[$v_type], '(Từ ngày ' . $v_begin_date . ' đến ngày ' . $v_end_date . ')');

$pdf->SetFont('liennd.times', '', 12);

$html = $v_type;

$v_count_doc = count($arr_all_doc_for_print);
$v_char_count = 0;
$v_page = 1;
$v_is_new_page = FALSE;
$tr = '';
$pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'round', 'dash' => 5, 'color' => array(0, 0, 0)));
if ($v_direction == 'VBDEN')
{
    $v_xml_report_file_path = 'apps/' . $this->app_name . '/' . $this->module_name . '/'
                    . $this->module_name . '_views/xml/xml_' . strtolower($v_type) . '_' . strtolower($v_direction) . '_report.xml';

    if (!file_exists($v_xml_report_file_path))
    {
        $v_first_page_head = '<tr>';
        $v_first_page_head .= '<td width="' . $arr_vbden_column_width['ngay_den'] . '" align="center"><b>Ngày đến</b></td>';
        $v_first_page_head .= '<td width="' . $arr_vbden_column_width['so_den'] . '" align="center"><b>Số đến</b></td>';


        $v_first_page_head .= '<td width="' . $arr_vbden_column_width['tac_gia'] . '" align="center"><b>Tác giả</b></td>';
        $v_first_page_head .= '<td width="' . $arr_vbden_column_width['so_ky_hieu'] . '" align="center"><b>Số, ký hiệu</b></td>';
        $v_first_page_head .= '<td width="' . $arr_vbden_column_width['ngay_thang'] . '" align="center"><b>Ngày tháng</b></td>';
        $v_first_page_head .= '<td width="' . $arr_vbden_column_width['trich_yeu'] . '" align="center"><b>Tên, loại và trích yếu nội dung</b></td>';
        $v_first_page_head .= '<td width="' . $arr_vbden_column_width['nguoi_nhan'] . '" align="center"><b>Đơn vị hoặc <br/>người nhận</b></td>';
        $v_first_page_head .= '<td width="' . $arr_vbden_column_width['ky_nhan'] . '" align="center"><b>Ký nhận</b></td>';
        $v_first_page_head .= '<td width="' . $arr_vbden_column_width['ghi_chu'] . '" align="center"><b>Ghi chú</b></td>';
        $v_first_page_head .= '</tr>';

        $v_cont_page_head = '<tr>';

        $v_cont_page_head .= '<td width="' . $arr_vbden_column_width['ngay_den'] . '" align="center"><i>(1)</i></td>';
        $v_cont_page_head .= '<td width="' . $arr_vbden_column_width['so_den'] . '" align="center"><i>(2)</i></td>';
        $v_cont_page_head .= '<td width="' . $arr_vbden_column_width['tac_gia'] . '" align="center"><i>(3)</i></td>';
        $v_cont_page_head .= '<td width="' . $arr_vbden_column_width['so_ky_hieu'] . '" align="center"><i>(4)</i></td>';
        $v_cont_page_head .= '<td width="' . $arr_vbden_column_width['ngay_thang'] . '" align="center"><i>(5)</i></td>';
        $v_cont_page_head .= '<td width="' . $arr_vbden_column_width['trich_yeu'] . '" align="center"><i>(6)</i></td>';
        $v_cont_page_head .= '<td width="' . $arr_vbden_column_width['nguoi_nhan'] . '" align="center"><i>(7)</i></td>';
        $v_cont_page_head .= '<td width="' . $arr_vbden_column_width['ky_nhan'] . '" align="center"><i>(8)</i></td>';
        $v_cont_page_head .= '<td width="' . $arr_vbden_column_width['ghi_chu'] . '" align="center"><i>(9)</i></td>';
        $v_cont_page_head .= '</tr>';

        $v_thead = '<tr><td colspan="1000"><table border="1" cellpadding="5" cellspacing="0">';
        $v_thead .= $v_first_page_head . $v_cont_page_head;
        $v_thead .= '</table></td></tr>';
        $pdf->set_thead($v_thead);

        $html = '<table class="sms-log" border="1" cellpadding="5" cellspacing="0">'
                    . $v_first_page_head
                    . $v_cont_page_head;

        for ($i=0; $i<$v_count_doc; $i++)
        {

            $v_xml_data = $arr_all_doc_for_print[$i]['C_XML_DATA'];
            $dom_xml_data = simplexml_load_string($v_xml_data);

            $r = $dom_xml_data->xpath("/data/item[@id='doc_ngay_den']/value");
            $v_ngay_den = sizeof($r) ? $r[0] : '';

            $r = $dom_xml_data->xpath("/data/item[@id='doc_so_den']/value");
            $v_so_den = sizeof($r) ? $r[0] : '';

            //$r = $dom_xml_data->xpath("/data/item[@id='doc_nguoi_gui']/value");
            $v_tac_gia = $arr_all_doc_for_print[$i]['DOC_NOI_GUI'];

            $v_so_ky_hieu = $arr_all_doc_for_print[$i]['DOC_SO_KY_HIEU'];

            $r = $dom_xml_data->xpath("/data/item[@id='doc_ngay_van_ban']/value");
            $v_doc_ngay_van_ban = sizeof($r) ? $r[0] : '';

            $v_trich_yeu = $arr_all_doc_for_print[$i]['DOC_TRICH_YEU'];

            $r = $dom_xml_data->xpath("/data/item[@id='doc_noi_nhan']/value");
            $v_doc_noi_nhan = sizeof($r) ? $r[0] : '';

            $html .= '<tr>';


            $html .= '<td width="' . $arr_vbden_column_width['ngay_den'] . '">' . $v_ngay_den . '</td>';
            $html .= '<td width="' . $arr_vbden_column_width['so_den'] . '">' . $v_so_den . '</td>';
            $html .= '<td width="' . $arr_vbden_column_width['tac_gia'] . '">' . $v_tac_gia . '</td>';
            $html .= '<td width="' . $arr_vbden_column_width['so_ky_hieu'] . '">' . $v_so_ky_hieu . '</td>';
            $html .= '<td width="' . $arr_vbden_column_width['ngay_thang'] . '">' . $v_doc_ngay_van_ban . '</td>';
            $html .= '<td width="' . $arr_vbden_column_width['trich_yeu'] . '">' . $v_trich_yeu . '</td>';
            $html .= '<td width="' . $arr_vbden_column_width['nguoi_nhan'] . '">' . $v_doc_noi_nhan . '</td>';
            $html .= '<td width="' . $arr_vbden_column_width['ky_nhan'] . '"></td>';
            $html .= '<td width="' . $arr_vbden_column_width['ghi_chu'] . '"></td>';
            $html .= '</tr>';
        }//end for
    }
    else //Có xml report
    {
        $dom_xml_report = simplexml_load_file($v_xml_report_file_path);

        //The header
        $v_first_page_head     = '<tr>';
        $v_cont_page_head      = '<tr>';
        $cols = $dom_xml_report->xpath("//item");
        $i=1;
        foreach ($cols as $col)
        {
            $v_first_page_head .= '<td width="' . strval($col->attributes()->size) . '" align="center"><b>' . trim($col->attributes()->name) . '&nbsp;</b></td>';
            $v_cont_page_head .= '<td width="' . strval($col->attributes()->size) . '" align="center"><i>(' . $i . ')</i></td>';
            $i++;
        }
        $v_first_page_head .= '</tr>';
        $v_cont_page_head .= '</tr>';

        $v_thead = '<tr><td colspan="1000"><table border="1" cellpadding="5" cellspacing="0">';
        $v_thead .= $v_first_page_head . $v_cont_page_head;
        $v_thead .= '</table></td></tr>';
        $pdf->set_thead($v_thead);

        //The Body
        $html = '<table class="sms-log" border="1" cellpadding="5" cellspacing="0">'
                        . $v_first_page_head
                        . $v_cont_page_head;
        for ($i=0; $i<$v_count_doc; $i++)
        {

            $v_xml_data = $arr_all_doc_for_print[$i]['C_XML_DATA'];
            $dom_xml_data = simplexml_load_string($v_xml_data);

            reset($cols);
            $html .= '<tr>';
            foreach ($cols as $col)
            {
                $v_col_id = str_replace('xml/', '', $col->attributes()->id);
                $r = $dom_xml_data->xpath("/data/item[@id='" . $v_col_id . "']/value");
                $v_col_data = sizeof($r) ? $r[0] : '';
                $html .= '<td width="' . $col->attributes()->size . '">' . $v_col_data . '</td>';
            }
            $html .= '</tr>';
        }//end for


        //$html = '<table class="sms-log" border="1" cellpadding="5" cellspacing="0"><tr><td>Có xml qui dinh sổ</td></tr>';
    }//end if xml-report
}
elseif ($v_direction == 'VBDI')
{
    $arr_vbdi_column_width = array(
        'so_ky_hieu'        => 65,
        'ngay_di'           => 65,
        'trich_yeu'         => 240,
        'nguoi_ky'          => 100,
        'noi_nhan'          => 100,
        'noi_luu'           => 90,
        'so_luong_ban'      => 50,
        'ghi_chu'           => 50,
    );

    $v_first_page_head = '<tr>';

    $v_first_page_head .= '<td width="' . $arr_vbdi_column_width['ngay_di'] . '" align="center"><b>Ngày tháng văn bản</b></td>';
    $v_first_page_head .= '<td width="' . $arr_vbdi_column_width['so_ky_hieu'] . '" align="center"><b>Số, ký hiệu văn bản </b></td>';
    $v_first_page_head .= '<td width="' . $arr_vbdi_column_width['trich_yeu'] . '" align="center"><b>Tên loại và trích yếu nội dung văn bản</b></td>';
    $v_first_page_head .= '<td width="' . $arr_vbdi_column_width['nguoi_ky'] . '" align="center"><b>Người ký</b></td>';
    $v_first_page_head .= '<td width="' . $arr_vbdi_column_width['noi_nhan'] . '" align="center"><b>Nơi nhận văn bản</b></td>';
    $v_first_page_head .= '<td width="' . $arr_vbdi_column_width['noi_luu'] . '" align="center"><b>Đơn vị, người nhận bản lưu</b></td>';
    $v_first_page_head .= '<td width="' . $arr_vbdi_column_width['so_luong_ban'] . '" align="center"><b>Số lượng bản</b></td>';
    $v_first_page_head .= '<td width="' . $arr_vbdi_column_width['ghi_chu'] . '" align="center"><b>Ghi chú</b></td>';
    $v_first_page_head .= '</tr>';

    $v_cont_page_head = '<tr>';

    $v_cont_page_head .= '<td width="' . $arr_vbdi_column_width['ngay_di'] . '" align="center"><i>(1)</i></td>';
    $v_cont_page_head .= '<td width="' . $arr_vbdi_column_width['so_ky_hieu'] . '" align="center"><i>(2)</i></td>';
    $v_cont_page_head .= '<td width="' . $arr_vbdi_column_width['trich_yeu'] . '" align="center"><i>(3)</i></td>';
    $v_cont_page_head .= '<td width="' . $arr_vbdi_column_width['nguoi_ky'] . '" align="center"><i>(4)</i></td>';
    $v_cont_page_head .= '<td width="' . $arr_vbdi_column_width['noi_nhan'] . '" align="center"><i>(5)</i></td>';
    $v_cont_page_head .= '<td width="' . $arr_vbdi_column_width['noi_luu'] . '" align="center"><i>(6)</i></td>';
    $v_cont_page_head .= '<td width="' . $arr_vbdi_column_width['so_luong_ban'] . '" align="center"><i>(7)</i></td>';
    $v_cont_page_head .= '<td width="' . $arr_vbdi_column_width['ghi_chu'] . '" align="center"><i>(8)</i></td>';
    $v_cont_page_head .= '</tr>';

    $v_thead = '<tr><td colspan="1000"><table border="1" cellpadding="5" cellspacing="0">';
    $v_thead .= $v_first_page_head . $v_cont_page_head;
    $v_thead .= '</table></td></tr>';
    $pdf->set_thead($v_thead);

    $html = '<table class="sms-log" border="1" cellpadding="5" cellspacing="0">'
                . $v_first_page_head
                . $v_cont_page_head;

    for ($i=0; $i<$v_count_doc; $i++)
    {

        $v_xml_data = $arr_all_doc_for_print[$i]['C_XML_DATA'];
        $dom_xml_data = simplexml_load_string($v_xml_data);

        $v_so_ky_hieu = $arr_all_doc_for_print[$i]['DOC_SO_KY_HIEU'];

        $r = $dom_xml_data->xpath("/data/item[@id='doc_ngay_van_ban']/value");
        $v_ngay_di = sizeof($r) ? $r[0] : '';

        $v_trich_yeu = $arr_all_doc_for_print[$i]['DOC_TRICH_YEU'];

        $r = $dom_xml_data->xpath("/data/item[@id='doc_nguoi_ky']/value");
        $v_nguoi_ky = sizeof($r) ? $r[0] : '';

        $r = $dom_xml_data->xpath("/data/item[@id='doc_noi_nhan']/value");
        $v_noi_nhan = sizeof($r) ? $r[0] : '';

        $r = $dom_xml_data->xpath("/data/item[@id='doc_noi_luu']/value");
        $v_noi_luu = sizeof($r) ? $r[0] : '';

        $r = $dom_xml_data->xpath("/data/item[@id='doc_so_luong_ban']/value");
        $v_so_luong_ban = sizeof($r) ? $r[0] : '';

        $html .= '<tr>';

        $html .= '<td width="' . $arr_vbdi_column_width['ngay_di'] . '">' . $v_ngay_di . '</td>';
        $html .= '<td width="' . $arr_vbdi_column_width['so_ky_hieu'] . '">' . $v_so_ky_hieu . '</td>';
        $html .= '<td width="' . $arr_vbdi_column_width['trich_yeu'] . '">' . $v_trich_yeu . '</td>';
        $html .= '<td width="' . $arr_vbdi_column_width['nguoi_ky'] . '">' . $v_nguoi_ky . '</td>';
        $html .= '<td width="' . $arr_vbdi_column_width['noi_nhan'] . '">' . $v_noi_nhan . '</td>';
        $html .= '<td width="' . $arr_vbdi_column_width['noi_luu'] . '">' . $v_noi_luu . '</td>';
        $html .= '<td width="' . $arr_vbdi_column_width['so_luong_ban'] . '">' . $v_so_luong_ban . '</td>';
        $html .= '<td width="' . $arr_vbdi_column_width['ghi_chu'] . '"></td>';
        $html .= '</tr>';
    }//end for
} //end $v_direction??


$html .= '</table>';
$pdf->writeHtmlReport($html);
$pdf->lastPage();


//Change To Avoid the PDF Error
ob_end_clean();
//Close and output PDF document
$v_attach_file_path = 'so_' . strtolower($v_direction)
            . '_' . str_replace('/', '.', $v_begin_date)
            . '_den_' . str_replace('/', '.', $v_end_date)
            . '.pdf';
$pdf->Output($v_attach_file_path, 'I');