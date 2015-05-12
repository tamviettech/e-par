<?php

if (!defined('SERVER_ROOT'))
{
    exit('No direct script access allowed');
}
error_reporting(0);

//Cau hinh phan ky
$v_xml_signer_file_path = CONST_APPS_DIR . $this->app_name . DS . 'modules' . DS . $this->module_name . DS
        . $this->module_name . '_views' . DS . 'xml' . DS . $VIEW_DATA['report_code'] . '_signer.xml';
if (!file_exists($v_xml_signer_file_path))
{
    $v_xml_signer_file_path = CONST_APPS_DIR . $this->app_name . DS . 'modules' . DS . $this->module_name . DS
            . $this->module_name . '_views' . DS . 'xml' . DS . 'report_signer.xml';
}

//Cau hinh Phan than bao cao
$v_xml_report_file_path = CONST_APPS_DIR . $this->app_name . DS . 'modules' . DS . $this->module_name . DS
        . $this->module_name . '_views' . DS . 'xml' . DS . $VIEW_DATA['report_code'] . '.xml';
if (!file_exists($v_xml_report_file_path))
{
    die('Chưa cấu hình xml báo cáo cho mẫu này!');
}
$dom_xml_report = simplexml_load_file($v_xml_report_file_path);

include SERVER_ROOT . 'libs' . DS . 'tcpdf' . DS . 'config' . DS . 'lang' . DS . 'vn.php';

//VIEW_DATA
$v_count_report_rows = count($arr_all_report_data);

// create new PDF document
$v_layout = strtoupper(get_xml_value($dom_xml_report, '/report/@layout')) == 'P' ? 'P' : 'L';
$dom_unit_info = simplexml_load_file('public/xml/xml_unit_info.xml');

if (Session::get('la_can_bo_cap_xa'))
    $v_unit_name = Session::get('ou_name');
else
    $v_unit_name = xpath($dom_unit_info, '//full_name', XPATH_STRING);

$v_unit_name = mb_strtoupper(str_ireplace('UBND', 'Uỷ ban nhân dân', $v_unit_name), 'UTF-8');
$v_unit_short_name = get_xml_value($dom_unit_info, '/unit/name');

//INIT SUBTOTAL
$v_need_calc_subtotal = FALSE;
$subtotals            = $dom_xml_report->xpath('//sub_total/@group');
if (sizeof($subtotals) > 0)
{
    $v_need_calc_subtotal = TRUE;
    $v_group_by_id        = get_xml_value($dom_xml_report, '//sub_total/@group');
    $v_group_by_text      = get_xml_value($dom_xml_report, '//sub_total/@text');
}

//INIT TOTAL
$totals = $dom_xml_report->xpath('//total/item');
foreach ($totals as $total)
{
    if (isset($total->attributes()->id))
    {
        $id  = str_replace('xml/', '', $total->attributes()->id);
        $$id = 0;
    }
}

//Create HTML string
//CSS
//$html              = get_xml_value(simplexml_load_file($v_xml_signer_file_path), '//css');
//The header
$v_first_page_head = '<tr>'; //header cho trang dau tien
$v_cont_page_head  = '<tr>'; //header cho cac trang tiep theo
$cols              = $dom_xml_report->xpath("//list/item");
$v_total_column    = count($cols);
$i                 = 1;
foreach ($cols as $col)
{
    $v_first_page_head .= '<td width="' . strval($col->attributes()->size) . '" align="center"><b>' . str_replace('[br]', '<br/>', trim($col->attributes()->name)) . '</b></td>';
    $v_cont_page_head .= '<td width="' . strval($col->attributes()->size) . '" align="center" class="sub-header"><i>(' . $i . ')</i></td>';
    $i++;
}
$v_first_page_head .= '</tr>';
$v_cont_page_head .= '</tr>';

$v_thead = '<tr><td colspan="1000"><table border="1" cellpadding="5" cellspacing="0">';
$v_thead .= $v_first_page_head . $v_cont_page_head;
//$v_thead .= $v_cont_page_head;
$v_thead .= '</table></td></tr>';


