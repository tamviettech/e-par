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
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class form_struct_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    /***
     * Get data file xml
     * 
     * @param string $xml_file_path Path file xml
     * @return array $data_form_struc Data file xml
     */
    public function data_xml_form_struct($xml_file_path)
    {
        $doc_xml = new DOMDocument('1.0', 'utf-8');
        $doc_xml->load($xml_file_path);
        $tags_line = $doc_xml->getElementsByTagName("line");
        $data_form_struct = array();
        foreach ($tags_line as $tag_line)
        {
            $obj_line = array();
            $tags_item = $tag_line->getElementsByTagName("item");
            $obj_line['line_label'] = $tag_line->getAttribute('label');
            $obj_atrr = array();
            $count_item = $tags_item->length;
            for ($i = 0; $i < $count_item; $i++)
            {
                $obj_atrr['item_type'] = $tags_item->item($i)->getAttribute('type');
                $obj_atrr['item_id'] = $tags_item->item($i)->getAttribute('id');
                $obj_atrr['item_name'] = $tags_item->item($i)->getAttribute('name');
                $obj_atrr['item_allownull'] = $tags_item->item($i)->getAttribute('allownull');
                $obj_atrr['item_validate'] = $tags_item->item($i)->getAttribute('validate');
                $obj_atrr['item_label'] = $tags_item->item($i)->getAttribute('label');
                $obj_atrr['item_default_value'] = $tags_item->item($i)->getAttribute('default_value');
                $obj_atrr['item_size'] = $tags_item->item($i)->getAttribute('size');
                $obj_atrr['item_css'] = $tags_item->item($i)->getAttribute('css');
                $obj_atrr['item_event'] = $tags_item->item($i)->getAttribute('Event');
                $obj_atrr['item_view'] = $tags_item->item($i)->getAttribute('view');
                $obj_line[] = $obj_atrr;
            }
            $data_form_struct[] = $obj_line;
        }
        return $data_form_struct;
    }

    /***
     * Update data file xml
     * 
     * @param string $xml_file_path Path file xml
     * @param string $xml_file_path Path file xml
     * @param string $arr_line Array line data
     * @return array $arr_item Array item data
     */
    public function update_xml_form_struct($xml_file_path, $arr_line = null, $arr_item = null)
    {
        //Khoi tao lai xml moi
        $docDest = new DOMDocument('1.0', 'utf-8');
        $docDest->formatOutput = true;
        $docDest->load($xml_file_path);
        $dels = $docDest->getElementsByTagName('form');

        foreach ($dels as $del)
        {
            while ($del->hasChildNodes())
            {
                $del->removeChild($del->childNodes->item(0));
            }
        }
        $root = $docDest->documentElement;
        foreach ($arr_line as $line_key => $line_val)
        {
            //tao 1 line
            $line = $docDest->createElement('line');
            $line->setAttribute('label', $line_val);
            $start_item = 0;
            $muti_item = 1;

            //tao item
            foreach ($arr_item as $item_key => $item_val)
            {
                if ($start_item === 0)
                {
                    $item = $docDest->createElement('item');
                    $line->appendChild($item);
                    $start_item++;
                }
                $arr_item_key = explode('-', $item_key);
                if (strcmp($arr_item_key[0], $line_key) === 0)
                {
                    if (strpos($arr_item_key[1], '.1') > 0 && $muti_item === 1)
                    {
                        $item = $docDest->createElement('item');
                        $line->appendChild($item);
                        $muti_item++;
                    }
                    else
                    {
                        $item->setAttribute(trim($arr_item_key[1], '.1'), $item_val);
                    }
                }
            }
            $line->setAttribute('cols', $muti_item);
            $root->appendChild($line);
        }

        $docDest->save($xml_file_path);
        
        $this->popup_exec_done();
    }
    public function creat_new_xml_file($xml_file_path)
    {
        $docDest = new DOMDocument('1.0', 'utf-8');
        $docDest->formatOutput = true;
        $docDest->load($xml_file_path);
        $tag_form = $docDest->createElement('form');
        $docDest->appendChild($tag_form);
        $docDest->save($xml_file_path);
    }

}
