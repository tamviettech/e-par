<?php
defined('SERVER_ROOT') or die;
$dom_unit      = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');

if (Session::get('la_can_bo_cap_xa'))
    $unit_fullname = Session::get('ou_name');
else
    $unit_fullname = mb_strtoupper(xpath($dom_unit, '//full_name', XPATH_STRING), 'UTF-8');

//Cau hinh phan ky
$v_xml_signer_file_path = CONST_APPS_DIR . $this->app_name . DS . 'modules' . DS . $this->module_name . DS
        . $this->module_name . '_views' . DS . 'xml' . DS . $VIEW_DATA['report_code'] . '_signer.xml';
if (!file_exists($v_xml_signer_file_path))
{
    $v_xml_signer_file_path = CONST_APPS_DIR . $this->app_name . DS . 'modules' . DS . $this->module_name . DS
            . $this->module_name . '_views' . DS . 'xml' . DS . 'report_signer.xml';
}
//css
$v_css = get_xml_value(simplexml_load_file($v_xml_signer_file_path), '//css');

ob_end_clean();
ob_start();
?>

<table width="100%" >
    <tbody>
        <tr >
            <td align="center" class="item" excel-merge="3" excel-height="200">
                <b><?php echo $unit_fullname ?></b>
                <br/>
                <?php echo str_repeat('_', mb_strlen($unit_fullname, 'UTF-8')) ?>
                <br/><br/>
                Số: …/BC-…
            </td>
            <td align="center" class="item" excel-merge="4" excel-height="200">
                <b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</b><br/>
                <b>Độc lập - Tự do - Hạnh Phúc</b><br/>
                <?php echo str_repeat('_', mb_strlen('Độc lập - Tự do - Hạnh Phúc', 'UTF-8')) ?>
                <br/>
                <i>Ngày <?php echo $now->format('d') ?> tháng <?php echo $now->format('m') ?> năm <?php echo $now->format('Y') ?></i>
            </td>
        </tr>
        <tr >
            <td align="center" excel-merge="7" excel-height="200" colspan="2" >
                BÁO CÁO HỒ SƠ TRẢ KẾT QUẢ QUÁ HẠN<br/>
                <?php echo "Từ ngày $begin đến ngày $end" ?>
                <br/>
                <p align="center">Kính gửi: <?php echo str_repeat('.', 100) ?></p>
            </td>
        </tr>
    </tbody>
</table>



<?php 
    $colgroup      = array(10, 15, 15, 15, 15, 15, 15);
    $excel_cols    = array(10, 27, 27, 17, 17, 17, 17);
?>
<table width="100%" class="report_list">
    <?php $thead         = array('TT', 'Mã hồ sơ', 'Tên công dân', 'Ngày tiếp nhận', 'Ngày trả', 'Quá hạn', 'Lý do') ?>
    <thead class="reprot_header">
        <tr>
            <?php for ($i = 0; $i < count($colgroup); $i++): ?>
                <td width="<?php echo $colgroup[$i] ?>%" align="center"><b><?php echo $thead[$i] ?></b></td>
            <?php endfor; ?>
        </tr>
        <tr>
            <?php for ($i = 0; $i < count($colgroup); $i++): ?>
                <td width="<?php echo $colgroup[$i] ?>%" align="center"><b><?php echo "($i)" ?></b></td>
            <?php endfor; ?>
        </tr>
    </thead>
    <tbody>
        <?php for ($i = 0; $i < count($arr_all_record); $i++): ?>
            <?php
            $record = $arr_all_record[$i];
            ?>
            <tr >
                <td align="center" width="<?php echo $colgroup[0] ?>%"><?php echo $i + 1 ?></td>
                <td align="center" width="<?php echo $colgroup[1] ?>%"><?php echo $record['C_RECORD_NO'] ?></td>
                <td align="center" width="<?php echo $colgroup[2] ?>%"><?php echo $record['C_CITIZEN_NAME'] ?></td>
                <td align="center" width="<?php echo $colgroup[3] ?>%"><?php echo date_create($record['C_RECEIVE_DATE'])->format('d-m-Y') ?></td>
                <td align="center" width="<?php echo $colgroup[4] ?>%"><?php echo date_create($record['C_CLEAR_DATE'])->format('d-m-Y') ?></td>
                <td align="center" width="<?php echo $colgroup[5] ?>%"><?php echo abs($record['C_BIZ_DAYS_EXCEED']) ?> ngày</td>
                <td align="center" width="<?php echo $colgroup[6] ?>%"><?php ?></td>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>