//The Body
$html .= '<table class="report_list" border="1" cellpadding="4" cellspacing="0">' . $v_first_page_head . $v_cont_page_head;
for ($i = 0; $i < $v_count_report_rows; $i++)
{
    $v_xml_data   = isset($arr_all_report_data[$i]['C_XML_DATA']) ? $arr_all_report_data[$i]['C_XML_DATA'] : '<root/>';
    $dom_xml_data = simplexml_load_string($v_xml_data);

    $v_xml_processing = isset($arr_all_report_data[$i]['C_XML_PROCESSING']) ? $arr_all_report_data[$i]['C_XML_PROCESSING'] : '<root/>';
    $dom_processing   = @simplexml_load_string($v_xml_processing);


    if ($v_need_calc_subtotal)
    {
        $subtotals_items = $dom_xml_report->xpath('//sub_total/item');

        $v_prev_group_by_value = ($i > 0) ? $arr_all_report_data[$i - 1][$v_group_by_id] : '';
        $v_group_by_value      = $arr_all_report_data[$i][$v_group_by_id];

        if ($v_group_by_value != $v_prev_group_by_value)
        {
            reset($subtotals_items);
            foreach ($subtotals_items as $item)
            {
                $id     = str_replace('xml/', '', $item->attributes()->id);
                $subid  = $v_group_by_value . $id;
                $$subid = 0;
            }

            $html .= '<tr>';
            $html .= '<td colspan="1000" class="group_name">' . $arr_all_report_data[$i][$v_group_by_text] . '</td>';
            $html .= '</tr>';
        }
    }

    $html .= '<tr nobr="true">';
    reset($cols);
    foreach ($cols as $col)
    {
        //Cell data
        $v_col_id = strval($col->attributes()->id);
        $v_align  = isset($col->attributes()->align) ? ' align="' . $col->attributes()->align . '"' : '';

        if ($v_col_id == 'RN')
        {
            $html .= '<td width="' . $col->attributes()->size . '"' . $v_align . '>' . strval($i + 1) . '</td>';
        }
        else
        {
            if (strpos($v_col_id, 'xml/') !== FALSE) //Cot du lieu nam trong XML
            {
                $v_col_id   = str_replace('xml/', '', $v_col_id);
                $r          = $dom_xml_data->xpath("/data/item[@id='" . $v_col_id . "']/value");
                $v_col_data = sizeof($r) ? $r[0] : '';
            }
            elseif (strpos($v_col_id, 'xml_processing/') !== FALSE) //Cot du lieu nam trong XML Processing
            {
                
            }
            else //Cot tuong minh
            {
                $v_col_data = isset($arr_all_report_data[$i][$v_col_id]) ? $arr_all_report_data[$i][$v_col_id] : '';

                if ($v_col_id == 'C_RECEIVE_DATE')
                {
                    $v_col_data = $this->break_date_string(jwDate::yyyymmdd_to_ddmmyyyy($v_col_data, 1));
                }
                elseif ($v_col_id == 'C_RETURN_DATE')
                {
                    $v_col_data = $this->break_date_string($this->return_date_by_text($v_col_data));
                }
            }

            //format number ??
            if (isset($col->attributes()->number_format) && parse_boolean($col->attributes()->number_format))
            {
                $html .= '<td width="' . $col->attributes()->size . '"' . $v_align . '>' . number_format($v_col_data, 0, ',', '.') . '</td>';
            }
            else
            {
                $html .= '<td width="' . $col->attributes()->size . '"' . $v_align . '>' . strval($v_col_data) . '</td>';
            }

            //SUBTOTAL ??
            if ($v_need_calc_subtotal)
            {
                //TOTAL ???
                $v_subtotal_id = get_xml_value($dom_xml_report, "//sub_total/item[@id='$v_col_id']/@id");
                if ($v_subtotal_id == $v_col_id)
                {
                    $v_subtotal_id = $v_group_by_value . $v_col_id;
                    $$v_subtotal_id += floatval($v_col_data);
                }
            }

            //TOTAL ???
            $r = $dom_xml_report->xpath("//total/item[@id='$v_col_id']/@id");
            if (sizeof($r) > 0)
            {
                $$v_col_id += floatval($v_col_data);
            }
        }//end if ($v_col_id == 'RN')
    }//end foreach $cols
    $html .= '</tr>';

    //Neu dong sau chuyen sang group khac, ghi subtotal
    if ($v_need_calc_subtotal)
    {
        $v_next_group_by_value = ($i < ($v_count_report_rows - 1)) ? $arr_all_report_data[$i + 1][$v_group_by_id] : '';
        if ($v_group_by_value != $v_next_group_by_value)
        {
            reset($subtotals_items);
            $html .= '<tr class="subtotal">';
            $v_used_td = 0;
            foreach ($subtotals_items as $subtotal)
            {
                $colspan = isset($subtotal->attributes()->colspan) ? ' colspan="' . strval($subtotal->attributes()->colspan) . '"' : '1';
                $align   = isset($subtotal->attributes()->align) ? ' align="' . strval($subtotal->attributes()->align) . '"' : '';
                $v_used_td += isset($subtotal->attributes()->colspan) ? intval($subtotal->attributes()->colspan) : 1;

                if (isset($subtotal->attributes()->id))
                {
                    $v_subtotal_id = $v_group_by_value . strval($subtotal->attributes()->id);
                    $v_cell_data   = $$v_subtotal_id;
                }
                else
                {
                    $v_cell_data = isset($subtotal->attributes()->name) ? strval($subtotal->attributes()->name) : '';
                }

                //format number ??
                if (isset($subtotal->attributes()->number_format) && parse_boolean($subtotal->attributes()->number_format))
                {
                    $html .= '<td ' . $colspan . $align . '>' . number_format($v_cell_data, 0, ',', '.') . '</td>';
                }
                else
                {
                    $html .= '<td ' . $colspan . $align . '>' . $v_cell_data . '</td>';
                }
            } //end foreach $subtotals_items
            for ($jj = $v_used_td + 1; $jj <= $v_total_column; $jj++)
            {
                $html .= '<td></td>';
            }
            $html .= '</tr>';
        }
    }
}//end for $i
//Total
if (sizeof($totals) > 0)
{
    $html .= '<tr class="summary" nobr="true">';
    reset($totals);
    foreach ($totals as $total)
    {
        $colspan = isset($total->attributes()->colspan) ? ' colspan="' . strval($total->attributes()->colspan) . '"' : '';
        $align   = isset($total->attributes()->align) ? ' align="' . strval($total->attributes()->align) . '"' : '';

        if (isset($total->attributes()->id))
        {
            $id          = strval($total->attributes()->id);
            $v_cell_data = $$id;
        }
        else
        {
            $v_cell_data = isset($total->attributes()->name) ? strval($total->attributes()->name) : '';
        }
        //format_number??
        if (isset($total->attributes()->number_format) && parse_boolean($total->attributes()->number_format))
        {
            $html .= '<td ' . $colspan . $align . '>' . number_format($v_cell_data, 0, ',', '.') . '</td>';
        }
        else
        {
            $html .= '<td ' . $colspan . $align . '>' . $v_cell_data . '</td>';
        }
    } //end foreach $totals
    $html .= '</tr>';
}
$html .= '</table>';

