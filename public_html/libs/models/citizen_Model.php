<?php

class citizen_Model extends Model
{

    protected $_arr_fields_to_save = array(
        'txtName'
    );

    function update_citizen($id, $new_xml_data)
    {
        if ($id == '')
        {
            return;
        }
        $v_current_xml_data = $this->db->GetOne('Select C_XML_DATA From t_cores_citizen Where PK_CITIZEN=?', array($id));
        $arr_current_data = array();
        if ($v_current_xml_data != null)
        {
            $arr_current_data = $this->_parse_xml_data_to_array($v_current_xml_data);
        }
        $dom_new_data = simplexml_load_string($new_xml_data, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($dom_new_data == false)
        {
            return;
        }
        //lọc các trường cần ghi lại
        foreach ($dom_new_data->children() as $dom_item)
        {
            if (!in_array((string) $dom_item->attributes()->id, $this->_arr_fields_to_save))
            {
                continue;
            }
            $arr_current_data[(string) $dom_item->attributes()->id] = (string) $dom_item->value;
        }
        //build xml to save
        $v_xml_to_update = '';
        foreach ($arr_current_data as $k => $v)
        {
            $v_xml_to_update .= "<item id='$k'><value><![CDATA[$v]]></value></item>";
        }
        $v_xml_to_update = "<?xml version='1.0' encoding='UTF-8' ?><data>$v_xml_to_update</data>";
        //execute
        if ($v_current_xml_data != null)
        {
            $this->db->Execute("Update t_cores_citizen Set C_XML_DATA=?, C_LAST_UPDATE=NOW()", array($v_xml_to_update));
        }
        else
        {
            $this->db->Execute('Delete From t_cores_citizen Where PK_CITIZEN=?', array($id));
            $this->db->Execute("Insert Into t_cores_citizen(PK_CITIZEN, C_XML_DATA, C_LAST_UPDATE) Values(?,?, NOW())", array($id, $v_xml_to_update));
        }
    }

    protected function _parse_xml_data_to_array(SimpleXMLElement $dom_xml)
    {
        $arr = array();
        foreach ($dom_xml->children() as $dom_item)
        {
            $arr[(string) $dom_item->attributes()->id] = (string) $dom_item->value;
        }
        return $arr;
    }

    function qry_single_citizen_data($id)
    {
        $xml_data = $this->db->GetOne('Select C_XML_DATA From t_cores_citizen Where PK_CITIZEN=?', array($id));
        $ret = array();
        $dom_xml = simplexml_load_string($xml_data, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($dom_xml != false)
        {
            $ret = $this->_parse_xml_data_to_array($dom_xml);
        }
        return $ret;
    }

}
