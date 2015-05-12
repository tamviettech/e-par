<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('tcpdf.php');

class ZREPORT extends TCPDF
{

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
    }

    function company_info()
    {
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

    function report_title($main_title, $sub_title, $sub_title2 = '')
    {
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

    function report_date($name, $date = NULL)
    {
        if ($date == NULL)
        {
            $date = Date('d-m-Y');
        }
        $this->Cell(0, 20, '', 0, 1, 'L', 0, '', 0);

        $this->SetFont('liennd.times', 'I', 12);
//      $txt = $name . ', ngày ' . Date('d', strtotime($date)) . ' tháng ' . Date('m', strtotime($date)) . ' năm ' . Date('Y', strtotime($date));
//      $this->Cell(0,5, $txt, 0, 1, 'R', 0, '', 0);

//      Huong  : Khong hien thi ten don vi khi bao cao 
        $txt = 'Ngày ' . Date('d', strtotime($date)) . ' tháng ' . Date('m', strtotime($date)) . ' năm ' . Date('Y', strtotime($date));
        $this->MultiCell(140, 3, '', 0, 'C', 0, 0, '', '', true);
        $this->MultiCell(140, 5, $txt, 0, 'C', 0, 0, '', '', true);
//--        
        
    }

    public function writeHtmlReport($html, $ln = true, $fill = false, $reseth = false, $cell = false, $align = '')
    {
        $this->SetLineStyle(array('width' => 0.1, 'cap'   => 'butt', 'join'  => 'round', 'dash'  => 5, 'color' => array(0, 0, 0)));
        $this->writeHTML($html, $ln     = true, $fill   = false, $reseth = false, $cell   = false, $align  = '');
    }

    /**
     * Output fonts.
     * @author Nicola Asuni
     * @protected
     */
    protected function _putfonts()
    {
        $nf = $this->n;
        foreach ($this->diffs as $diff)
        {
            //Encodings
            $this->_newobj();
            $this->_out('<< /Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences [' . $diff . '] >>' . "\n" . 'endobj');
        }
        $mqr = $this->get_mqr();
        $this->set_mqr(false);
        foreach ($this->FontFiles as $file => $info)
        {
            // search and get font file to embedd
            $fontdir  = $info['fontdir'];
            $file     = strtolower($file);
            $fontfile = '';
            // search files on various directories
            if (($fontdir !== false) AND file_exists($fontdir . $file))
            {
                $fontfile = $fontdir . $file;
            }
            elseif (file_exists($this->_getfontpath() . $file))
            {
                $fontfile = $this->_getfontpath() . $file;
            }
            elseif (file_exists($file))
            {
                $fontfile = $file;
            }
            if (!$this->empty_string($fontfile))
            {
                $font       = file_get_contents($fontfile);
                $compressed = (substr($file, -2) == '.z');
                if ((!$compressed) AND (isset($info['length2'])))
                {
                    $header = (ord($font{0}) == 128);
                    if ($header)
                    {
                        // strip first binary header
                        $font = substr($font, 6);
                    }
                    if ($header AND (ord($font[$info['length1']]) == 128))
                    {
                        // strip second binary header
                        $font = substr($font, 0, $info['length1']) . substr($font, ($info['length1'] + 6));
                    }
                }
                elseif ($info['subset'] AND ((!$compressed) OR ($compressed AND function_exists('gzcompress'))))
                {
                    if ($compressed)
                    {
                        // uncompress font
                        $font = gzuncompress($font);
                    }
                    // merge subset characters
                    $subsetchars = array(); // used chars
                    foreach ($info['fontkeys'] as $fontkey)
                    {
                        $fontinfo = $this->getFontBuffer($fontkey);
                        $subsetchars += $fontinfo['subsetchars'];
                    }
                    // rebuild a font subset
                    //$font            = $this->_getTrueTypeFontSubset($font, $subsetchars);
                    // Alcal: $font2cache modification
                    // This modification creates utf-8 fonts only the first time,
                    // after that it uses cache file which dramatically reduces execution time
                    if (!file_exists($fontfile . '.cached'))
                    {
                        // calculate $font first time
                        $subsetchars = array_fill(0, 512, true); // fill subset for all chars 0-512
                        // Latin Extended Additional (256: 1E00–1EFF)
                        for ($i = 7680; $i <= 7936; $i++)
                        {
                            $subsetchars[$i] = true;
                        }
                        $font       = $this->_getTrueTypeFontSubset($font, $subsetchars); // this part is actually slow!
                        // and then save $font to file for further use
                        $fp         = fopen($fontfile . '.cached', 'w');
                        $flat_array = serialize($font); //
                        fwrite($fp, $flat_array);
                        fclose($fp);
                    }
                    else
                    {
                        // cache file exist, load file
                        $fp         = fopen($fontfile . '.cached', 'r');
                        $flat_array = fread($fp, filesize($fontfile . '.cached'));
                        fclose($fp);
                        $font       = unserialize($flat_array);
                    }
                    // calculate new font length
                    $info['length1'] = strlen($font);
                    if ($compressed)
                    {
                        // recompress font
                        $font = gzcompress($font);
                    }
                }
                $this->_newobj();
                $this->FontFiles[$file]['n'] = $this->n;
                $stream                      = $this->_getrawstream($font);
                $out                         = '<< /Length ' . strlen($stream);
                if ($compressed)
                {
                    $out .= ' /Filter /FlateDecode';
                }
                $out .= ' /Length1 ' . $info['length1'];
                if (isset($info['length2']))
                {
                    $out .= ' /Length2 ' . $info['length2'] . ' /Length3 0';
                }
                $out .= ' >>';
                $out .= ' stream' . "\n" . $stream . "\n" . 'endstream';
                $out .= "\n" . 'endobj';
                $this->_out($out);
            }
        }
        $this->set_mqr($mqr);
        foreach ($this->fontkeys as $k)
        {
            //Font objects
            $font = $this->getFontBuffer($k);
            $type = $font['type'];
            $name = $font['name'];
            if ($type == 'core')
            {
                // standard core font
                $out = $this->_getobj($this->font_obj_ids[$k]) . "\n";
                $out .= '<</Type /Font';
                $out .= ' /Subtype /Type1';
                $out .= ' /BaseFont /' . $name;
                $out .= ' /Name /F' . $font['i'];
                if ((strtolower($name) != 'symbol') AND (strtolower($name) != 'zapfdingbats'))
                {
                    $out .= ' /Encoding /WinAnsiEncoding';
                }
                if ($k == 'helvetica')
                {
                    // add default font for annotations
                    $this->annotation_fonts[$k] = $font['i'];
                }
                $out .= ' >>';
                $out .= "\n" . 'endobj';
                $this->_out($out);
            }
            elseif (($type == 'Type1') OR ($type == 'TrueType'))
            {
                // additional Type1 or TrueType font
                $out = $this->_getobj($this->font_obj_ids[$k]) . "\n";
                $out .= '<</Type /Font';
                $out .= ' /Subtype /' . $type;
                $out .= ' /BaseFont /' . $name;
                $out .= ' /Name /F' . $font['i'];
                $out .= ' /FirstChar 32 /LastChar 255';
                $out .= ' /Widths ' . ($this->n + 1) . ' 0 R';
                $out .= ' /FontDescriptor ' . ($this->n + 2) . ' 0 R';
                if ($font['enc'])
                {
                    if (isset($font['diff']))
                    {
                        $out .= ' /Encoding ' . ($nf + $font['diff']) . ' 0 R';
                    }
                    else
                    {
                        $out .= ' /Encoding /WinAnsiEncoding';
                    }
                }
                $out .= ' >>';
                $out .= "\n" . 'endobj';
                $this->_out($out);
                // Widths
                $this->_newobj();
                $s = '[';
                for ($i = 32; $i < 256; ++$i)
                {
                    if (isset($font['cw'][$i]))
                    {
                        $s .= $font['cw'][$i] . ' ';
                    }
                    else
                    {
                        $s .= $font['dw'] . ' ';
                    }
                }
                $s .= ']';
                $s .= "\n" . 'endobj';
                $this->_out($s);
                //Descriptor
                $this->_newobj();
                $s = '<</Type /FontDescriptor /FontName /' . $name;
                foreach ($font['desc'] as $fdk => $fdv)
                {
                    if (is_float($fdv))
                    {
                        $fdv = sprintf('%F', $fdv);
                    }
                    $s .= ' /' . $fdk . ' ' . $fdv . '';
                }
                if (!$this->empty_string($font['file']))
                {
                    $s .= ' /FontFile' . ($type == 'Type1' ? '' : '2') . ' ' . $this->FontFiles[$font['file']]['n'] . ' 0 R';
                }
                $s .= '>>';
                $s .= "\n" . 'endobj';
                $this->_out($s);
            }
            else
            {
                // additional types
                $mtd = '_put' . strtolower($type);
                if (!method_exists($this, $mtd))
                {
                    $this->Error('Unsupported font type: ' . $type);
                }
                $this->$mtd($font);
            }
        }
    }

}

?>
