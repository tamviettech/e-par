<?php

class update_Model extends Model
{

    function gop_cot_ten_dia_chi()
    {
        $common_dir = SERVER_ROOT . 'apps/r3/xml-config/common/';
        foreach (scandir($common_dir) as $dir_item)
        {
            $path = $common_dir . $dir_item;
            //bo qua dir
            if ($dir_item == '.' || $dir_item == '..' || is_dir($path))
            {
                continue;
            }

            //kiem tra ten file
            if (strpos($dir_item, '_list.xml') === false)
            {
                continue;
            }

            //lay noi dung file
            $xml = file_get_contents($path);
            $dom = simplexml_load_string($xml);

            //kiem tra noi dung
            if (strpos($xml, '<display_all>') === false || !$dom)
            {
                continue;
            }

            //lấy cột tên, địa chỉ
            $dom_citizen_name = xpath($dom, "//item[@id='C_CITIZEN_NAME']", XPATH_DOM);
            $dom_address      = xpath($dom, "//item[@id='xml/txtDiaChi']", XPATH_DOM);
            $dom_action       = xpath($dom, "//item[@type='action']", XPATH_DOM);
            $has_action_col   = $dom_action->attributes()->type == 'action' ? true : false;
            if ($dom_citizen_name->attributes()->id != 'C_CITIZEN_NAME' || $dom_address->attributes()->id != 'xml/txtDiaChi')
            {
                continue;
            }

            //sửa xml
            $width_citizen                        = (int) $dom_citizen_name->attributes()->size;
            $width_address                        = (int) $dom_address->attributes()->size;
            $dom_citizen_name->attributes()->id   = "C_CITIZEN_NAME,'&#x3C;br&#x3E;',xml/txtDiaChi";
            $dom_citizen_name->attributes()->type = "text_concat";
            $dom_citizen_name->attributes()->size = $has_action_col ? ($width_citizen + $width_address / 2) . '%' : ($width_citizen + $width_address) . '%';

            $doc = dom_import_simplexml($dom_address);
            $doc->parentNode->removeChild($doc);

            //lưu xml
            echo $path . '<hr>';
            $xml = $this->format_xml($dom->asXML());
            file_put_contents($path, $xml);
        }
    }

    function format_xml($str)
    {
        $dom                     = new DOMDocument;
        $dom->preserveWhiteSpace = FALSE;
        $dom->encoding           = 'utf-8';
        $dom->version            = '1.0';
        $dom->formatOutput       = TRUE;

        $dom->loadXML($str);
        return $dom->saveXml();
    }

}
