<?php
defined('DS') or die;

$dom_unit      = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
$unit_fullname = mb_strtoupper(xpath($dom_unit, '//full_name', XPATH_STRING), 'UTF-8');
//Cau hinh phan ky
$v_xml_signer_file_path = CONST_APPS_DIR . $this->app_name . DS . 'modules' . DS . $this->module_name . DS
        . $this->module_name . '_views' . DS . 'xml' . DS . $VIEW_DATA['report_code'] . '_signer.xml';

if (!file_exists($v_xml_signer_file_path))
{
    $v_xml_signer_file_path = CONST_APPS_DIR . $this->app_name . DS . 'modules' . DS . $this->module_name . DS
            . $this->module_name . '_views' . DS . 'xml' . DS . 'report_signer.xml';
}
$v_css = get_xml_value(simplexml_load_file($v_xml_signer_file_path), '//css');

ob_end_clean();
ob_start();
ini_set('display_errors',0);

?>

<table width="100%">
    <tbody>
        <tr>
            <td align="center" class="item" excel-merge="3" excel-height="200">
                <b ><?php echo $unit_fullname ?></b>
                <br/>
                <?php echo str_repeat('_', mb_strlen($unit_fullname, 'UTF-8')) ?>
                <br/><br/>
                Số: …/BC-…
            </td>
            <td align="center" class="item" excel-merge="4" excel-height="200">
                <b >CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</b>
                <br/>
                <b>Độc lập - Tự do - Hạnh Phúc</b>
                <br/>
                <?php echo str_repeat('_', mb_strlen('Độc lập - Tự do - Hạnh Phúc', 'UTF-8')) ?>
                <br/>
                <i >Ngày <?php echo $now->format('d') ?> tháng <?php echo $now->format('m') ?> năm <?php echo $now->format('Y') ?></i>
            </td>
        </tr>
        <tr>
            <td colspan="2" excel-merge="7" excel-height="200" align="center">
                <h2 align="center" >
                    <?php echo $repoer_title?><br/>
                </h2>
                <p align="center">Kính gửi: <?php echo str_repeat('.', 100) ?></p>
            </td>
        </tr>
    </tbody>
</table>
<table border="1" class="report_list">
    <thead>
        <tr>
            <th rowspan="2" align="center"  width="8%" ><b>STT</b></th>
            <th rowspan="2" align="center"  width="32%"><b>Tên phòng ban</b></th>
            <th rowspan="2" align="center"  width="12%"><b>Tổng số hồ sơ</b></th>
            <th colspan="2" align="center"  width="24%"><b>Đúng hạn</b></th>
            <th colspan="2" align="center"  width="24%"><b>Chậm tiến độ</b></th>
        </tr>
        <tr excel-column="3">
            <th align="center" ><b>Số lượng</b></th>
            <th align="center" ><b>Tỷ lệ</b></th>
            <th align="center" ><b>Số lượng</b></th>
            <th align="center" ><b>Tỷ lệ</b></th>
        </tr>
        <tr>
            <?php
            array_walk(range(1, 7), function($data) {
                        echo "<td align=\"center\"><b>($data)</b></td>";
                    });
            ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        $i = 1;
        foreach($arr_all_report_data as $arr_report_data):
               $v_unit_name    =  $arr_report_data['C_NAME'];
               $v_total_record =  $arr_report_data['C_TOTAL_RECORD'];
               $v_on_time      =  $arr_report_data['C_ON_TIME'];
               $v_delays       =  $arr_report_data['C_DELAYS'];
               
               $v_on_time_percent = '-';
               $v_delay_percent   = '-';
               if($v_total_record > 0)
               {
                   $v_on_time_percent = ($v_on_time/$v_total_record)*100;
                   $v_on_time_percent = round($v_on_time_percent,2) . '%';
                   
                   $v_delay_percent = ($v_delays/$v_total_record)*100;
                   $v_delay_percent = round($v_delay_percent,2) . '%';
               }
               
        ?>
        <tr>
           <td align="center" width="8%" ><?php echo $i;?></td>
           <td width="32%" ><?php echo $v_unit_name?></td>
           <td width="12%" align="center" ><?php echo $v_total_record?></td>
           <td width="12%" align="center" ><?php echo $v_on_time?></td>
           <td width="12%" align="center" ><?php echo $v_on_time_percent;?></td>
           <td width="12%" align="center" ><?php echo $v_delays?></td>
           <td width="12%" align="center" ><?php echo $v_delay_percent;?></td>
        </tr>
       <?php 
       $i++;
       endforeach;
       ?>
    </tbody>
