<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class record_Controller extends Controller
{

    function __construct()
    {
        deny_bad_http_referer();
        parent::__construct('license', 'record');
        $this->view->template->show_left_side_bar = FALSE;
        //Kiem tra dang nhap
        session::check_login();
    }

    function main()
    {
        $this->dsp_all_record();
    }

    function dsp_all_record()
    {
        check_permission('XEM_DANH_SACH_HO_SO', 'LICENSE') or die($this->access_denied());
        $VIEW_DATA['v_filter'] = get_post_var('txt_filter');
        $VIEW_DATA['v_license_type_code'] = get_post_var('txt_license_type_code');
        $VIEW_DATA['v_start_date'] = get_post_var('txt_start_date');
        $VIEW_DATA['v_end_date'] = get_post_var('txt_end_date');
        $VIEW_DATA['arr_all_license_type'] = $this->model->qry_all_active_license_type();
        $VIEW_DATA['arr_all_record'] = $this->model->qry_all_record();
        $this->view->render('dsp_all_record', $VIEW_DATA);
    }

    function dsp_single_record($v_record_id)
    {
        require_once 'libs/Suid.php';
        check_permission('XEM_DANH_SACH_HO_SO', 'LICENSE') or die($this->access_denied());
        $VIEW_DATA['arr_all_sector'] = $this->model->qry_all_sector();
        $VIEW_DATA['arr_all_license_type'] = $this->model->qry_all_active_license_type();
        $VIEW_DATA['arr_all_record_file'] = $this->model->qry_all_record_file($v_record_id);

        if ($v_record_id)
        {
            $VIEW_DATA['arr_single_record'] = $this->model->qry_single_record($v_record_id);
        }
        $this->view->render('dsp_single_record', $VIEW_DATA);
    }
    

    function delete_record()
    {
        check_permission('XOA_HO_SO', 'LICENSE') or die($this->access_denied());
        $this->model->goback_url = $this->view->get_controller_url() . 'dsp_all_record';
        $this->model->delete_record();
    }

    function print_record($v_record_id)
    {

        function startsWith($haystack, $needle)
        {
            return $needle === "" || strpos($haystack, $needle) === 0;
        }

        function endsWith($haystack, $needle)
        {
            return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
        }

        function number2text($args)
        {
            return implode('...', $args);
        }

        function get_docx_template($license_type_code)
        {
            return SERVER_ROOT . 'apps/license/xml-config/' . $license_type_code . '/' . $license_type_code . '_template.docx';
        }

        function get_xml_template_config($license_type_code)
        {
            return SERVER_ROOT . 'apps/license/xml-config/' . $license_type_code . '/' . $license_type_code . '_template_config.xml';
        }

        function template_transform($arr_single_item, $record_type_code)
        {
            include('libs/phpmswordparser.php');
            $p = new phpmswordparser;
            $v_xml_data = $arr_single_item['C_XML_DATA'];
            $v_xml_data = new SimpleXMLElement($v_xml_data);
            $v_xml_template_config_path = get_xml_template_config($record_type_code);
            $xml_template_config = simplexml_load_file($v_xml_template_config_path);
            $p->setTemplateFile(get_docx_template($record_type_code));
            $v_output_path = uniqid();
            $p->setOutputFile($v_output_path);
            var_dump($xml_template_config);
            $items = $xml_template_config->xpath('item');
            foreach ($items as $item)
            {
                $code = (string) $item['code'];
                $source = (string) $item['source'];
                if (!$code || !$source)
                {
                    continue;
                }
                if ($source == 'database')
                {
                    $column = (string) $item['column'];
                    if (startsWith($column, "xml/"))
                    {
                        $xml_item_name = substr($column, 4);
                        if (!$xml_item_name)
                        {
                            continue;
                        }
                        $xpath = '//item[@id="' . $xml_item_name . '"]/value';
                        $result = $v_xml_data->xpath($xpath);
                        $value = isset($result[0]) ? $result[0] : '';
                    }
                    else
                    {
                        $value = isset($arr_single_item[$column]) ? $arr_single_item[$column] : '';
                    }
                }
                else if ($source == 'function')
                {
                    $function = (string) $item['function'];
                    if (!$function)
                    {
                        continue;
                    }
                    $args_column = (string) $item['args_column'];
                    $arr_args_column = explode(',', $args_column);
                    $arr_args_value = array();

                    foreach ($arr_args_column as $column)
                    {
                        if (startsWith($column, "xml/"))
                        {
                            $xml_item_name = substr($column, 4);
                            if (!$xml_item_name)
                            {
                                continue;
                            }
                            $xpath = '//item[@id="' . $xml_item_name . '"]/value';
                            $result = $v_xml_data->xpath($xpath);
                            $arr_args_value[] = isset($result[0]) ? $result[0] : '';
                        }
                        else
                        {
                            $arr_args_value[] = $arr_single_item[$column];
                        }
                        $value = $function($arr_args_value);
                    }
                }
                else if ($source == 'date')
                {
                    $value = date_format(new DateTime(), 'd/m/Y');
                }
                else
                {
                    $value = '';
                }
                $p->addPlaceholder($code, $value);
            }
            $ok = $p->createDocument();

            if (!$ok)
            {
                print "ERR: " . $p->error;
            }
            else
            {
                header('Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                if (file_exists($v_output_path))
                {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . basename($arr_single_item['C_CODE'] . '.docx'));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($v_output_path));
                    ob_clean();
                    flush();
                    readfile($v_output_path);
                    unlink($v_output_path);
                }
            }
        }

        $arr_single_record = $this->model->qry_single_record($v_record_id);
        $v_license_type_code = $this->model->qry_license_type_code_by_record_id($v_record_id);
        template_transform($arr_single_record, $v_license_type_code);
    }

    function update_record()
    {
        $this->model->goback_url = $this->view->get_controller_url();
        $this->model->prepare_update_record();
    }

}
