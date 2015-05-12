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
        <tr>
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
        <tr>
            <td align="center"  excel-merge="7" excel-height="200" colspan="2" >
                BÁO CÁO CHI TIẾT HỒ SƠ QUÁ HẠN<br/>
                <?php echo "Từ ngày ".$_GET['txt_begin']." đến ngày ".$_GET['txt_end'] ?>
                <br/>
                <p align="center">Kính gửi: <?php echo str_repeat('.', 100) ?></p>
            </td>
        </tr>
    </tbody>
</table>

<?php 
    $colgroup      = array(5, 10, 10, 10, 10,10 , 45);
    $excel_cols    = array(10, 17, 27, 17, 17, 17, 53);
?>

<table style="width: 100%" class="report_list" border="1">
     <?php $thead         = array('TT', 'Mã hồ sơ', 'Tên công dân', 'Ngày tiếp nhận', 'Ngày trả hẹn', 'Ngày trả thực tế', 'Chi tiết quá hạn') ?>
   
    <thead class="reprot_header">
        <tr>
            <?php for ($i = 0; $i < count($colgroup); $i++): ?>
                <th width="<?php echo $colgroup[$i] ?>%" align="center"><b><?php echo $thead[$i] ?></b></th>
            <?php endfor; ?>
        </tr>
        <tr>
            <?php for ($i = 0; $i < count($colgroup); $i++): ?>
                <td width="<?php echo $colgroup[$i] ?>%" align="center"><b><?php echo "($i)" ?></b></td>
            <?php endfor; ?>
        </tr>
    </thead>
            <?php if(isset($arr_all_record)):; ?>
            <?php for($i =0;$i<sizeof($arr_all_record); $i++):; ?>
                <?php
                    $v_record_id             = isset($arr_all_record[$i]['PK_RECORD']) ? $arr_all_record[$i]['PK_RECORD'] : '';
                    $v_record_type           = isset($arr_all_record[$i]['FK_RECORD_TYPE']) ? $arr_all_record[$i]['FK_RECORD_TYPE'] : '';
                    $v_record_no             = isset($arr_all_record[$i]['C_RECORD_NO']) ? $arr_all_record[$i]['C_RECORD_NO'] : '';
                    $v_record_receive_date   = isset($arr_all_record[$i]['C_RECEIVE_DATE']) ? $arr_all_record[$i]['C_RECEIVE_DATE'] : '';
                    $v_record_return_date    = isset($arr_all_record[$i]['C_RETURN_DATE']) ? $arr_all_record[$i]['C_RETURN_DATE'] : '';
                    $v_xml_processing        = isset($arr_all_record[$i]['C_XML_PROCESSING']) ? $arr_all_record[$i]['C_XML_PROCESSING'] : '';
                    $v_record_clear_date     = isset($arr_all_record[$i]['C_CLEAR_DATE']) ? $arr_all_record[$i]['C_CLEAR_DATE'] : '';
                    $v_xml_workflow          = isset($arr_all_record[$i]['C_XML_WORKFLOW']) ? $arr_all_record[$i]['C_XML_WORKFLOW'] : '';
                    $v_citizen_name          = isset($arr_all_record[$i]['C_CITIZEN_NAME']) ? $arr_all_record[$i]['C_CITIZEN_NAME'] : '';
                    $v_village_id            = isset($arr_all_record[$i]['FK_VILLAGE_ID']) ? $arr_all_record[$i]['FK_VILLAGE_ID'] : '';
                    $v_pase_date             = isset($arr_all_record[$i]['C_PAUSE_DATE']) ? $arr_all_record[$i]['C_PAUSE_DATE'] : '';
                    $v_biz_days_exceed       = isset($arr_all_record[$i]['C_BIZ_DAYS_EXCEED']) ? $arr_all_record[$i]['C_BIZ_DAYS_EXCEED'] : 0;
                    $v_return_date           = isset($arr_all_record[$i]['C_RETURN_DATE']) ? $arr_all_record[$i]['C_RETURN_DATE'] : '';
                    $v_clear_date            = isset($arr_all_record[$i]['C_CLEAR_DATE']) ? $arr_all_record[$i]['C_CLEAR_DATE'] : '';
                    $v_record_type_code      = isset($arr_all_record[$i]['C_RECORD_TYPE_CODE']) ? $arr_all_record[$i]['C_RECORD_TYPE_CODE'] : ''; 
                    $v_step_biz_days_exceed  = isset($arr_all_record[$i]['step_biz_days_exceed']) ? $arr_all_record[$i]['step_biz_days_exceed'] : array(); 
                    if(sizeof($v_step_biz_days_exceed) > 0):
                        $dom_xml_processing = simplexml_load_string($v_xml_processing);
                        $dom_xml_workflow  = simplexml_load_string($v_xml_workflow);
                    
                        $v_biz_done_task_code  = get_xml_value($dom_xml_workflow, "//task[@biz_done='true']/@code");
                        $v_clear_date_biz_done = get_xml_value($dom_xml_processing, "//step[@code='$v_biz_done_task_code']/datetime");
                        $v_clear_date_biz_done     = (sizeof($v_clear_date_biz_done) > 0 && isset($v_clear_date_biz_done)) ? $v_clear_date_biz_done : $v_clear_date;  
                        
                    
                ?>  
                <tr>
                    <td align="center" width="<?php echo $colgroup[0] ?>%"><?php echo $i +1;?></td>
                    <td align="center" width="<?php echo $colgroup[1] ?>%"><?php echo $v_record_no;?></td>
                    <td align="center" width="<?php echo $colgroup[2] ?>%"><?php echo $v_citizen_name; ?></td>
                    <td align="center" width="<?php echo $colgroup[3] ?>%"><?php   echo jwDate::yyyymmdd_to_ddmmyyyy( $v_record_receive_date); ?></td>
                    <td align="center" width="<?php echo $colgroup[4] ?>%"><?php echo jwDate::yyyymmdd_to_ddmmyyyy($v_return_date); ?></td>
                    <td align="center" width="<?php echo $colgroup[5] ?>%"><?php echo jwDate::yyyymmdd_to_ddmmyyyy($v_clear_date_biz_done); ?></td>
                    <td align="center" width="<?php echo $colgroup[6] ?>%">    
                       
                        <table  style="width: 100%" border="1">
                            <thead style="font-weight: bold;text-align: center;">
                               <tr>
                                   <th style="width: 10%"><b>STT</b></th>
                                   <th style="width: 65%"><b>Quy trình</b></th>
                                   <th style="width: 25%"><b>Thời gian quá hạn</b></th>
                               </tr>
                           </thead>
                            <?php 
                            for($o =0; $o<count($v_step_biz_days_exceed) ; $o ++)
                            {
                            ?>
                                <tr>
                                    <td style="width: 10%;text-align: center"><?php echo $o +1;?></td>
                                    <td style="width: 65%"><?php echo $v_step_biz_days_exceed[$o]['step_name']?></td>
                                    <td style="width: 25%;text-align: center"><?php echo $v_step_biz_days_exceed[$o]['step_biz_days_exceed']?></td>
                                </tr>
                            <?php  }; ?>
                        </table>
                    </td>
                </tr>
                <?php endif;?>
            <?php endfor;?>
            <?php endif;?>
        </table>