//Chu ky
$html .= get_xml_value(simplexml_load_file($v_xml_signer_file_path), '//item');

$v_format = get_post_var('hdn_format','');
switch ($v_format)
{
    case 'excel':
        require_once (dirname(__FILE__) . '/../R3_PHPExcel_Reader_HTML.class.php');
        $objHTML = new R3_PHPExcel_Reader_HTML();
        $v_totals = 0;
        if(sizeof($totals) > 0)
        {
            $v_totals = 1;
        }
        $v_latest_row = 6 + count($arr_all_report_data) + $v_totals;
        $v_latest_col = $objHTML->arr_excel_column[count($cols) -1 ];
                
        //$html = str_replace('&', '&amp;', $html);
        $v_html_file_name = SERVER_ROOT . 'cache/' . uniqid() . '.html';  

		$html = '<?xml encoding="UTF-8"><html><head><meta http-equiv="Content-type" content="text/html; charset=UTF-8"/></head><body>' . $html . '</body></html>';
        file_put_contents($v_html_file_name, unicode_to_composite($html));
                
        $objPHPExcel = new PHPExcel();
        // Set properties
        $excel_report_title = 'report_' . $VIEW_DATA['report_code'];
        $objPHPExcel->getProperties()->setCreator("Quang Tran");
        $objPHPExcel->getProperties()->setLastModifiedBy("Quang Tran");
        $objPHPExcel->getProperties()->setTitle($excel_report_title);
        $objPHPExcel->getProperties()->setSubject($excel_report_title);
        $objPHPExcel->getProperties()->setDescription($excel_report_title);
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(9);
        
        //set sheet insert
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        
        $mysheet = $objPHPExcel->getActiveSheet();  
        $mysheet->getStyle()->getAlignment()->setWrapText(true);
        $mysheet->getRowDimension()->setRowHeight(73);
        
        $v_haft_cols = floor (count($cols) / 2);

        $mysheet->SetCellValue('A1', $v_unit_name. "\n__________");
        $mysheet->mergeCells("A1:{$objHTML->arr_excel_column[$v_haft_cols - 1]}1");
        $mysheet->getStyle("A1")->getAlignment()->setWrapText(true);
        $mysheet->getStyle('A1')
                                ->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $mysheet->getStyle('A1')
                                    ->getAlignment()
                                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $v_haft_cols_symbol = $objHTML->arr_excel_column[$v_haft_cols];
        $v_latest_cols_symbol = $objHTML->arr_excel_column[count($cols) -1 ];

        $mysheet->SetCellValue("{$v_haft_cols_symbol}1", "Cộng hoà xã hội chủ nghĩa Việt Nam\nĐộc lập - Tự do -Hạnh phúc");
        $mysheet->getStyle("{$v_haft_cols_symbol}1")->getAlignment()->setWrapText(true);
        $mysheet->mergeCells("{$v_haft_cols_symbol}1:{$v_latest_cols_symbol}1");
        $mysheet->getStyle("{$v_haft_cols_symbol}1")
                                ->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $mysheet->getStyle("{$v_haft_cols_symbol}1")
                                    ->getAlignment()
                                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        
        $mysheet->SetCellValue("A2", $report_title . "\n" .$report_subtitle);
        $mysheet->mergeCells("A2:{$v_latest_col}2");
        $mysheet->getStyle("A2")->getAlignment()->setWrapText(true);
        $mysheet->getStyle('A2')
                                    ->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $mysheet->getStyle('A2')
                                    ->getAlignment()
                                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $mysheet->getRowDimension('2')->setRowHeight(40);
        //set width cho column
        $i=0;
        foreach($cols as $col)
        {
            $v_col_width   = (int) strval($col->attributes()->excel_width);
            $mysheet->getColumnDimension($objHTML->arr_excel_column[$i])->setWidth($v_col_width);//stt
            $i++;
        }
        
        $objHTML->loadIntoExisting($v_html_file_name, $objPHPExcel,4);
        
        //Set border style
        
        $mysheet->getStyle("A5:{$v_latest_col}5")->applyFromArray($objHTML->styleArrayForHeader);
        $mysheet->getStyle("A6:{$v_latest_col}{$v_latest_row}")->applyFromArray($objHTML->styleArrayForData);
        //$cols
               
        $url_save_file = str_replace('.html', '.xls', $v_html_file_name);
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save($url_save_file);
        
        if (file_exists($url_save_file)) 
        {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($url_save_file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($url_save_file));
            ob_clean();
            flush();
            readfile($url_save_file);
        }
                
        unlink($v_html_file_name);
        unlink($url_save_file);
        break;
    case 'doc':
        $date = Date('d-m-Y');
        //lay html 
        $html = get_xml_value(simplexml_load_file($v_xml_signer_file_path), '//css'). '
                <style>
                    *{
                        font-family:"Time New Roman";
                        font-size: 16px; 
                    }
                    table
                    {
                        width: 100%;
                    }
                    .report_list
                    {
                        width: 100%;
                        border: 2px solid #0D0D0D;
                    }

                    .report_list th,.report_list tr,.report_list td
                    {
                        border: 2px solid #0D0D0D;
                    }

                    .report_list td
                    {
                        font-size: 14px;
                        font-weight: 700;
                    }
                    .report_list b
                    {
                        font-size: 16px;
                    }
                    .report_list .sub-header
                    {
                        font-weight: bold;
                        font-size: 16px;
                    }
                    table.signer
                    {
                        width: 100%;
                        border: 0px;
                    }
                    table.signer td,th,tr
                    {
                        border: 0px;
                    }
                    .reprot_header .item
                    {
                        text-align: center;
                        width: 50%;
                        font-weight: bold;
                        font-size: 16px;
                    }
                    .reprot_header .date
                    {
                        font-style: italic;
                        text-align: center;
                        width: 50%;
                        font-weight: normal;
                        font-size: 16px;
                    }
                    .header
                    {
                        width: 100%;
                        text-align: center;
                        font-weight: bold;
                        font-size: 16px;
                        margin: 15px 0px 10px 0px;

                    }
                    .formPrint
                    {
                        position: fixed;
                        top: 0px;
                        left: 0px;
                    }
                    @media print
                    {
                        .formPrint
                        {
                            display: none;
                        }
                        @page
                        {
                            margin: 10px;
                        }
                    }
                </style>
                <table class="reprot_header">
                    <tr>
                        <td class="item">
                            <b>
                                 '.$v_unit_name.'
                                <br/>_________
                        </b>
                        </td>
                        <td class="item">
                            CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM <br> Độc lập - Tự do - Hạnh phúc <br> _________
                        </td>
                    </tr>
                    <tr>
                        <td class="item">
                            &nbsp;
                        </td>
                        <td class="date">
                            <i>
                                '. 'Ngày ' . Date('d', strtotime($date)) . ' tháng ' . Date('m', strtotime($date)) . ' năm ' . Date('Y', strtotime($date)).'
                            </i>
                        </td>
                    </tr>
                </table>
                <div class="header">
                        '. $report_title . '<br>' .$report_subtitle.'
                </div>' . $html;
        $html = html_entity_decode($html);
        
        //include thu vien html_to_doc
        $library_dir = SERVER_ROOT . 'libs/html_to_doc.inc.php';
        include($library_dir);
        
        //tao doi tuong 
        $htmltodoc = new HTML_TO_DOC();
        
        //Thiep lap ten file default
        $file_name_save = 'report_' . $VIEW_DATA['report_code'];
        //tao file doc
        $htmltodoc->createDoc($html,$file_name_save,true);
        break;
    case 'pdf':
        $pdf      = new ZREPORT($v_layout, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Ngo Duc Lien');

        // set header and footer fonts
        $pdf->setPrintHeader(0);
        $pdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 023', '');
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

        $pdf->SetFont('liennd.times', '', 16);
        $slogan = 'Độc lập - Tự do - Hạnh phúc';
        $txt    = mb_strtoupper("CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM", 'UTF-8') . "\n$slogan\n" . str_repeat('_', mb_strlen($slogan, 'UTF-8'));

        if ($v_layout == 'L')
        {
            $pdf->MultiCell(140, 3, $v_unit_name, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(140, 5, $txt, 0, 'C', 0, 0, '', '', true);
        }
        else
        {
            $pdf->MultiCell(100, 3, $v_unit_name, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(100, 5, $txt, 0, 'C', 0, 0, '', '', true);
        }

        $pdf->report_date($v_unit_short_name);

        $pdf->report_title($report_title, $report_subtitle);

        $pdf->SetFont('liennd.times', '', 12);
        $pdf->SetLineStyle(array('width' => 0.1, 'cap'   => 'butt', 'join'  => 'round', 'dash'  => 5, 'color' => array(0, 0, 0)));

        $pdf->set_thead($v_thead);
        $html = get_xml_value(simplexml_load_file($v_xml_signer_file_path), '//css') . $html;
        $pdf->writeHtmlReport($html);
        $pdf->lastPage();

        //echo 'Line: ' . __LINE__ . '<br>File: ' . __FILE__;
        //var_dump::display($html);exit;
        //Change To Avoid the PDF Error
        @ob_end_clean();
        //Close and output PDF document
        $v_attach_file_path = $VIEW_DATA['report_code'] . '.pdf';
        $pdf->Output($v_attach_file_path, 'D');
        break;
    default :
        $v_unit_short_name = get_xml_value($dom_unit_info,'/unit/name');
        echo get_xml_value(simplexml_load_file($v_xml_signer_file_path), '//css');
        ?>
        <style>
            *
            {
                font-family: "Time New Roman"
            }
            table
            {
                width: 100%;
            }
            .report_list
            {
                width: 100%;
                border: 2px solid #0D0D0D;
            }

            .report_list th,.report_list tr,.report_list td
            {
                border: 2px solid #0D0D0D;
            }

            .report_list td
            {
                font-size: 14px;
                font-weight: 700;
            }
            .report_list b
            {
                font-size: 16px;
            }
            .report_list .sub-header
            {
                font-weight: bold;
                font-size: 16px;
            }
            table.signer
            {
                width: 100%;
                border: 0px;
            }
            table.signer td,th,tr
            {
                border: 0px;
            }
            .reprot_header .item
            {
                text-align: center;
                width: 50%;
                font-weight: bold;
                font-size: 16px;
            }
            .reprot_header .date
            {
                text-align: center;
                width: 50%;
                font-weight: normal;
                font-size: 16px;
            }
            .header
            {
                width: 100%;
                text-align: center;
                font-weight: bold;
                font-size: 16px;
                margin: 15px 0px 10px 0px;

            }
            .formPrint
            {
                position: fixed;
                top: 0px;
                left: 0px;
            }
            @media print
            {
                .formPrint
                {
                    display: none;
                }  
                @page
                {
                    margin: 10px;
                }
            }
        </style>
        <form class="formPrint" action="" method="POST">
            <?php echo $this->hidden('hdn_print_pdf','1');?>
            <?php echo $this->hidden('hdn_format','');?>
            <input type="button" value="Kết xuất PDF" onclick="this.form.hdn_format.value='pdf'; this.form.submit();" />
            <input type="button" value="Kết xuất DOC" onclick="this.form.hdn_format.value='doc'; this.form.submit();" />
            <input type="button" value="Kết xuất Excel" onclick="this.form.hdn_format.value='excel'; this.form.submit();"/>
            <input type="button" value="In" onclick="javascript:window.print();"/>
            <input type="button" value="Đóng cửa sổ" onclick="window.parent.hidePopWin();"/>
        </form>
        <table class="reprot_header">
            <tr>
                <td class="item">
                    <?php
                     echo $v_unit_name;
                     echo '<br>';
                     echo '_________';
                    ?>
                </td>
                <td class="item">
                    <?php
                    echo "CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM <br> Độc lập - Tự do - Hạnh phúc <br> _________";
                    ?>
                </td>
            </tr>
            <tr>
                <td class="item">
                    &nbsp;
                </td>
                <td class="date">
                    <?php
                        $date = Date('d-m-Y');
                        echo 'Ngày ' . Date('d', strtotime($date)) . ' tháng ' . Date('m', strtotime($date)) . ' năm ' . Date('Y', strtotime($date));
                    ?>
                </td>
            </tr>
        </table>
        <div class="header">
            <?php
                echo $report_title . '<br>' .$report_subtitle;
            ?>
        </div>
        <?php
        echo $html;
}