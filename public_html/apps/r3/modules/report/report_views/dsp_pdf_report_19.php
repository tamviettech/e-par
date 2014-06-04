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
<?php
defined('SERVER_ROOT') or die;
$dom_unit      = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');

if (Session::get('la_can_bo_cap_xa'))
    $unit_fullname = Session::get('ou_name');
else
    $unit_fullname = mb_strtoupper(xpath($dom_unit, '//full_name', XPATH_STRING), 'UTF-8');

ob_end_clean();
ob_start();
?>

<table width="100%" >
    <tbody>
        <tr>
            <td align="center"
                ><b><?php echo $unit_fullname ?></b>
                <br/>
                <?php echo str_repeat('_', mb_strlen($unit_fullname, 'UTF-8')) ?>
                <br/><br/>
                Số: …/BC-…
            </td>
            <td align="center" >
                <b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</b><br/>
                <b>Độc lập - Tự do - Hạnh Phúc</b><br/>
                <?php echo str_repeat('_', mb_strlen('Độc lập - Tự do - Hạnh Phúc', 'UTF-8')) ?>
                <br/>
                <?php echo xpath($dom_unit, '//name', XPATH_STRING) ?>, ngày <?php echo $now->format('d') ?> tháng <?php echo $now->format('m') ?> năm <?php echo $now->format('Y') ?>
            </td>
        </tr>
    </tbody>
</table>
<h3 align="center" >BÁO CÁO CHI TIẾT HỒ SƠ QUÁ HẠN<br/>
    <?php echo "Từ ngày ".$_GET['txt_begin']." đến ngày ".$_GET['txt_end'] ?>
</h3>

<p align="center">Kính gửi: <?php echo str_repeat('.', 100) ?></p>
<?php $colgroup      = array(5, 10, 10, 10, 10,10 , 45) ?>

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

if((int) get_post_var('hdn_print_pdf',0) == 1)
{
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
}
else
{
    ?>
    <style>
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
                font-size: 18px;
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
                font-size: 18px;
            }
            .reprot_header .date
            {
                text-align: center;
                width: 50%;
                font-weight: normal;
                font-size: 18px;
            }
            .header
            {
                width: 100%;
                text-align: center;
                font-weight: bold;
                font-size: 25px;
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
        <input type="submit" value="Kết xuất pdf" />
        <input type="button" value="In" onclick="javascript:window.print();"/>
        <input type="button" value="Đóng cửa sổ" onclick="window.parent.hidePopWin();"/>
    </form>
    <?php
    echo $html;
}

