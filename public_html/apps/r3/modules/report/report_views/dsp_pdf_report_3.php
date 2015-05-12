<?php
defined('DS') or die;

$dom_unit      = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
$unit_fullname = mb_strtoupper(xpath($dom_unit, '//full_name', XPATH_STRING), 'UTF-8');

if (Session::get('la_can_bo_cap_xa') == true)
    $unit_fullname = session::get('ou_name');
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
ini_set('display_errors',0);
function to_number($number)
{
    if(is_numeric($number))
    {
        if($number == 0)
        {
            return '';
        }
        return $number;
    }
    return '';
}
?>

<table width="100%">
    <tbody>
        <tr>
            <td align="center" class="item" excel-merge="7" excel-height="200">
                <b ><?php echo $unit_fullname ?></b>
                <br/>
                <?php echo str_repeat('_', mb_strlen($unit_fullname, 'UTF-8')) ?>
                <br/><br/>
                Số: …/BC-…
            </td>
            <td align="center" class="item" excel-merge="8" excel-height="200">
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
            <td colspan="2" excel-merge="15" excel-height="150" align="center">
                <h3 style="font-size: 16px;" align="center" >BÁO CÁO CHI TIẾT HỒ SƠ QUÁ HẠN<br/>
                    <?php echo "Từ ngày ".$_GET['txt_begin']." đến ngày ".$_GET['txt_end'] ?>
                </h3>
                <p align="center">Kính gửi: <?php echo str_repeat('.', 100) ?></p>
            </td>
        </tr>
    </tbody>