<h4>&nbsp;</h4>
<table>
    <tr nobr="true">
        <td align="left"><b>Nơi nhận:</b><br/><br/>
            - <?php echo str_repeat('.', 50) ?><br/>
            - <?php echo str_repeat('.', 50) ?><br/>
            - <?php echo str_repeat('.', 50) ?><br/>
            - <?php echo str_repeat('.', 50) ?><br/>
            - Lưu<br/>
        </td>
        <td align="center"><b>LÃNH ĐẠO ĐƠN VỊ</b><br/>
            <font>(Ký và ghi rõ họ tên)</font>
        </td>
    </tr>
</table>
<?php
$html = ob_get_clean();
$v_format = get_post_var('hdn_format','');
switch ($v_format)
{
    case 'pdf':
        require_once SERVER_ROOT . 'libs/tcpdf/zreport.php';
    
        $pdf = new ZREPORT(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('');

        // set header and footer fonts
        $pdf->setPrintHeader(0);
        $pdf->SetHeaderData('', PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 023', 'asdadsadsadsa');
        //$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, 'B', 16));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', 13));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->AddPage('LANDSCAPE');
        //set auto page breaks
        //$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('liennd.times', '', 12);

        $pdf->writeHTML($html);
        $v_attach_file_path = $VIEW_DATA['report_code'] . '.pdf';
        $pdf->Output($v_attach_file_path,'D');
        break;
    case 'excel':
        require_once (dirname(__FILE__) . '/../R3_PHPExcel_Reader_HTML.class.php');
        $objHTML = new R3_PHPExcel_Reader_HTML();
        //count unique doing group code
        
        $v_latest_row = 6 + count($arr_all_record);
        $v_latest_col = $objHTML->arr_excel_column[count($thead) -1 ];
        //$html = str_replace('&', '&amp;', $html);
        $v_html_file_name = SERVER_ROOT . 'cache/' . uniqid() . '.html';  

        $html = '<?xml encoding="UTF-8"><html><head><meta http-equiv="Content-type" content="text/html; charset=UTF-8"/></head><body>' . $html . '</body></html>';
        file_put_contents($v_html_file_name, unicode_to_composite($html));

        $objPHPExcel = new PHPExcel();
        // Set properties

        $report_title = 'report_18';
        $objPHPExcel->getProperties()->setCreator("Quang Tran");
        $objPHPExcel->getProperties()->setLastModifiedBy("Quang Tran");
        $objPHPExcel->getProperties()->setTitle($report_title);
        $objPHPExcel->getProperties()->setSubject($report_title);
        $objPHPExcel->getProperties()->setDescription($report_title);
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(9);

        //set sheet insert
        $objPHPExcel->setActiveSheetIndex(0);
        $mysheet = $objPHPExcel->getActiveSheet();
        $mysheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $mysheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $mysheet->getStyle()->getAlignment()->setWrapText(true);
//        $mysheet->getRowDimension()->setRowHeight(73);


        //set width cho column
        $i=0;
        foreach($excel_cols as $col_width)
        {
            $mysheet->getColumnDimension($objHTML->arr_excel_column[$i])->setWidth($col_width);//stt
            $i++;
        }


        $objHTML->loadIntoExisting($v_html_file_name, $objPHPExcel,1);

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
        $html = $v_css . '<style>
                    *
                    {
                        font-family: "Time New Roman"
                    }
                    .item b{
                        font-size: 16px;
                    }
                        table
                        {
                            width: 100%;
                            border-spacing: 0px;
                        }
                        .report_list
                        {
                            width: 100%;
                            border: 1px solid #0D0D0D;
                        }

                        .report_list th,.report_list tr,.report_list td
                        {
                            border: 1px solid #0D0D0D;
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
                </style>' . $html;
        //lay html 
        $html = html_entity_decode($html);
        
        //include thu vien html_to_doc
        $library_dir = SERVER_ROOT . 'libs/html_to_doc.inc.php';
        include($library_dir);
        
        //tao doi tuong 
        $htmltodoc = new HTML_TO_DOC();
        
        //Thiep lap ten file default
        $file_name_save = 'report_18';
        //tao file doc
        $htmltodoc->createDoc($html,$file_name_save,true);
        break;
    default :
        echo $v_css;
        ?>
            <style>
                *
                {
                    font-family: "Time New Roman"
                }
                .item b{
                    font-size: 16px;
                }
                    table
                    {
                        width: 100%;
                        border-spacing: 0px;
                    }
                    .report_list
                    {
                        width: 100%;
                        border: 1px solid #0D0D0D;
                    }

                    .report_list th,.report_list tr,.report_list td
                    {
                        border: 1px solid #0D0D0D;
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
        <?php
        echo $html;
        break;
}