<h4>&nbsp;</h4>
<table>
    <tr nobr="true">
        <td align="left" excel-merge="3" excel-height="200"><b>Nơi nhận:</b><br/><br/>
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

        $report_title = 'report_19';
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
                    .item b
                    {
                        font-size: 16px;
                    }
                        table
                        {
                            width: 100%;
                            border-spacing: 0px;
                            padding: 0;
                            margin: 0;
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
                        .report_list table *,.report_list table
                        {
                            border-collapse: collapse

                        }
                        .report_list table
                        {
                            width: 100%;
                            margin: 0;
                            padding: 0;

                        }
                        report_list table th
                        {
                            border:0;
                        }
                        .report_list table td
                        {
                            border-bottom: 1px solid #0D0D0D;
                            border-left: solid 1px #0D0D0D;
                        }
                        .report_list tr
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
        $file_name_save = 'report_19';
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
                .item b
                {
                    font-size: 16px;
                }
                    table
                    {
                        width: 100%;
                        border-spacing: 0px;
                        padding: 0;
                        margin: 0;
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
                    .report_list table *,.report_list table
                    {
                        border-collapse: collapse

                    }
                    .report_list table
                    {
                        width: 100%;
                        margin: 0;
                        padding: 0;

                    }
                    report_list table th
                    {
                        border:0;
                    }
                    .report_list table td
                    {
                        border-bottom: 1px solid #0D0D0D;
                        border-left: solid 1px #0D0D0D;
                    }
                    .report_list tr
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
    