</table>
<table border="1" class="report_list">
    <thead>
        <tr>
            <th rowspan="2" width="6%" align="center"><b>STT</b></th>
            <th rowspan="2" align="center"  width="16%"><b>Lĩnh vực</b></th>
            <th rowspan="2" align="center"  width="6%"><b>Tiếp nhận</b></th>
            <th colspan="6" align="center"  width="36%"><b>Đã giải quyết</b></th>
            <th colspan="4" align="center"  width="24%"><b>Đang giải quyết</b></th>
            <th rowspan="2" align="center"  width="6%"><b>Đang thực hiện NVTC</b></th>
            <th rowspan="2" align="center"  width="6%"><b>Tỷ lệ sớm và đúng hạn</b></th>
        </tr>
        <tr excel-column="3">
            <th align="center"  width="6%"><b>Tổng</b></th>
            <th align="center"  width="6%"><b>Trả trước hạn</b></th>
            <th align="center"  width="6%"><b>Trả đúng hạn</b></th>
            <th align="center"  width="6%"><b>Trả quá hạn</b></th>
            <th align="center"  width="6%"><b>HS bị từ chối</b></th>
            <th align="center"  width="6%"><b>HS Công dân rút</b></th>
            <th align="center"  width="6%"><b>Tổng</b></th>
            <th align="center"  width="6%"><b>Chưa đến hạn</b></th>
            <th align="center"  width="6%"><b>Quá hạn</b></th>
            <th align="center"  width="6%"><b>Phải bổ sung</b></th>
        </tr>
        <tr>
            <?php
            array_walk(range(1, 15), function($data) {
                        echo "<td align=\"center\">($data)</td>";
                    });
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
            $tong_da_tra_chua_den_han = 0;
            $tong_da_tra_dung_han = 0;
            $tong_da_tra_cham_han = 0;
            $tong_da_tra_bi_tu_choi = 0;
            $tong_da_tra_cong_dan_rut = 0;
            
            $tong_dang_xu_ly_cham = 0;
            $tong_dang_xu_ly_chua_den_han = 0;
            $tong_dang_xu_ly_cho_bo_sung = 0;
            $tong_dang_xu_ly_nvtc = 0;
            
            $tong_tiep_nhan_all  = 0;
            $tong_da_tra_all  = 0;
            $tong_dang_xu_ly_all = 0;
            $tong_percent = 0;
            
        ?>
        <?php foreach ($arr_all_listtype_code as $arr_listtype_code):
                $i           = isset($i) ? $i + 1 : 1;
        
                $v_listtype_code = $arr_listtype_code['C_CODE'];
                $v_listtype_name = $arr_listtype_code['C_NAME'];
                
                //du lieu
                $da_tra_chua_den_han  = isset($arr_all_spec['arr_da_tra_chua_den_han'][$v_listtype_code])?$arr_all_spec['arr_da_tra_chua_den_han'][$v_listtype_code]:0;
                $da_tra_dung_han      = isset($arr_all_spec['arr_da_tra_dung_han'][$v_listtype_code])?$arr_all_spec['arr_da_tra_dung_han'][$v_listtype_code]:0;
                $da_tra_cham_han      = isset($arr_all_spec['arr_da_tra_cham_han'][$v_listtype_code])?$arr_all_spec['arr_da_tra_cham_han'][$v_listtype_code]:0;
                $da_tra_bi_tu_choi    = isset($arr_all_spec['arr_da_tra_bi_tu_choi'][$v_listtype_code])?$arr_all_spec['arr_da_tra_bi_tu_choi'][$v_listtype_code]:0;
                $da_tra_cong_dan_rut  = isset($arr_all_spec['arr_da_tra_cong_dan_rut'][$v_listtype_code])?$arr_all_spec['arr_da_tra_cong_dan_rut'][$v_listtype_code]:0;
                
                $dang_xu_ly_cham         = isset($arr_all_spec['arr_dang_xu_ly_cham'][$v_listtype_code])?$arr_all_spec['arr_dang_xu_ly_cham'][$v_listtype_code]:0;
                $dang_xu_ly_chua_den_han = isset($arr_all_spec['arr_dang_xu_ly_chua_den_han'][$v_listtype_code])?$arr_all_spec['arr_dang_xu_ly_chua_den_han'][$v_listtype_code]:0;
                $dang_xu_ly_cho_bo_sung  = isset($arr_all_spec['arr_dang_xu_ly_cho_bo_sung'][$v_listtype_code])?$arr_all_spec['arr_dang_xu_ly_cho_bo_sung'][$v_listtype_code]:0;
                $dang_xu_ly_nvtc         = isset($arr_all_spec['arr_dang_xu_ly_nvtc'][$v_listtype_code])?$arr_all_spec['arr_dang_xu_ly_nvtc'][$v_listtype_code]:0;
                
                //tinh tong da tra
                $tong_da_tra     = $da_tra_chua_den_han + $da_tra_dung_han + $da_tra_cham_han + $da_tra_bi_tu_choi + $da_tra_cong_dan_rut;
                $tong_dang_xu_ly = $dang_xu_ly_cham + $dang_xu_ly_chua_den_han + $dang_xu_ly_cho_bo_sung;
                
                $tong_tiep_nhan  = $tong_da_tra + $tong_dang_xu_ly;
                //tinh percent cho tung row
                $v_percent = 0;
                $tong_giai_quyet = $da_tra_chua_den_han + $da_tra_dung_han + $da_tra_cham_han;
                $v_percent    = ($tong_giai_quyet > 0) ? ($da_tra_dung_han + $da_tra_chua_den_han) * 100 / $tong_giai_quyet : '_';
                
                //tinh toan cho row tong ket
                $tong_da_tra_chua_den_han = $tong_da_tra_chua_den_han + $da_tra_chua_den_han;
                $tong_da_tra_dung_han     = $tong_da_tra_dung_han + $da_tra_dung_han;
                $tong_da_tra_cham_han     = $tong_da_tra_cham_han + $da_tra_cham_han;
                $tong_da_tra_bi_tu_choi   = $tong_da_tra_bi_tu_choi + $da_tra_bi_tu_choi;
                $tong_da_tra_cong_dan_rut = $tong_da_tra_cong_dan_rut + $da_tra_cong_dan_rut;

                $tong_dang_xu_ly_cham         = $tong_dang_xu_ly_cham + $dang_xu_ly_cham;
                $tong_dang_xu_ly_chua_den_han = $tong_dang_xu_ly_chua_den_han + $dang_xu_ly_chua_den_han;
                $tong_dang_xu_ly_cho_bo_sung  = $tong_dang_xu_ly_cho_bo_sung + $dang_xu_ly_cho_bo_sung;
                $tong_dang_xu_ly_nvtc         = $tong_dang_xu_ly_nvtc + $dang_xu_ly_nvtc;

                $tong_tiep_nhan_all  = $tong_tiep_nhan_all + $tong_tiep_nhan;
                $tong_da_tra_all     = $tong_da_tra_all + $tong_da_tra;
                $tong_dang_xu_ly_all = $tong_dang_xu_ly_all + $tong_dang_xu_ly;
        ?>
            <tr nobr="true">
                <td align="center" width="6%"><?php echo $i ?></td>
                <td align="left" width="16%"><?php echo $v_listtype_name?></td>
                <td align="center" width="6%"><?php echo to_number($tong_tiep_nhan) ?></td>

                <td align="center" width="6%"><?php echo to_number($tong_da_tra) ?></td>
                <td align="center" width="6%"><?php echo to_number($da_tra_chua_den_han)?></td>
                <td align="center" width="6%"><?php echo to_number($da_tra_dung_han)?></td>
                <td align="center" width="6%"><?php echo to_number($da_tra_cham_han)?></td>
                <td align="center" width="6%"><?php echo to_number($da_tra_bi_tu_choi)?></td>
                <td align="center" width="6%"><?php echo to_number($da_tra_cong_dan_rut)?></td>

                <td align="center" width="6%"><?php echo to_number($tong_dang_xu_ly) ?></td>
                <td align="center" width="6%"><?php echo to_number($dang_xu_ly_chua_den_han)?></td>
                <td align="center" width="6%"><?php echo to_number($dang_xu_ly_cham)?></td>
                <td align="center" width="6%"><?php echo to_number($dang_xu_ly_cho_bo_sung)?></td>

                <td align="center" width="6%"><?php echo to_number($dang_xu_ly_nvtc)?></td>

                <td align="center" width="6%"><?php echo ($v_percent != '_')? number_format($v_percent, 0, ',', '.') . '%' : '_' ?></td>
            </tr>
        <?php endforeach; ?>
        <?php 
            $tong_giai_quyet = $tong_da_tra_dung_han + $tong_da_tra_chua_den_han + $tong_da_tra_cham_han;
            $tong_percent    = ($tong_giai_quyet > 0) ? ($tong_da_tra_dung_han + $tong_da_tra_chua_den_han) * 100 / $tong_giai_quyet : 0;
        ?>
        <tr nobr="true">
            <td align="center" width="6%"></td>
            <th align="center" width="16%"><b>Tổng cộng</b></th>
            <td align="center" width="6%"><?php echo to_number($tong_tiep_nhan_all) ?></td>

            <td align="center" width="6%"><?php echo $tong_da_tra_all ?></td>
            <td align="center" width="6%"><?php echo $tong_da_tra_chua_den_han ?></td>
            <td align="center" width="6%"><?php echo $tong_da_tra_dung_han ?></td>
            <td align="center" width="6%"><?php echo $tong_da_tra_cham_han ?></td>
            <td align="center" width="6%"><?php echo $tong_da_tra_bi_tu_choi ?></td>
            <td align="center" width="6%"><?php echo $tong_da_tra_cong_dan_rut ?></td>

            <td align="center" width="6%"><?php echo $tong_dang_xu_ly_all ?></td>
            <td align="center" width="6%"><?php echo $tong_dang_xu_ly_chua_den_han ?></td>
            <td align="center" width="6%"><?php echo $tong_dang_xu_ly_cham ?></td>
            <td align="center" width="6%"><?php echo $tong_dang_xu_ly_cho_bo_sung ?></td>

            <td align="center" width="6%"><?php echo $tong_dang_xu_ly_nvtc ?></td>

            <td align="center" width="6%"><?php echo number_format($tong_percent, 0, ',', '.') . '%' ?></td>
        </tr>
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
        require_once (dirname(__FILE__) . '/../R3_PHPExcel_Reader_HTML.class.php');
        $objHTML = new R3_PHPExcel_Reader_HTML();
        
        $v_latest_row = 8 + count($arr_all_listtype_code);
        $v_latest_col = $objHTML->arr_excel_column[14];
                
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
        $mysheet->getColumnDimension('B')->setWidth(28); //Lĩnh vực
        $mysheet->getColumnDimension('C')->setWidth(9); //Tiếp nhận
        $mysheet->getColumnDimension('D')->setWidth(9); //Tổng
        $mysheet->getColumnDimension('E')->setWidth(9); //Trả trước hạn
        $mysheet->getColumnDimension('F')->setWidth(9); //Trả đúng hạn
        $mysheet->getColumnDimension('G')->setWidth(9); //Trả quá hạn
        $mysheet->getColumnDimension('H')->setWidth(9); //HS bị từ chối
        $mysheet->getColumnDimension('I')->setWidth(9); //HS công dân rút
        $mysheet->getColumnDimension('J')->setWidth(9); //Tổng
        $mysheet->getColumnDimension('K')->setWidth(9); //Chưa đến hạn
        $mysheet->getColumnDimension('L')->setWidth(9); //Quá hạn
        $mysheet->getColumnDimension('M')->setWidth(9); //Phải bổ sung
        $mysheet->getColumnDimension('N')->setWidth(9); //Đang thực hiện NVTC
        $mysheet->getColumnDimension('O')->setWidth(9); //Tỷ lệ đúng và sớm hạn
        
        $mysheet->getStyle()->getAlignment()->setWrapText(true);
//        $mysheet->getRowDimension()->setRowHeight(73);
        
        $objHTML->loadIntoExisting($v_html_file_name, $objPHPExcel,1);
        
        //Set border style
        $mysheet->getStyle("A5:{$v_latest_col}10")->applyFromArray($objHTML->styleArrayForHeader);
        $mysheet->getStyle("A7:{$v_latest_col}{$v_latest_row}")->applyFromArray($objHTML->styleArrayForData);
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
        $html = $v_css . '<style>
                    *
                    {
                        font-family: "Time New Roman"
                    }

                    .item b,.item
                    {
                        font-size: 16px;
                    }
                    table
                    {
                        width: 100%;
                        border-spacing:0px;
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
                        .page-break	{ display: block; page-break-before: always; }
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
        $file_name_save = 'report_3';
        //tao file doc
        $htmltodoc->createDoc($html,$file_name_save,true);
        break;
    default:
        echo $v_css;
        ?>
            <style>
        *
        {
            font-family: "Time New Roman"
        }
        
        .item b,.item
        {
            font-size: 16px;
        }
        table
        {
            width: 100%;
            border-spacing:0px;
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
            .page-break	{ display: block; page-break-before: always; }
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
    