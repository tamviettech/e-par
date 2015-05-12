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
    <strong>BÁO CÁO<br/>
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
<br/>
<?php $number = 1; ?>
<?php foreach ($arr_all_month as $month => $v_count_record_type): ?>
    - Tháng <?php echo $month ?>: <?php echo $v_count_record_type ?> cấp phép<br/>
<?php endforeach; ?>
<br/><br/>
<table width="500">
    <tr>
        <td width="33%">
            <strong>Nơi nhận:</strong>
            <?php echo str_repeat('<p>- ...............................................</p>', 6) ?>
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
