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

<?php if (!defined('SERVER_ROOT')) exit('No direct script access allowed');

class login_log_Controller extends Controller {

    function __construct()
    {
        deny_bad_http_referer();
        
        parent::__construct('cores', 'login_log');
        $this->view->template->show_left_side_bar = FALSE;

        //Kiem tra dang nhap
        $this->check_login();
    }

    function main()
    {
        //Kiem tra quyen
        (Session::get('is_admin') == 1) Or die($this->access_denied());

        $this->dsp_all_login_log();
    }
 
    public function dsp_all_login_log()
    {
        $VIEW_DATA['arr_all_login_log'] = $this->model->qry_all_login_log();
        
        $this->view->render(dsp_all_login_log);
    }
}