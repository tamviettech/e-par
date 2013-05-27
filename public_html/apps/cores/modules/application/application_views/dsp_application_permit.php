<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

$arr_single_application = $VIEW_DATA['arr_single_application'];
$v_xml_file_name = $arr_single_application['C_XML_PERMIT_FILE_NAME'];

$this->load_xml($v_xml_file_name);
echo $this->render_form_display_single();
