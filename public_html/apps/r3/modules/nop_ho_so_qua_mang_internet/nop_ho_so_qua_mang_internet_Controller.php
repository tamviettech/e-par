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
<?php

if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

class nop_ho_so_qua_mang_internet_Controller extends Controller
{

    const CONST_NOP_HO_SO_URL_REWRITE = 'nop_ho_so/';

    /**
     * @var \View 
     */
    public $view;

    /**
     * @var \nop_ho_so_qua_mang_internet_Model 
     */
    public $model;

    function __construct()
    {
        parent::__construct('r3', 'nop_ho_so_qua_mang_internet');
        $this->view->template->show_left_side_bar = 1;

        $this->view->template->controller_url = $this->view->get_controller_url();

        include_once SERVER_ROOT . 'libs' . DS . 'recaptchalib.php';

        @session::init();
    }

    private function _check_init_session()
    {
        //tam thoi chau kie tra
        return;
        if (Session::get('nop_ho_so') != 1)
        {
            echo "<strong>Phần mềm hỗ trợ giải quyết thủ tục hành chính theo cơ chế một cửa điện tử hiện đại huyện Lạng Giang</strong> - tỉnh Bắc Giang";
            echo '<br/>';
            echo "Xin mời bấm <a href='" . SITE_ROOT . self::CONST_NOP_HO_SO_URL_REWRITE . "'>vào đây</a> để tiếp tục";
            Session::set('nop_ho_so', 1);
            die();
        }
    }

    function main()
    {
        $this->nhap_thong_tin();
    }

