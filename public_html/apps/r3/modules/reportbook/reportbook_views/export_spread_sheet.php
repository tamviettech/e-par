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

defined('SERVER_ROOT') or die();
require_once(SERVER_ROOT . 'libs/excel/PHPExcel.php');

$objPHPExcel = new PHPExcel();
$page_width  = 130;
// Set properties
$objPHPExcel->getProperties()->setCreator(Session::get('user_name'));
$objPHPExcel->getProperties()->setLastModifiedBy(Session::get('user_name'));
$objPHPExcel->getProperties()->setTitle($arr_single_book['c_name']);
$objPHPExcel->getProperties()->setSubject("Sổ theo dõi hồ sơ");

$objPHPExcel->setActiveSheetIndex(0);
$my_sheet = $objPHPExcel->getActiveSheet();

$objWriter  = new $writer_class($objPHPExcel);
$excel_cols = range('A', 'Z');

if ($this->load_abs_xml($this->get_book_config(strtolower($arr_single_book['c_code']))))
{
    $cols           = $this->dom->xpath("//display_all/list/item[@type != 'primarykey']");
    $count_cols     = count($cols);
    $count_rows     = isset($arr_all_record[0]['TOTAL_RECORD']) ? (int) $arr_all_record[0]['TOTAL_RECORD'] : 0;
    $data_first_row = 3;

    //Thêm mô tả
    $my_sheet->mergeCells('A1:' . $excel_cols[$count_cols - 1] . '1');
    $title = $arr_single_book['c_name'] . "\n"
            . 'Từ ngày: ' . $begin_date . "\n"
            . 'Đến ngày:' . $end_date;
    $my_sheet->setCellValue('A1', $title);
    $my_sheet->getRowDimension('1')->setRowHeight(60);
    $my_sheet->getStyle()->getAlignment()->setWrapText(true);

    //Thêm header
    reset($excel_cols);
    foreach ($cols as $col)
    {
        $excel_cur_col = current($excel_cols);
        $my_sheet->SetCellValue($excel_cur_col . ($data_first_row - 1), $col->attributes()->name);
        $my_sheet->getColumnDimension($excel_cur_col)
                ->setWidth(str_replace('%', '', $col->attributes()->size) * $page_width / 100);
        next($excel_cols);
    }
    //Duyệt dần từng bản ghi và đưa vào excel
    reset($excel_cols);
    for ($i = 0; $i < $count_rows; $i++)
    {
        $row = $arr_all_record[$i];
        for ($j = 0; $j < $count_cols; $j++)
        {
            $cell = $this->get_book_row_value_by_xml($row, $cols[$j]->attributes()->id);
            $my_sheet->SetCellValue($excel_cols[$j] . ($i + $data_first_row), (string) $cell);
        }
    }

    //border
    $styleArray = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
            )
        )
    );
    $my_sheet->getStyle('A1:' . $excel_cols[$count_cols - 1] . ($count_rows + $data_first_row))
            ->applyFromArray($styleArray);

    $my_sheet->getStyle('A1:' . $excel_cols[$count_cols - 1] . ($count_rows + $data_first_row))
            ->getAlignment()
            ->setWrapText(true)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

    //decorate
    $range = 'A' . ($data_first_row - 1) . ':' . $excel_cols[$count_cols - 1] . ($data_first_row - 1);
    $my_sheet->getStyle($range)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('C0C0C0');
    $my_sheet->getStyle($range)->getFont()->setBold(true);
    $my_sheet->getStyle('A1')->getFont()->setSize(14);
    $my_sheet->getStyle('A1')
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $my_sheet->getStyle('A1:A2')
            ->getAlignment()
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    $my_sheet->getStyle('A' . ($data_first_row - 1) . ':' . $excel_cols[$count_cols - 1] . ($data_first_row - 1))
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    //can giua stt
    $my_sheet->getStyle('A' . $data_first_row . ':' . 'A' . ($count_rows + $data_first_row))
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}

if (!DEBUG_MODE)
{
    $objWriter->save('php://output');
}
