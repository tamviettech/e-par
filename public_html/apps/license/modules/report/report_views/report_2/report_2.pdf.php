<?php
ob_end_clean();

ob_start();
defined('DS') or die;
/* @var $this \View */
require_once SERVER_ROOT . 'libs/tcpdf/zreport.php';

$pdf = new ZREPORT();

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(Session::get('user_name'));
$pdf->SetTitle('Báo cáo');
$pdf->SetSubject('Báo cáo');
$pdf->setFontSubsetting(false);
// set default header data
$pdf->SetHeaderData('', 0, '', PDF_HEADER_STRING);
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
// set auto page breaks
$pdf->SetAutoPageBreak(true, PDF_MARGIN_FOOTER + PDF_MARGIN_BOTTOM);
// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->AddPage();

$date = date_create('now');

function _xpath($dom, $xpath)
{
    if (get_class($dom) != 'SimpleXMLElement')
    {
        return false;
    }
    $r = $dom->xpath($xpath);
    return isset($r[0]) ? $r[0] : null;
}

//unit
$dom_unit = simplexml_load_file(SERVER_ROOT . 'public/xml/xml_unit_info.xml');
$v_unit_fullname = _xpath($dom_unit, '//full_name');
?>
<br/>
<br/>
<table width="500">
    <tr>
        <td width="45%" align="center">
            <strong><?php echo mb_strtoupper($v_unit_fullname, 'UTF-8') ?></strong>
        </td>
        <td width="55%" align="center">
            <strong>
                CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<BR/>
                <u>Độc lập - Tự do - Hạnh phúc</u>
            </strong>
        </td>
    </tr>
</table>
<div align="right">
    <br/>
    <?php echo 'Ngày ' . $date->format('d') . ' tháng ' . $date->format('m') . ' năm ' . $date->format('Y') ?>
</div>
<br/>
<div align="center">
    <strong>
        BÁO CÁO<br/>
        Kết quả cấp phép từng tháng<br/>
        <?php
        if ($year):
            echo 'Trong năm ' . $year . '<br/>';
        endif;
        ?>
    </strong>
    <br/>
    __________
</div>
<?php foreach ($arr_all_month as $v_month => $arr_data_single_month): ?>
    <?php
    if (empty($arr_data_single_month))
    {
        continue;
    }
    ?>
    <br/>
    <?php echo 'Tháng ' . $v_month; ?>
    <br/>
    <br/>
    <table border="1" cellpadding="4" cellspacing="0" width="500">
        <tbody>
            <tr>
                <td width="10%" align="center"><strong>STT</strong></td>
                <td width="65%" align="center"><strong>Tên loại giấy phép</strong></td>
                <td width="25%" align="center"><strong>Số lượng cấp phép</strong></td>
            </tr>
            <tr>
                <td align="center"><strong>(1)</strong></td>
                <td align="center"><strong>(2)</strong></td>
                <td align="center"><strong>(3)</strong></td>
            </tr>
            <?php $number = 1; ?>
            <?php foreach ($arr_data_single_month as $v_record_type_name => $v_count_record_type): ?>
                <tr>
                    <td align="center"><?php echo $number++ ?></td>
                    <td><?php echo $v_record_type_name ?></td>
                    <td align="center"><?php echo $v_count_record_type ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endforeach; ?>
<br/><br/>
<table width="500">
    <tr>
        <td width="33%">
            <strong>Nơi nhận:</strong>
            <?php echo str_repeat('<p>- ...............................................</p>', 6) ?>
        </td>
        <td>
        </td>
        <td width="33%">

        </td>
    </tr>
</table>

<?php
// output the HTML content
$pdf->writeHTML(ob_get_clean(), true, 0, true, 0);
// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------
//Close and output PDF document
$pdf->Output('Bao-cao-' . $date->format('Y-m-d'));
