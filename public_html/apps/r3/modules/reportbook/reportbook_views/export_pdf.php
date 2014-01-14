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
defined('SERVER_ROOT') or die();
// Include the main TCPDF library (search for installation path).
require_once(SERVER_ROOT . 'libs/tcpdf/zreport.php');

// create new PDF document
$pdf = new ZREPORT('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(Session::get('user_name'));
$pdf->SetTitle($arr_single_book['c_name']);
$pdf->SetSubject('');
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

//Cover page
if (session::get('la_can_bo_cap_xa') == TRUE)
{
    $v_unit_full_name = (string) Session::get('ou_name');
    $scope            = 'xã';
}
else
{
    $v_unit_full_name = (string) $this->get_unit_info('full_name');
    $scope            = 'huyện';
}
$v_unit_short_name = substr($v_unit_full_name, strpos($v_unit_full_name, $scope));
$v_unit_full_name  = mb_strtoupper($v_unit_full_name, "UTF-8");
$pdf->AddPage();
$pdf->writeHTML("<h2 align=\"center\" style=\"text-decoration:underline;font-size:16;\">$v_unit_full_name</h2>");
$prepend           = str_repeat('<h1> </h1>', 8);
$bookname          = str_replace('(', '<br/>(', $arr_single_book['c_name']);
$pdf->writeHTML($prepend . '<h1 align="center" style="font-size:18">' . $bookname . '</h1>');
$pdf->writeHTML(
        str_repeat('<h1> </h1>', 5)
        . '<h3 align="center" style="font-size:14">'
        . ucfirst($v_unit_short_name)
        . ', năm ' . date_create_from_format('d-m-Y', $begin_date)->format('Y')
        . '</h3>'
);
//content page
$pdf->AddPage();

$html = '';
?>

<?php if ($this->load_abs_xml($this->get_book_config(strtolower($arr_single_book['c_code'])))): ?>
    <?php
    $cols       = $this->dom->xpath("//display_all/list/item[@type != 'primarykey']");
    $count_cols = count($cols);
    $count_rows = isset($arr_all_record[0]['TOTAL_RECORD']) ? (int) $arr_all_record[0]['TOTAL_RECORD'] : 0;
    ?>
    <?php ob_start(); ?>
    <table class="report_list" border="1" cellpadding="4" cellspacing="0">
        <thead>
            <tr>
                <?php for ($j = 0; $j < $count_cols; $j++): ?>
                    <td align="center" width="<?php echo $cols[$j]->attributes()->size ?>"><b><?php echo $cols[$j]->attributes()->name ?></b></td>
                <?php endfor; ?>
            </tr>
            <tr>
                <?php for ($j = 0; $j < $count_cols; $j++): ?>
                    <?php
                    //nếu ko có thuộc tính này lấy STT cột
                    $text = isset($cols[$j]->attributes()->subhead) ? $cols[$j]->attributes()->subhead : $j+1;
                    $width = $cols[$j]->attributes()->size;
                    ?>
                    <td align="center" width="<?php echo $width ?>"><?php echo $text ?></td>
                <?php endfor; ?>
            </tr>
        </thead>
        <?php for ($i = 0; $i < $count_rows; $i++): ?>
            <?php $row = $arr_all_record[$i]; ?>
            <tr nobr="true">
                <?php for ($j = 0; $j < $count_cols; $j++): ?>
                    <?php
                    $text_align = (string) $cols[$j]->attributes()->align;
                    $text       = $this->get_book_row_value_by_xml($row, $cols[$j]->attributes()->id);
                    ?>
                    <td align="<?php echo $text_align ?>" width="<?php echo $cols[$j]->attributes()->size ?>"><?php echo $text ?></td>
                <?php endfor; ?>
            </tr>
            <?php unset($arr_all_record[$i]) ?>
        <?php endfor; ?>
    </table>
    <?php $html = ob_get_clean(); ?>
<?php endif; ?>

<?php
// output the HTML content
$pdf->writeHTML($html, true, 0, true, 0);

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------
//Close and output PDF document
if (!DEBUG_MODE)
{
    $pdf->Output('php://output', 'F');
}
else
{
    echo memory_get_usage() / 1024 / 1024 . 'MB';
}

