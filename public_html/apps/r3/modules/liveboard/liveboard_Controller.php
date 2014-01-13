<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

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

class liveboard_Controller extends Controller
{

    /**
     *
     * @var \liveboard_Model 
     */
    public $model;

    function __construct()
    {
        parent::__construct('r3', 'liveboard');
        $this->view->template->show_left_side_bar = FALSE;

        /*
          //Kiem tra session
          session::init();
          $login_name = session::get('login_name');
          if ($login_name == NULL)
          {
          session::destroy();
          header('location:' . SITE_ROOT . 'login.php');
          exit;
          }
         */
    }

    function main()
    {
        //Danh sach tat ca cac xa
        $arr_all_village = $this->model->qry_all_village();
        
        $VIEW_DATA['arr_all_village'] = $arr_all_village;
        $this->view->render('dsp_main', $VIEW_DATA);
    }

    public function dsp_liveboard()
    {
        $VIEW_DATA['scope'] = '0,1,2,3';
        $VIEW_DATA['title'] = 'Bảng theo dõi tình hình giải quyết thủ tục hành chính ngày ' . date('d-m-Y');
        $this->view->render('dsp_liveboard', $VIEW_DATA);
    }

    public function dsp_liveboard_huyen()
    {
        $VIEW_DATA['scope'] = '2,3';
        $VIEW_DATA['title'] = 'Bảng theo dõi tình hình giải quyết thủ tục hành chính tiếp nhận tại huyện ngày ' . date('d-m-Y');
        $this->view->render('dsp_liveboard', $VIEW_DATA);
    }

    public function dsp_liveboard_xa($village_id=NULL)
    {
        if ($village_id != NULL)
        {
            $VIEW_DATA['title'] = 'Bảng theo dõi tình hình giải quyết thủ tục hành chính tiếp nhận tại xã ' . get_request_var('v') . ' ngày ' . date('d-m-Y');
            $VIEW_DATA['v_village_id'] = $village_id;
        }
        else
        {
            $VIEW_DATA['title'] = 'Bảng theo dõi tổng hợp tình hình giải quyết thủ tục hành chính tiếp nhận tại xã ngày ' . date('d-m-Y');
            $VIEW_DATA['v_village_id'] = 0;
        }
        
        $VIEW_DATA['scope'] = '0,1';
        
        $this->view->render('dsp_liveboard', $VIEW_DATA);
    }

    /**
     * Danh muc TTHC
     */
    public function dsp_tthc()
    {
        $this->model->scope        = '0,1,2,3';
        $VIEW_DATA['arr_all_type'] = $this->model->qry_all_record_type();
        $arr_workflow              = array();
        foreach ($VIEW_DATA['arr_all_type'] as &$type)
        {
            $dom_workflow   = @simplexml_load_file(SERVER_ROOT . 'apps/r3/xml-config/'
                    . $type['C_CODE'] . '/' . $type['C_CODE'] . '_workflow.xml');
            $type['C_TIME'] = $dom_workflow ? $dom_workflow->attributes()->totaltime : '';
        }
        $this->view->render('dsp_tthc', $VIEW_DATA);
    }

    public function arp_liveboard($scope = '0,2,3')
    {
        $this->model->scope                               = replace_bad_char($scope);
        $VIEW_DATA['scope']                               = $scope;
        
        $v_village_id = get_request_var('village_id',0);
        $VIEW_DATA['v_village_id']                 = $v_village_id;
        
        //Lay danh sach loai hs
        $VIEW_DATA['arr_all_record_type']                 = $this->model->qry_all_record_type();
        //Tiep nhan trong ngay
        $VIEW_DATA['arr_count_today_receive_record']      = $this->model->qry_count_today_receive_record($v_village_id);
        //Da ban giao trong ngay
        $VIEW_DATA['arr_count_today_handover_record']     = $this->model->qry_count_today_handover_record($v_village_id);
        //Dang xu ly
        $VIEW_DATA['arr_count_execing_record']            = $this->model->qry_count_execing_record($v_village_id);
        //Dung tien do
        $VIEW_DATA['arr_count_in_schedule_record']        = $this->model->qry_count_in_schedule_record($v_village_id);
        //Cham tien do
        $VIEW_DATA['arr_count_over_deadline_record']      = $this->model->qry_count_over_deadline_record($v_village_id);
        //Qua han
        $VIEW_DATA['arr_count_expried_record']            = $this->model->qry_count_expried_record($v_village_id);
        //Bo sung
        $VIEW_DATA['arr_count_supplement_record']         = $this->model->qry_count_supplement_record($v_village_id);
        //Cho tra kq
        $VIEW_DATA['arr_count_waiting_for_return_record'] = $this->model->qry_count_waiting_for_return_record($v_village_id);
        //Da tra kq
        $VIEW_DATA['arr_count_returned_record']           = $this->model->qry_count_returned_record($v_village_id);
        //Dang tamh dung
        $VIEW_DATA['arr_count_pausing_record']            = $this->model->qry_count_pausing_record($v_village_id);

        $this->view->render('dsp_apr_liveboard', $VIEW_DATA);
    }

//end func
}