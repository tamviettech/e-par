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
defined('DS') or die;

$dom_unit      = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
$unit_fullname = mb_strtoupper(xpath($dom_unit, '//full_name', XPATH_STRING), 'UTF-8');
ob_end_clean();
ob_start();
error_reporting(0);
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
            <td align="center"
                ><b><?php echo $unit_fullname ?></b>
                <br/>
                <?php echo str_repeat('_', mb_strlen($unit_fullname, 'UTF-8')) ?>
                <br/><br/>
                Số: …/BC-…
            </td>
            <td align="center"
                ><b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</b><br/>
                <b>Độc lập - Tự do - Hạnh Phúc</b><br/>
                <?php echo str_repeat('_', mb_strlen('Độc lập - Tự do - Hạnh Phúc', 'UTF-8')) ?>
                <br/>
                <?php echo xpath($dom_unit, '//name', XPATH_STRING) ?>, ngày <?php echo $now->format('d') ?> tháng <?php echo $now->format('m') ?> năm <?php echo $now->format('Y') ?>
            </td>
        </tr>
    </tbody>
</table>
<h3 align="center"
    >TÌNH HÌNH, KẾT QUẢ GIẢI QUYẾT THỦ TỤC HÀNH CHÍNH<br/>
        <?php echo mb_strtoupper($subtitle, 'UTF-8') ?>
</h3>
<p align="center">Kính gửi: <?php echo str_repeat('.', 100) ?></p>
<table border="1" class="report_list">
    <thead>
        <tr>
            <th rowspan="2" width="6%" align="center"><b>STT</b></th>
            <th rowspan="2"align="center"  width="16%"><b>Lĩnh vực</b></th>
            <th rowspan="2"align="center"  width="6%"><b>Tiếp nhận</b></th>
            <th colspan="6"align="center"  width="36%"><b>Đã giải quyết</b></th>
            <th colspan="4"align="center"  width="24%"><b>Đang giải quyết</b></th>
            <th rowspan="2"align="center"  width="6%"><b>Đang thực hiện NVTC</b></th>
            <th rowspan="2"align="center"  width="6%"><b>Tỷ lệ sớm và đúng hạn</b></th>
        </tr>
        <tr>
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
        <td align="left"><b>Nơi nhận:</b><br/><br/>
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


if((int) get_post_var('hdn_print_pdf',0) == 1)
{
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
    
}
else
{
    ?>
    <style>
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
            font-size: 18px;
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
    </style>
    <form class="formPrint" action="" method="POST">
        <?php echo $this->hidden('hdn_print_pdf','1');?>
        <input type="submit" value="Kết xuất pdf" />
        <input type="button" value="Đóng cửa sổ" onclick="window.parent.hidePopWin();" />
    </form>
<?php
    echo $html;
}
?>