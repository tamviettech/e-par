<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');


$v_xml_form_struct_full_path = str_replace('\\', '/', $this->get_xml_config($v_record_type_code, 'form_struct'));
$v_xslt_full_path = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . 'common' . DS . 'guide.xsl';

$xml_string = '<root>';
$xml_string .= '<static_data>';
$xml_string .= '<record_type_code><![CDATA[' . $v_record_type_code . ']]></record_type_code>';
$xml_string .= '<record_type_name><![CDATA[' . $v_record_type_name . ']]></record_type_name>';
$xml_string .= '<user_name><![CDATA[' . Session::get('user_name') . ']]></user_name>';
$xml_string .= '<xml_form_struct_full_path><![CDATA[' . $v_xml_form_struct_full_path . ']]></xml_form_struct_full_path>';
$xml_string .= '</static_data>';
$xml_string .= '</root>';

$xsl_string = file_get_contents($v_xslt_full_path);

//Thu tuc cap huyen -> lay ten Huyen
$v_la_thu_tuc_lien_thong = 0;
if (!Session::get('la_can_bo_cap_xa'))
{
    $v_unit_full_name = $this->get_unit_info('full_name');
}
else //Thu tuc cap xa -> lay ten xa
{
    $v_unit_full_name = Session::get('ou_name');
    $v_la_thu_tuc_lien_thong = 1;
}

$xslt = new Xslt();
$xslt->setXmlString($xml_string);
$xslt->setXslString($xsl_string);

$xslt->setParameter(array(
    'p_site_root'             => SITE_ROOT
    , 'p_current_date'          => date('d/m/Y')
    , 'p_user_full_name'        => Session::get('user_name')
    , 'p_unit_full_name'        => $v_unit_full_name
    , 'p_la_thu_tuc_lien_thong' => $v_la_thu_tuc_lien_thong
        )
);

if ($xslt->transform())
{
    $html = $xslt->getOutput();
    $xslt->destroy();
}
else
{
    $html = '';
}

echo $html;
