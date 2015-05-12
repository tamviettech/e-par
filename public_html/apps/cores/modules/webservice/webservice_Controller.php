<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class webservice_Controller extends Controller {

    function __construct() {
        parent::__construct('cores', 'webservice');
    }

    public function main()
    {
        return NULL;
    }

    public function arp_data_for_xlist_ddli($listtype_code)
    {
        $v_format = get_request_var('format','json');
        $v_listtpe_code = strtoupper(replace_bad_char($listtype_code));
        $arr_list = $this->model->list_get_all_by_listtype_code($listtype_code);

        if ($v_format == 'json')
        {
            @ob_clean();
            header('content-type:application/json');
            echo json_encode($arr_list);
        }
        elseif ($v_format == 'xml')
        {
            $xml = '<data>';
            for ($i=0, $n=count($arr_list); $i<$n; $i++)
            {
                $xml .= '<item value="' . $arr_list[$i]['C_CODE'] . '" name="' . $arr_list[$i]['C_NAME'] . '" />';
            }
            $xml .= '</data>';

            @ob_clean();
            header('content-type:text/xml');
            echo xml_add_declaration($xml);
        }
    }
}