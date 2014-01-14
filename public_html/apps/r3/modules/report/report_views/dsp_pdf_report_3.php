<?php
/**
Copyright (C) 2012 Tam Viet Tech.

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

$dom_unit      = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
$unit_fullname = mb_strtoupper(xpath($dom_unit, '//full_name', XPATH_STRING), 'UTF-8');
ob_end_clean();
ob_start();
error_reporting(0);
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
<table width="100%" border="1" cellpadding="3">
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
        $tong_tiep_nhan = 0;

        $tong_da_giai_quyet = 0;
        $tong_tra_truoc_han = 0;
        $tong_tra_dung_han  = 0;
        $tong_tra_qua_han   = 0;
        $tong_rut           = 0;
        $tong_tu_choi       = 0;

        $tong_dang_giai_quyet = 0;
        $tong_chua_den_han    = 0;
        $tong_qua_han         = 0;
        $tong_bo_sung         = 0;
        $tong_thue            = 0;

        $tong_percent = 0;
        ?>
        <?php foreach ($arr_all_spec as $spec => $months): ?>
            <?php
            $i           = isset($i) ? $i + 1 : 1;
            $v_tiep_nhan = 0;

            $v_da_giai_quyet = 0;
            $v_tra_truoc_han = 0;
            $v_tra_dung_han  = 0;
            $v_tra_qua_han   = 0;
            $v_bi_tu_choi    = 0;
            $v_cong_dan_rut  = 0;

            $v_dang_giai_quyet = 0;
            $v_chua_den_han    = 0;
            $v_qua_han         = 0;
            $v_bo_sung         = 0;

            $v_percent = 0;

            foreach ($months as $month)
            {
                $v_da_giai_quyet += $month['C_COUNT_DA_TRA_KET_QUA'];
                $v_tiep_nhan += $month['C_COUNT_TONG_TIEP_NHAN'];
                $v_tra_truoc_han += $month['C_COUNT_DA_TRA_KET_QUA_TRUOC_HAN'];
                $v_tra_dung_han += $month['C_COUNT_DA_TRA_KET_QUA_DUNG_HAN'];
                $v_tra_qua_han += $month['C_COUNT_DA_TRA_KET_QUA_QUA_HAN'];
                $v_bi_tu_choi += $month['C_COUNT_TU_CHOI'];
                $v_cong_dan_rut += $month['C_COUNT_CONG_DAN_RUT'];
            }

            $v_dang_giai_quyet = $month['C_COUNT_DANG_THU_LY'];
            $v_chua_den_han    = $month['C_COUNT_DANG_THU_LY_DUNG_TIEN_DO'] + $month['C_COUNT_DANG_THU_LY_CHAM_TIEN_DO'];
            $v_qua_han         = $month['C_COUNT_THU_LY_QUA_HAN'];
            $v_bo_sung         = $month['C_COUNT_BO_SUNG'];
            $v_thue            = $month['C_COUNT_THUE'];

            $v_giai_quyet = $v_tra_dung_han + $v_tra_truoc_han + $v_tra_qua_han;
            $v_percent    = $v_giai_quyet ? ($v_tra_dung_han + $v_tra_truoc_han) * 100 / $v_giai_quyet : '';
            //tinh tong
            $tong_tiep_nhan += $v_tiep_nhan;

            $tong_da_giai_quyet += $v_da_giai_quyet;
            $tong_tra_truoc_han += $v_tra_truoc_han;
            $tong_tra_dung_han += $v_tra_dung_han;
            $tong_tra_qua_han += $v_tra_qua_han;
            $tong_rut += $v_cong_dan_rut;
            $tong_tu_choi += $v_bi_tu_choi;

            $tong_dang_giai_quyet += $v_dang_giai_quyet;
            $tong_chua_den_han += $v_chua_den_han;
            $tong_qua_han += $v_qua_han;
            $tong_bo_sung += $v_bo_sung;
            $tong_thue += $v_thue;

            $tong_giai_quyet = $tong_tra_dung_han + $tong_tra_truoc_han + $tong_tra_qua_han;
            $tong_percent    = $tong_giai_quyet ? ($tong_tra_dung_han + $tong_tra_truoc_han) * 100 / $tong_giai_quyet : 0;
            ?>
            <tr nobr="true">
                <td align="center" width="6%"><?php echo $i ?></td>
                <td align="left" width="16%"><?php echo $spec ?></td>
                <td align="center" width="6%"><?php echo $v_tiep_nhan ? $v_tiep_nhan : '' ?></td>

                <td align="center" width="6%"><?php echo $v_da_giai_quyet ? $v_da_giai_quyet : '' ?></td>
                <td align="center" width="6%"><?php echo $v_tra_truoc_han ? $v_tra_truoc_han : '' ?></td>
                <td align="center" width="6%"><?php echo $v_tra_dung_han ? $v_tra_dung_han : '' ?></td>
                <td align="center" width="6%"><?php echo $v_tra_qua_han ? $v_tra_qua_han : '' ?></td>
                <td align="center" width="6%"><?php echo $v_bi_tu_choi ? $v_bi_tu_choi : '' ?></td>
                <td align="center" width="6%"><?php echo $v_cong_dan_rut ? $v_cong_dan_rut : '' ?></td>

                <td align="center" width="6%"><?php echo $v_dang_giai_quyet ? $v_dang_giai_quyet : '' ?></td>
                <td align="center" width="6%"><?php echo $v_chua_den_han ? $v_chua_den_han : '' ?></td>
                <td align="center" width="6%"><?php echo $v_qua_han ? $v_qua_han : '' ?></td>
                <td align="center" width="6%"><?php echo $v_bo_sung ? $v_bo_sung : '' ?></td>

                <td align="center" width="6%"><?php echo $v_thue ? $v_thue : '' ?></td>

                <td align="center" width="6%"><?php echo $v_percent ? number_format($v_percent, 0, ',', '.') . '%' : '_' ?></td>
            </tr>
        <?php endforeach; ?>
        <tr nobr="true">
            <td align="center" width="6%"></td>
            <th align="center" width="16%"><b>Tổng cộng</b></th>
            <td align="center" width="6%"><?php echo $tong_tiep_nhan ?></td>

            <td align="center" width="6%"><?php echo $tong_da_giai_quyet ?></td>
            <td align="center" width="6%"><?php echo $tong_tra_truoc_han ?></td>
            <td align="center" width="6%"><?php echo $tong_tra_dung_han ?></td>
            <td align="center" width="6%"><?php echo $tong_tra_qua_han ?></td>
            <td align="center" width="6%"><?php echo $tong_tu_choi ?></td>
            <td align="center" width="6%"><?php echo $tong_rut ?></td>

            <td align="center" width="6%"><?php echo $tong_dang_giai_quyet ?></td>
            <td align="center" width="6%"><?php echo $tong_chua_den_han ?></td>
            <td align="center" width="6%"><?php echo $tong_qua_han ?></td>
            <td align="center" width="6%"><?php echo $tong_bo_sung ?></td>

            <td align="center" width="6%"><?php echo $tong_thue ?></td>

            <td align="center" width="6%"><?php echo number_format($tong_percent, 0, ',', '.') . '%' ?></td>
        </tr>
    </tbody>
</table>
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
$pdf->writeHTML(ob_get_clean());
$pdf->Output();
?>