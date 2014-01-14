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

<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

/**
 * In phiếu biên nhận hồ sơ bổ sung cho công dân
 */
$arr_single_record = $VIEW_DATA['arr_single_record'];

$v_record_no                = $arr_single_record['C_RECORD_NO'];
$v_record_type_code         = $arr_single_record['RECORD_TYPE_CODE'];
$v_record_type_name         = $arr_single_record['RECORD_TYPE_NAME'];
$v_receive_date             = $arr_single_record['C_RECEIVE_DATE'];
$v_return_date              = $arr_single_record['C_RETURN_DATE'];
$v_xml_data                 = $arr_single_record['C_XML_DATA'];

$v_receive_date = jwDate::yyyymmdd_to_ddmmyyyy($v_receive_date, TRUE);
$v_return_date  = jwDate::yyyymmdd_to_ddmmyyyy($v_return_date, TRUE);

$v_xml_form_struct_full_path = str_replace('\\', '/', $this->get_xml_config($v_record_type_code, 'form_struct'));
$v_xslt_full_path            = $this->get_xsl_ho_teplate($v_record_type_code);

$xml_string = '<root>';
$xml_string .= '<static_data>';
$xml_string .= '<record_type_code>' . $v_record_type_code . '</record_type_code>';
$xml_string .= '<record_type_name>' . $v_record_type_name . '</record_type_name>';
$xml_string .= '<record_no>' . $v_record_no . '</record_no>';
$xml_string .= '<receive_date>' . $v_receive_date . '</receive_date>';
$xml_string .= '<return_date>' . $v_return_date . '</return_date>';
$xml_string .= '<xml_form_struct_full_path><![CDATA[' . $v_xml_form_struct_full_path . ']]></xml_form_struct_full_path>';
$xml_string .= '</static_data>';
$xml_string .= $v_xml_data;
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