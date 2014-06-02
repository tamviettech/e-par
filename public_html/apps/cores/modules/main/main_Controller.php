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

class main_Controller extends Controller {

    function __construct()
    {
        parent::__construct('cores', 'main');
        $this->view->template->show_left_side_bar =FALSE;

        //Kiem tra dang nhap
        session::check_login();
    }
    function main()
    {
        $VIEW_DATA['arr_my_application'] = $this->model->qry_all_application();
        $this->view->render('dsp_main_screen', $VIEW_DATA);
    }
}