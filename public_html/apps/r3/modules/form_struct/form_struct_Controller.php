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

class form_struct_Controller extends Controller
{

    /**
     *
     * @var \record_type_Model 
     */
    public $model;

    /**
     *
     * @var \View 
     */
    public $view;

    function __construct()
    {
        parent::__construct('r3', 'form_struct');
        $this->view->template->show_left_side_bar = FALSE;

        $this->view->template->app_name = 'R3';

        //Kiem tra session
        session::init();
        //Kiem tra dang nhap
        session::check_login();
    }

    public function main()
    {
        $this->dsp_form_struct();
    }
    public function dsp_form_struct()
    {
        if (get_request_var('sel_record_type'))
        {
            $v_record_type_code = get_request_var('sel_record_type');
            $xml_file_path = $this->view->get_xml_config($v_record_type_code, 'form_struct');
            if ($xml_file_path === NULL)
            {
                $xml_file_path = SERVER_ROOT . 'apps' . DS . $this->app_name . DS . 'xml-config' . DS . $v_record_type_code;
                if (!file_exists($xml_file_path))
                {
                    mkdir($xml_file_path, 0777, true);
                }
                $xml_file_path .= DS . $v_record_type_code . '_form_struct.xml';
                $this->model->creat_new_xml_file($xml_file_path);
            }
            $data_form_struct = $this->model->data_xml_form_struct($xml_file_path);
            $VIEW_DATA['data_form_struct'] = $data_form_struct;
            $this->view->render('form_struct_object_attr', $VIEW_DATA);
        }
    }

    public function update_form_struct()
    {
        $v_record_type_code = get_request_var('sel_record_type');
        $xml_file_path = $this->view->get_xml_config($v_record_type_code, 'form_struct');
        $this->model->update_xml_form_struct($xml_file_path, $this->_get_arr_line($_POST['arr_line']), $this->_get_arr_attr_item($_POST['arr_item']));
    }

    private function _get_arr_line($array_line)
    {
        $arr_lines = array();
        foreach ($array_line as $line)
        {
            $arr_line = explode('-', $line);
            $line_id = $arr_line[0];
            $line_label = $arr_line[1];
            $arr_lines[$line_id] = $line_label;
        }
        return $arr_lines;
    }

    private function _get_arr_attr_item($array_item)
    {
        $arr_item = array();
        foreach ($array_item as $item)
        {
            $_arrt_attr_item = explode('/', $item);
            if (isset($arr_item[$_arrt_attr_item[0]]))
            {
                $_arrt_attr_item[0] .= '.1';
            }
            $arr_item[$_arrt_attr_item[0]] = $_arrt_attr_item[1];
        }
        return $arr_item;
    }

}