    public function nhap_thong_tin($record_type_id = 0)
    {
        $this->view->title = 'Đăng ký hồ sơ';
        $this->view->arr_web_link  = $this->model->qry_all_web_link();
        
        if (!empty($_POST))
        {
            $VIEW_DATA['response'] = $this->model->do_send();
            if ($VIEW_DATA['response']->success)
            {
                $this->view->layout_render('public_service/panel/dsp_layout','do_send', $VIEW_DATA);
                exit;
            }
        }
        $record_type_id                     = (int) $record_type_id;
        $VIEW_DATA['active_record_type_id'] = $record_type_id;
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_send_over_internet_record_type(true);
        $VIEW_DATA['action']                = __FUNCTION__;
        
        foreach ($VIEW_DATA['arr_all_record_type'] as $arr_single_record_type)
        {
            $v_id                                           = $arr_single_record_type['PK_RECORD_TYPE'];
            $v_name                                         = $arr_single_record_type['C_NAME'];
            $VIEW_DATA['arr_all_record_type_option'][$v_id] = $v_name;
        }
        if ($record_type_id > 0)
        {
            $VIEW_DATA['v_record_type_code'] = $this->model->db->getOne("Select C_CODE 
                From t_r3_record_type 
                Where PK_RECORD_TYPE=? 
                And C_SEND_OVER_INTERNET='1'", array($record_type_id));
            
            $this->view->menu_active = 'dsp_registration_list';
            $this->view->layout_render('public_service/panel/dsp_layout','form', $VIEW_DATA);
        }
    }

    function svc_kiem_tra_nop_internet($record_type_code = '')
    {
        $this->model->db->debug = DEBUG_MODE < 10 ? false : true;
        $resp                   = new stdClass();
        if ($record_type_code)
        {
            $arr_single = $this->model->qry_single_record_type($record_type_code);
            if (!$arr_single)
            {
                $resp->result  = false;
                $resp->message = 'Không tồn tại mã ' . $record_type_code;
            }
            elseif (!$arr_single['C_SEND_OVER_INTERNET'])
            {
                $resp->result  = false;
                $resp->message = 'Thủ tục này chưa hỗ trợ nộp trực tuyến: ' . $record_type_code;
            }
            elseif (!$arr_single['C_STATUS'])
            {
                $resp->result  = false;
                $resp->message = 'Thủ tục này không còn hiệu lực: ' . $record_type_code;
            }
            else
            {
                $resp->result  = true;
                $resp->message = FULL_SITE_ROOT . self::CONST_NOP_HO_SO_URL_REWRITE . 'nhap_thong_tin/' . $arr_single['PK_RECORD_TYPE'];
            }
        }
        else
        {
            $resp = array();
            foreach ($this->model->qry_all_record_types(" C_SEND_OVER_INTERNET = '1' ") as $record_type)
            {
                $record_type['C_URL']         = FULL_SITE_ROOT . self::CONST_NOP_HO_SO_URL_REWRITE . 'nhap_thong_tin/' . $record_type['PK_RECORD_TYPE'];
                $resp[$record_type['C_CODE']] = $record_type;
            }
        }

        header('Content-type: application/json');
        die(json_encode($resp));
    }

    function danh_sach_thu_tuc()
    {
        //Danh sách loại thủ tục tiếp nhận HỒ SƠ qua mạng
        $VIEW_DATA['arr_all_record_type'] = $this->model->qry_all_send_over_internet_record_type();
        $VIEW_DATA['arr_all_spec']        = $this->model->assoc_list_get_all_by_listtype_code('DANH_MUC_LINH_VUC');
        $this->render('danh_sach_thu_tuc', $VIEW_DATA);
    }

    public function do_send()
    {
        $VIEW_DATA['response'] = $this->model->do_send();
        $this->render('do_send', $VIEW_DATA);
    }

    public function svc_kiem_tra_tham_dinh($record_type_code = '')
    {
        $this->model->db->debug = DEBUG_MODE < 10 ? false : true;
        $resp                   = new stdClass();
        if ($record_type_code)
        {
            $arr_single = $this->model->qry_single_record_type($record_type_code);
            if (!$arr_single)
            {
                $resp->result  = false;
                $resp->message = 'Không tồn tại mã ' . $record_type_code;
            }
            elseif (!$arr_single['C_ALLOW_VERIFY_RECORD'])
            {
                $resp->result  = false;
                $resp->message = 'Thủ tục này chưa hỗ trợ nộp thử: ' . $record_type_code;
            }
            elseif (!$arr_single['C_STATUS'])
            {
                $resp->result  = false;
                $resp->message = 'Thủ tục này không còn hiệu lực: ' . $record_type_code;
            }
            else
            {
                $resp->result  = true;
                $resp->message = FULL_SITE_ROOT . self::CONST_NOP_HO_SO_URL_REWRITE . 'nop_de_kiem_tra/' . $arr_single['PK_RECORD_TYPE'];
            }
        }
        else
        {
            $resp = array();
            foreach ($this->model->qry_all_record_types(" C_ALLOW_VERIFY_RECORD>0 ") as $record_type)
            {
                $record_type['C_URL']         = FULL_SITE_ROOT . self::CONST_NOP_HO_SO_URL_REWRITE . 'nop_de_kiem_tra/' . $record_type['PK_RECORD_TYPE'];
                $resp[$record_type['C_CODE']] = $record_type;
            }
        }
        header('Content-type: application/json');
        die(json_encode($resp));
    }

    public function nop_de_kiem_tra($record_type_id = 0)
    {
        if (!empty($_POST))
        {
            $VIEW_DATA['response'] = $this->model->do_send(FALSE);
            if ($VIEW_DATA['response']->success)
            {
                $this->render('do_send', $VIEW_DATA);
                exit;
            }
        }
        $record_type_id                     = (int) $record_type_id;
        $VIEW_DATA['active_record_type_id'] = $record_type_id;
        $VIEW_DATA['arr_all_record_type']   = $this->model->qry_all_send_over_internet_record_type(true);
        $VIEW_DATA['before_title']          = '<a href="javascript:;" title="Trợ giúp" onclick="dsp_help()">KIỂM TRA TRƯỚC HỒ SƠ: </a>';
        $VIEW_DATA['action']                = __FUNCTION__;
        foreach ($VIEW_DATA['arr_all_record_type'] as $arr_single_record_type)
        {
            $v_id                                           = $arr_single_record_type['PK_RECORD_TYPE'];
            $v_name                                         = $arr_single_record_type['C_NAME'];
            $VIEW_DATA['arr_all_record_type_option'][$v_id] = $v_name;
        }
        if ($record_type_id > 0)
        {
            $VIEW_DATA['v_record_type_code'] = $this->model->db->getOne('Select C_CODE 
                From t_r3_record_type 
                Where PK_RECORD_TYPE=?
                And C_ALLOW_VERIFY_RECORD=1', array($record_type_id));
            $this->render('form', $VIEW_DATA);
        }
    }

    function tro_giup()
    {
        $this->view->render('tro_giup');
    }

    /**
     * Danh sách hồ sơ nộp thử đã được thụ lý
     */
    function svc_danh_sach_da_kiem_tra($timestamp = 0)
    {
        $this->model->db->debug = DEBUG_MODE < 10 ? 0 : 1;
        header('Content-type: application/json');
        echo json_encode($this->model->qry_ho_so_da_kiem_tra($timestamp));
    }

    function svc_gui_lai_email($record_no)
    {
        echo '<html><head></head><body>';
        $arr_single_record = $this->model->db->GetRow("Select R.*, RT.C_NAME As C_RECORD_TYPE_NAME
            From t_r3_internet_record R
            Inner Join t_r3_record_type RT
                On R.FK_RECORD_TYPE = RT.PK_RECORD_TYPE
            Where R.C_RECORD_NO=?", array($record_no));
        if ($arr_single_record)
        {
            if ($arr_single_record['C_CLEAR_DATE'])
            {
                $v_email       = $arr_single_record['C_RETURN_EMAIL'];
                $v_prefix_mail = substr($v_email, 0, strpos($v_email, '@'));
                $v_sufix_mail  = substr($v_email, strpos($v_email, '@'));
                if ($this->model->announce_return_internet_record($arr_single_record))
                {

                    echo "Kết quả thụ lý đã được gửi vào địa chỉ 
                    <script>document.write('$v_prefix_mail' + '$v_sufix_mail');</script>
                    &nbsp;Nếu chưa thấy thư bạn cần tìm trong mục thư rác";
                }
                else
                {
                    echo "Gửi thư cho địa chỉ  <script>document.write('$v_prefix_mail' + '$v_sufix_mail');</script> thất bại.
                            &nbsp;Ông/bà thử lại lần sau hoặc do địa chỉ thư điện tử điền lúc nộp hồ sơ chưa đúng.";
                }
            }
            else
            {
                echo 'Hồ sơ chưa hoàn thành thụ lý: ' . $record_no;
            }
        }
        else
        {
            echo 'Không tìm thấy hồ sơ có mã số: ' . $record_no;
        }
        echo '</body></html>';
    }

    protected function render($view, $VIEW_DATA = array())
    {
        ob_start();
        $this->view->render($view, $VIEW_DATA);
        $VIEW_DATA['content'] = ob_get_clean();
        $this->view->render('layout', $VIEW_DATA);
    }

}