</table>
<table style="margin-top: 10px;">
    <tr nobr="true">
        <td align="left" excel-merge="3" excel-height="200">
            <b>Nơi nhận:</b><br/><br/>
            - <?php echo str_repeat('.', 50) ?><br/>
            - <?php echo str_repeat('.', 50) ?><br/>
            - <?php echo str_repeat('.', 50) ?><br/>
            - <?php echo str_repeat('.', 50) ?><br/>
            - Lưu<br/>
        </td>
        <td align="center" style="vertical-align: top;">
            <b>LÃNH ĐẠO ĐƠN VỊ</b><br/>
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
        $pdf = new ZREPORT(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Ngo Duc Lien');

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
        $pdf->Output($v_attach_file_path, 'D');
        break;
    
    case 'excel':
        ini_set('display_errors',0);
        require_once (dirname(__FILE__) . '/../R3_PHPExcel_Reader_HTML.class.php');
        $objHTML = new R3_PHPExcel_Reader_HTML();
        
        $v_latest_row = 8 + count($arr_report_data);
        $v_latest_col = $objHTML->arr_excel_column[6];
                
        //$html = str_replace('&', '&amp;', $html);
        $v_html_file_name = SERVER_ROOT . 'cache/' . uniqid() . '.html';  

		$html = '<?xml encoding="UTF-8"><html><head><meta http-equiv="Content-type" content="text/html; charset=UTF-8"/></head><body>' . $html . '</body></html>';
//		$html = mb_convert_encoding($html, 'UTF-16LE', 'UTF-8');
        
        file_put_contents($v_html_file_name,unicode_to_composite($html));
                
        $objPHPExcel = new PHPExcel();
        // Set properties
        $report_title = 'report_3';
        $objPHPExcel->getProperties()->setCreator("Quang Tran");
        $objPHPExcel->getProperties()->setLastModifiedBy("Quang Tran");
        $objPHPExcel->getProperties()->setTitle($report_title);
        $objPHPExcel->getProperties()->setSubject($report_title);
        $objPHPExcel->getProperties()->setDescription($report_title);
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(9);
        
        
        //
        //set sheet insert
        $objPHPExcel->setActiveSheetIndex(0);
        $mysheet = $objPHPExcel->getActiveSheet();
        $mysheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $mysheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        
        //set width cho column
        $mysheet->getColumnDimension('A')->setWidth(8);//stt
        $mysheet->getColumnDimension('B')->setWidth(44); //Lĩnh vực
        $mysheet->getColumnDimension('C')->setWidth(12); //Tiếp nhận
        $mysheet->getColumnDimension('D')->setWidth(12); //Tổng
        $mysheet->getColumnDimension('E')->setWidth(12); //Trả trước hạn
        $mysheet->getColumnDimension('F')->setWidth(12); //Trả đúng hạn
        $mysheet->getColumnDimension('G')->setWidth(12); //Trả quá hạn
        
        $mysheet->getStyle()->getAlignment()->setWrapText(true);
//        $mysheet->getRowDimension()->setRowHeight(73);
        
        $objHTML->loadIntoExisting($v_html_file_name, $objPHPExcel,1);
        
        //Set border style
        $mysheet->getStyle("A5:{$v_latest_col}7")->applyFromArray($objHTML->styleArrayForHeader);
        $mysheet->getStyle("A8:{$v_latest_col}{$v_latest_row}")->applyFromArray($objHTML->styleArrayForData);
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
                
        //unlink($v_html_file_name);
        unlink($url_save_file);
        break;
    case 'doc':
         //lay html 
        $html = $v_css. '<style>
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
                        font-weight: normal;
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
                </style>'. $html;
        $html = html_entity_decode($html);
        
        //include thu vien html_to_doc
        $library_dir = SERVER_ROOT . 'libs/html_to_doc.inc.php';
        include($library_dir);
        
        //tao doi tuong 
        $htmltodoc = new HTML_TO_DOC();
        
        //Thiep lap ten file default
        $file_name_save = $VIEW_DATA['report_code'];
        //tao file doc
        $htmltodoc->createDoc($html,$file_name_save,true);
        break;
    default:
        echo $v_css;
        ?>
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
                font-weight: normal;
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
    <form class="formPrint" action="" method="POST">
        <?php echo $this->hidden('hdn_print_pdf','1');?>
        <?php echo $this->hidden('hdn_format','');?>
        <input type="button" value="Kết xuất PDF" onclick="this.form.hdn_format.value='pdf'; this.form.submit();" />
        <input type="button" value="Kết xuất DOC" onclick="this.form.hdn_format.value='doc'; this.form.submit();" />
        <input type="button" value="Kết xuất Excel" onclick="this.form.hdn_format.value='excel'; this.form.submit();"/>
        <input type="button" value="In" onclick="javascript:window.print();"/>
        <input type="button" value="Đóng cửa sổ" onclick="window.parent.hidePopWin();" />
    </form>
    <?php
    echo $html;
    break;
}//end switch case
    ?>
    