<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('tcpdf.php');
class ZREPORT extends TCPDF {
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
    }

    function company_info() {
    //Form title
        $this->SetFont('liennd.times', 'B', 14);
        $this->Cell(0, 5, LANG_COMPANY_NAME, 0, 1, 'L', 0, '', 0);

        $this->SetFont('liennd.times', '', 12);
        $this->Cell(0, 5, LANG_COMPANY_ADDRESS, 0, 1, 'L', 0, '', 0);

        if (defined('LANG_COMPANY_EMAIL'))
        {
            $this->SetFont('liennd.times', '', 12);
            $this->Cell(0, 5, LANG_COMPANY_EMAIL, 0, 1, 'L', 0, '', 0);
        }

        $this->SetFont('liennd.times', '', 14);
        $this->Cell(0, 0, 'Cộng hoà xã hội chủ nghĩa Việt Nam', 0, 1, 'R', 0, 0, 0, false, 'M', 'T');
        $this->Cell(0, 0, 'Độc lập - Tự do - Hạnh phúc', 0, 1, 'R', 0, 0, 0, false, 'M', 'T');
        //Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M') {
        // Image example
        //$this->Image('msg-logo.png', 188, 7, 100, 10, '', '', '', true, 150);
    }

    static function vn_former_header()
    {

    }

    function report_title($main_title,$sub_title, $sub_title2 = '') {
        //space
        $this->Cell(0, 5, '', 0, 1, 'C', 0, '', 0);

        $this->SetFont('liennd.times', 'B', 18);
        $this->Cell(0, 10, $main_title, 0, 1, 'C', 0, '', 0);

        $this->SetFont('liennd.times', 'B', 16);
        $this->Cell(0, 5, $sub_title, 0, 1, 'C', 0, '', 0);

        if ($sub_title2 != '')
        {
            $this->SetFont('liennd.times', 'Italic', 14);
            $this->Cell(0, 5, $sub_title2, 0, 1, 'C', 0, '', 0);
        }

        //space
        $this->Cell(0, 5, '', 0, 1, 'C', 0, '', 0);
    }

    function report_date($name, $date=NULL)
    {
        if ($date == NULL)
        {
            $date = Date('d-m-Y');
        }
        $this->Cell(0, 20, '', 0, 1, 'R', 0, '', 0);

        $this->SetFont('liennd.times', 'Italic', 12);
        $txt = $name . ', ngày ' . Date('d', strtotime($date)) . ' tháng ' . Date('m', strtotime($date)) . ' năm ' . Date('Y', strtotime($date));
        $this->Cell(0, 5, $txt, 0, 1, 'R', 0, '', 0);
    }

    public function writeHtmlReport($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='') {
        $this->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'round', 'dash' => 5, 'color' => array(0, 0, 0)));
        $this->writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='');
    }

}
?>
