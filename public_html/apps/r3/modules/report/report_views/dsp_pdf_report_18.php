<?php
defined('SERVER_ROOT') or die;

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
<h3 align="center" >BÁO CÁO HỒ SƠ TRẢ KẾT QUẢ QUÁ HẠN<br/>
    <?php echo "Từ ngày $begin đến ngày $end" ?>
</h3>

<p align="center">Kính gửi: <?php echo str_repeat('.', 100) ?></p>
<?php $colgroup      = array(10, 15, 15, 15, 15, 15, 15) ?>
<table width="100%" border="1" cellpadding="3">
    <?php $thead         = array('TT', 'Mã hồ sơ', 'Tên công dân', 'Ngày tiếp nhận', 'Ngày trả', 'Quá hạn', 'Lý do') ?>
    <thead>
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
            <tr nobr="true">
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
$pdf->writeHTML(ob_get_clean());
$pdf->Output();
?>