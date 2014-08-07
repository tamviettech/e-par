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

defined('DS') or die;

/**
 * Hiển thị thông báo ở các trang tác nghiệp
 */
class notice_Controller extends Controller
{

    /**
     * @var \View 
     */
    public $view;

    function __construct()
    {
        parent::__construct('r3', 'notice');
    }

    function main($role)
    {
        $this->model->db->debug           = DEBUG_MODE < 10 ? 0 : 1;
        
        //echo '<hr>Đếm hồ sơ được chuyển tiếp theo quy trình:<br/>';
        $VIEW_DATA['arr_all_record_type'] = $this->model->qry_all_notice_record_type($role);
        
        
        //echo '<hr>Đếm hồ sơ nhận được do bị trả lại:<br/>';
        $VIEW_DATA['arr_all_rollbacked']  = $this->model->qry_all_rollbacked($role);

        $this->view->render('main', $VIEW_DATA);
    }

}