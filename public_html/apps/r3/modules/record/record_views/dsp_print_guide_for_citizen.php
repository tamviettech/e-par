<?php 
/**
// File name   : 
// Version     : 1.0.0.1
// Begin       : 2012-12-01
// Last Update : 2010-12-25
// Author      : TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
//Copyright (C) 2012-2013  TamViet Technology, Ha Noi, Viet Nam. http://www.tamviettech.vn

// E-PAR is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// E-PAR is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// See LICENSE.TXT file for more information.
*/
if (!defined('SERVER_ROOT')) exit('No direct script access allowed');


$v_xml_form_struct_full_path = str_replace('\\', '/', $this->get_xml_config($v_record_type_code, 'form_struct'));
$v_xslt_full_path            = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . 'common' . DS . 'guide.xsl';

$xml_string = '<root>';
$xml_string .= '<static_data>';
$xml_string .= '<record_type_code>' . $v_record_type_code . '</record_type_code>';
$xml_string .= '<record_type_name>' . $v_record_type_name . '</record_type_name>';
$xml_string .= '<xml_form_struct_full_path><![CDATA[' . $v_xml_form_struct_full_path . ']]></xml_form_struct_full_path>';
$xml_string .= '</static_data>';
$xml_string .= xml_remove_declaration($v_xml_data);
$xml_string .= '</root>';

$xsl_string = file_get_contents($v_xslt_full_path);

$xslt = new Xslt();
$xslt->setXmlString($xml_string);
$xslt->setXslString($xsl_string);

$xslt->setParameter(array(
    'p_site_root' => SITE_ROOT
    , 'p_current_date' => date('d/m/Y')
    )
);

if ($xslt->transform()) {
    $html = $xslt->getOutput();
    $xslt->destroy();
} else {
    $html = '';
}

echo $html;